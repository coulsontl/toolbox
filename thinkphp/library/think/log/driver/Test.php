<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\log\driver;

use think\App;

/**
 * 测试日志驱动
 * 主要用于Vercel等只读文件系统环境
 */
class Test
{
    protected $config = [
        'time_format' => 'c',
    ];

    // 实例化并传入参数
    public function __construct(App $app, $config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 日志写入接口
     * @access public
     * @param  array    $log    日志信息
     * @param  bool     $append 是否追加请求信息
     * @return bool
     */
    public function save(array $log = [], $append = false)
    {
        // 不保存日志，只返回成功
        return true;
    }
} 