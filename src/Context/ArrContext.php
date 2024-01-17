<?php

namespace Haoa\Util\Context;

/**
 * 需要手动进行清理
 */
class ArrContext extends BaseContext
{

    public static $context = [];

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        if (empty($key)) {
            return new \RuntimeException("key [{$key}] cannot be empty");
        }
        return self::$context[$key] ?? null;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        if (empty($key)) {
            return new \RuntimeException("key [{$key}] cannot be empty");
        }
        self::$context[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function has($key)
    {
        if (empty($key)) {
            return new \RuntimeException("key [{$key}] cannot be empty");
        }
        return isset(self::$context[$key]);
    }

    public function delete($key)
    {
        if (empty($key)) {
            return new \RuntimeException("key [{$key}] cannot be empty");
        }
        unset(self::$context[$key]);
    }

    public function clear()
    {
        self::$context = [];
    }

}