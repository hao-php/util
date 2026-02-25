<?php

namespace Haoa\Util\Singleton;


use Haoa\Util\Context\RunContext;

trait ContextSingletonTrait
{

    protected static $keyPre;

    public static function getKeyPre()
    {
        if (empty(self::$keyPre)) {
            return '_contextSingleton:';
        }
        return self::$keyPre;
    }

    public static function setKeyPre(string $pre)
    {
        self::$keyPre = $pre;
    }

    public static function getInstance(): static
    {
        $key = self::getKeyPre() . static::class;
        $obj = RunContext::get($key);
        if ($obj !== null) {
            return $obj;
        }
        $obj = new static();
        RunContext::set($key, $obj);
        return $obj;
    }

}
