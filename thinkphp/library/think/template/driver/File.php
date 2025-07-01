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

class File
{
    protected $cacheFile;
    protected $contents = []; // 内存缓存存储

    /**
     * 写入编译缓存
     * @access public
     * @param  string $cacheFile 缓存的文件名
     * @param  string $content 缓存的内容
     * @return void|array
     */
    public function write($cacheFile, $content)
    {
        // 检查是否在Vercel环境
        if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
            // 在Vercel环境中使用内存存储
            $this->contents[$cacheFile] = $content;
            return;
        }

        // 检测模板目录
        $dir = dirname($cacheFile);

        if (!is_dir($dir)) {
            try {
                mkdir($dir, 0755, true);
            } catch (\Exception $e) {
                // 如果无法创建目录，使用内存存储
                $this->contents[$cacheFile] = $content;
                return;
            }
        }

        // 生成模板缓存文件
        if (false === file_put_contents($cacheFile, $content)) {
            // 如果无法写入文件，使用内存存储
            $this->contents[$cacheFile] = $content;
        }
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

        // 检查内存缓存
        if (isset($this->contents[$cacheFile])) {
            // 使用内存中的缓存
            eval('?>' . $this->contents[$cacheFile]);
            return;
        }

        //载入模版缓存文件
        include $this->cacheFile;
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
        // 检查内存缓存
        if (isset($this->contents[$cacheFile])) {
            return true;
        }
        
        // 缓存文件不存在, 直接返回false
        if (!file_exists($cacheFile)) {
            return false;
        }

        if (0 != $cacheTime && time() > filemtime($cacheFile) + $cacheTime) {
            // 缓存是否在有效期
            return false;
        }

        return true;
    }
}
