<?php

namespace App\Command;

use Core\ApiTelegramBot;
use Telegram\Bot\Objects\Update as ObjectsUpdate;

/**
 * Обработка главных команд
 */
class MainCommand extends BaseCommand
{
    /**
     * Команда - Описание бота
     * @Command(text="/start")
     */
    public function start(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $this->state_request->setStateNull($updates);

        $text = '🙋 Это многофункциональный бот.'
            . PHP_EOL . 'Он умеет:'
            . PHP_EOL . '✅ Производить разного вида манипуляции с текстом.'
            . PHP_EOL . '<b><u>Например:</u></b>'
            . PHP_EOL . '📃 - найти русские или английские слова в тексте'
            . PHP_EOL . '🔢 - разбить число на разряды'
            . PHP_EOL
            . PHP_EOL . '<b>✅Переводить текст</b>'
            . PHP_EOL . '🗒 - Автоматически определит, английский это или русский текст, и переведёт его'
            . PHP_EOL
            . PHP_EOL . '<b>✅ Показывать погоду в любом городе планеты</b>'
            . PHP_EOL . '⛅ - по названию города или геолокации.'
            . PHP_EOL
            . PHP_EOL . '/help - посмотрите список команд.';

        $telegram->sendMessage([
            'chat_id' => $updates["message"]["chat"]["id"],
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    /**
     * Команда - Список команд
     * @Command(text="/help")
     */
    public function help(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $text = '<b><u>Список команд</u></b>'
            . PHP_EOL
            . PHP_EOL . '/start - Описание бота'
            . PHP_EOL . '/help - Список команд'
            . PHP_EOL . '/text - Работа с текстом'
            . PHP_EOL . '/translate - Переводчик.'
            . PHP_EOL . '/weather - Погода';

        $telegram->sendMessage([
            'chat_id' => $updates["message"]["chat"]["id"],
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    /**
     * Обработка не текстовой команды.
     */
    public function answerIfNotString(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $text = "Привет <b>{$updates["message"]["from"]["username"]}!</b> Я понимаю только текст.";
        $telegram->sendMessage([
            'chat_id' => $updates["message"]["chat"]["id"],
            'parse_mode' => 'HTML',
            'text' => $text,
        ]);
    }

    /**
     * Обработка не сущесвующей команды.
     * @Command(text="fallback")
     */
    public function fallback(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $text = $updates["message"]["text"];

        $chat_id = $updates["message"]["chat"]["id"];

        $updates = "🤷По запросу \"<b>$text</b>\" ничего не найдено.";

        $telegram->sendMessage([
            'chat_id' => $chat_id,
            'parse_mode' => 'HTML',
            'text' => $updates
        ]);
    }
}