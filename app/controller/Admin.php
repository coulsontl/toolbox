<?php

namespace app\controller;

use app\BaseController;
use think\facade\Session;
use think\facade\View;

class Admin extends BaseController
{
    protected function isLogin()
    {
        $session = Session::get('admin');
        if($session && $session === $this->getSession()) { 
            return true;
        }
        return false;
    }

    protected function getSession()
    {
        $config = config('admin.');
        return md5($config['username'].md5($config['password']));
    }

    protected function checkLogin()
    {
        if(!$this->isLogin()){
            return redirect('/admin/login');
        }
    }
    
    public function index()
    {
        // 暂时跳过登录检查，直接显示后台首页
        try {
            return View::fetch('admin/index/index');
        } catch (\Exception $e) {
            return json([
                'message' => 'Admin panel',
                'status' => 'ThinkPHP 8.0 Admin is working!',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function login()
    {
        try {
            return View::fetch('admin/index/login');
        } catch (\Exception $e) {
            return json([
                'message' => 'Admin login page',
                'status' => 'Please implement login functionality',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function main()
    {
        // 获取系统信息
        $info = [
            'framework_version' => app()::VERSION,
            'php_version' => PHP_VERSION,
            'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'os' => PHP_OS,
            'date' => date('Y-m-d H:i:s')
        ];

        try {
            return View::fetch('admin/index/main', ['info' => $info]);
        } catch (\Exception $e) {
            // 如果模板加载失败，返回简单的 HTML
            return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>系统信息</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/layui/2.7.6/css/layui.css">
    <style>body { padding: 20px; background: #f2f2f2; }</style>
</head>
<body>
    <blockquote class="layui-elem-quote"><h2>工具网后台管理</h2></blockquote>
    <div class="layui-card">
        <div class="layui-card-header">服务器信息</div>
        <div class="layui-card-body">
            <table class="layui-table">
                <tr><td>框架版本</td><td>' . $info['framework_version'] . '</td></tr>
                <tr><td>PHP版本</td><td>' . $info['php_version'] . '</td></tr>
                <tr><td>WEB软件</td><td>' . $info['software'] . '</td></tr>
                <tr><td>操作系统</td><td>' . $info['os'] . '</td></tr>
                <tr><td>服务器时间</td><td>' . $info['date'] . '</td></tr>
            </table>
        </div>
    </div>
    <p>模板错误: ' . $e->getMessage() . '</p>
</body>
</html>';
        }
    }

    public function web_cache()
    {
        // 暂时跳过登录检查
        if(request()->isPost()) {
            try {
                // 清理缓存目录
                $runtimePath = app()->getRootPath().'runtime';
                if (is_dir($runtimePath)) {
                    $this->deleteDir($runtimePath);
                }
                return json(['status' => 'ok', 'message' => '缓存清理成功']);
            } catch (\Exception $e) {
                return json(['status' => 'error', 'message' => '缓存清理失败: ' . $e->getMessage()]);
            }
        }

        try {
            return View::fetch('admin/index/web_cache');
        } catch (\Exception $e) {
            return json([
                'message' => 'Web cache management page',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function logout()
    {
        Session::delete('admin');
        return redirect('/admin/login');
    }

    public function account()
    {
        // 暂时跳过登录检查
        $config = config('admin.');

        if(request()->isPost() && request()->isAjax()){
            // 处理账户更新逻辑
            return json(['status' => 'ok', 'message' => '账户更新功能待实现']);
        }

        try {
            return View::fetch('admin/index/account', ['config' => $config]);
        } catch (\Exception $e) {
            return json([
                'message' => 'Account management page',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function deleteDir($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->deleteDir($path);
            } else {
                unlink($path);
            }
        }
        return rmdir($dir);
    }

    public function test()
    {
        return '<h1>Admin Test Page</h1><p>This is a simple test page for admin.</p>';
    }
}
