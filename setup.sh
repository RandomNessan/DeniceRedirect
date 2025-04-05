#!/bin/bash

echo "🚀 开始设置网站和数据库信息..."

# 交互式获取网站 URL
read -p "请输入您的网站 URL (例如: pool.spacesyun.com): " SITE_URL
WEB_ROOT="/www/wwwroot/${SITE_URL}/public"

# 交互式获取数据库信息
read -p "请输入数据库主机名 (默认: localhost): " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "请输入数据库名称: " DB_NAME
read -p "请输入数据库用户名: " DB_USER
read -sp "请输入数据库密码: " DB_PASS
echo

# 创建 .env 文件
cat <<EOF > .env
# 数据库配置
DB_HOST="$DB_HOST"
DB_NAME="$DB_NAME"
DB_USER="$DB_USER"
DB_PASS="$DB_PASS"
EOF

echo "✅ .env 文件已创建！"

# 读取 .env 文件
export $(grep -v '^#' .env | xargs)

# 确保 MySQL 客户端已安装
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL 客户端未安装，请先安装 mysql-client！"
    exit 1
fi

# 初始化数据库
echo "🚀 正在初始化数据库 ${DB_NAME}..."
mysql -h "${DB_HOST}" -u "${DB_USER}" -p"${DB_PASS}" < init.sql

if [ $? -eq 0 ]; then
    echo "✅ 数据库初始化成功！"
else
    echo "❌ 数据库初始化失败，请检查 MySQL 配置！"
    exit 1
fi

# 创建软链接
echo "🚀 正在创建软链接 /admin -> ../admin"
cd "$WEB_ROOT" || { echo "❌ 目录 $WEB_ROOT 不存在，请检查您的输入！"; exit 1; }

if [ -L "admin" ]; then
    echo "✅ 软链接已存在，无需重复创建。"
else
    ln -s ../admin admin
    echo "✅ 软链接创建成功！"
fi

# 验证软链接
ls -l admin

echo "🎉 网站和数据库已成功配置完成！"
echo "============================================================"
echo "默认用户名: admin"
echo "默认密码: admin"

echo "请手动设置 /public 目录为 Running directory "
echo "设置完成后即可访问https://" ${SITE_URL} "/admin 进行管理操作"
echo "============================================================"
