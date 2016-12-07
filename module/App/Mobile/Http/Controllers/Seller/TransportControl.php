<?php namespace App\Mobile\Http\Controllers\Seller;

use App\Mobile\Http\Controllers\MobileSellerControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 商家运费模板
 */
class TransportControl extends MobileSellerControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function indexOp() {
        $this->transport_listOp();
    }
    
    /**
     * 返回商家店铺商品分类列表
     */
    public function transport_listOp() {
        $model_transport = Model('transport');
        $transport_list = $model_transport->getTransportList(['store_id' => $this->store_info['store_id']]);
        output_data(['transport_list' => $transport_list]);
    }
}
