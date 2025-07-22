<?php

namespace app\index\controller;

class Test
{
    public function index()
    {
        return 'Hello ThinkPHP 8.0!';
    }
    
    public function info()
    {
        return json([
            'framework' => 'ThinkPHP',
            'version' => '8.1.3',
            'php_version' => PHP_VERSION,
            'status' => 'working'
        ]);
    }
}
