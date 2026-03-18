<?php

namespace Haoa\Util\Tests\Context;

use Haoa\Util\Context\CoroutineContext;
use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;
use Swoole\Runtime;

class CoroutineContextTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Runtime::enableCoroutine();
    }

    private function runInCoroutine(callable $callback): void
    {
        Coroutine\run(function () use ($callback) {
            $callback();
        });
    }

    /**
     * 测试协程内基本的 set/get 操作
     */
    public function testSetAndGetInCoroutine(): void
    {
        $this->runInCoroutine(function () {
            $context = new CoroutineContext();
            $context->set('key', 'value');
            $this->assertEquals('value', $context->get('key'));
        });
    }

    /**
     * 测试多个协程之间的数据隔离
     */
    public function testCoroutineIsolation(): void
    {
        $results = [];

        Coroutine\run(function () use (&$results) {
            $context = new CoroutineContext();

            $cid1 = Coroutine::create(function () use ($context, &$results) {
                $context->set('data', 'from_coroutine_1');
                Coroutine::sleep(0.01); // 让出执行权
                $results[1] = $context->get('data');
            });

            $cid2 = Coroutine::create(function () use ($context, &$results) {
                $context->set('data', 'from_coroutine_2');
                Coroutine::sleep(0.01); // 让出执行权
                $results[2] = $context->get('data');
            });

            Coroutine::join([$cid1, $cid2]);
        });

        $this->assertEquals('from_coroutine_1', $results[1]);
        $this->assertEquals('from_coroutine_2', $results[2]);
    }

    /**
     * 测试 clear 只影响当前协程的数据
     */
    public function testClearOnlyAffectsCurrentCoroutine(): void
    {
        $results = [];

        Coroutine\run(function () use (&$results) {
            $context = new CoroutineContext();

            $cid1 = Coroutine::create(function () use ($context, &$results) {
                $context->set('key', 'value1');
                Coroutine::sleep(0.01);
                $results[1] = $context->get('key');
            });

            $cid2 = Coroutine::create(function () use ($context, &$results) {
                $context->set('key', 'value2');
                $context->clear(); // 清空当前协程的数据
                $results[2] = $context->get('key');
            });

            Coroutine::join([$cid1, $cid2]);
        });

        $this->assertEquals('value1', $results[1]); // 协程1不受影响
        $this->assertNull($results[2]); // 协程2已清空
    }

    /**
     * 测试多个协程使用相同 key 时的数据隔离
     */
    public function testMultipleCoroutinesWithSameKey(): void
    {
        $results = [];
        $coroutineCount = 10;

        Coroutine\run(function () use (&$results, $coroutineCount) {
            $context = new CoroutineContext();
            $cids = [];

            for ($i = 1; $i <= $coroutineCount; $i++) {
                $cids[] = Coroutine::create(function () use ($context, &$results, $i) {
                    $context->set('counter', $i);
                    Coroutine::sleep(0.001 * $i);
                    $results[$i] = $context->get('counter');
                });
            }

            Coroutine::join($cids);
        });

        for ($i = 1; $i <= $coroutineCount; $i++) {
            $this->assertEquals($i, $results[$i], "Coroutine {$i} should have its own value");
        }
    }

    /**
     * 测试子协程无法访问父协程的数据
     */
    public function testParentCoroutineDataNotVisibleToChild(): void
    {
        $results = [];

        Coroutine\run(function () use (&$results) {
            $context = new CoroutineContext();

            $context->set('parent_key', 'parent_value');

            $cid = Coroutine::create(function () use ($context, &$results) {
                // 子协程不应该看到父协程的数据
                $results['child_get_parent'] = $context->get('parent_key');
                $context->set('child_key', 'child_value');
                $results['child_own'] = $context->get('child_key');
            });

            Coroutine::join([$cid]);
        });

        $this->assertNull($results['child_get_parent']); // 子协程看不到父协程数据
        $this->assertEquals('child_value', $results['child_own']);
    }
}