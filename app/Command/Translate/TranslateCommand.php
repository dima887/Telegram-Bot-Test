<?php

namespace App\Command\Translate;

use App\Command\BaseCommand;
use Core\ApiTelegramBot;
use Dejurin\GoogleTranslateForFree;
use Telegram\Bot\Objects\Update as ObjectsUpdate;

/**
 * Обработка команд для перевода с русского на английский и наоборот
 *
 * Для перевода используется библиотека php-google-translate-for-free
 * https://github.com/dejurin/php-google-translate-for-free
 */
class TranslateCommand extends BaseCommand
{
    /**
     * Команда - Переводчик.
     * @Command(text="/translate")
     */
    public function translate(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $state = 'translate';

        $this->state_request->setState($state, $updates);

        $text = "✅ Наберите текст"
            . PHP_EOL . "Я автоматически определяю, английский это или русский, и перевожу.";

        $telegram->sendMessage([
            'chat_id' => $updates["message"]["chat"]["id"],
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    /**
     *  Получить перевод
     * @Command(input="translate")
     */
    public function getTranslate(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $this->state_request->setStateNull($updates);

        $translate = $this->defineLanguage($updates);

        $attempts = 5;

        $tr = new GoogleTranslateForFree();

        $result = $tr->translate($translate['source'], $translate['target'], $updates['message']['text'], $attempts);

        if ($result) {
            $telegram->sendMessage([
                'chat_id' => $updates['message']['chat']['id'],
                'text' => $translate['on_lang']
                    . PHP_EOL
                    . PHP_EOL . $result
                    . PHP_EOL
                    . PHP_EOL . '/help - Список команд'
                    . PHP_EOL . '/translate - Переводчик',
            ]);
            return;
        }
        $text = '‼Я не смог перевести это...‼'
            . PHP_EOL . '/help - Список команд'
            . PHP_EOL . '/translate - Переводчик';

        $telegram->sendMessage([
            'chat_id' => $updates['message']['chat']['id'],
            'text' => $text,
        ]);
    }

    /**
     * Определяет язык для перевода
     */
    private function defineLanguage(object $updates): array
    {
        if (preg_match('#[a-z]+#i', $updates['message']['text'])) {
            $result['source'] = 'en';
            $result['target'] = 'ru';
            $result['on_lang'] = '✅Русский:⬇';
        }else {
            $result['source'] = 'ru';
            $result['target'] = 'en';
            $result['on_lang'] = '✅Английский:⬇';
        }
        return $result;
    }
}