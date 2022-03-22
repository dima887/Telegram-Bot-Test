<?php

namespace Core;

use Command;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Проходится по классу и собирает команды, на которые срабатывает обработчик
 */
class CommandsCollector
{
    /**
     * Выбирает из класса все публичные методы, затем вытаскивает
     * из них текст команд, на которые они должны срабатывать и формирует
     * массив вида:
     *
     * [
     *      '/start' => ['MainCommand', 'start']
     * ]
     *
     * Т.е. на введенную пользователем команду будет срабатывать метод start класса MainCommand
     * @throws ReflectionException
     */
    public function collect($class): array
    {
        $reader    = new AnnotationReader();
        $refClass  = new ReflectionClass($class);

        $refMethods = $refClass -> getMethods(ReflectionMethod::IS_PUBLIC);

        $commands = [];
        foreach($refMethods as $method)
        {
            $command = $reader->getMethodAnnotation($method, Command::class);

            if($command) {
                $commands[$command->text] = [$method->class, $method->name];
                $commands[$command->callback] = [$method->class, $method->name];
                $commands[$command->input] = [$method->class, $method->name];
            }
        }

        return $commands;
    }
}