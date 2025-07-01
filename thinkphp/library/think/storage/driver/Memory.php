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

namespace think\storage\driver;

/**
 * 内存存储驱动 
 * 主要用于Vercel等只读文件系统环境
 */
class Memory
{
    protected static $contents = [];

    /**
     * 架构函数
     * @access public
     */
    public function __construct()
    {
    }

    /**
     * 文件内容读取
     * @access public
     * @param  string $filename  文件名
     * @param  string $type      文件类型
     * @return string
     */
    public function read($filename, $type = '')
    {
        return isset(self::$contents[$filename]) ? self::$contents[$filename] : '';
    }

    /**
     * 文件写入
     * @access public
     * @param  string $filename  文件名
     * @param  string $content  文件内容
     * @return bool
     */
    public function put($filename, $content)
    {
        self::$contents[$filename] = $content;
        return true;
    }

    /**
     * 文件追加写入
     * @access public
     * @param  string $filename  文件名
     * @param  string $content  追加的文件内容
     * @return bool
     */
    public function append($filename, $content)
    {
        if (isset(self::$contents[$filename])) {
            self::$contents[$filename] .= $content;
        } else {
            self::$contents[$filename] = $content;
        }
        return true;
    }

    /**
     * 加载文件
     * @access public
     * @param  string $filename  文件名
     * @param  array  $vars      传入变量
     * @return void
     */
    public function load($filename, $vars = [])
    {
        if (isset(self::$contents[$filename])) {
            // 参数赋值
            extract($vars, EXTR_OVERWRITE);
            // 读取模板文件内容
            eval('?>' . self::$contents[$filename]);
        }
    }

    /**
     * 文件是否存在
     * @access public
     * @param  string $filename  文件名
     * @return bool
     */
    public function has($filename)
    {
        return isset(self::$contents[$filename]);
    }

    /**
     * 文件删除
     * @access public
     * @param  string $filename  文件名
     * @return bool
     */
    public function unlink($filename)
    {
        if (isset(self::$contents[$filename])) {
            unset(self::$contents[$filename]);
            return true;
        }
        return false;
    }

    /**
     * 获取文件时间戳
     * @access public
     * @param  string $filename  文件名
     * @return int
     */
    public function mtime($filename)
    {
        return time();
    }

    /**
     * 获取文件大小
     * @access public
     * @param  string $filename  文件名
     * @return int
     */
    public function size($filename)
    {
        return isset(self::$contents[$filename]) ? strlen(self::$contents[$filename]) : 0;
    }

    /**
     * 创建目录
     * @access public
     * @param  string $dirname  目录名
     * @return bool
     */
    public function mkdir($dirname)
    {
        return true;
    }

    /**
     * 删除目录
     * @access public
     * @param  string $dirname  目录名
     * @return bool
     */
    public function rmdir($dirname)
    {
        return true;
    }
} 