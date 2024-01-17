<?php

namespace Haoa\Util\SingleTon;


use Haoa\Util\Context\RunContext;

trait ContextSingleTonTrait
{

    protected static $keyPre;

    public static function getKeyPre()
    {
        if (empty(self::$keyPre)) {
            return '_contextSingleTon:';
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
        if (!empty($obj)) {
            return $obj;
        }
        $obj = new static();
        RunContext::set($key, $obj);
        return $obj;
    }

}