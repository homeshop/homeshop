<?php namespace App\Mobile\Http\Controllers\Seller;

use App\Mobile\Http\Controllers\MobileSellerControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 商家注销
 */
class AlbumControl extends MobileSellerControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function image_uploadOp() {
        $logic_goods = Logic('goods');
        $result = $logic_goods->uploadGoodsImage($_POST['name'], $this->seller_info['store_id'], $this->store_grade['sg_album_limit']);
        if(!$result['state']){
            output_error($result['msg']);
        }
        output_data(['image_name' => $result['data']['name']]);
    }
}
