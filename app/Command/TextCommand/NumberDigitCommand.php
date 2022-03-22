<?php

namespace App\Command\TextCommand;

use App\Command\BaseCommand;
use Core\ApiTelegramBot;
use Telegram\Bot\Objects\Update as ObjectsUpdate;

class NumberDigitCommand extends BaseCommand
{
    /**
     * Команда - Введите число которое нужно разбить по разрядам.
     * 10000000 => 10.000.000
     * @Command(callback="select_number_on_digit","again_number_on_digit")
     */
    public function selectNumberDigit(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $state = 'number_on_digit';
        $this->state_request->setState($state, $updates);

        $text = 'Введите число которое нужно разбить по разрядам.'
            . PHP_EOL . '<b>Например:</b> 10000000'
            . PHP_EOL . '<b>Результат:</b> 10.000.000';

        if ($updates['callback_query']['data'] === 'select_number_on_digit') {
            $telegram->anySendRequest('editMessageText', [
                'chat_id' => $updates['callback_query']["message"]["chat"]["id"],
                'message_id' => $updates['callback_query']['message']['message_id'],
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);
            return;
        }
        //Если выбрано "Ещё раз"
        $telegram->sendMessage([
            'chat_id' => $updates['callback_query']["message"]["chat"]["id"],
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }

    /**
     * Команда - Получить число по разрядам
     * 10000000 => 10.000.000
     * @Command(input="number_on_digit")
     */
    public function numberOnDigit(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $result = $this->getNumberOnDigit($updates['message']['text']);
        $text = $result
            . PHP_EOL
            . PHP_EOL . '/help - Список команд'
            . PHP_EOL . '/text - Текст';

        $telegram->sendMessage([
            'chat_id' => $updates["message"]["chat"]["id"],
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $telegram->replyKeyboardMarkup([
                'inline_keyboard' => $this->keyboards->again_number_on_digit
            ])
        ]);

        $this->state_request->setStateNull($updates);
    }

    /**
     * Разбить число по разрядам.
     */
    private function getNumberOnDigit(string $text): string
    {
        $pattern = '#(?<=\d)(?=(\d{3})+(?!\d))#';

        $replacement = '.';

        return preg_replace($pattern, $replacement, $text);
    }
}