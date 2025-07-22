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

use think\facade\Route;



// 默认首页
Route::get('/', 'Index/index');

// 后台路由（明确匹配，优先级最高）
Route::rule('admin$', 'Admin/index');
Route::rule('admin/login', 'Admin/login');
Route::rule('admin/main', 'Admin/main');
Route::rule('admin/web_cache', 'Admin/web_cache');
Route::rule('admin/logout', 'Admin/logout');
Route::rule('admin/account', 'Admin/account');
Route::rule('admin/test', 'Admin/test');
Route::rule('lgguan', function(){
    return redirect('/admin');
});

// API 路由
Route::rule('doapi', 'Index/api');
Route::rule('api', 'Index/api');

// 重定向 jsonhelper 到 superjson
Route::rule('formats/jsonhelper', function(){
    return redirect('/json/');
});

// 静态页面路由
Route::rule('ip/:ip', 'Index/index?act=ip')->pattern(['ip' => '.*']);

// 通配符路由（必须放在最后，排除 admin 开头的路径）
Route::rule(':act', 'Index/index')->pattern(['act' => '(?!admin)[^/]+']);
