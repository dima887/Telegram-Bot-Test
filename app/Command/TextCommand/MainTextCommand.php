<?php

namespace App\Command\TextCommand;

use App\Command\BaseCommand;
use Core\ApiTelegramBot;
use Telegram\Bot\Objects\Update as ObjectsUpdate;

/**
 * Обработка текстовых команд.
 */
class MainTextCommand extends BaseCommand
{
    /**
     * Команда - Список текстовых команд
     * @Command(text="/text") or @Command(callback="/text")
     */
    public function text(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $text = "Список текстовых команд. Выберите что вы хотите сделать.";

        if ($updates['callback_query']) {
            $telegram->anySendRequest('editMessageText', [
                'chat_id' => $updates['callback_query']["message"]["chat"]["id"],
                'message_id' => $updates['callback_query']['message']['message_id'],
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => $telegram->replyKeyboardMarkup([
                    'inline_keyboard' => $this->keyboards->main_text_inline
                ])
            ]);
            return;
        }
        $telegram->sendMessage([
            'chat_id' => $updates["message"]["chat"]["id"],
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $telegram->replyKeyboardMarkup([
                'inline_keyboard' => $this->keyboards->main_text_inline
            ])
        ]);
    }
}