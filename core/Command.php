<?php

/**
 * @Annotation
 * @Target({"CLASS","METHOD"})
 */
final class Command
{
    public $text;
    public $callback;
    public $input;
}