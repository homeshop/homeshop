<?php
/**
 * WordPress基础配置文件。
 *
 * 这个文件被安装程序用于自动生成wp-config.php配置文件，
 * 您可以不使用网站，您需要手动复制这个文件，
 * 并重命名为“wp-config.php”，然后填入相关信息。
 *
 * 本文件包含以下配置选项：
 *
 * * MySQL设置
 * * 密钥
 * * 数据库表名前缀
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/zh-cn:%E7%BC%96%E8%BE%91_wp-config.php
 *
 * @package WordPress
 */

define('WP_DIRNAME',basename(__DIR__));

define('WP_HOME','http://'.$_SERVER['HTTP_HOST'].'/'.WP_DIRNAME);
define('WP_SITEURL',WP_HOME.'/app');

define('WP_ALLOW_REPAIR', true);

//define('TEMPLATEPATH',dirname(ABSPATH).'/theme');

define('WP_CONTENT_DIR',dirname(ABSPATH));
define('WP_CONTENT_URL','http://'.$_SERVER['HTTP_HOST'].'/'.WP_DIRNAME);

//define('WP_PLUGIN_DIR',dirname(dirname(ABSPATH)).DIRECTORY_SEPARATOR.'plugin');
//define('WP_PLUGIN_URL','http://'.$_SERVER['HTTP_HOST'].'/plugin');
//
//define('WPMU_PLUGIN_DIR',realpath(dirname(dirname(ABSPATH)).'/plugin/common'));
//define('WPMU_PLUGIN_URL','http://'.$_SERVER['HTTP_HOST'].'/plugin/common');


// ** MySQL 设置 - 具体信息来自您正在使用的主机 ** //
/** WordPress数据库的名称 */
define('DB_NAME', 'shop');

/** MySQL数据库用户名 */
define('DB_USER', 'root');

/** MySQL数据库密码 */
define('DB_PASSWORD', 'root');

/** MySQL主机 */
define('DB_HOST', 'localhost');

/** 创建数据表时默认的文字编码 */
define('DB_CHARSET', 'utf8mb4');

/** 数据库整理类型。如不确定请勿更改 */
define('DB_COLLATE', '');

/**#@+
 * 身份认证密钥与盐。
 *
 * 修改为任意独一无二的字串！
 * 或者直接访问{@link https://api.wordpress.org/secret-key/1.1/salt/
 * WordPress.org密钥生成服务}
 * 任何修改都会导致所有cookies失效，所有用户将必须重新登录。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '/AAM,M%7<TMnJdak#!oSLqPA2GJ0Et%*:24J{$aG+/0ysVhcc5hJm2JaO1SG^KaY');
define('SECURE_AUTH_KEY',  '*N`GUM#06T:&1h`@=-Jw<!iAt|4`cGxHT>u$ WQsB5y?hnn)Q,@sW:jVpIOS`ro.');
define('LOGGED_IN_KEY',    '_`EU1J* ^X~ ybT[R4r`D8pea<F^M}WEET|?t6CfT5<3gVfp^/BV`=;#%27B4##l');
define('NONCE_KEY',        '|,$h`xq2#ZyK|$kXB[4D$s8reSdTXJIU_#{?xL45[6+qt~/yt`~EyQXp=myf.,At');
define('AUTH_SALT',        'c3q)~^6=<}|Z4qVN*[1N#vCoMANzj@`kx*K:e!R>j+UIbJm&4RdEj@h@<dm0zb]B');
define('SECURE_AUTH_SALT', 'OjWj?;8TD6o0.utK[uze9{`YvXl$&VvE+J MK!UKA,VB_P%=5OKV](![jKkP[!sG');
define('LOGGED_IN_SALT',   'K>}=Uu7*7{akS]#H1&3wDPh4Kf&tj6~f Sam~sq&T(JZkI>7 kN,&U{:9&c2W@{G');
define('NONCE_SALT',       'F,Tc2lcaz`~/p-.n*dn^]_yL-j56#G)FEu/QcxZJtUl2GP}bzc&Tz*u}$?)(`q^g');

/**#@-*/

/**
 * WordPress数据表前缀。
 *
 * 如果您有在同一数据库内安装多个WordPress的需求，请为每个WordPress设置
 * 不同的数据表前缀。前缀名只能为数字、字母加下划线。
 */
$table_prefix  = 'wmt_topic_';

/**
 * 开发者专用：WordPress调试模式。
 *
 * 将这个值改为true，WordPress将显示所有用于开发的提示。
 * 强烈建议插件开发者在开发环境中启用WP_DEBUG。
 *
 * 要获取其他能用于调试的信息，请访问Codex。
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/**
 * zh_CN本地化设置：启用ICP备案号显示
 *
 * 可在设置→常规中修改。
 * 如需禁用，请移除或注释掉本行。
 */
define('WP_ZH_CN_ICP_NUM', true);

/* 好了！请不要再继续编辑。请保存本文件。使用愉快！ */

/** WordPress目录的绝对路径。 */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** 设置WordPress变量和包含文件。 */
require_once(ABSPATH . 'wp-settings.php');
