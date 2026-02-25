<?php

namespace Haoa\Util\Context;

use Swoole\Coroutine;

class CoContext extends BaseContext
{

    const KEY_CONTEXT = __CLASS__ . ':context';

    public function get($key)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException("key cannot be empty");
        }
        return Coroutine::getContext()[self::KEY_CONTEXT][$key] ?? null;
    }

    public function has($key)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException("key cannot be empty");
        }
        return isset(Coroutine::getContext()[self::KEY_CONTEXT][$key]);
    }

    public function set($key, $value)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException("key cannot be empty");
        }
        Coroutine::getContext()[self::KEY_CONTEXT][$key] = $value;
    }

    public function delete($key)
    {
        if (empty($key)) {
            throw new \InvalidArgumentException("key cannot be empty");
        }
        unset(Coroutine::getContext()[self::KEY_CONTEXT][$key]);
    }

    public function clear()
    {
        unset(Coroutine::getContext()[self::KEY_CONTEXT]);
    }

}