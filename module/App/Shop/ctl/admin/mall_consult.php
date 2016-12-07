<?php

/**
 * 平台客观咨询管理
 */
class mall_consultControl extends SystemControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 咨询管理
     */
    public function indexOp() {
        // 咨询类型列表
        $type_list = Model('mall_consult_type')->getMallConsultTypeList([], 'mct_id,mct_name', 'mct_id');
        Tpl::output('type_list', $type_list);
        Tpl::setDirquna('shop');
        Tpl::showpage('mall_consult.index');
    }
    
    /**
     * 咨询管理
     */
    public function get_xmlOp() {
        $condition = [];
        if($_POST['query'] != '' && in_array($_POST['qtype'], ['member_name', 'mc_content'])){
            $condition[ $_POST['qtype'] ] = ['like', "%{$_POST['query']}%"];
        }
        $sort_fields = ['mc_addtime', 'is_reply', 'mc_id'];
        if(in_array($_POST['sortorder'], ['asc', 'desc']) && in_array($_POST['sortname'], $sort_fields)){
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        $model_mallconsult = Model('mall_consult');
        $consult_list = $model_mallconsult->getMallConsultList($condition, '*', $_POST['rp'], $order);
        $data = [];
        $data['now_page'] = $model_mallconsult->shownowpage();
        $data['total_num'] = $model_mallconsult->gettotalnum();
        foreach($consult_list as $consult_info){
            $list = [];
            $operation_detail = '';
            $list['operation'] = "<a class='btn red' onclick=\"fg_delete({$consult_info['mc_id']})\"><i class=\"fa fa-trash-o\"></i>删除</a>";
            if($consult_info['is_reply']){
                $list['operation'] .= "<a class=\"btn green\" href=\"index.php?act=mall_consult&op=consult_reply&id={$consult_info['mc_id']}\"><i class=\"fa fa-list-alt\"></i>编辑</a>";
            } else {
                $list['operation'] .= "<a class=\"btn green\" href=\"index.php?act=mall_consult&op=consult_reply&id={$consult_info['mc_id']}\"><i class=\"fa fa-list-alt\"></i>回复</a>";
            }
            $list['consult_content'] = "<span title='{$consult_info['mc_content']}'>{$consult_info['mc_content']}</span>";
            $list['member_name'] = $consult_info['member_name'];
            $list['member_id'] = $consult_info['member_id'];
            $list['payment_time'] = date('Y-m-d H:i:s', $consult_info['mc_addtime']);
            $list['order_from'] = str_replace([0, 1], ['未回复', '已回复'], $consult_info['is_reply']);
            $data['list'][ $consult_info['mc_id'] ] = $list;
        }
        exit(Tpl::flexigridXML($data));
    }
    
    /**
     * 回复咨询
     */
    public function consult_replyOp() {
        $model_mallconsult = Model('mall_consult');
        if(chksubmit()){
            $mc_id = intval($_POST['mc_id']);
            $reply_content = trim($_POST['reply_content']);
            if($mc_id <= 0 || $reply_content == ''){
                showMessage(L('param_error'));
            }
            $update['is_reply'] = 1;
            $update['mc_reply'] = $reply_content;
            $update['mc_reply_time'] = TIMESTAMP;
            $update['admin_id'] = $this->admin_info['id'];
            $update['admin_name'] = $this->admin_info['name'];
            $result = $model_mallconsult->editMallConsult(['mc_id' => $mc_id], $update);
            if($result){
                $consult_info = $model_mallconsult->getMallConsultInfo(['mc_id' => $mc_id]);
                // 发送用户消息
                $param = [];
                $param['code'] = 'consult_mall_reply';
                $param['member_id'] = $consult_info['member_id'];
                $param['param'] = [
                    'consult_url' => urlShop('member_mallconsult', 'mallconsult_info', ['id' => $mc_id])
                ];
                QueueClient::push('sendMemberMsg', $param);
                
                showMessage('回复成功', urlAdminShop('mall_consult', 'index'));
            } else {
                showMessage('回复失败');
            }
        }
        $id = intval($_GET['id']);
        if($id <= 0){
            showMessage(L('param_error'));
        }
        
        $consult_info = $model_mallconsult->getMallConsultDetail($id);
        Tpl::output('consult_info', $consult_info);
        Tpl::setDirquna('shop');
        Tpl::showpage('mall_consult.reply');
    }
    
    /**
     * 删除平台客服咨询
     */
    public function del_consultOp() {
        if(preg_match('/^[\d,]+$/', $_GET['del_id'])){
            $_GET['del_id'] = explode(',', trim($_GET['del_id'], ','));
            $result = Model('mall_consult')->delMallConsult(['mc_id' => ['in', $_GET['del_id']]]);
            if($result){
                $this->log('删除平台客服咨询' . '[ID:' . $id . ']', null);
                showMessage(Language::get('nc_common_del_succ'));
            } else {
                showMessage(Language::get('nc_common_del_fail'));
            }
        }
        showMessage(Language::get('nc_common_del_fail'));
    }
    
    /**
     * 批量删除平台客服咨询
     */
    public function del_consult_batchOp() {
        $ids = $_POST['id'];
        if(empty($ids)){
            showMessage(Language::get('nc_common_del_fail'));
        }
        $result = Model('mall_consult')->delMallConsult(['mc_id' => ['in', $ids]]);
        if($result){
            $this->log('删除平台客服咨询' . '[ID:' . implode(',', $ids) . ']', null);
            showMessage(Language::get('nc_common_del_succ'));
        } else {
            showMessage(Language::get('nc_common_del_fail'));
        }
    }
    
    /**
     * 咨询类型列表
     */
    public function type_listOp() {
        $model_mct = Model('mall_consult_type');
        if(chksubmit()){
            $mctid_array = $_POST['del_id'];
            if(!is_array($mctid_array)){
                showMessage(L('param_error'));
            }
            foreach($mctid_array as $val){
                if(!is_numeric($val)){
                    showMessage(L('param_error'));
                }
            }
            
            $result = $model_mct->delMallConsultType(['mct_id' => ['in', $mctid_array]]);
            
            if($result){
                $this->log('删除平台客服咨询类型 ID:' . implode(',', $mctid_array), 1);
                showMessage(L('nc_common_del_succ'));
            } else {
                $this->log('删除平台客服咨询类型 ID:' . implode(',', $mctid_array), 0);
                showMessage(L('nc_common_del_fail'));
            }
        }
        $type_list = $model_mct->getMallConsultTypeList([], '*');
        Tpl::output('type_list', $type_list);
        Tpl::setDirquna('shop');
        Tpl::showpage('mall_consult.type_list');
    }
    
    /**
     * 新增咨询类型
     */
    public function type_addOp() {
        if(chksubmit()){
            // 验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = [
                ["input" => $_POST["mct_name"], "require" => "true", "message" => '请填写咨询类型名称'],
                [
                    "input" => $_POST["mct_sort"],
                    "require" => "true",
                    'validator' => 'Number',
                    "message" => '请正确填写咨询类型排序'
                ],
            ];
            $error = $obj_validate->validate();
            if($error != ''){
                showMessage(Language::get('error') . $error, '', '', 'error');
            }
            $insert = [];
            $insert['mct_name'] = trim($_POST['mct_name']);
            $insert['mct_introduce'] = $_POST['mct_introduce'];
            $insert['mct_sort'] = intval($_POST['mct_sort']);
            $result = Model('mall_consult_type')->addMallConsultType($insert);
            if($result){
                $this->log('新增咨询类型', 1);
                showMessage(L('nc_common_save_succ'), urlAdminShop('mall_consult', 'type_list'));
            } else {
                $this->log('新增咨询类型', 0);
                showMessage(L('nc_common_save_fail'));
            }
        }
        Tpl::setDirquna('shop');
        Tpl::showpage('mall_consult.type_add');
    }
    
    /**
     * 编辑咨询类型
     */
    public function type_editOp() {
        $model_mct = Model('mall_consult_type');
        if(chksubmit()){
            // 验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = [
                ["input" => $_POST["mct_name"], "require" => "true", "message" => '请填写咨询类型名称'],
                [
                    "input" => $_POST["mct_sort"],
                    "require" => "true",
                    'validator' => 'Number',
                    "message" => '请正确填写咨询类型排序'
                ],
            ];
            $error = $obj_validate->validate();
            if($error != ''){
                showMessage(Language::get('error') . $error, '', '', 'error');
            }
            $where = [];
            $where['mct_id'] = intval($_POST['mct_id']);
            $update = [];
            $update['mct_name'] = trim($_POST['mct_name']);
            $update['mct_introduce'] = $_POST['mct_introduce'];
            $update['mct_sort'] = intval($_POST['mct_sort']);
            $result = $model_mct->editMallConsultType($where, $update);
            if($result){
                $this->log('编辑平台客服咨询类型 ID:' . $where['mct_id'], 1);
                showMessage(L('nc_common_op_succ'), urlAdminShop('mall_consult', 'type_list'));
            } else {
                $this->log('编辑平台客服咨询类型 ID:' . $where['mct_id'], 0);
                showMessage(L('nc_common_op_fail'));
            }
        }
        
        $mct_id = intval($_GET['mct_id']);
        if($mct_id <= 0){
            showMessage(L('param_error'));
        }
        $mct_info = $model_mct->getMallConsultTypeInfo(['mct_id' => $mct_id]);
        Tpl::output('mct_info', $mct_info);
        Tpl::setDirquna('shop');
        Tpl::showpage('mall_consult.type_edit');
    }
    
    /**
     * 删除咨询类型
     */
    public function type_delOp() {
        $mct_id = intval($_GET['mct_id']);
        if($mct_id <= 0){
            showMessage(L('param_error'));
        }
        $result = Model('mall_consult_type')->delMallConsultType(['mct_id' => $mct_id]);
        if($result){
            $this->log('删除平台客服咨询类型 ID:' . $mct_id, 1);
            showMessage(L('nc_common_del_succ'));
        } else {
            $this->log('删除平台客服咨询类型 ID:' . $mct_id, 0);
            showMessage(L('nc_common_del_fail'));
        }
    }
}
