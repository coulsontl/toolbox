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

namespace think\template\driver;

use think\Exception;

class Memory
{
    protected $cacheFile;
    protected static $contents = [];

    /**
     * 写入编译缓存
     * @access public
     * @param  string $cacheFile 缓存的文件名
     * @param  string $content 缓存的内容
     * @return void|array
     */
    public function write($cacheFile, $content)
    {
        // 将内容存储在静态数组中
        self::$contents[$cacheFile] = $content;
    }

    /**
     * 读取编译编译
     * @access public
     * @param  string  $cacheFile 缓存的文件名
     * @param  array   $vars 变量数组
     * @return void
     */
    public function read($cacheFile, $vars = [])
    {
        $this->cacheFile = $cacheFile;

        if (!empty($vars) && is_array($vars)) {
            // 模板阵列变量分解成为独立变量
            extract($vars, EXTR_OVERWRITE);
        }

        // 从内存中读取模板
        if (isset(self::$contents[$cacheFile])) {
            eval('?>' . self::$contents[$cacheFile]);
        } else {
            throw new Exception('Template not found: ' . $cacheFile);
        }
    }

    /**
     * 检查编译缓存是否有效
     * @access public
     * @param  string  $cacheFile 缓存的文件名
     * @param  int     $cacheTime 缓存时间
     * @return boolean
     */
    public function check($cacheFile, $cacheTime)
    {
        return isset(self::$contents[$cacheFile]);
    }
} 