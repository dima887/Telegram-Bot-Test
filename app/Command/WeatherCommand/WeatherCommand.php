<?php

namespace App\Command\WeatherCommand;

use App\Command\BaseCommand;
use Core\ApiTelegramBot;
use Exception;
use Storage\Sticker\Sticker;
use Telegram\Bot\Objects\Update as ObjectsUpdate;

/**
 * Обрабатка команд для показа погоды
 *
 * Погода от Weather API. https://openweathermap.org/api
 */
class WeatherCommand extends BaseCommand
{
    /**
     * Команда - Погода. Способы указания местоположения.
     * @Command(text="/weather")
     */
    public function weather(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $text = '☀Я могу показать Вам погоду в любом городе мира.'
            . PHP_EOL . 'Для получения погоды вы можете указать название <b>города</b> или название <b>города + код</b> страны.'
            . PHP_EOL . 'Также вы можете отправить <b>геолокацию</b>(доступно с мобильных устройств).'
            . PHP_EOL . '⬇Выберите один из вариантов.⬇';

        $telegram->sendMessage([
            'chat_id' => $updates["message"]["chat"]["id"],
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $telegram->replyKeyboardMarkup([
                'inline_keyboard' => $this->keyboards->select_city_weather_inline
            ])
        ]);
    }

    /**
     * Команда - Выбрать в inline клавиатуре показ погоды по названию города.
     * @Command(callback="weather_city")
     */
    public function selectWeatherCity(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $state = 'show_weather_city';
        $this->state_request->setState($state, $updates);
        $text = '✅Введите название города, код страны(опционально):'
            . PHP_EOL . 'Примеры: moscow или москва,ру';
        $telegram->anySendRequest('editMessageText', [
            'chat_id' => $updates['callback_query']['message']['chat']['id'],
            'message_id' => $updates['callback_query']['message']['message_id'],
            'text' => $text,
        ]);
    }

    /**
     * Команда - Показать информацию о погоде по названию города
     * @Command(input="show_weather_city")
     */
    public function weatherCity(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $weather_url = WEATHER_URL . "&q={$updates['message']['text']}";
        $this->getAnswerWeather($weather_url, $telegram, $updates);
        $this->state_request->setStateNull($updates);
    }

    /**
     * Команда - Выбрать в inline клавиатуре показ погоды по геолокации
     * @Command(callback="weather_location")
     */
    public function selectWeatherLocation(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        $state = 'show_weather_location';
        $this->state_request->setState($state, $updates);
        $text = '✅Отправьте свою геолокацию(доступно с мобильных устройств):';
        $telegram->anySendRequest('editMessageText', [
            'chat_id' => $updates['callback_query']['message']['chat']['id'],
            'message_id' => $updates['callback_query']['message']['message_id'],
            'text' => $text,
        ]);
    }

    /**
     * Команда - Показать информацию о погоде по геолокации
     * @Command(input="show_weather_location")
     */
    public function weatherLocation(ApiTelegramBot $telegram, ObjectsUpdate $updates): void
    {
        try {
            $weather_url = WEATHER_URL . "&lat={$updates['message']['location']['latitude']}&lon={$updates['message']['location']['longitude']}";
            $this->getAnswerWeather($weather_url, $telegram, $updates);
            $this->state_request->setStateNull($updates);
        } catch (Exception $e) {
            $error = date('Y-m-d H:i:s') . PHP_EOL . 'Ошибка: ' . $e->getMessage() . PHP_EOL .
                'Файл' . $e->getFile() . 'Строка' . $e->getLine() . PHP_EOL .
                'Код ошибки' . $e->getCode() . PHP_EOL .
                '=====================================================================================' . PHP_EOL;
            file_put_contents(__DIR__ . '/../../../errors.txt', print_r($error, 1), FILE_APPEND);
        }
    }

    /**
     * Ответ. Информация о погоде.
     */
    private function getAnswerWeather(string $weather_url, object $telegram, object $updates): void
    {
        $res = json_decode(file_get_contents($weather_url));

        if (empty($res)) {
            $telegram->sendMessage([
                'chat_id' => $updates["message"]["chat"]["id"],
                'text' => '‼Не корректно указан город‼'
                    . PHP_EOL . '/help - Список команд'
                    . PHP_EOL . '/weather - Погода',
                'parse_mode' => 'HTML',
            ]);
            return;
        }
        $sticker = new Sticker();

        $telegram->sendSticker([
            'chat_id' => $updates['message']['chat']['id'],
            'sticker' => $sticker->weather[$res->weather[0]->icon],
        ]);

        $temperature = round($res->main->temp);

        $text = "<u>Информация о погоде:</u>" . PHP_EOL . "Город: <b>$res->name</b>" . PHP_EOL . "Страна: <b>{$res->sys->country}</b>"
            . PHP_EOL . "Погода: <b>{$res->weather[0]->description}</b>" . PHP_EOL . "Температура: <b>{$temperature}℃</b>"
            . PHP_EOL . '/help - Список команд' . PHP_EOL . '/weather - Погода';

        $telegram->sendMessage([
            'chat_id' => $updates["message"]["chat"]["id"],
            'text' => $text,
            'parse_mode' => 'HTML',
        ]);
    }
}