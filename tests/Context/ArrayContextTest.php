<?php

namespace Haoa\Util\Tests\Context;

use Haoa\Util\Context\ArrayContext;
use PHPUnit\Framework\TestCase;

class ArrayContextTest extends TestCase
{
    private ArrayContext $context;

    protected function setUp(): void
    {
        $this->context = new ArrayContext();
    }

    /**
     * 测试基本的 set/get 操作
     */
    public function testSetAndGet(): void
    {
        $this->context->set('key1', 'value1');
        $this->assertEquals('value1', $this->context->get('key1'));

        $this->context->set('key2', ['nested' => 'data']);
        $this->assertEquals(['nested' => 'data'], $this->context->get('key2'));
    }

    /**
     * 测试获取不存在的 key 返回 null
     */
    public function testGetNonExistentKey(): void
    {
        $this->assertNull($this->context->get('nonexistent'));
    }

    /**
     * 测试 has 方法检测 key 是否存在
     */
    public function testHas(): void
    {
        $this->assertFalse($this->context->has('key'));

        $this->context->set('key', 'value');
        $this->assertTrue($this->context->has('key'));
    }

    /**
     * 测试 delete 方法删除指定 key
     */
    public function testDelete(): void
    {
        $this->context->set('key', 'value');
        $this->assertTrue($this->context->has('key'));

        $this->context->delete('key');
        $this->assertFalse($this->context->has('key'));
    }

    /**
     * 测试 clear 方法清空所有数据
     */
    public function testClear(): void
    {
        $this->context->set('key1', 'value1');
        $this->context->set('key2', 'value2');

        $this->context->clear();

        $this->assertFalse($this->context->has('key1'));
        $this->assertFalse($this->context->has('key2'));
    }

    /**
     * 测试多个实例之间的数据隔离
     */
    public function testMultipleInstancesAreIsolated(): void
    {
        $context1 = new ArrayContext();
        $context2 = new ArrayContext();

        $context1->set('key', 'value1');
        $context2->set('key', 'value2');

        $this->assertEquals('value1', $context1->get('key'));
        $this->assertEquals('value2', $context2->get('key'));
    }
}