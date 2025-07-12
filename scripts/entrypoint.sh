#!/bin/sh
set -e

# 环境变量已经在 config/admin.php 中直接读取，无需 sed 替换

# 启动 php-fpm
php-fpm &

# 启动 nginx
exec nginx -g 'daemon off;' 