<?php namespace App\Mobile\Http\Controllers\Member;

use App\Mobile\Http\Controllers\MobileMemberControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 我的代金券
 */
class PointsControl extends MobileMemberControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 积分日志列表
     */
    public function pointslogOp() {
        $where = [];
        $where['pl_memberid'] = $this->member_info['member_id'];
        //查询积分日志列表
        $points_model = Model('points');
        $log_list = $points_model->getPointsLogList($where, '*', 0, $this->page);
        $page_count = $points_model->gettotalpage();
        output_data(['log_list' => $log_list], mobile_page($page_count));
    }
}
