<?php

namespace Haoa\Util\Singleton;

use Haoa\Util\Context\RunContext;

trait ContextSingletonTrait
{
    private static string $keyPrefix = '_singleton:';

    public static function getKeyPrefix(): string
    {
        return self::$keyPrefix;
    }

    public static function setKeyPrefix(string $prefix): void
    {
        self::$keyPrefix = $prefix;
    }

    public static function getInstance(): static
    {
        $key = self::$keyPrefix . static::class;
        $instance = RunContext::get($key);
        if ($instance !== null) {
            return $instance;
        }
        $instance = new static();
        RunContext::set($key, $instance);
        return $instance;
    }
}
