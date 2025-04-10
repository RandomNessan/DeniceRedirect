#!/bin/bash

echo "🚀 开始设置网站和数据库信息..."


read -p "请输入您的网站 URL (例如: aaa.bbb.com): " SITE_URL
WEB_ROOT="/www/wwwroot/${SITE_URL}"

if [ ! -d "$WEB_ROOT" ]; then
    echo "📂 目录 $WEB_ROOT 不存在，正在创建..."
    mkdir -p "$WEB_ROOT"
    echo "✅ 目录创建成功！"
fi


cd /root || exit 1
echo "🚀 正在克隆仓库..."
git clone https://github.com/RandomNessan/DeniceRedirect.git


if [ ! -d "/root/DeniceRedirect" ]; then
    echo "❌ 克隆失败，请检查网络或 GitHub 仓库地址！"
    exit 1
fi


echo "📂 正在复制文件到 $WEB_ROOT ..."
cp -r /root/DeniceRedirect/* "$WEB_ROOT"


echo "🗑️ 正在删除临时目录 /root/DeniceRedirect..."
rm -rf /root/DeniceRedirect
echo "✅ 删除完成！"


WEB_PUBLIC="$WEB_ROOT/public"
if [ ! -d "$WEB_PUBLIC" ]; then
    echo "❌ 目录 $WEB_PUBLIC 不存在，请检查网站结构！"
    exit 1
fi

cd "$WEB_PUBLIC" || exit 1
echo "🚀 正在创建软链接 /admin -> ../admin"
if [ -L "admin" ]; then
    echo "✅ 软链接已存在，无需重复创建。"
else
    ln -s ../admin admin
    echo "✅ 软链接创建成功！"
fi


ls -l admin

echo "🚀 开始设置数据库信息..."


read -p "请输入数据库主机名 (默认: localhost): " DB_HOST
DB_HOST=${DB_HOST:-localhost}

read -p "请输入数据库名称: " DB_NAME
read -p "请输入数据库用户名: " DB_USER
read -sp "请输入数据库密码: " DB_PASS
echo


cat <<EOF > "$WEB_ROOT/.env"
# 数据库配置
DB_HOST="$DB_HOST"
DB_NAME="$DB_NAME"
DB_USER="$DB_USER"
DB_PASS="$DB_PASS"
EOF

echo "✅ .env 文件已创建！"

# 读取 .env 文件
export $(grep -v '^#' "$WEB_ROOT/.env" | xargs)

# 确保 MySQL 客户端已安装
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL 客户端未安装，请先安装 mysql-client！"
    exit 1
fi

# 初始化数据库
echo "🚀 正在初始化数据库 ${DB_NAME}..."
mysql -h "${DB_HOST}" -u "${DB_USER}" -p"${DB_PASS}" < "$WEB_ROOT/init.sql"

if [ $? -eq 0 ]; then
    echo "✅ 数据库初始化成功！"
else
    echo "❌ 数据库初始化失败，请检查 MySQL 配置！"
fi

echo "🎉 网站部署完成！"
echo "============================================================"
echo "网站访问信息> "
echo "默认用户名: admin"
echo "默认密码: admin"
echo "现在可以访问 https://"${SITE_URL}"/admin 对网站进行管理操作"
echo "============================================================"
