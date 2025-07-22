# Docker 部署指南

## 环境要求

- Docker 20.10+
- Docker Compose 2.0+

## 快速开始

### 1. 构建镜像

```bash
docker build -t toolbox:latest .
```

### 2. 运行容器

```bash
# 直接运行
docker run -d -p 8080:80 --name toolbox toolbox:latest

# 或使用 Docker Compose
docker-compose up -d
```

### 3. 访问应用

打开浏览器访问：http://localhost:8080

## 配置说明

### 环境变量

- `APP_ENV`: 应用环境 (production/development)
- `APP_DEBUG`: 调试模式 (true/false)

### 目录挂载

建议挂载以下目录以持久化数据：

```bash
docker run -d \
  -p 8080:80 \
  -v ./runtime:/www/toolbox/runtime \
  --name toolbox \
  toolbox:latest
```

## 技术栈

- **PHP**: 8.3-fpm-alpine
- **Web Server**: Nginx
- **Framework**: ThinkPHP 8.1.3
- **Architecture**: 单容器部署

## 特性

- ✅ PHP 8.3 支持
- ✅ ThinkPHP 8.0 适配
- ✅ Nginx 优化配置
- ✅ 生产环境优化
- ✅ 自动权限设置
- ✅ 缓存目录管理

## 故障排除

### 权限问题

如果遇到权限问题，确保运行时目录有正确的权限：

```bash
chmod -R 775 runtime
```

### 缓存清理

进入容器清理缓存：

```bash
docker exec -it toolbox sh
rm -rf runtime/cache/* runtime/temp/*
```

## 开发模式

开发时可以挂载源代码：

```bash
docker run -d \
  -p 8080:80 \
  -v .:/www/toolbox \
  -e APP_DEBUG=true \
  --name toolbox-dev \
  toolbox:latest
```
