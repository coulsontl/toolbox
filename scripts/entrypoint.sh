#!/bin/sh
set -e

# Update admin.php config
if [ ! -z "$username" ] && [ ! -z "$password" ]; then
    sed -i "s/'username'.*.admin'/'username' => '$username'/" /www/toolbox/config/admin.php
    sed -i "s/'password'.*.admin'/'password' => '$password'/" /www/toolbox/config/admin.php
fi

# 启动 php-fpm
php-fpm &

# 启动 nginx
exec nginx -g 'daemon off;' 