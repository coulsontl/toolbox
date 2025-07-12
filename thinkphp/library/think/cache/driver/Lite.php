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

namespace think\cache\driver;

use think\cache\Driver;

/**
 * 文件类型缓存类
 * @author    liu21st <liu21st@gmail.com>
 */
class Lite extends Driver
{
    protected $options = [
        'prefix' => '',
        'path'   => '',
        'expire' => 0, // 等于 10*365*24*3600（10年）
    ];
    
    // 内存缓存
    protected static $cache = [];

    /**
     * 架构函数
     * @access public
     *
     * @param  array $options
     */
    public function __construct($options = [])
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }

        if (substr($this->options['path'], -1) != DIRECTORY_SEPARATOR) {
            $this->options['path'] .= DIRECTORY_SEPARATOR;
        }

    }

    /**
     * 取得变量的存储文件名
     * @access protected
     * @param  string $name 缓存变量名
     * @return string
     */
    protected function getCacheKey($name)
    {
        return $this->options['path'] . $this->options['prefix'] . md5($name) . '.php';
    }

    /**
     * 判断缓存是否存在
     * @access public
     * @param  string $name 缓存变量名
     * @return mixed
     */
    public function has($name)
    {
        return $this->get($name) ? true : false;
    }

    /**
     * 读取缓存
     * @access public
     * @param  string $name 缓存变量名
     * @param  mixed  $default 默认值
     * @return mixed
     */
    public function get($name, $default = false)
    {
        $this->readTimes++;
        
        // 检查内存缓存
        $cacheKey = $this->getCacheKey($name);
        if (isset(self::$cache[$cacheKey])) {
            // 判断是否过期
            if (self::$cache[$cacheKey]['expire'] >= time()) {
                return self::$cache[$cacheKey]['data'];
            } else {
                // 清除已经过期的内存缓存
                unset(self::$cache[$cacheKey]);
            }
        }
        
        // 检查Vercel环境
        if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
            return $default;
        }

        $filename = $this->getCacheKey($name);

        if (is_file($filename)) {
            // 判断是否过期
            $mtime = filemtime($filename);

            if ($mtime < time()) {
                // 清除已经过期的文件
                try {
                    unlink($filename);
                } catch (\Exception $e) {
                    // 忽略错误
                }
                return $default;
            }

            $content = include $filename;
            
            // 缓存到内存
            self::$cache[$cacheKey] = [
                'data'   => $content,
                'expire' => $mtime
            ];
            
            return $content;
        } else {
            return $default;
        }
    }

    /**
     * 写入缓存
     * @access public
     * @param  string        $name  缓存变量名
     * @param  mixed         $value 存储数据
     * @param  int|\DateTime $expire 有效时间 0为永久
     * @return bool
     */
    public function set($name, $value, $expire = null)
    {
        $this->writeTimes++;

        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }

        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp();
        } else {
            $expire = 0 === $expire ? 10 * 365 * 24 * 3600 : $expire;
            $expire = time() + $expire;
        }

        $filename = $this->getCacheKey($name);
        
        // 缓存到内存
        self::$cache[$filename] = [
            'data'   => $value,
            'expire' => $expire
        ];

        // 在Vercel环境中不写入文件系统
        if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
            return true;
        }

        if ($this->tag && !is_file($filename)) {
            $first = true;
        }
        
        try {
            $ret = file_put_contents($filename, ("<?php return " . var_export($value, true) . ";"));

            // 通过设置修改时间实现有效期
            if ($ret) {
                isset($first) && $this->setTagItem($filename);
                touch($filename, $expire);
            }

            return $ret;
        } catch (\Exception $e) {
            // 在Vercel环境下，忽略文件写入错误
            if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
                return true;
            } else {
                throw $e;
            }
        }
    }

    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param  string    $name 缓存变量名
     * @param  int       $step 步长
     * @return false|int
     */
    public function inc($name, $step = 1)
    {
        if ($this->has($name)) {
            $value = $this->get($name) + $step;
        } else {
            $value = $step;
        }

        return $this->set($name, $value, 0) ? $value : false;
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param  string    $name 缓存变量名
     * @param  int       $step 步长
     * @return false|int
     */
    public function dec($name, $step = 1)
    {
        if ($this->has($name)) {
            $value = $this->get($name) - $step;
        } else {
            $value = -$step;
        }

        return $this->set($name, $value, 0) ? $value : false;
    }

    /**
     * 删除缓存
     * @access public
     * @param  string $name 缓存变量名
     * @return boolean
     */
    public function rm($name)
    {
        $this->writeTimes++;
        
        $filename = $this->getCacheKey($name);
        
        // 从内存缓存中删除
        if (isset(self::$cache[$filename])) {
            unset(self::$cache[$filename]);
        }
        
        // 在Vercel环境中不操作文件系统
        if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
            return true;
        }
        
        try {
            return unlink($filename);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 清除缓存
     * @access public
     * @param  string $tag 标签名
     * @return bool
     */
    public function clear($tag = null)
    {
        if ($tag) {
            // 指定标签清除
            $keys = $this->getTagItem($tag);
            
            // 清除内存缓存
            foreach ($keys as $key) {
                if (isset(self::$cache[$key])) {
                    unset(self::$cache[$key]);
                }
            }
            
            // 在Vercel环境中不操作文件系统
            if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
                return true;
            }
            
            foreach ($keys as $key) {
                try {
                    unlink($key);
                } catch (\Exception $e) {
                    // 忽略错误
                }
            }

            $this->rm($this->getTagKey($tag));
            return true;
        }
        
        // 清除所有内存缓存
        self::$cache = [];

        $this->writeTimes++;
        
        // 在Vercel环境中不操作文件系统
        if (isset($_SERVER['VERCEL']) && $_SERVER['VERCEL'] === '1') {
            return true;
        }
        
        try {
            $files = glob($this->options['path'] . ($this->options['prefix'] ? $this->options['prefix'] . DIRECTORY_SEPARATOR : '') . '*.php');
            foreach ($files as $file) {
                unlink($file);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
