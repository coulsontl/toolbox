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

// +----------------------------------------------------------------------
// | 日志设置
// +----------------------------------------------------------------------

// 检测是否在Vercel环境中
$is_vercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']);
$log_type = $is_vercel ? 'test' : 'File';

return [
    // 日志记录方式，内置 file socket 支持扩展
    'type'        => $log_type,
    // 日志保存目录 - Vercel 环境使用 /tmp
    'path'        => $is_vercel ? '/tmp/log' : '',
    // 日志记录级别
    'level'       => [],
    // 单文件日志写入
    'single'      => false,
    // 独立日志级别
    'apart_level' => [],
    // 最大日志文件数量
    'max_files'   => 0,
    // 是否关闭日志写入
    'close'       => defined('RUNTIME_MEMORY_CACHE') && RUNTIME_MEMORY_CACHE === true ? true : false,
];
