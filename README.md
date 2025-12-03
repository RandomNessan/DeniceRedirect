### 一个有多种风格主题的线路检测站。界面无任何特征, 一定程度上可以起到防被ban的作用。一些从事有趣行业的运营者可以直接套用。

# 部署方式>

1. 在vps上安装aapanel然后安装下列软件: Nginx 1.24; MySQL 8.0; PHP 7.4。
2. 解析域名后创建网站, 创建数据库,php选择7.4。 数据库名和数据表名必须为"pool_route"，若选择其他命名需要在初始化之前进入 init.sql 修改。
3. 回到vps, 清空根目录下默认文件后进行以下操作。
    
    ## 克隆Repo
	git clone https://github.com/RandomNessan/DeniceRedirect.git

	## 更改可执行文件权限
	chmod +x setup.sh

	## 运行setup文件进行安装
    ./setup.sh
   
5. 开启SSL(可选)。
6. index.php有多种风格可选：Commercial, Cyber, Dream, Hacker。自行切换即可。
7. 管理员账号密码皆为admin。
