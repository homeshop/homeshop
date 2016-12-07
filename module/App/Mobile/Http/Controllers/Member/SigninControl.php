<?php namespace App\Mobile\Http\Controllers\Member;

use App\Mobile\Http\Controllers\MobileMemberControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 签到
 */
class SigninControl extends MobileMemberControl {
    
    public function __construct() {
        parent::__construct();
        if(!C('signin_isuse')){
            output_error('签到失败', ['state' => 'isclose']);
        }
    }
    
    /**
     * 签到
     */
    public function signin_addOp() {
        //查询今天是否已签到
        $model_signin = Model('signin');
        $result = $model_signin->isAbleSignin($this->member_info['member_id']);
        if(!$result['done']){
            output_error($result['msg']);
        }
        try {
            $points = C('points_signin');
            //增加签到记录
            $result = Model('signin')->addSignin([
                'points' => $points,
                'member_id' => $this->member_info['member_id'],
                'member_name' => $this->member_info['member_name']
            ]);
            if(!$result){
                throw Exception('签到失败');
            }
            //增加积分
            $result = Model('points')->savePointsLog('signin', [
                'pl_memberid' => $this->member_info['member_id'],
                'pl_membername' => $this->member_info['member_name'],
                'pl_points' => $points
            ]);
            if(!$result){
                throw Exception('签到失败');
            }
            output_data(['point' => $this->member_info['member_points'] + $points]);
        } catch(Exception $e) {
            output_error($e->getMessage());
        }
    }
    
    /**
     * 获取是否能签到
     */
    public function checksigninOp() {
        $result = Model('signin')->isAbleSignin($this->member_info['member_id']);
        if(!$result['done']){
            output_error($result['msg']);
        }
        output_data(['points_signin' => C('points_signin')]);
    }
    
    /**
     * 获得签到日志
     */
    public function signin_listOp() {
        $model_signin = Model('signin');
        $where = [];
        $where['sl_memberid'] = $this->member_info['member_id'];
        $signin_list = $model_signin->getSigninList($where, '*', 0, $this->page, 'sl_id desc');
        $page_count = $model_signin->gettotalpage();
        if($signin_list){
            foreach($signin_list as $k => $v){
                $v['sl_addtime_text'] = @date('Y-m-d H:i:s', $v['sl_addtime']);
                $signin_list[ $k ] = $v;
            }
        }
        output_data(['signin_list' => $signin_list], mobile_page($page_count));
    }
}