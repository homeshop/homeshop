<?php namespace App\Mobile\Http\Controllers\Seller;

use App\Mobile\Http\Controllers\MobileSellerControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 商家注销
 */
class LogoutControl extends MobileSellerControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 注销
     */
    public function indexOp() {
        if(empty($_POST['seller_name']) || !in_array($_POST['client'], $this->client_type_array)){
            output_error('参数错误');
        }
        $model_mb_seller_token = Model('mb_seller_token');
        if($this->seller_info['seller_name'] == $_POST['seller_name']){
            $condition = [];
            $condition['seller_id'] = $this->seller_info['seller_id'];
            $model_mb_seller_token->delSellerToken($condition);
            output_data('1');
        } else {
            output_error('参数错误');
        }
    }
}
