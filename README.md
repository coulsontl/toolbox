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

## 1 docker

设置用户名密码

- username：用户名（默认值：admin）
- password：密码（默认值：admin）

后台管理地址：`http://192.168.3.34:8080/admin`

```bash
docker run -d --restart always \
	--name tools \
	-p 8080:80 \
	-e username=admin \
	-e password=admin \
	cleverest/toolbox
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
  tools:
    image: cleverest/toolbox
    container_name: tools
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

