FROM php:8.1-fpm-alpine

# 安装 nginx
RUN apk add --no-cache nginx

# 设置工作目录
WORKDIR /www/toolbox

# 复制项目文件到容器中
COPY . .

# 移动配置文件到对应位置并清理不需要的目录
RUN mv scripts/nginx.conf /etc/nginx/http.d/default.conf \
    && mv scripts/entrypoint.sh /entrypoint.sh \
    && chmod +x /entrypoint.sh \
    && rm -rf scripts/ \
    && rm -rf runtime/temp \
    && chown -R www-data:www-data . \
    && chmod -R 755 . \
    && chmod -R 775 runtime public

# 暴露 Web 端口
EXPOSE 80

# 设置启动脚本
ENTRYPOINT ["/entrypoint.sh"]