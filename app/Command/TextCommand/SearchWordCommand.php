<?php

namespace App\Command\TextCommand;

use App\Command\BaseCommand;
use Core\ApiTelegramBot;
use Telegram\Bot\Objects\Update as ObjectsUpdate;


/**
 * Обрабатка команд по поиску русских или английских слов в тексте
 */
class SearchWordCommand extends BaseCommand
{
    /**
     * Команда - Выбрать какие слова искать в тексте, русские или английские.
     * @Command(callback="search_word")
     */
    public function search_word(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $text = "Какие слова будем искать?";

        $telegram->anySendRequest('editMessageText', [
            'chat_id' => $updates['callback_query']["message"]["chat"]["id"],
            'message_id' => $updates['callback_query']['message']['message_id'],
            'text' => $text,
            'reply_markup' => $telegram->replyKeyboardMarkup([
                'inline_keyboard' => $this->keyboards->search_word_inline
            ])
        ]);
    }

    /**
     * Команда - Выбор в inline клавиатуре поиск русских слов в тексте
     * select = ru
     * @Command(callback="select_ru_word")
     */
    public function selectRuWord(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $state = 'search_ru_word';

        $this->state_request->setState($state, $updates);

        $text = '✅Поиск русских слов.'
            . PHP_EOL . 'Наберите или вставьте текст и вы получите список русских слов из текста.';

        $telegram->anySendRequest('editMessageText', [
            'chat_id' => $updates['callback_query']['message']['chat']['id'],
            'message_id' => $updates['callback_query']['message']['message_id'],
            'text' => $text,
        ]);
    }

    /**
     * Команда - Поиск русских слов
     * search = ru
     * @Command(input="search_ru_word")
     */
    public function searchRuWord(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $matches = $this->getRuWord($updates['message']['text']);

        $match = implode("\n", $matches[0]);

        $count = count($matches[0]);

        $text = "<b>✅Найдено $count слов(а).</b>"
            . PHP_EOL . '<u>⬇Список русских слов:⬇</u>'
            . PHP_EOL
            . PHP_EOL . $match
            . PHP_EOL
            . PHP_EOL . '/help - Список команд'
            . PHP_EOL . '/text - Текст';

        $telegram->sendMessage([
            'chat_id' => $updates["message"]["chat"]["id"],
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);

        $this->state_request->setStateNull($updates);
    }

    /**
     * Команда - Выбор в inline клавиатуре поиск английских слов в тексте
     * select = en
     * @Command(callback="select_en_word")
     */
    public function selectEnWord(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $state = 'search_en_word';

        $this->state_request->setState($state, $updates);

        $text = '✅Поиск английских слов.'
            . PHP_EOL . 'Наберите или вставьте текст и вы получите список английских слов из текста.';

        $telegram->anySendRequest('editMessageText', [
            'chat_id' => $updates['callback_query']['message']['chat']['id'],
            'message_id' => $updates['callback_query']['message']['message_id'],
            'text' => $text,
        ]);
    }

    /**
     * Команда - Поиск английских слов
     * search = en
     * @Command(input="search_en_word")
     */
    public function searchEnWord(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $matches = $this->getEnWord($updates['message']['text']);

        $match = implode("\n", $matches[0]);

        $count = count($matches[0]);

        $text = "<b>✅Найдено $count слов(а).</b>"
            . PHP_EOL . '<u>⬇Список английских слов:⬇</u>'
            . PHP_EOL
            . PHP_EOL . $match
            . PHP_EOL
            . PHP_EOL . '/help - Список команд'
            . PHP_EOL . '/text - Текст';

        $telegram->sendMessage([
            'chat_id' => $updates["message"]["chat"]["id"],
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);

        $this->state_request->setStateNull($updates);
    }

    /**
     * Получить русские слова из текста.
     */
    private function getRuWord(string $text): array
    {
        preg_match_all('#(\b[А-яёЁ]+)\b#u', $text, $matches);

        return $matches;
    }

    /**
     * Получить английские слова из текста.
     */
    private function getEnWord(string $text): array
    {
        preg_match_all('#(\b[a-zA-Z]+\b)#u', $text, $matches);

        return $matches;
    }
}