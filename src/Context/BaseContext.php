<?php

namespace Haoa\Util\Context;

abstract class BaseContext
{
    abstract public function get($key);

    abstract function set($key, $value);

    abstract public function has($key);

    abstract public function delete($key);

    public function mustGet($key)
    {
        if (!$this->has($key)) {
            return new \RuntimeException("key [{$key}] does not exist");
        }
        return $this->get($key);
    }

}