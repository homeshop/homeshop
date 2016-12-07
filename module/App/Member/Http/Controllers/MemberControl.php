<?php namespace App\Member\Http\Controllers;

/**
 * 会员中心——账户概览
 */
class  MemberControl extends BaseMemberControl {
    
    /**
     * 我的商城
     */
    public function homeOp() {
        $model_consume = Model('consume');
        $consume_list = $model_consume->getConsumeList(['member_id' => $_SESSION['member_id']], '*', 0, 10);
        Tpl::output('consume_list', $consume_list);
        Tpl::showpage('member_home');
    }
}
