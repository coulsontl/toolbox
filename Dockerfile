FROM php:8.3-fpm-alpine

# 安装系统依赖和NGINX
RUN apk update && apk add --no-cache \
    nginx \
    curl \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -rf /var/cache/apk/*

# 创建必要的目录
RUN mkdir -p /var/log/nginx \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /run/nginx \
    && mkdir -p /etc/supervisor/conf.d

# 设置工作目录
WORKDIR /var/www/html

# 先复制 composer 文件
COPY composer.json composer.lock ./

# 安装 PHP 依赖
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 复制其余项目文件
COPY . .

# 复制NGINX配置
COPY nginx.conf /etc/nginx/nginx.conf

# 创建supervisor配置
RUN echo '[supervisord]' > /etc/supervisor/conf.d/supervisord.conf && \
    echo 'nodaemon=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'user=root' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '[program:nginx]' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'command=nginx -g "daemon off;"' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stdout_logfile=/var/log/supervisor/nginx.log' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stderr_logfile=/var/log/supervisor/nginx_error.log' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '[program:php-fpm]' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'command=php-fpm -F' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stdout_logfile=/var/log/supervisor/php-fpm.log' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stderr_logfile=/var/log/supervisor/php-fpm_error.log' >> /etc/supervisor/conf.d/supervisord.conf

# 创建PHP-FPM配置
RUN echo '[www]' > /usr/local/etc/php-fpm.d/www.conf && \
    echo 'user = www-data' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'group = www-data' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'listen = 127.0.0.1:9000' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'listen.owner = www-data' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'listen.group = www-data' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm = dynamic' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_children = 50' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.start_servers = 5' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.min_spare_servers = 5' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_spare_servers = 35' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_requests = 500' >> /usr/local/etc/php-fpm.d/www.conf

# 创建PHP配置
RUN echo 'memory_limit = 256M' > /usr/local/etc/php/conf.d/custom.ini && \
    echo 'upload_max_filesize = 100M' >> /usr/local/etc/php/conf.d/custom.ini && \
    echo 'post_max_size = 100M' >> /usr/local/etc/php/conf.d/custom.ini && \
    echo 'max_execution_time = 300' >> /usr/local/etc/php/conf.d/custom.ini && \
    echo 'max_input_time = 300' >> /usr/local/etc/php/conf.d/custom.ini

# 设置权限和准备运行时目录
RUN mkdir -p runtime/cache runtime/log runtime/temp \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 777 runtime \
    && composer dump-autoload --optimize

# 暴露端口
EXPOSE 80

# 启动supervisor来管理NGINX和PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]