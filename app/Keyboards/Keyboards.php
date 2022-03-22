<?php

namespace App\Keyboards;

/**
 * Класс содержащий все клавиатуры
 */
class Keyboards
{
    /**
     * Клавиатура - Главная клавиатура текстовых команд.
     */
    public array $main_text_inline = [
        [
            ['text' => 'Поиск слов', 'callback_data' => 'search_word'],
            ['text' => 'Число на разряды', 'callback_data' => 'select_number_on_digit'],
        ],
    ];

    /**
     * Ещё раз разбить число на разряды
     */
    public array $again_number_on_digit = [
        [
            ['text' => 'Ещё раз', 'callback_data' => 'again_number_on_digit'],
        ]
    ];

    /**
     * Клавиатура - На каком языке искать слова.
     */
    public array $search_word_inline = [
        [
            ['text' => 'Поиск русских слов', 'callback_data' => 'select_ru_word'],
            ['text' => 'Поиск английских слов', 'callback_data' => 'select_en_word'],
        ],
        [
            ['text' => 'Назад', 'callback_data' => '/text'],
        ],
    ];

    /**
     * Клавиатура - Варианты определения местоположения для показа погоды.
     */
    public array $select_city_weather_inline = [
        [
            ['text' => 'Город', 'callback_data' => 'weather_city'],
            ['text' => 'Геолокация', 'callback_data' => 'weather_location'],
        ]
    ];
}