<?php

namespace Haoa\Util\Context;

abstract class BaseContext
{
    abstract public function get($key);

    abstract function set($key, $value);

    abstract public function has($key);

    abstract public function delete($key);

    abstract public function clear();

}