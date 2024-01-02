<?php

namespace Haoa\Util\Context;

class CoContext extends BaseContext
{

    public function get($key)
    {
        return \Swoole\Coroutine::getContext()['context'][$key] ?? null;
    }

    public function has($key)
    {
        return isset(\Swoole\Coroutine::getContext()['context'][$key]);
    }

    public function set($key, $value)
    {
        \Swoole\Coroutine::getContext()['context'][$key] = $value;
    }

    public function delete($key)
    {
        unset(\Swoole\Coroutine::getContext()['context'][$key]);
    }

    public function clear()
    {
        unset(\Swoole\Coroutine::getContext()['context']);
    }
}