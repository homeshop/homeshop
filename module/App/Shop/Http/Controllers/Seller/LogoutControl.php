<?php namespace App\Shop\Http\Controllers\Seller;

use App\Shop\Http\Controllers\BaseSellerControl;
use App\Shop\Http\Controllers\Language;
use App\Shop\Http\Controllers\Tpl;


/**
 * 店铺卖家注销
 */
class  LogoutControl extends BaseSellerControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function indexOp() {
        $this->logoutOp();
    }
    
    public function logoutOp() {
        $this->recordSellerLog('注销成功');
        // 清除店铺消息数量缓存
        setNcCookie('storemsgnewnum'.$_SESSION['seller_id'], 0, -3600);
        session_destroy();
        redirect('index.php?act=seller_login');
    }
    
}
