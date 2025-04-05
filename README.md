# DeniceRedirect
A php based website software that allows your site-visitor's acknowledgement of which URL is more accessable.

随手编写的小工具，主要功能是用户页面的链接跳转和测速。
界面无任何特征，一定程度上可以起到防被ban的作用。一些从事有趣行业的运营者可以直接套用。

部署方式>
  依赖>
   在vps上安装aapanel然后安装下列软件:
			Nginx 1.24
			MySQL 8.0
			PHP 7.4

  域名解析>
    没什么好说的
    
  创建网站>
    创建数据库，php选择7.4。

  回到vps>
    清空根目录下默认文件后，通过SSH登录VPS并进行以下操作。
		
   	        # 克隆Repo
		git clone https://github.com/RandomNessan/DeniceRedirect.git

		# 进入克隆下来的文件夹
		cd DeniceRedirect/

		# 更改可执行文件权限
		chmod +x setup.sh

		# 运行setup文件
	       ./setup.sh

   初始化完毕后进入aapanel网站设置，将网站运行目录(Site Directory)设置为"/public"。开启SSL(可选)。
