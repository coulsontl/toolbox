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
}

// Continue to the main application
require __DIR__ . '/../public/index.php'; 