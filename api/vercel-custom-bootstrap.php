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

// 设置环境变量标识 Vercel 环境
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

// 确保缓存和日志目录存在
$cache_dir = $tmp_dir . '/cache';
$log_dir = $tmp_dir . '/log';
$view_dir = $tmp_dir . '/view';

if (!is_dir($cache_dir)) mkdir($cache_dir, 0777, true);
if (!is_dir($log_dir)) mkdir($log_dir, 0777, true);
if (!is_dir($view_dir)) mkdir($view_dir, 0777, true);

// 不设置缓存配置，让应用使用配置文件中的设置

// 执行应用
$response = $app->http->run();
$response->send();