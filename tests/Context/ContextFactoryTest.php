<?php

namespace Haoa\Util\Tests\Context;

use Haoa\Util\Context\ContextFactory;
use Haoa\Util\Context\ArrayContext;
use PHPUnit\Framework\TestCase;

class ContextFactoryTest extends TestCase
{
    /**
     * 测试在非协程环境下返回 ArrayContext
     */
    public function testCreateReturnsArrayContextInNonCoroutine(): void
    {
        $context = ContextFactory::create();
        $this->assertInstanceOf(ArrayContext::class, $context);
    }

    /**
     * 测试工厂方法返回正确的上下文类型
     */
    public function testCreateReturnsCorrectType(): void
    {
        // 非协程环境下应该返回 ArrayContext
        $context = ContextFactory::create();
        $this->assertInstanceOf(ArrayContext::class, $context);
    }
}