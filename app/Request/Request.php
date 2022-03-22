<?php

namespace App\Request;

use Core\Db;

/**
 * Базовый класс для запросов к базе данных
 */
class Request
{
    public Db $pdo;

    public function __construct()
    {
        $this->pdo = new Db();
    }

}