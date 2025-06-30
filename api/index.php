<?php
// api/index.php

// 获取项目根目录
$rootPath = dirname(__DIR__);

// 设置应用目录
define('APP_PATH', $rootPath . '/application/');

// 加载框架引导文件
require $rootPath . '/thinkphp/start.php';