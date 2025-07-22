FROM php:8.3-fpm-alpine

# 安装 nginx 和必要的 PHP 扩展
RUN apk add --no-cache nginx \
    && docker-php-ext-install pdo pdo_mysql

# 设置工作目录
WORKDIR /www/toolbox

# 复制项目文件到容器中
COPY . .

# 移动配置文件到对应位置并清理不需要的目录
RUN mv scripts/nginx.conf /etc/nginx/http.d/default.conf \
    && mv scripts/entrypoint.sh /entrypoint.sh \
    && chmod +x /entrypoint.sh \
    && rm -rf scripts/ \
    && rm -rf tp8_test/ \
    && mkdir -p runtime/cache runtime/log runtime/temp \
    && chown -R www-data:www-data . \
    && chmod -R 755 . \
    && chmod -R 775 runtime public app/view

# 暴露 Web 端口
EXPOSE 80

# 设置启动脚本
ENTRYPOINT ["/entrypoint.sh"]