#!/bin/sh
set -e

# 设置环境变量
export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}

# 确保运行时目录存在并设置正确权限
mkdir -p runtime/cache runtime/log runtime/temp
chown -R www-data:www-data runtime
chmod -R 775 runtime

# Composer 依赖已在构建时安装，这里只需要确保自动加载是最新的
if [ -f "composer.json" ]; then
    echo "Optimizing Composer autoloader..."
    composer dump-autoload --optimize --no-dev
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