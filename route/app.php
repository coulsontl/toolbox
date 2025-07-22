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

// 后台路由
Route::rule('admin/:c/:a', 'admin/:c/:a');
Route::rule('lgguan', function(){
    return redirect('admin/index/index');
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
Route::rule(':act', 'Index/index');
