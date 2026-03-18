<?php

namespace Haoa\Util\Context;

use Haoa\Util\Util;

/**
 * 上下文门面类
 *
 * 根据运行环境自动选择合适的上下文实现：
 * - 协程环境：使用 CoroutineContext（数据隔离由 Swoole 协程上下文保证）
 * - 非协程环境：使用 ArrayContext（需在请求结束时手动调用 destroy()）
 */
class RunContext
{
    private static ?ContextInterface $instance = null;
    private static bool $isCoroutine = false;

    private static function instance(): ContextInterface
    {
        if (self::$instance === null) {
            self::$isCoroutine = Util::isCoroutine();
            self::$instance = ContextFactory::create();
        }
        return self::$instance;
    }

    public static function init(): void
    {
        self::instance();
    }

    public static function get(string $key): mixed
    {
        return self::instance()->get($key);
    }

    public static function set(string $key, mixed $value): void
    {
        self::instance()->set($key, $value);
    }

    public static function mustGet(string $key): mixed
    {
        $value = self::instance()->get($key);
        if ($value === null && !self::instance()->has($key)) {
            throw new \RuntimeException("Key [{$key}] does not exist");
        }
        return $value;
    }

    public static function has(string $key): bool
    {
        return self::instance()->has($key);
    }

    public static function delete(string $key): void
    {
        self::instance()->delete($key);
    }

    public static function destroy(): void
    {
        if (self::$instance !== null) {
            self::$instance->clear();
            if (!self::$isCoroutine) {
                self::$instance = null;
            }
        }
    }
}