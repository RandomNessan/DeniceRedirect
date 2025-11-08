#!/bin/bash

echo "🚀 开始设置数据库信息..."

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
fi
