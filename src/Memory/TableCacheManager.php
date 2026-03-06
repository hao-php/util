<?php

declare(strict_types=1);

namespace Haoa\Util\Memory;

use Haoa\Util\Util;
use Swoole\Table;

class TableCacheManager
{
    private static array $tables = [];

    // index => type 映射
    private static array $indexToTypeMap = [];
    // type => index 映射
    private static array $typeToIndexMap = [];
    // string type => valueSize 映射
    private static array $stringTypeConfig = [];

    const TYPE_INT = 'int';
    const TYPE_FLOAT = 'float';
    const TYPE_STRINGS = 'strings';

    private array $configExample = [
        TableCacheManager::TYPE_INT => ['table_size' => 128],
        TableCacheManager::TYPE_FLOAT => ['table_size' => 128],
        TableCacheManager::TYPE_STRINGS => [
            'string32' => ['table_size' => 128, 'value_size' => 32],
            'string64' => ['table_size' => 128, 'value_size' => 64],
            'string128' => ['table_size' => 128, 'value_size' => 128],
            'string256' => ['table_size' => 128, 'value_size' => 256],
            'string1K' => ['table_size' => 128, 'value_size' => 1024],
            'string2K' => ['table_size' => 128, 'value_size' => 2048],
            'string4K' => ['table_size' => 128, 'value_size' => 4096],
        ],
    ];

    public static function init(array $config): void
    {
        $sizeTotal = 0;

        foreach ($config as $type => $typeConfig) {
            if ($type === self::TYPE_INT) {
                self::$tables[$type] = new TableCache($typeConfig['table_size'], Table::TYPE_INT);
                self::$indexToTypeMap[] = $type;
                $sizeTotal += $typeConfig['table_size'];
                continue;
            }
            if ($type === self::TYPE_FLOAT) {
                self::$tables[$type] = new TableCache($typeConfig['table_size'], Table::TYPE_FLOAT);
                self::$indexToTypeMap[] = $type;
                $sizeTotal += $typeConfig['table_size'];
                continue;
            }
            if ($type === self::TYPE_STRINGS) {
                // 按 value_size 从小到大排序
                uasort($typeConfig, fn($a, $b) => $a['value_size'] <=> $b['value_size']);
                foreach ($typeConfig as $stringType => $stringConfig) {
                    self::$tables[$stringType] = new TableCache($stringConfig['table_size'], Table::TYPE_STRING, $stringConfig['value_size']);
                    self::$indexToTypeMap[] = $stringType;
                    // 保存 string type 的 valueSize 配置
                    self::$stringTypeConfig[$stringType] = $stringConfig['value_size'];
                    $sizeTotal += $stringConfig['table_size'];
                }
            }
        }

        self::$typeToIndexMap = array_flip(self::$indexToTypeMap);

        $indexTable = new TableCache($sizeTotal + 128, Table::TYPE_INT, 1);
        TableRouter::init($indexTable);
    }

    public static function getTables(): array
    {
        return self::$tables;
    }

    public static function getIndexToTypeMap(): array
    {
        return self::$indexToTypeMap;
    }

    public static function getStringTypeConfig(): array
    {
        return self::$stringTypeConfig;
    }

    public static function set(string $key, mixed $value, int $ttl = 10): bool
    {
        $type = TableRouter::detectType($value);
        $valueType = gettype($value);
        $logString = "key:{$key}, valueType: {$valueType}";
        if (is_string($value)) {
            $len = strlen($value);
            $logString .= ", valueLen: {$len}";
        } else {
            $logString .= ", value: {$value}";
        }
        Util::$enableDebug && Util::$logger && Util::$logger->debug('TableCacheManager set, ' . $logString);

        if ($type === '') {
            Util::$logger && Util::$logger->notice("TableCacheManager set, detectType failed, {$logString}");
            return false;
        }

        $index = self::$typeToIndexMap[$type];
        $table = self::$tables[$type];

        // 先设置索引，再设置值，保证一致性
        $indexSet = TableRouter::setIndex($key, $index, $ttl);
        if (!$indexSet) {
            Util::$logger && Util::$logger->notice("TableCacheManager set, index set failed, {$logString}");
            return false;
        }

        $valueSet = $table->set($key, $value, $ttl);
        if (!$valueSet) {
            // 回滚索引
            TableRouter::delIndex($key);
            Util::$logger && Util::$logger->notice("TableCacheManager set, value set failed, {$logString}");
            return false;
        }

        return true;
    }

    public static function get(string $key): mixed
    {
        $table = TableRouter::getTableByKey($key);
        if ($table === null) {
            return null;
        }
        return $table->get($key);
    }

    public static function del(string $key): bool
    {
        $table = TableRouter::getTableByKey($key);
        if ($table === null) {
            return false;
        }
        // 同时删除数据和索引
        $dataDeleted = $table->del($key);
        $indexDeleted = TableRouter::delIndex($key);
        return $dataDeleted && $indexDeleted;
    }

    public static function getSummary(): array
    {
        $data = [
            'size'   => 0, // 总槽位数
            'count'  => 0, // 总记录数
            'memory_size' => 0, // 总内存（字节）
        ];
        foreach (self::$tables as $type => $table) {
            /** @var TableCache $table */

            $data['size'] += $table->getSize();
            $data['count'] += $table->count();
            $data['memory_size'] += $table->getMemorySize();
        }

        $data['memory'] = Util::formatBytes($data['memory_size']);

        return $data;
    }

    public static function stats(): array
    {
        $data = [];
        foreach (self::$tables as $type => $table) {
            /** @var TableCache $table */

            $stats = $table->stats();
            if ($stats) {
                $stats['size'] = $table->getSize();
                $stats['count'] = $table->count();
                $stats['memory_size'] = $table->getMemorySize();
                $stats['memory'] = Util::formatBytes($stats['memory_size']);
            }

            $data[$type] = $stats;
        }
        return $data;
    }
}
