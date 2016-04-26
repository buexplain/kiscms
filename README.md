Keep it simple！这个是一个简单的便于二次开发的web内容管理系统。大多数情况下，它不能满足您的需求。<br/>

建议安装环境： Linux + Apache + PHP or Linux + Nginx + PHP<br/>

安装步骤：<br/>
1、导入（工具：phpmyadmin） kiscms.sql<br/>
2、配置数据库连接文件 Application/Common/Conf/db.php<br/>
3、进入后台更改密码，后台地址：http://域名/admin/Sign/index.html<br/>
4、如果要保护后台地址，可以更改 Application/Common/Conf/config.php 中的 URL_MODULE_MAP 进行模块映射<br/>
5、同时修改一下 Application/Common/Conf/site.php 的 fileBrowseUrl地址，将其改为 URL_MODULE_MAP 的key值<br/>

后台初始化登录帐号，将其中的#去掉：<br/>
> admin#@admin.c#om    12345678 <br/>
> buexplain#@163.c#om  12345678 <br/>
> guest#@guest.c#om    12345678 <br/>

###Apache服务器的伪静态规则
    <IfModule expires_module>
        ExpiresActive On
        <FilesMatch "\.(gif|jpg|png|js|css)$">
            ExpiresDefault "access plus 30 days"
        </FilesMatch>
    </IfModule>
    <IfModule mod_rewrite.c>
         RewriteEngine on
         RewriteCond %{REQUEST_FILENAME} !-d
         RewriteCond %{REQUEST_FILENAME} !-f
         RewriteRule ^(.*)$ index.php?s=$1 [QSA,PT,L]
    </IfModule>
###Nginx服务器的伪静态规则
    #如果请求既不是一个文件，也不是一个目录，则执行一下重写规则
    if (!-e $request_filename)
    {
        #地址作为将参数rewrite到index.php上。
        rewrite ^/(.*)$ /index.php?s=$1;
    }
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
    {
        access_log off;
        expires      30d;
    }
    location ~ .*\.(js|css)?$
    {
        access_log off;
        expires      12h;
    }
###Tengine服务器的伪静态规则
    ExpiresActive On
    <FilesMatch "\.(gif|jpg|png|js|css)$">
        ExpiresDefault "access plus 30 days"
    </FilesMatch>
     RewriteEngine on
     RewriteCond %{REQUEST_FILENAME} !-d
     RewriteCond %{REQUEST_FILENAME} !-f
     RewriteRule ^(.*)$ index.php?s=$1 [QSA,PT,L]


[我的博客](http://www.kiscms.com "kiscms官网")<br/>
QQ交流群：89292141
