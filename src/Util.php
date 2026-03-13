<?php
namespace Haoa\Util;

use Psr\Log\LoggerInterface;

class Util
{

    public static ?LoggerInterface $logger;


    public static function setLogger(LoggerInterface $logger): void
    {
        self::$logger = $logger;
    }

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

    /**
     * 格式化字节数为易读格式
     *
     * @param int $bytes 字节数
     * @param int $precision 保留小数位数
     * @return string 格式化后的字符串，如 "1.5MB"
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $maxIndex = count($units) - 1;
        $i = 0;
        while ($bytes > 1024 && $i < $maxIndex) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, $precision) . $units[$i];
    }

}