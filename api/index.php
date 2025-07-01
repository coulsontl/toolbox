<?php
/**
 * Here is the serverless function entry
 * for deployment with Vercel.
 */

// 在Vercel环境中使用自定义启动文件
if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
    require __DIR__ . '/vercel-custom-bootstrap.php';
    exit;
}

// 正常环境下使用标准入口
require __DIR__ . '/vercel.php';
