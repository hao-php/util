<?php

namespace Haoa\Util\Context;

use Swoole\Coroutine;

/**
 * 协程上下文（用于 Swoole 协程环境）
 */
class CoroutineContext implements ContextInterface
{
    private const KEY = __CLASS__;

    private function getContext(): array
    {
        $context = Coroutine::getContext();
        if (!isset($context[self::KEY])) {
            $context[self::KEY] = [];
        }
        return $context[self::KEY];
    }

    public function get(string $key): mixed
    {
        return $this->getContext()[$key] ?? null;
    }

    public function set(string $key, mixed $value): void
    {
        Coroutine::getContext()[self::KEY][$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($this->getContext()[$key]);
    }

    public function delete(string $key): void
    {
        unset(Coroutine::getContext()[self::KEY][$key]);
    }

    public function clear(): void
    {
        unset(Coroutine::getContext()[self::KEY]);
    }
}