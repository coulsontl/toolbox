<?php
/**
 * 针对Vercel环境的自定义启动文件 - ThinkPHP 8.0
 */

// PHP 兼容性设置
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');
date_default_timezone_set('Asia/Shanghai');

// 设置运行时目录为/tmp目录（Vercel中唯一可写的目录）
$tmp_dir = '/tmp/vercel_php_runtime_' . md5($_SERVER['VERCEL_URL'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost');
if (!is_dir($tmp_dir)) {
    mkdir($tmp_dir, 0777, true);
}

// 设置ThinkPHP 8.0的关键路径
define('APP_PATH', __DIR__ . '/../app/');
define('ROOT_PATH', dirname(__DIR__) . '/');
define('RUNTIME_PATH', $tmp_dir . '/');

// 加载 Composer 自动加载
require __DIR__ . '/../vendor/autoload.php';

// 创建应用实例，传入根目录路径
$app = new \think\App(ROOT_PATH);

// 设置应用路径
$app->setAppPath(APP_PATH);
$app->setRuntimePath(RUNTIME_PATH);

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
    'view' => [
        'type' => 'Think',
        'view_path' => APP_PATH . 'view/',
        'cache_path' => $tmp_dir . '/view/',
    ],
]);

// 执行应用
$response = $app->http->run();
$response->send();