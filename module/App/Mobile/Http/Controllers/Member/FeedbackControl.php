<?php namespace App\Mobile\Http\Controllers\Member;

use App\Mobile\Http\Controllers\MobileMemberControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 我的反馈
 */
class FeedbackControl extends MobileMemberControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 添加反馈
     */
    public function feedback_addOp() {
        $model_mb_feedback = Model('mb_feedback');
        $param = [];
        $param['content'] = $_POST['feedback'];
        $param['type'] = $this->member_info['client_type'];
        $param['ftime'] = TIMESTAMP;
        $param['member_id'] = $this->member_info['member_id'];
        $param['member_name'] = $this->member_info['member_name'];
        $result = $model_mb_feedback->addMbFeedback($param);
        if($result){
            output_data('1');
        } else {
            output_error('保存失败');
        }
    }
}
