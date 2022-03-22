<?php

namespace Core;

use App\Command\MainCommand;
use App\Command\TextCommand\MainTextCommand;
use App\Command\TextCommand\NumberDigitCommand;
use App\Command\TextCommand\SearchWordCommand;
use App\Command\Translate\TranslateCommand;
use App\Command\WeatherCommand\WeatherCommand;
use App\Request\StateRequest;
use Exception;
use ReflectionException;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;


/**
 * Класс для работы с API telegram
 */
class ApiTelegramBot extends Api
{
    /**
     * Массив с классами команд
     */
    private array $actions = [
        MainCommand::class,
        MainTextCommand::class,
        SearchWordCommand::class,
        NumberDigitCommand::class,
        WeatherCommand::class,
        TranslateCommand::class,
    ];

    /**
     * Универсальный метод для обращения к методам API Telegram
     */
    public function anySendRequest(string $method, array $params = []): Message
    {
        $response = $this->post($method, $params);

        return new Message($response->getDecodedBody());
    }

    /**
     * Запуск бота
     */
    public function botApp(ApiTelegramBot $bot): void
    {
        try {
            // получаем обновленные данные
            $updates = $bot->getWebhookUpdates();

            $inputCommand = $this->getInputCommand($updates);
            if (is_null($inputCommand)) {
                $answer = new MainCommand();
                $answer->answerIfNotString($bot, $updates);
                return;
            }

            $this->matchCommand($inputCommand, $bot, $updates);
            //file_put_contents(__DIR__ . '/../logs.txt', print_r($updates, 1), FILE_APPEND);
        } catch (Exception $e) {
            $error = date('Y-m-d H:i:s') . PHP_EOL . 'Ошибка: ' . $e->getMessage() . " " . $e->getFile() . " " . $e->getLine() .
                PHP_EOL . '=====================================================================================' . PHP_EOL;
            file_put_contents(__DIR__ . '/../errors.txt', print_r($error, 1), FILE_APPEND);
        }
    }

    /**
     * Сверяем входящую команду со списком имеющихся команд
     */
    private function matchCommand(string $inputCommand, object $bot, object $updates): void
    {
        try {
            $commands = $this->getListCommand();

            foreach ($commands as $commandText => $commandAction) {

                if ($commandText === $inputCommand) {

                    [$class, $method] = $commandAction;

                    $obj = new $class();
                    $obj->$method($bot, $updates);
                    return;
                }
            }

            $this->notFoundCommand($commands, $bot, $updates);

        } catch (Exception $e) {
            $error = date('Y-m-d H:i:s') . PHP_EOL . 'Ошибка: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() . ' ' . $e->getCode(). PHP_EOL . '=====================================================================================' . PHP_EOL;
            file_put_contents(__DIR__ . '/../errors.txt', print_r($error, 1), FILE_APPEND);
        }
    }

    /**
     * Получить существующие команды в массив.
     */
    private function getListCommand(): array
    {
        $collector = new CommandsCollector();

        $commands = [];
        foreach($this->actions as $action)
        {
            try {
                $classCommands = $collector->collect($action);
                $commands = array_merge($commands, $classCommands);
            } catch (ReflectionException $e) {
                $error = date('Y-m-d H:i:s') . PHP_EOL . 'Ошибка: ' . $e->getMessage() . PHP_EOL . '=====================================================================================' . PHP_EOL;
                file_put_contents(__DIR__ . '/../errors.txt', print_r($error, 1), FILE_APPEND);
            }
        }
        return $commands;
    }

    /**
     * Получить входящую команду.
     */
    private function getInputCommand(object $updates): string|null
    {
        $stateRequest = new StateRequest();

        $state = $stateRequest->getState($updates);

        return $state['state'] ?? $updates['callback_query']['data'] ?? $updates["message"]["text"];
    }

    /**
     * Если команда не найдена.
     */
    private function notFoundCommand(array $commands, object $bot, object $updates): void
    {
        [$class, $method] = $commands['fallback'];
        $obj = new $class();
        $obj->$method($bot, $updates);
    }
}