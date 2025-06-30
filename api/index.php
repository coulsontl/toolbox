<?php
// 显示所有错误
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 输出环境信息
echo "<pre>";
echo "Current directory: " . getcwd() . "\n";
echo "Root directory listing:\n";
var_dump(scandir('/var/task/user'));
echo "\nAPI directory listing:\n";
var_dump(scandir('/var/task/user/api'));
echo "</pre>";
exit;

// 获取项目根目录
$rootPath = dirname(__DIR__);

// 设置应用目录
define('APP_PATH', $rootPath . '/application/');

// 加载框架引导文件
require $rootPath . '/thinkphp/start.php';