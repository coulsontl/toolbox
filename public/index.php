<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 支持事先使用静态方法设置Request对象和Config对象

// 如果在Vercel环境中，设置相应的驱动
if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
    // 设置模板引擎使用内存驱动
    Config::set('template.type', defined('TEMPLATE_DRIVER_TYPE') ? TEMPLATE_DRIVER_TYPE : 'File');
    // 禁用模板缓存
    Config::set('template.tpl_cache', false);
    // 设置缓存类型
    Config::set('cache.type', defined('CACHE_DRIVER_TYPE') ? CACHE_DRIVER_TYPE : 'File');
    // 设置日志类型
    Config::set('log.type', defined('LOG_DRIVER_TYPE') ? LOG_DRIVER_TYPE : 'File');
}

// 执行应用并响应
Container::get('app')->run()->send();
