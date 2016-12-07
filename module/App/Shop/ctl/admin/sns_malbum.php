<?php

/**
 * 会员相册管理
 */
class sns_malbumControl extends SystemControl {
    
    public function __construct() {
        parent::__construct();
        Language::read('sns_malbum');
    }
    
    public function indexOp() {
        $this->class_listOp();
    }
    
    /**
     * 相册设置
     */
    public function settingOp() {
        $model_setting = Model('setting');
        if(chksubmit()){
            //构造更新数据数组
            $update_array = [];
            $update_array['malbum_max_sum'] = intval($_POST['malbum_max_sum']);
            $result = $model_setting->updateSetting($update_array);
            if($result === true){
                showMessage(Language::get('nc_common_save_succ'));
            } else {
                showMessage(Language::get('nc_common_save_fail'));
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting', $list_setting);
        Tpl::setDirquna('shop');
        Tpl::showpage('sns_malbum.setting');
    }
    
    /**
     * 相册列表
     */
    public function class_listOp() {
        Tpl::setDirquna('shop');
        Tpl::showpage('sns_malbum.classlist');
    }
    
    /**
     * 输出XML数据
     */
    public function get_xmlOp() {
        $model = Model();
        $condition = [];
        if($_POST['query'] != ''){
            $condition[ $_POST['qtype'] ] = ['like', '%' . $_POST['query'] . '%'];
        }
        $order = '';
        $param = ['ac_id', 'ac_name', 'member_id', 'store_name', 'ac_cover', 'pic_count', 'ac_des'];
        if(in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], ['asc', 'desc'])){
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        $page = $_POST['rp'];
        $ac_list = $model->table('sns_albumclass')->where($condition)->page($page)->order($order)->select();
        
        $memberid_array = [];
        $acid_array = [];
        foreach($ac_list as $value){
            $memberid_array[] = $value['member_id'];
            $acid_array[] = $value['ac_id'];
        }
        
        // 会员名称
        $member_list = Model('member')->getMemberList([
            'member_id' => [
                'in',
                $memberid_array
            ]
        ], 'member_id,member_name');
        $member_array = [];
        foreach($member_list as $value){
            $member_array[ $value['member_id'] ] = $value['member_name'];
        }
        
        // 图片数量
        $count_list = $model->cls()->table('sns_albumpic')->field('count(ap_id) as count,ac_id')->where([
            'ac_id' => [
                'in',
                $acid_array
            ]
        ])->group('ac_id')->select();
        $count_array = [];
        foreach($count_list as $val){
            $count_array[ $val['ac_id'] ] = $val['count'];
        }
        $data = [];
        $data['now_page'] = $model->shownowpage();
        $data['total_num'] = $model->gettotalnum();
        foreach($ac_list as $value){
            $param = [];
            $param['operation'] = "<a class='btn green' href='index.php?act=sns_malbum&op=pic_list&id=" . $value['ac_id'] . "' class='url'><i class='fa fa-list-alt'></i>查看</a>";
            $param['ac_id'] = $value['ac_id'];
            $param['ac_name'] = $value['ac_name'];
            $param['member_id'] = $value['member_id'];
            $param['member_name'] = $member_array[ $value['member_id'] ];
            $param['ac_cover'] = "<a href='javascript:void(0);' class='pic-thumb-tip' onMouseOut='toolTip()' onMouseOver='toolTip(\"<img src=" . ($value['ac_cover'] != '' ? UPLOAD_SITE_URL . DS . ATTACH_MALBUM . DS . $value['member_id'] . DS . $value['ac_cover'] : ADMIN_SITE_URL . '/templates/' . TPL_NAME . '/images/member/default_image.png') . ">\")'><i class='fa fa-picture-o'></i></a>";
            $param['pic_count'] = intval($count_array[ $value['ac_id'] ]);
            $param['ac_des'] = $value['ac_des'];
            $data['list'][ $value['ac_id'] ] = $param;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }
    
    /**
     * 图片列表
     */
    public function pic_listOp() {
        $model = Model();
        // 删除图片
        if(chksubmit()){
            $where = ['ap_id' => ['in', $_POST['id']]];
            $ap_list = $model->table('sns_albumpic')->where($where)->select();
            if(empty($ap_list)){
                showMessage(Language::get('snsalbum_choose_need_del_img'));
            }
            foreach($ap_list as $val){
                @unlink(BASE_UPLOAD_PATH . DS . ATTACH_MALBUM . DS . $val['member_id'] . DS . $val['ap_cover']);
                @unlink(BASE_UPLOAD_PATH . DS . ATTACH_MALBUM . DS . $val['member_id'] . DS . str_ireplace('.', '_240.', $val['ap_cover']));
                @unlink(BASE_UPLOAD_PATH . DS . ATTACH_MALBUM . DS . $val['member_id'] . DS . str_ireplace('.', '_1280.', $val['ap_cover']));
            }
            $model->table('sns_albumpic')->where($where)->delete();
            $this->log(L('nc_del,nc_member_album_manage') . '[ID:' . implode(',', $_POST['id']) . ']', 1);
            showMessage(Language::get('nc_common_del_succ'));
        }
        $id = intval($_GET['id']);
        
        $where = [];
        if($id > 0){
            $where['ac_id'] = $id;
        }
        if($_GET['pic_name'] != ''){
            $where['ap_name|ap_cover'] = ['like', '%' . $_GET['pic_name'] . '%'];
        }
        $pic_list = $model->table('sns_albumpic')->where($where)->page(33)->select();
        Tpl::output('id', $id);
        Tpl::output('showpage', $model->showpage(2));
        Tpl::output('pic_list', $pic_list);
        Tpl::setDirquna('shop');
        Tpl::showpage('sns_malbum.piclist');
    }
    
    /**
     * 删除图片
     */
    public function del_picOp() {
        $id = intval($_GET['id']);
        if($id <= 0){
            showMessage(Language::get('param_error'));
        }
        $model = Model();
        $ap_info = $model->table('sns_albumpic')->where(['ap_id' => $id])->find();
        if(!empty($ap_info)){
            @unlink(BASE_UPLOAD_PATH . DS . ATTACH_MALBUM . DS . $ap_info['member_id'] . DS . $ap_info['ap_cover']);
            @unlink(BASE_UPLOAD_PATH . DS . ATTACH_MALBUM . DS . $ap_info['member_id'] . DS . str_ireplace('.', '_240.', $ap_info['ap_cover']));
            @unlink(BASE_UPLOAD_PATH . DS . ATTACH_MALBUM . DS . $ap_info['member_id'] . DS . str_ireplace('.', '_1280.', $ap_info['ap_cover']));
            $model->table('sns_albumpic')->where(['ap_id' => $id])->delete();
        }
        showMessage(Language::get('nc_common_del_succ'));
    }
}
