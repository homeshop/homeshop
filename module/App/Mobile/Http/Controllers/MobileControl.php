<?php namespace App\Mobile\Http\Controllers;

/********************************** 前台control父类 **********************************************/
class MobileControl  {
    
    //客户端类型
    protected $client_type_array = ['android', 'wap', 'wechat', 'ios', 'windows'];
    //列表默认分页数
    protected $page = 5;
    
    public function __construct() {
        Language::read('mobile');
        //分页数处理
        $page = intval($_GET['page']);
        if($page > 0){
            $this->page = $page;
        }
    }
}






