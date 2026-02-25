<?php

namespace Haoa\Util\Singleton;

trait SimpleSingletonTrait
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