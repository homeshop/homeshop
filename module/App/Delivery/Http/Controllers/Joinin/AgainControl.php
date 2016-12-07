<?php namespace App\Delivery\Http\Controllers\Joinin;

use App\Delivery\Http\Controllers\BaseDeliveryCenterControl;
use App\Delivery\Http\Controllers\Language;
use App\Delivery\Http\Controllers\Tpl;

/**
 * 物流自提服务站首页
 */
class  AgainControl extends BaseDeliveryCenterControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 编辑信息
     */
    public function indexOp() {
        $model_dp = Model('delivery_point');
        $dpoint_info = $model_dp->getDeliveryPointFailInfo(['dlyp_id' => $_SESSION['dlyp_id']]);
        Tpl::output('dpoint_info', $dpoint_info);
        Tpl::showpage('joinin_again', 'login_layout');
    }
    
    /**
     * 保存申请
     */
    public function edit_deliveryOp() {
        if(!chksubmit()){
            showDialog(L('wrong_argument'));
        }
        $dlyp_id = $_POST['did'];
        if($dlyp_id <= 0){
            showDialog(L('nc_common_op_fail'));
        }
        $update = [];
        $update['dlyp_name'] = $_POST['dname'];
        $update['dlyp_passwd'] = md5($_POST['dpasswd']);
        $update['dlyp_truename'] = $_POST['dtruename'];
        $update['dlyp_mobile'] = $_POST['dmobile'];
        $update['dlyp_telephony'] = $_POST['dtelephony'];
        $update['dlyp_address_name'] = $_POST['daddressname'];
        $update['dlyp_area_1'] = intval($_POST['area_id_1']);
        $update['dlyp_area_2'] = intval($_POST['area_id_2']);
        $update['dlyp_area_3'] = intval($_POST['area_id_3']);
        $update['dlyp_area_4'] = intval($_POST['area_id_4']);
        $update['dlyp_area'] = intval($_POST['area_id']);
        $update['dlyp_area_info'] = $_POST['region'];
        $update['dlyp_address'] = $_POST['daddress'];
        $update['dlyp_idcard'] = $_POST['didcard'];
        $update['dlyp_addtime'] = TIMESTAMP;
        $update['dlyp_state'] = 10;
        $update['dlyp_fail_reason'] = '';
        $upload = new UploadFile();
        $upload->set('default_dir', ATTACH_DELIVERY);
        $result = $upload->upfile('didcardimg');
        if(!$result){
            showDialog($upload->error);
        }
        $update['dlyp_idcard_image'] = $upload->file_name;
        $result = Model('delivery_point')->editDeliveryPoint($update, ['dlyp_id' => $dlyp_id]);
        if($result){
            showDialog('操作成功，等待管理员审核', 'index.php?act=login', 'succ');
        } else {
            showDialog(L('nc_common_op_fail'));
        }
    }
    
    /**
     * ajax验证用户名是否存在
     */
    public function checkOp() {
        $where = [];
        $dlyp_id = intval($_GET['did']);
        if($dlyp_id <= 0){
            echo 'false';
            die;
        }
        $where['dlyp_id'] = ['neq', $dlyp_id];
        if($_GET['dname'] != ''){
            $where['dlyp_name'] = $_GET['dname'];
        }
        if($_GET['didcard'] != ''){
            $where['dlyp_idcard'] = $_GET['didcard'];
        }
        if($_GET['dmobile'] != ''){
            $where['dlyp_mobile'] = $_GET['dmobile'];
        }
        $dp_info = Model('delivery_point')->getDeliveryPointInfo($where);
        if(empty($dp_info)){
            echo 'true';
            die;
        } else {
            echo 'false';
            die;
        }
    }
}
