<?php namespace App\Mobile\Http\Controllers\Member;

use App\Mobile\Http\Controllers\MobileMemberControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 浏览历史
 */
class GoodsbrowseControl extends MobileMemberControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 我的足迹
     */
    public function browse_listOp() {
        $model_goodsbrowse = Model('goods_browse');
        //查询浏览记录
        $where = [];
        $where['member_id'] = $this->member_info['member_id'];
        $browselist = $model_goodsbrowse->getGoodsbrowseList($where, '*', $this->page, 0, 'browsetime desc');
        $page_count = $model_goodsbrowse->gettotalpage();
        $goodsid_arr = [];
        foreach((array)$browselist as $k => $v){
            $goodsid_arr[] = $v['goods_id'];
        }
        //查询商品信息
        $browselist_new = [];
        if($goodsid_arr){
            $goods_list_tmp = Model('goods')->getGoodsList([
                'goods_id' => [
                    'in',
                    $goodsid_arr
                ]
            ], 'goods_id, goods_name, goods_promotion_price,goods_promotion_type, goods_marketprice, goods_image, store_id, gc_id, gc_id_1, gc_id_2, gc_id_3');
            $goods_list = [];
            foreach((array)$goods_list_tmp as $v){
                $goods_list[ $v['goods_id'] ] = $v;
            }
            foreach($browselist as $k => $v){
                if($goods_list[ $v['goods_id'] ]){
                    $tmp = [];
                    $tmp = $goods_list[ $v['goods_id'] ];
                    $tmp["browsetime"] = $v['browsetime'];
                    $tmp["goods_image_url"] = cthumb($goods_list[ $v['goods_id'] ]['goods_image'], 360, $goods_list[ $v['goods_id'] ]['store_id']);
                    if(date('Y-m-d', $v['browsetime']) == date('Y-m-d', time())){
                        $tmp['browsetime_day'] = '今天';
                    } elseif(date('Y-m-d', $v['browsetime']) == date('Y-m-d', (time() - 86400))) {
                        $tmp['browsetime_day'] = '昨天';
                    } else {
                        $tmp['browsetime_day'] = date('Y年m月d日', $v['browsetime']);
                    }
                    $tmp['browsetime_text'] = $tmp['browsetime_day'] . date('H:i', $v['browsetime']);
                    $browselist_new[] = $tmp;
                }
            }
        }
        output_data(['goodsbrowse_list' => $browselist_new], mobile_page($page_count));
    }
    
    /**
     * 清空足迹
     */
    public function browse_clearallOp() {
        if(Model('goods_browse')->delGoodsbrowse(['member_id' => $this->member_info['member_id']])){
            output_data('1');
        } else {
            output_error('清空失败');
        }
    }
}
