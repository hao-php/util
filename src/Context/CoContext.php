<?php

namespace Haoa\Util\Context;

use Swoole\Coroutine;

class CoContext extends BaseContext
{

    const KEY_CONTEXT = '_context';

    public function get($key)
    {
        return Coroutine::getContext()[self::KEY_CONTEXT][$key] ?? null;
    }

    public function has($key)
    {
        return isset(Coroutine::getContext()[self::KEY_CONTEXT][$key]);
    }

    public function set($key, $value)
    {
        Coroutine::getContext()[self::KEY_CONTEXT][$key] = $value;
    }

    public function delete($key)
    {
        unset(Coroutine::getContext()[self::KEY_CONTEXT][$key]);
    }

    public function clear()
    {
        unset(Coroutine::getContext()[self::KEY_CONTEXT]);
    }
}