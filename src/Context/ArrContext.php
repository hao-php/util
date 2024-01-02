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
        return self::$context[$key] ?? null;
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        self::$context[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function has($key)
    {
        return isset(self::$context[$key]);
    }

    public function delete($key)
    {
        unset(self::$context[$key]);
    }

    /**
     * @return mixed
     */
    public function clear()
    {
        self::$context = [];
    }

}