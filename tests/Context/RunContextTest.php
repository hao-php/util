<?php

namespace Haoa\Util\Tests\Context;

use Haoa\Util\Context\RunContext;
use PHPUnit\Framework\TestCase;

class RunContextTest extends TestCase
{
    protected function tearDown(): void
    {
        RunContext::destroy();
    }

    /**
     * 测试基本的 set/get 操作
     */
    public function testSetAndGet(): void
    {
        RunContext::set('key1', 'value1');
        $this->assertEquals('value1', RunContext::get('key1'));
    }

    /**
     * 测试获取不存在的 key 返回 null
     */
    public function testGetNonExistentKey(): void
    {
        $this->assertNull(RunContext::get('nonexistent'));
    }

    /**
     * 测试 has 方法检测 key 是否存在
     */
    public function testHas(): void
    {
        $this->assertFalse(RunContext::has('key'));

        RunContext::set('key', 'value');
        $this->assertTrue(RunContext::has('key'));
    }

    /**
     * 测试 mustGet 在 key 不存在时抛出异常
     */
    public function testMustGetThrowsExceptionWhenKeyNotExists(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Key [nonexistent] does not exist');

        RunContext::mustGet('nonexistent');
    }

    /**
     * 测试 mustGet 在 key 存在时返回值
     */
    public function testMustGetReturnsValueWhenKeyExists(): void
    {
        RunContext::set('key', 'value');
        $this->assertEquals('value', RunContext::mustGet('key'));
    }

    /**
     * 测试 delete 方法删除指定 key
     */
    public function testDelete(): void
    {
        RunContext::set('key', 'value');
        $this->assertTrue(RunContext::has('key'));

        RunContext::delete('key');
        $this->assertFalse(RunContext::has('key'));
    }

    /**
     * 测试 destroy 方法清空所有数据
     */
    public function testDestroy(): void
    {
        RunContext::set('key1', 'value1');
        RunContext::set('key2', 'value2');

        RunContext::destroy();

        $this->assertFalse(RunContext::has('key1'));
        $this->assertFalse(RunContext::has('key2'));
    }

    /**
     * 测试 destroy 后可以重新使用
     */
    public function testDestroyAllowsReuse(): void
    {
        RunContext::set('key', 'value1');
        RunContext::destroy();

        RunContext::set('key', 'value2');
        $this->assertEquals('value2', RunContext::get('key'));
    }

    /**
     * 测试 init 方法初始化上下文
     */
    public function testInit(): void
    {
        $this->assertNull(RunContext::get('test'));

        RunContext::init();

        // init() 不应该抛出异常
        $this->assertTrue(true);
    }
}