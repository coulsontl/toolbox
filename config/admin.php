<?php
// +----------------------------------------------------------------------
// | 管理员配置
// +----------------------------------------------------------------------

// 获取环境变量，支持 Docker 环境变量
$admin_username = getenv('username') ?: getenv('ADMIN_USERNAME') ?: 'admin';
$admin_password = getenv('password') ?: getenv('ADMIN_PASSWORD') ?: 'admin';

return [
    'username' => $admin_username,
    'password' => $admin_password,
];