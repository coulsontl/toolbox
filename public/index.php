<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 - ThinkPHP 8.0 ]

// PHP 8+ 兼容性设置
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// 加载 Composer 自动加载
require __DIR__ . '/../vendor/autoload.php';

// 创建应用实例，传入根目录路径
try {
    $app = new \think\App(dirname(__DIR__) . '/');
} catch (\Exception $e) {
    die('App creation failed: ' . $e->getMessage());
}

// 设置应用路径
$app->setAppPath(__DIR__ . '/../app/');

// 设置调试模式（本地开发）
$app->config->set([
    'app' => [
        'debug' => true,
        'trace' => true,
    ],
]);

// 如果在Vercel环境中，设置相应的配置
if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
    // 设置运行时路径到可写目录
    $tmp_dir = '/tmp/vercel_php_runtime_' . md5($_SERVER['VERCEL_URL'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost');
    if (!is_dir($tmp_dir)) {
        @mkdir($tmp_dir, 0777, true);
    }
    $app->setRuntimePath($tmp_dir . '/');

    // 配置 Vercel 兼容的设置
    $app->config->set([
        'cache' => [
            'default' => 'array',
            'stores' => [
                'array' => [
                    'type' => 'array',
                ],
            ],
        ],
        'log' => [
            'default' => 'test',
            'channels' => [
                'test' => [
                    'type' => 'test',
                ],
            ],
        ],
        'app' => [
            'debug' => true,
            'trace' => true,
        ],
    ]);
}

// 执行应用并响应
try {
    $response = $app->http->run();
    $response->send();
} catch (\Exception $e) {
    die('Application run failed: ' . $e->getMessage() . '<br>File: ' . $e->getFile() . '<br>Line: ' . $e->getLine());
}
