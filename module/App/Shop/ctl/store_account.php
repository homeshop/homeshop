<?php

/**
 * 卖家账号管理
 */
class store_accountControl extends BaseSellerControl {
    
    public function __construct() {
        parent::__construct();
        Language::read('member_store_index');
    }
    
    public function account_listOp() {
        $model_seller = Model('seller');
        $condition = ['store_id' => $_SESSION['store_id'], 'seller_group_id' => ['gt', 0]];
        $seller_list = $model_seller->getSellerList($condition);
        Tpl::output('seller_list', $seller_list);
        
        if(!empty($seller_list)){
            $memberid_array = [];
            foreach($seller_list as $val){
                $memberid_array[] = $val['member_id'];
            }
            $member_name_array = Model('member')->getMemberList([
                'member_id' => [
                    'in',
                    $memberid_array
                ]
            ], 'member_id,member_name');
            $member_name_array = array_under_reset($member_name_array, 'member_id');
            Tpl::output('member_name_array', $member_name_array);
            
            $model_seller_group = Model('seller_group');
            $seller_group_list = $model_seller_group->getSellerGroupList(['store_id' => $_SESSION['store_id']]);
            $seller_group_array = array_under_reset($seller_group_list, 'group_id');
            Tpl::output('seller_group_array', $seller_group_array);
        }
        
        $this->profile_menu('account_list');
        Tpl::showpage('store_account.list');
    }
    
    public function account_addOp() {
        $model_seller_group = Model('seller_group');
        $seller_group_list = $model_seller_group->getSellerGroupList(['store_id' => $_SESSION['store_id']]);
        if(empty($seller_group_list)){
            showMessage('请先建立账号组', urlShop('store_account_group', 'group_add'), '', 'error');
        }
        Tpl::output('seller_group_list', $seller_group_list);
        $this->profile_menu('account_add');
        Tpl::showpage('store_account.add');
    }
    
    public function account_editOp() {
        $seller_id = intval($_GET['seller_id']);
        if($seller_id <= 0){
            showMessage('参数错误', '', '', 'error');
        }
        $model_seller = Model('seller');
        $seller_info = $model_seller->getSellerInfo(['seller_id' => $seller_id]);
        if(empty($seller_info) || intval($seller_info['store_id']) !== intval($_SESSION['store_id'])){
            showMessage('账号不存在', '', '', 'error');
        }
        Tpl::output('seller_info', $seller_info);
        
        $model_seller_group = Model('seller_group');
        $seller_group_list = $model_seller_group->getSellerGroupList(['store_id' => $_SESSION['store_id']]);
        if(empty($seller_group_list)){
            showMessage('请先建立账号组', urlShop('store_account_group', 'group_add'), '', 'error');
        }
        Tpl::output('seller_group_list', $seller_group_list);
        
        $this->profile_menu('account_edit');
        Tpl::showpage('store_account.edit');
    }
    
    public function account_saveOp() {
        $member_name = $_POST['member_name'];
        $password = $_POST['password'];
        $member_info = $this->_check_seller_member($member_name, $password);
        if(!$member_info){
            showDialog('用户验证失败', 'reload', 'error');
        }
        
        $seller_name = $_POST['seller_name'];
        if($this->_is_seller_name_exist($seller_name)){
            showDialog('卖家账号已存在', 'reload', 'error');
        }
        
        $group_id = intval($_POST['group_id']);
        
        // 客户端登陆选项判断
        $is_client = 0;
        if(intval($_POST['is_client']) > 0){
            $is_client = 1;
        }
        
        $seller_info = [
            'seller_name' => $seller_name,
            'member_id' => $member_info['member_id'],
            'seller_group_id' => $group_id,
            'store_id' => $_SESSION['store_id'],
            'is_admin' => 0,
            'is_client' => $is_client,
        ];
        $model_seller = Model('seller');
        $result = $model_seller->addSeller($seller_info);
        
        if($result){
            $this->recordSellerLog('添加账号成功，账号编号'.$result);
            showDialog(Language::get('nc_common_op_succ'), urlShop('store_account', 'account_list'), 'succ');
        } else {
            $this->recordSellerLog('添加账号失败');
            showDialog(Language::get('nc_common_save_fail'), urlShop('store_account', 'account_list'), 'error');
        }
    }
    
