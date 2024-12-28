# 使用官方 PHP 8.1 镜像作为基础镜像
FROM php:8.1-cli

# 设置工作目录
WORKDIR /app

# 安装系统依赖和 PHP 扩展
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        zip \
        pdo \
        pdo_mysql \
        gd \
    && rm -rf /var/lib/apt/lists/*

# 安装 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 允许 Composer 以超级用户身份运行
ENV COMPOSER_ALLOW_SUPERUSER=1

# 复制项目文件到容器中
COPY . .

# 设置目录权限
RUN chmod -R 755 . \
    && chmod -R 777 public

# 暴露端口（使用 8080）
EXPOSE 8080

# 使用 PHP 内置服务器启动项目
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
