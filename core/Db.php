<?php

namespace Core;

use PDO;
use PDOException;

/**
 * Подключение к базе данных
 */
class Db
{
    public PDO $pdo;

    public function __construct()
    {
        $db = require __DIR__ . '/../config/config_db.php';

        try {
            $this->pdo = new PDO($db['dsn'], $db['user'], $db['pass'], $db['options']);
        } catch (PDOException $e) {
            $error = date('Y-m-d H:i:s') . PHP_EOL . 'Подключение не удалось: ' . $e->getMessage() . PHP_EOL .
                'Файл' . $e->getFile() . 'Строка' . $e->getLine() . PHP_EOL .
                'Код ошибки' . $e->getCode() . PHP_EOL .
                '=====================================================================================' . PHP_EOL;
            file_put_contents(__DIR__ . '/../errors.txt', print_r($error, 1), FILE_APPEND);
        }
    }
}