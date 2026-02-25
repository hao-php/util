<?php
namespace Haoa\Util;

class Util
{
    /**
     * @return bool
     */
    public static function isCoroutine(): bool
    {
        if (!class_exists(\Swoole\Coroutine::class)) {
            return false;
        }
        if (\Swoole\Coroutine::getCid() != -1) {
            return true;
        }
        return false;
    }

    public static function scopeDefer(?\SplStack &$context, callable $callback): void
    {
        $context ??= new class() extends \SplStack {
            public function __destruct()
            {
                while ($this->count() > 0) {
                    \call_user_func($this->pop());
                }
            }
        };

        $context->push($callback);
    }

}