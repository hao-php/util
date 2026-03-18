<?php

namespace Haoa\Util\Context;

use Haoa\Util\Util;

/**
 * 上下文工厂
 */
class ContextFactory
{
    public static function create(): ContextInterface
    {
        return Util::isCoroutine()
            ? new CoroutineContext()
            : new ArrayContext();
    }
}