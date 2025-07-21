<?php
/**
 * 针对Vercel环境的自定义启动文件
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

// 在应用运行前，确保模板引擎使用内存驱动
\think\Config::set('template.compile_type', 'Memory');
\think\Config::set('template.tpl_cache', false);
\think\Config::set('cache.type', 'array');
\think\Config::set('log.type', 'test');
\think\Config::set('log.close', true);

// 执行应用
\think\Container::get('app')->run()->send(); 