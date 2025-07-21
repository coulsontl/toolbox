<?php
/**
 * Vercel 环境下的模板文件驱动重写
 * 将所有文件操作重定向到内存或 /tmp 目录
 */

namespace think\template\driver;

use think\Exception;

class File
{
    protected $cacheFile;
    protected static $contents = [];
    protected static $tmp_dir = null;

    public function __construct()
    {
        if (self::$tmp_dir === null) {
            self::$tmp_dir = '/tmp/vercel_php_' . md5($_SERVER['VERCEL_URL'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost');
            if (!is_dir(self::$tmp_dir)) {
                @mkdir(self::$tmp_dir, 0777, true);
            }
        }
    }

    /**
     * 写入编译缓存
     * @access public
     * @param  string $cacheFile 缓存的文件名
     * @param  string $content 缓存的内容
     * @return void|array
     */
    public function write($cacheFile, $content)
    {
        // 在 Vercel 环境中，优先使用内存存储
        if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
            self::$contents[$cacheFile] = $content;
            return;
        }

        // 尝试写入 /tmp 目录
        $tmpCacheFile = self::$tmp_dir . '/template/' . basename($cacheFile);
        $dir = dirname($tmpCacheFile);
        
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        if (@file_put_contents($tmpCacheFile, $content) === false) {
            // 如果写入失败，回退到内存存储
            self::$contents[$cacheFile] = $content;
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

        // 优先从内存中读取
        if (isset(self::$contents[$cacheFile])) {
            eval('?>' . self::$contents[$cacheFile]);
            return;
        }

        // 尝试从 /tmp 目录读取
        $tmpCacheFile = self::$tmp_dir . '/template/' . basename($cacheFile);
        if (file_exists($tmpCacheFile)) {
            include $tmpCacheFile;
            return;
        }

        // 如果都没有找到，抛出异常
        throw new Exception('Template not found: ' . $cacheFile);
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
        // 检查内存中是否存在
        if (isset(self::$contents[$cacheFile])) {
            return true;
        }

        // 检查 /tmp 目录中是否存在
        $tmpCacheFile = self::$tmp_dir . '/template/' . basename($cacheFile);
        if (file_exists($tmpCacheFile)) {
            return filemtime($tmpCacheFile) >= $cacheTime;
        }

        return false;
    }
}