    public function account_edit_saveOp() {
        // 客户端登陆选项判断
        $is_client = 0;
        if(intval($_POST['is_client']) > 0){
            $is_client = 1;
        }
        
        $param = ['seller_group_id' => intval($_POST['group_id']), 'is_client' => $is_client,];
        
        $condition = ['seller_id' => intval($_POST['seller_id']), 'store_id' => $_SESSION['store_id']];
        $model_seller = Model('seller');
        $result = $model_seller->editSeller($param, $condition);
        if($result){
            $this->recordSellerLog('编辑账号成功，账号编号：'.$_POST['seller_id']);
            showDialog(Language::get('nc_common_op_succ'), urlShop('store_account', 'account_list'), 'succ');
        } else {
            $this->recordSellerLog('编辑账号失败，账号编号：'.$_POST['seller_id'], 0);
            showDialog(Language::get('nc_common_save_fail'), urlShop('store_account', 'account_list'), 'error');
        }
    }
    
    public function account_delOp() {
        $seller_id = intval($_POST['seller_id']);
        if($seller_id > 0){
            $condition = [];
            $condition['seller_id'] = $seller_id;
            $condition['store_id'] = $_SESSION['store_id'];
            $model_seller = Model('seller');
            $result = $model_seller->delSeller($condition);
            if($result){
                $this->recordSellerLog('删除账号成功，账号编号'.$seller_id);
                showDialog(Language::get('nc_common_op_succ'), 'reload', 'succ');
            } else {
                $this->recordSellerLog('删除账号失败，账号编号'.$seller_id);
                showDialog(Language::get('nc_common_save_fail'), 'reload', 'error');
            }
        } else {
            showDialog(Language::get('wrong_argument'), 'reload', 'error');
        }
    }
    
    public function check_seller_name_existOp() {
        $seller_name = $_GET['seller_name'];
        $result = $this->_is_seller_name_exist($seller_name);
        if($result){
            echo 'true';
        } else {
            echo 'false';
        }
    }
    
    private function _is_seller_name_exist($seller_name) {
        $condition = [];
        $condition['seller_name'] = $seller_name;
        $model_seller = Model('seller');
        return $model_seller->isSellerExist($condition) || Model('store_joinin')->isExist($condition);
    }
    
    public function check_seller_memberOp() {
        $member_name = $_GET['member_name'];
        $password = $_GET['password'];
        $result = $this->_check_seller_member($member_name, $password);
        if($result){
            echo 'true';
        } else {
            echo 'false';
        }
    }
    
    private function _check_seller_member($member_name, $password) {
        $member_info = $this->_check_member_password($member_name, $password);
        if($member_info && !$this->_is_seller_member_exist($member_info['member_id'])){
            return $member_info;
        } else {
            return false;
        }
    }
    
    private function _check_member_password($member_name, $password) {
        $condition = [];
        $condition['member_name'] = $member_name;
        $condition['member_passwd'] = md5($password);
        $model_member = Model('member');
        $member_info = $model_member->getMemberInfo($condition);
        return $member_info;
    }
    
    private function _is_seller_member_exist($member_id) {
        $condition = [];
        $condition['member_id'] = $member_id;
        $model_seller = Model('seller');
        return $model_seller->isSellerExist($condition);
    }
    
    /**
     * 用户中心右边，小导航
     * @param string $menu_key 当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_key = '') {
        $menu_array = [];
        $menu_array[] = [
            'menu_key' => 'account_list',
            'menu_name' => '账号列表',
            'menu_url' => urlShop('store_account', 'account_list')
        ];
        if($menu_key === 'account_add'){
            $menu_array[] = [
                'menu_key' => 'account_add',
                'menu_name' => '添加账号',
                'menu_url' => urlShop('store_account', 'account_add')
            ];
        }
        if($menu_key === 'account_edit'){
            $menu_array[] = [
                'menu_key' => 'account_edit',
                'menu_name' => '编辑账号',
                'menu_url' => urlShop('store_account', 'account_edit')
            ];
        }
        
        Tpl::output('member_menu', $menu_array);
        Tpl::output('menu_key', $menu_key);
    }
    
}
