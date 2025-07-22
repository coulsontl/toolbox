#!/bin/sh
set -e

# 设置环境变量
export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}

# 确保运行时目录存在并设置正确权限
mkdir -p runtime/cache runtime/log runtime/temp
chown -R www-data:www-data runtime
chmod -R 775 runtime

# 如果存在 Composer，确保依赖已安装
if [ -f "composer.json" ] && [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader
fi

# 清理缓存（如果需要）
if [ "$APP_ENV" = "production" ]; then
    echo "Clearing cache for production..."
    rm -rf runtime/cache/* runtime/temp/* 2>/dev/null || true
fi

echo "Starting ThinkPHP 8.0 application..."

# 启动 php-fpm
php-fpm &

# 启动 nginx
exec nginx -g 'daemon off;'