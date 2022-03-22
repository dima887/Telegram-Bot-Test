<?php

require 'vendor/autoload.php';

use Core\ApiTelegramBot;


/**
 * Класс для работы с API Telegram.
 * Передаём в класс токен бота
 */
$telegram =  new ApiTelegramBot(TOKEN);


/**
 * Запускаем бота
 */
$telegram->botApp($telegram);
