<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think;

// PHP 8.1 兼容性设置 - 忽略弃用警告
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// 设置自定义错误处理器来处理 strpos 弃用警告
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // 忽略 strpos 弃用警告
    if ($errno === E_DEPRECATED && strpos($errstr, 'strpos()') !== false) {
        return true;
    }
    // 对于其他错误，使用默认处理
    return false;
}, E_DEPRECATED);

// 载入Loader类
require __DIR__ . '/library/think/Loader.php';

// 注册自动加载
Loader::register();

// 注册错误和异常处理机制
Error::register();

// 实现日志接口
if (interface_exists('Psr\Log\LoggerInterface')) {
    interface LoggerInterface extends \Psr\Log\LoggerInterface
    {}
} else {
    interface LoggerInterface
    {}
}

// 在Vercel环境中使用内存缓存
if (defined('RUNTIME_MEMORY_CACHE') && RUNTIME_MEMORY_CACHE === true) {
    // 延迟到应用初始化时设置配置
    // \think\Config::set('template.compile_type', 'Memory');
}

// 注册类库别名
Loader::addClassAlias([
    'App'      => facade\App::class,
    'Build'    => facade\Build::class,
    'Cache'    => facade\Cache::class,
    'Config'   => facade\Config::class,
    'Cookie'   => facade\Cookie::class,
    'Db'       => Db::class,
    'Debug'    => facade\Debug::class,
    'Env'      => facade\Env::class,
    'Facade'   => Facade::class,
    'Hook'     => facade\Hook::class,
    'Lang'     => facade\Lang::class,
    'Log'      => facade\Log::class,
    'Request'  => facade\Request::class,
    'Response' => facade\Response::class,
    'Route'    => facade\Route::class,
    'Session'  => facade\Session::class,
    'Url'      => facade\Url::class,
    'Validate' => facade\Validate::class,
    'View'     => facade\View::class,
]);
