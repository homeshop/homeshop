<?php namespace App\Mobile\Http\Controllers;

/**
 * 店铺
 */
class VoucherControl extends MobileHomeControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 代金券列表
     */
    public function voucher_tpl_listOp() {
        $param = $_REQUEST;
        $model_voucher = Model('voucher');
        $templatestate_arr = $model_voucher->getTemplateState();
        $voucher_gettype_array = $model_voucher->getVoucherGettypeArray();
        $where = [];
        $where['voucher_t_state'] = $templatestate_arr['usable'][0];
        $store_id = intval($param['store_id']);
        if($store_id > 0){
            $where['voucher_t_store_id'] = $store_id;
        }
        $where['voucher_t_gettype'] = [
            'in',
            [$voucher_gettype_array['points']['sign'], $voucher_gettype_array['free']['sign']]
        ];
        if($param['gettype'] && in_array($param['gettype'], ['points', 'free'])){
            $where['voucher_t_gettype'] = $voucher_gettype_array[ $param['gettype'] ]['sign'];
        }
        $order = 'voucher_t_id asc';
        $voucher_list = $model_voucher->getVoucherTemplateList($where, '*', 20, 0, $order);
        if($voucher_list){
            foreach($voucher_list as $k => $v){
                $v['voucher_t_end_date_text'] = $v['voucher_t_end_date'] ? @date('Y年m月d日', $v['voucher_t_end_date']) : '';
                $voucher_list[ $k ] = $v;
            }
        }
        output_data(['voucher_list' => $voucher_list]);
    }
}
