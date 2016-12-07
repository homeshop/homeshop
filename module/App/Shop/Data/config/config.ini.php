<?php
$config = array();
$config['base_site_url'] 		= 'http://homeshop.dev';
$config['shop_site_url']        = 'http://homeshop.dev/shop';
$config['cms_site_url']         = 'http://homeshop.dev/cms';
$config['microshop_site_url']   = 'http://homeshop.dev/microshop';
$config['circle_site_url']      = 'http://homeshop.dev/circle';
$config['admin_site_url']       = 'http://homeshop.dev/admin';
$config['mobile_site_url']      = 'http://homeshop.dev/mobile';
$config['wap_site_url']         = 'http://homeshop.dev/wap';
$config['chat_site_url']        = 'http://homeshop.dev/chat';
$config['wechat_site_url']      = 'http://homeshop.dev/wechat/ems/';
$config['node_site_url'] 		= 'http://homeshop.dev:33'; //如果要启用IM，把 http://homeshop.dev 修改为您的服务器IP
$config['delivery_site_url']    = 'http://homeshop.dev/delivery';
$config['chain_site_url']       = 'http://homeshop.dev/chain';
$config['member_site_url']      = 'http://homeshop.dev/member';
$config['upload_site_url']      = 'http://homeshop.dev/data/upload';
$config['resource_site_url']    = 'http://homeshop.dev/data/resource';
$config['cms_modules_url']      = 'http://homeshop.dev/admin/modules/cms';
$config['microshop_modules_url']= 'http://homeshop.dev/admin/modules/microshop';
$config['circle_modules_url']   = 'http://homeshop.dev/admin/modules/circle';
$config['admin_modules_url']    = 'http://homeshop.dev/admin/modules/shop';
$config['mobile_modules_url']   = 'http://homeshop.dev/admin/modules/mobile';
$config['version']              = '201608030001';
$config['setup_date']           = '2016-08-06 00:37:07';
$config['gip']                  = 0;
$config['dbdriver']             = 'mysql';
$config['tablepre']             = 'homeshop_';
$config['db']['1']['dbhost']       = 'localhost';
$config['db']['1']['dbport']       = '3306';
$config['db']['1']['dbuser']       = 'root';
$config['db']['1']['dbpwd']        = 'root';
$config['db']['1']['dbname']       = 'homeshop';
$config['db']['1']['dbcharset']    = 'UTF-8';
$config['db']['slave']             = $config['db']['master'];
$config['session_expire']   = 3600;
$config['lang_type']        = 'zh_cn';
$config['cookie_pre']       = '4A17_';
$config['cache_open'] = false;
//$config['redis']['prefix']        = 'hao_';
//$config['redis']['master']['port']        = 6379;
//$config['redis']['master']['host']        = '127.0.0.1';
//$config['redis']['master']['pconnect']    = 0;
//$config['redis']['slave']             = array();
//$config['fullindexer']['open']      = false;
//$config['fullindexer']['appname']   = 'homeshop';
$config['debug']            = false;
$config['url_model'] = true; //如果要启用伪静态，把false修改为true
$config['subdomain_suffix'] = '';//如果要启用店铺二级域名，请填写不带www的域名，比如hoemshop.com
//$config['session_type'] = 'redis';
//$config['session_save_path'] = 'tcp://127.0.0.1:6379';
$config['node_chat'] = false;//如果要启用IM，把false修改为true
//流量记录表数量，为1~10之间的数字，默认为3，数字设置完成后请不要轻易修改，否则可能造成流量统计功能数据错误
$config['flowstat_tablenum'] = 3;
$config['queue']['open'] = false;
$config['queue']['host'] = '127.0.0.1';
$config['queue']['port'] = 6379;
//$config['oss']['open'] = false;
//$config['oss']['img_url'] = '';
//$config['oss']['api_url'] = '';
//$config['oss']['bucket'] = '';
//$config['oss']['access_id'] = '';
//$config['oss']['access_key'] = '';
$config['https'] = false;
return $config;