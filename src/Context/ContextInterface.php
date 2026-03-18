<?php

namespace Haoa\Util\Context;

/**
 * 上下文接口
 */
interface ContextInterface
{
    /**
     * 获取值
     */
    public function get(string $key): mixed;

    /**
     * 设置值
     */
    public function set(string $key, mixed $value): void;

    /**
     * 检查是否存在
     */
    public function has(string $key): bool;

    /**
     * 删除
     */
    public function delete(string $key): void;

    /**
     * 清空
     */
    public function clear(): void;
}