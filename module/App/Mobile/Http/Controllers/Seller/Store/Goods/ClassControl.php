<?php namespace App\Mobile\Http\Controllers\Seller\Store\Goods;

use App\Mobile\Http\Controllers\MobileSellerControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 商家店铺商品分类
 */
class ClassControl extends MobileSellerControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function indexOp() {
        $this->class_listOp();
    }
    
    /**
     * 返回商家店铺商品分类列表
     */
    public function class_listOp() {
        $store_goods_class = Model('store_goods_class')->getStoreGoodsClassPlainList($this->store_info['store_id']);
        output_data(['class_list' => $store_goods_class]);
    }
}
