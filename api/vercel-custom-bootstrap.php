<?php
/**
 * 针对Vercel环境的自定义启动文件
 */

// PHP 兼容性设置
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');
date_default_timezone_set('Asia/Shanghai');

// 加载 Vercel 兼容的模板文件驱动
require_once __DIR__ . '/vercel-template-file-override.php';

// 设置运行时目录为/tmp目录（Vercel中唯一可写的目录）
$tmp_dir = '/tmp/vercel_php_runtime_' . md5($_SERVER['VERCEL_URL'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost');
if (!is_dir($tmp_dir)) {
    mkdir($tmp_dir, 0777, true);
}

// 设置ThinkPHP的关键路径
define('THINK_PATH', __DIR__ . '/../thinkphp/');
define('APP_PATH', __DIR__ . '/../application/');
define('ROOT_PATH', dirname(__DIR__) . '/');
define('RUNTIME_PATH', $tmp_dir . '/');
define('CACHE_PATH', $tmp_dir . '/cache/');
define('LOG_PATH', $tmp_dir . '/log/');
define('TEMP_PATH', $tmp_dir . '/temp/');

// 加载ThinkPHP框架
require THINK_PATH . 'base.php';

// 在应用运行前，配置 Vercel 兼容的驱动
\think\Config::set('template', [
    'type' => 'Think',
    'compile_type' => 'File',
    'tpl_cache' => true,
    'cache_path' => $tmp_dir . '/template/',
    'compile_path' => $tmp_dir . '/template/',
]);

\think\Config::set('cache', [
    'type' => 'array',
    'path' => $tmp_dir . '/cache/',
]);

\think\Config::set('log', [
    'type' => 'test',
    'close' => true,
    'path' => $tmp_dir . '/log/',
]);

// 执行应用
\think\Container::get('app')->run()->send(); 