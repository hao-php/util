<?php

namespace Haoa\Util\Context;

use Haoa\Util\Util;

class RunContext
{

    private static $container = [];

    private static function getCid()
    {
        return \Swoole\Coroutine::getCid();
    }

    public static function getHandler(): BaseContext
    {
        $cid = self::getCid();
        if (!isset(self::$container[$cid])) {
            self::$container[$cid] = ContextFactory::getContext();
            // 协程结束时自动清理，防止内存泄漏
            if (Util::isCoroutine()) {
                \Swoole\Coroutine\defer(fn() => self::destroy());
            }
        }
        return self::$container[$cid];
    }

    public static function init()
    {
        return self::getHandler();
    }

    public static function get($key)
    {
        return self::getHandler()->get($key);
    }

    public static function set($key, $value)
    {
        return self::getHandler()->set($key, $value);
    }

    public static function mustGet($key)
    {
        return self::getHandler()->mustGet($key);
    }

    public static function has($key)
    {
        return self::getHandler()->has($key);
    }

    public static function delete($key)
    {
        return self::getHandler()->delete($key);
    }

    public static function destroy()
    {
        $cid = self::getCid();
        if (isset(self::$container[$cid])) {
            self::$container[$cid]->clear();
            unset(self::$container[$cid]);
        }
    }

}