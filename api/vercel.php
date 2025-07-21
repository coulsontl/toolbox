<?php
/**
 * Vercel PHP configuration
 */

// Enable debug mode
// This will show PHP errors in the Vercel function logs
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Set timezone
date_default_timezone_set('Asia/Shanghai');

// PHP compatibility fixes
// Suppress deprecation warnings and notices for older ThinkPHP versions
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

// 创建内存缓存变量
if (!isset($GLOBALS['_VERCEL_FILE_CACHE'])) {
    $GLOBALS['_VERCEL_FILE_CACHE'] = [];
}

// 在Vercel环境中重写文件系统函数
if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
    // 提前定义内存缓存常量
    if (!defined('RUNTIME_MEMORY_CACHE')) {
        define('RUNTIME_MEMORY_CACHE', true);
    }
    // 创建临时目录
    $tmp_dir = '/tmp/vercel_php_' . md5($_SERVER['VERCEL_URL'] ?? 'localhost');
    if (!is_dir($tmp_dir)) {
        try {
            mkdir($tmp_dir, 0777, true);
        } catch (\Exception $e) {
            // 忽略错误，继续使用内存缓存
        }
    }
    
    // 设置ThinkPHP的临时目录
    define('THINK_PATH', __DIR__ . '/../thinkphp/');
    define('RUNTIME_PATH', $tmp_dir . '/');
    
    // 确保使用内存驱动
    define('USE_MEMORY_DRIVERS', true);
    
    // 加载 Vercel 兼容的模板文件驱动
    require_once __DIR__ . '/vercel-template-file-override.php';

    // 设置环境变量
    $_ENV['TEMPLATE_DRIVER_TYPE'] = 'File';
    $_ENV['CACHE_DRIVER_TYPE'] = 'Array';
    $_ENV['LOG_DRIVER_TYPE'] = 'Test';
}

// Return empty response for favicon.ico requests
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/favicon.ico') {
    header('Content-Type: image/x-icon');
    exit;
}

// Handle Vercel serverless environment
if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
    // Fix path info for Vercel
    if (isset($_SERVER['PATH_INFO'])) {
        $_SERVER['ORIG_PATH_INFO'] = $_SERVER['PATH_INFO'];
    }
    
    // Set REQUEST_URI if not set
    if (!isset($_SERVER['REQUEST_URI']) && isset($_SERVER['SCRIPT_NAME'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
        if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
            $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
    
    // Ensure HTTP_HOST is set
    if (!isset($_SERVER['HTTP_HOST'])) {
        $_SERVER['HTTP_HOST'] = $_SERVER['VERCEL_URL'] ?? 'localhost';
    }
    
    // Fix for IP detection in Vercel
    if (!isset($_SERVER['REMOTE_ADDR'])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER['X-Forwarded-For'] ?? '127.0.0.1';
    }
    
    // 在Vercel环境中使用内存缓存
    if (!defined('RUNTIME_MEMORY_CACHE')) {
        define('RUNTIME_MEMORY_CACHE', true);
    }
}

// Continue to the main application
require __DIR__ . '/../public/index.php'; 