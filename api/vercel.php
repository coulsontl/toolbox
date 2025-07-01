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

// PHP 8.x compatibility fixes
// Suppress deprecation warnings for PHP 8.x
error_reporting(E_ALL & ~E_DEPRECATED);

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
    define('RUNTIME_MEMORY_CACHE', true);
}

// Continue to the main application
require __DIR__ . '/../public/index.php'; 