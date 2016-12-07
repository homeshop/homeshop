<?php namespace App\Mobile\Http\Controllers\Member;

use App\Mobile\Http\Controllers\MobileMemberControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 我的发票
 */
class InvoiceControl extends MobileMemberControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 发票信息列表
     */
    public function invoice_listOp() {
        $model_invoice = Model('invoice');
        $condition = [];
        $condition['member_id'] = $this->member_info['member_id'];
        $invoice_list = $model_invoice->getInvList($condition, 10, 'inv_id,inv_title,inv_content');
        output_data(['invoice_list' => $invoice_list]);
    }
    
    /**
     * 发票信息删除
     */
    public function invoice_delOp() {
        $inv_id = intval($_POST['inv_id']);
        if($inv_id <= 0){
            output_error('参数错误');
        }
        $model_invoice = Model('invoice');
        $result = $model_invoice->delInv(['inv_id' => $inv_id, 'member_id' => $this->member_info['member_id']]);
        if($result){
            output_data('1');
        } else {
            output_error('删除失败');
        }
    }
    
    /**
     * 发票信息添加
     */
    public function invoice_addOp() {
        $model_invoice = Model('invoice');
        $data = [];
        $data['inv_state'] = 1;
        $data['inv_title'] = $_POST['inv_title_select'] == 'person' ? '个人' : $_POST['inv_title'];
        $data['inv_content'] = $_POST['inv_content'];
        $data['member_id'] = $this->member_info['member_id'];
        $result = $model_invoice->addInv($data);
        if($result){
            output_data(['inv_id' => $result]);
        } else {
            output_error('添加失败');
        }
    }
    
    /**
     * 发票内容列表
     */
    public function invoice_content_listOp() {
        $invoice_content_list = [
            '明细',
            '酒',
            '食品',
            '饮料',
            '玩具',
            '日用品',
            '装修材料',
            '化妆品',
            '办公用品',
            '学生用品',
            '家居用品',
            '饰品',
            '服装',
            '箱包',
            '精品',
            '家电',
            '劳防用品',
            '耗材',
            '电脑配件'
        ];
        output_data(['invoice_content_list' => $invoice_content_list]);
    }
}
