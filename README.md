# ThinkPHP工具箱

基于ThinkPHP 5.1的工具箱项目，可部署到Vercel。

## 部署到Vercel

### 前提条件

1. 拥有一个[Vercel](https://vercel.com)账号
2. 已将此项目推送到GitHub、GitLab或Bitbucket

### 部署步骤

1. 登录到Vercel
2. 点击"New Project"
3. 导入你的Git仓库
4. 配置环境变量:
   - `DB_TYPE`: 数据库类型，如mysql
   - `DB_HOST`: 数据库主机
   - `DB_NAME`: 数据库名
   - `DB_USER`: 数据库用户名
   - `DB_PASS`: 数据库密码
   - `DB_PORT`: 数据库端口，默认3306
   - `ADMIN_USERNAME`: 管理员用户名，默认admin
   - `ADMIN_PASSWORD`: 管理员密码，默认admin
5. 点击"Deploy"

### 管理员账号配置

在Vercel环境中，管理员账号和密码通过环境变量配置，无法通过页面修改。如需修改管理员账号密码，请在Vercel控制台中修改以下环境变量：

- `ADMIN_USERNAME`: 管理员用户名
- `ADMIN_PASSWORD`: 管理员密码

修改环境变量后，需要重新部署应用才能生效。

### 注意事项

- Vercel是无服务器环境，所有文件操作都是临时的
- 如果需要持久化存储，请使用外部数据库或存储服务
- QQWry.dat文件较大，已在.vercelignore中排除，如需使用请调整配置

## 本地开发

```bash
# 安装依赖
composer install

# 启动服务
php think run
```

## 项目结构

- `/api`: Vercel无服务器函数入口
- `/application`: 应用目录
- `/config`: 配置文件目录
- `/public`: 公共资源目录
- `/thinkphp`: ThinkPHP框架目录

## 许可证

ThinkPHP遵循Apache2开源协议发布，并提供免费使用。

## Docker 部署（推荐）

### 快速开始

1. **克隆项目**
   ```bash
   git clone <repository-url>
   cd toolbox
   ```

2. **构建并启动**
   ```bash
   docker-compose up --build -d
   ```

3. **访问应用**
   - 打开浏览器访问: http://localhost:8080

### Docker 架构

**单容器解决方案** - 包含NGINX + PHP-FPM：
- **基础镜像**: PHP 8.3 FPM Alpine
- **Web服务器**: NGINX (高性能)
- **进程管理**: Supervisor
- **端口映射**: 8080:80
- **环境**: 生产模式
- **自动重启**: 启用

### 容器特性

- ✅ **高性能**: NGINX + PHP-FPM 架构
- ✅ **单容器**: 简化部署和管理
- ✅ **进程监控**: Supervisor 管理服务
- ✅ **路由修复**: 正确处理ThinkPHP路由
- ✅ **安全配置**: 隐藏敏感目录
- ✅ **静态文件优化**: Gzip压缩和缓存

### 服务管理

```bash
# 查看服务状态
docker-compose ps

# 查看日志
docker-compose logs

# 重启服务
docker-compose restart

# 停止服务
docker-compose down
```

## 1 docker（旧版本）

设置用户名密码

- username：用户名（默认值：admin）
- password：密码（默认值：admin）

后台管理地址：`http://192.168.3.34:8080/admin`

```bash
docker run -d --restart always \
	--name toolbox \
	-p 8080:80 \
	-e username=admin \
	-e password=admin \
	coulsontl/toolbox
```



## 2 docker compose

创建 `docker-compose.yml` 文件。如果不指定 `username` `password` ，默认用户名密码均为：`admin` 

设置用户名密码

- username：用户名（默认值：admin）
- password：密码（默认值：admin）

后台管理地址：`http://192.168.3.34:8080/admin`


```yaml
version: '3'

services:
  toolbox:
    image: coulsontl/toolbox
    container_name: toolbox
    restart: unless-stopped
    ports:
      - "8080:80"
    environment:
      - username=admin
      - password=admin
```

```bash
# 启动服务
docker-compose up -d
```

