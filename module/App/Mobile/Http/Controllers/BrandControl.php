<?php namespace App\Mobile\Http\Controllers;

/**
 * 前台品牌分类
 */
class BrandControl extends MobileHomeControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function recommend_listOp() {
        $brand_list = Model('brand')->getBrandPassedList(['brand_recommend' => '1'], 'brand_id,brand_name,brand_pic');
        if(!empty($brand_list)){
            foreach($brand_list as $key => $val){
                $brand_list[ $key ]['brand_pic'] = brandImage($val['brand_pic']);
            }
        }
        output_data(['brand_list' => $brand_list]);
    }
}
