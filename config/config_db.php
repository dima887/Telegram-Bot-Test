<?php

/**
 * Настройки для подключения к базе данных
 */
return [
    'dsn' => 'mysql:host=localhost;dbname=telegram_bot_test;charset=utf8',
    'user' => 'root',
    'pass' => '',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ],
];
