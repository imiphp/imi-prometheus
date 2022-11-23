<?php

declare(strict_types=1);

use Imi\Prometheus\PrometheusMeterRegistry;

$rootPath = \dirname(__DIR__) . '/';

return [
    'hotUpdate'    => [
        'status'    => false, // 关闭热更新去除注释，不设置即为开启，建议生产环境关闭

        // --- 文件修改时间监控 ---
        // 'monitorClass'    =>    \Imi\HotUpdate\Monitor\FileMTime::class,
        'timespan'    => 1, // 检测时间间隔，单位：秒

        // --- Inotify 扩展监控 ---
        // 'monitorClass'    =>    \Imi\HotUpdate\Monitor\Inotify::class,
        // 'timespan'    =>    1, // 检测时间间隔，单位：秒，使用扩展建议设为0性能更佳

        // 'includePaths'    =>    [], // 要包含的路径数组
        'excludePaths'    => [
            $rootPath . '.git',
            $rootPath . 'bin',
            $rootPath . 'logs',
        ], // 要排除的路径数组，支持通配符*
    ],
    'MeterRegistry' => [
        'driver'  => PrometheusMeterRegistry::class,
        'options' => [
            'adapter' => [
                'class'   => \Imi\Prometheus\Storage\Redis::class,
                'options' => [
                    'poolName' => 'redis',
                ],
            ],
        ],
    ],
    // 连接池监控
    'PoolMonitor' => [
        'enable'         => true, // 启用
        // 'pools'       => null, // 监控的连接池名称数组。如果为 null 则代表监控所有连接池
        // 'interval'    => 10, // 上报时间间隔，单位：秒
        // 'countKey'    => 'pool_count', // 连接总数量键名
        // 'usedKey'     => 'pool_used', // 忙碌连接数量键名
        // 'freeKey'     => 'pool_free', // 空间连接数量键名
        // 'workerIdTag' => 'worker_id', // 工作进程 ID 标签名
        // 'poolNameTag' => 'pool_name', // 连接池标签名
    ],
    // Swoole 服务器指标监控
    'SwooleServerMonitor' => [
        'enable'         => true, // 启用
        // 要监控的指标名称数组
        // 详见：https://wiki.swoole.com/#/server/methods?id=stats
        // 格式1：stats() 指标名称 => 实际指标名称
        // 格式2：stats() 指标名称同时作为实际指标名称
        'stats'          => [
            'connection_num' => 'swoole_connection',
            'request_count'  => 'swoole_request_count',
            'coroutine_num'  => 'swoole_coroutine_num',
            'coroutine_num',
        ],
        // 'interval'    => 10, // 上报时间间隔，单位：秒
        // 'workerIdTag' => 'worker_id', // 工作进程 ID 标签名
    ],
];
