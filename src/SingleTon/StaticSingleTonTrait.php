<?php

namespace Haoa\Util\SingleTon;

trait StaticSingleTonTrait
{

    private static $instance;

    static function getInstance(): static
    {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

}