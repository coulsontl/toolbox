<?php

namespace app\admin\controller;
use app\BaseController;
use think\facade\Session;
use think\facade\View;

class Base extends BaseController
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

    protected function fetch($template = '', $vars = [])
    {
        return View::fetch($template, $vars);
    }
}