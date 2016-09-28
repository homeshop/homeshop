
## 注意

不要用记事本打开或者修改程序里的文件，会产生bom,导致网站产生一些问题，
比如验证码不显示等问题，推荐Notepad++文本编辑器。

## 安装之前准备工作：

把你的PHP环境配置好，程序只支持php5.3以上的版本，最好的是php5.4，php.ini要支持以下：
 
    extension=php_curl.dll
    extension=php_openssl.dll
    date.timezone =PRC
    session.auto_start = 1
    asp_tags = Off
    short_open_tag = On


## 安装教程：

导入数据库根目录下的 33haov5.sql

打开 `\data\config\config.ini.php`

批量把 `homeshop.dev` 更换为你的域名

修改数据库连接，比如以下

    $config['db']['1']['dbhost']       = 'localhost';
    $config['db']['1']['dbport']       = '3306';
    $config['db']['1']['dbuser']       = 'root';
    $config['db']['1']['dbpwd']        = 'root';
    $config['db']['1']['dbname']       = 'homeshop';


进入后台-右上角-清理网站缓存 即可.


请检查以下目录及子目录有写入、修改权限，再重新安装：

    /data/cache
    /data/config
    /data/log
    /data/session
    /data/resource/phpqrcode/temp
    /data/upload
    /sql_back
    /install

## 后台账号密码：

    admin
    admin123
    
----------------------------------------------

## 更新与问题反馈

http://github.com/homeshop/homeshop

## test


