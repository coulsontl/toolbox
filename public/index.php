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

// [ 应用入口文件 ]
namespace think;

// PHP 8.1 兼容性设置 - 忽略弃用警告
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 支持事先使用静态方法设置Request对象和Config对象

// 如果在Vercel环境中，设置相应的驱动
if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
    // 获取容器实例
    $container = Container::getInstance();
    $config = $container->get('config');

    // 设置模板引擎使用内存驱动
    $config->set('template.type', defined('TEMPLATE_DRIVER_TYPE') ? TEMPLATE_DRIVER_TYPE : 'php');
    // 禁用模板缓存
    $config->set('template.tpl_cache', false);
    // 设置缓存类型
    $config->set('cache.type', defined('CACHE_DRIVER_TYPE') ? CACHE_DRIVER_TYPE : 'file');
    // 设置日志类型
    $config->set('log.type', defined('LOG_DRIVER_TYPE') ? LOG_DRIVER_TYPE : 'file');
    // 设置存储类型
    $config->set('storage.type', defined('STORAGE_TYPE') ? STORAGE_TYPE : 'file');
    // 禁用调试模式
    $config->set('app_debug', false);
    // 禁用应用Trace
    $config->set('app_trace', false);
}

// 执行应用并响应
Container::get('app')->run()->send();
