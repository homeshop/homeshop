<?php namespace App\Circle\Http\Controllers\Manage;

use App\Circle\Http\Controllers\BaseCircleManageControl;
use App\Circle\Http\Controllers\Language;
use App\Circle\Http\Controllers\Tpl;


/**
 * 圈子首页
 */
class  MapplyControl extends BaseCircleManageControl {
    
    public function __construct() {
        parent::__construct();
        Language::read('circle');
        $this->circleSEO();
    }
    
    /**
     * Apply to be a management
     */
    public function indexOp() {
        // Circle information
        $this->circleInfo();
        // Membership information
        $this->circleMemberInfo();
        // Members to join the circle list
        $this->memberJoinCircle();
        $model = Model();
        $mapply_list = $model->table('circle_mapply')->where(['circle_id' => $this->c_id])->page(10)->order('mapply_id desc')->select();
        if(!empty($mapply_list)){
            $memberid_array = [];
            $mapply_array = [];
            foreach($mapply_list as $val){
                $memberid_array[] = $val['member_id'];
                $mapply_array[ $val['member_id'] ] = $val;
            }
            $member_list = $model->table('circle_member')->field('cm_level,cm_levelname,member_id,member_name')->where([
                'circle_id' => $this->c_id,
                'member_id' => ['in', $memberid_array]
            ])->select();
            $mapply_list = [];
            if(!empty($member_list)){
                foreach($member_list as $val){
                    $mapply_list[ $val['member_id'] ] = array_merge($val, $mapply_array[ $val['member_id'] ]);
                }
            }
            Tpl::output('mapply_list', $mapply_list);
            Tpl::output('show_page', $model->showpage(2));
        }
        $this->sidebar_menu('managerapply');
        Tpl::showpage('group_manage_mapply');
    }
    
    /**
     * Management application approved
     */
    public function mapply_passOp() {
        // Verify the identity
        $rs = $this->checkIdentity('c');
        if(!empty($rs)){
            showDialog($rs);
        }
        $cmid_array = explode(',', $_GET['cm_id']);
        foreach($cmid_array as $key => $val){
            if(!is_numeric($val)){
                unset($cmid_array[ $key ]);
            }
        }
        if(empty($cmid_array)){
            showDialog(L('wrong_argument'));
        }
        $model = Model();
        // Calculate number allows you to add administrator
        $manage_count = $model->table('circle_member')->where([
            'circle_id' => $this->c_id,
            'is_identity' => 2
        ])->count();
        $i = intval(C('circle_managesum')) - intval($manage_count);
        $cmid_array = array_slice($cmid_array, 0, $i);
        // conditions
        $where = [];
        $where['member_id'] = ['in', $cmid_array];
        $where['circle_id'] = $this->c_id;
        // Update the data
        $update = [];
        $update['is_identity'] = 2;
        $model->table('circle_member')->where($where)->update($update);
        // Delete already through application information
        $model->table('circle_mapply')->where($where)->delete();
        // Update the application for membership
        $count = $model->table('circle_mapply')->where(['circle_id' => $this->c_id])->count();
        $model->table('circle')->where(['circle_id' => $this->c_id])->update(['new_mapplycount' => $count]);
        showDialog(L('nc_common_op_succ'), 'reload', 'succ');
    }
    
    /**
     * Management application to delete
     */
    public function delOp() {
        // Verify the identity
        $rs = $this->checkIdentity('c');
        if(!empty($rs)){
            showDialog($rs);
        }
        $cmid_array = explode(',', $_GET['cm_id']);
        foreach($cmid_array as $key => $val){
            if(!is_numeric($val)){
                unset($cmid_array[ $key ]);
            }
        }
        if(empty($cmid_array)){
            showDialog(L('wrong_argument'));
        }
        $model = Model();
        // conditions
        $where = [];
        $where['circle_id'] = $this->c_id;
        $where['member_id'] = ['in', $cmid_array];
        // Delete the information
        $model->table('circle_mapply')->where($where)->delete();
        // Update the application for membership
        $count = $model->table('circle_mapply')->where(['circle_id' => $this->c_id])->count();
        $model->table('circle')->where(['circle_id' => $this->c_id])->update(['new_mapplycount' => $count]);
        showDialog(L('nc_common_op_succ'), 'reload', 'succ');
    }
}
