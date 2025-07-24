<?php
/**
 * 启动脚本 - 解决 PHP 8.1 兼容性问题
 * 使用方法: php start.php
 */

// 设置错误报告级别，忽略弃用警告
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 检查 PHP 版本
if (version_compare(PHP_VERSION, '5.6.0', '<')) {
    die('PHP 版本必须 >= 5.6.0');
}

// 检查必要的扩展
$required_extensions = ['pdo', 'json', 'mbstring'];
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        die("缺少必要的 PHP 扩展: {$ext}");
    }
}

// 设置环境变量
putenv('APP_DEBUG=true');
putenv('APP_TRACE=false');

// 启动信息
echo "=================================\n";
echo "ThinkPHP 工具箱启动中...\n";
echo "PHP 版本: " . PHP_VERSION . "\n";
echo "启动时间: " . date('Y-m-d H:i:s') . "\n";
echo "=================================\n";

// 检查端口是否被占用
$host = '0.0.0.0';
$port = 8080;

$socket = @fsockopen($host, $port, $errno, $errstr, 1);
if ($socket) {
    fclose($socket);
    echo "警告: 端口 {$port} 已被占用，尝试使用端口 8081...\n";
    $port = 8081;
    
    $socket = @fsockopen($host, $port, $errno, $errstr, 1);
    if ($socket) {
        fclose($socket);
        die("错误: 端口 {$port} 也被占用，请手动指定其他端口\n");
    }
}

// 启动内置服务器
$command = sprintf(
    'php -S %s:%d -t public',
    $host,
    $port
);

echo "启动命令: {$command}\n";
echo "访问地址: http://localhost:{$port}\n";
echo "按 Ctrl+C 停止服务器\n";
echo "=================================\n\n";

// 执行启动命令
passthru($command);
