<?php namespace App\Circle\Http\Controllers\Member;

use App\Circle\Http\Controllers\BaseCircleControl;
use App\Circle\Http\Controllers\Language;
use App\Circle\Http\Controllers\Tpl;


/**
 * The AJAX call member information
 */
class  CardControl extends BaseCircleControl {
    
    public function mcard_infoOp() {
        $uid = intval($_GET['uid']);
        $member_list = Model()->table('circle_member')->field('member_id,circle_id,circle_name,cm_level,cm_exp')->where([
            'member_id' => $uid,
            'cm_state' => 1
        ])->select();
        if(empty($member_list)){
            echo 'false';
            exit;
        }
        echo json_encode($member_list);
        exit;
    }
}
