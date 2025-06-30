<?php
// 获取当前脚本的绝对路径
$currentPath = __DIR__;

// 设置应用目录
define('APP_PATH', realpath($currentPath . '/../application/'));

// 加载框架引导文件
$thinkphpPath = realpath($currentPath . '/../thinkphp/start.php');

if ($thinkphpPath) {
    require $thinkphpPath;
} else {
    echo "ThinkPHP framework files not found. Paths checked:<br>";
    echo "Current path: " . $currentPath . "<br>";
    echo "Attempted ThinkPHP path: " . $currentPath . '/../thinkphp/start.php' . "<br>";
    echo "Directory listing:<br>";
    print_r(scandir($currentPath . '/..'));
}