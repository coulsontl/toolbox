FROM php:8.1-fpm-alpine

# 安装 nginx
RUN apk add --no-cache nginx

# 设置工作目录
WORKDIR /www/toolbox

# 复制项目文件到容器中
COPY . .

# 设置目录权限并清理不必要的文件
RUN chown -R www-data:www-data . \
    && chmod -R 755 . \
    && chmod -R 775 runtime public

# 配置 nginx
COPY scripts/nginx.conf /etc/nginx/http.d/default.conf

# 复制并设置启动脚本
COPY scripts/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# 暴露 Web 端口
EXPOSE 80

# 设置启动脚本
ENTRYPOINT ["/entrypoint.sh"]