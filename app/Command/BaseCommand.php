<?php

namespace App\Command;

use App\Keyboards\Keyboards;
use App\Request\StateRequest;

/**
 * Базовый класс команд.
 */
class BaseCommand
{
    /**
     * Состояние переписки
     */
    protected StateRequest $state_request;

    /**
     * Клавиатуры
     */
    protected Keyboards $keyboards;

    public function __construct()
    {
        $this->state_request = new StateRequest();
        $this->keyboards = new Keyboards();
    }

}