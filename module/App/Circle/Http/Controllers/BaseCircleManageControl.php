<?php namespace App\Circle\Http\Controllers;




class  BaseCircleManageControl extends BaseCircleControl {
    
    protected $circle_info = [];   // 圈子详细信息
    protected $t_id        = 0;        // 话题id
    protected $theme_info  = [];    // 话题详细信息
    protected $identity    = 0;    // 身份 0游客 1圈主 2管理 3成员
    protected $cm_info     = [];   // 会员信息
    
    public function __construct() {
        parent::__construct();
        $this->c_id = intval($_GET['c_id']);
        if($this->c_id <= 0){
            @header("location: " . CIRCLE_SITE_URL);
        }
        Tpl::output('c_id', $this->c_id);
    }
    
    /**
     * 圈子信息
     */
    protected function circleInfo() {
        // 圈子信息
        $this->circle_info = Model()->table('circle')->where(['circle_id' => $this->c_id])->find();
        if(empty($this->circle_info)){
            @header("location: " . CIRCLE_SITE_URL);
        }
        Tpl::output('circle_info', $this->circle_info);
    }
    
    /**
     * 会员信息
     */
    protected function circleMemberInfo() {
        // 会员信息
        $this->cm_info = Model()->table('circle_member')->where([
            'circle_id' => $this->c_id,
            'member_id' => $_SESSION['member_id']
        ])->find();
        if(!empty($this->cm_info)){
            $this->identity = $this->cm_info['is_identity'];
            Tpl::output('cm_info', $this->cm_info);
        }
        if(in_array($this->identity, [0, 3])){
            @header("location: " . CIRCLE_SITE_URL);
        }
        Tpl::output('identity', $this->identity);
    }
    
    /**
     * 去除圈主
     */
    protected function removeCreator($array) {
        return array_diff($array, [$this->cm_info['member_id']]);
    }
    
    /**
     * 去除圈主和管理
     */
    protected function removeManager($array) {
        $where = [];
        $where['is_identity'] = ['in', [1, 2]];
        $where['circle_id'] = $this->c_id;
        $cm_info = Model()->table('circle_member')->where($where)->select();
        if(empty($cm_info)){
            return $array;
        }
        foreach($cm_info as $val){
            $array = array_diff($array, [$val['member_id']]);
        }
        return $array;
    }
    
    /**
     * 身份验证
     */
    protected function checkIdentity($type) {        // c圈主 m管理 cm圈主和管理
        $this->cm_info = Model()->table('circle_member')->where([
            'circle_id' => $this->c_id,
            'member_id' => $_SESSION['member_id']
        ])->find();
        $identity = intval($this->cm_info['is_identity']);
        $sign = false;
        switch($type) {
            case 'c':
                if($identity != 1){
                    $sign = true;
                }
                break;
            case 'm':
                if($identity != 2){
                    $sign = true;
                }
                break;
            case 'cm':
                if($identity != 1 && $identity != 2){
                    $sign = true;
                }
                break;
            default:
                $sign = true;
                break;
        }
        if($this->super){
            $sign = false;
        }
        if($sign){
            return L('circle_permission_denied');
        }
    }
    
    /**
     * 会员加入的圈子
     */
    protected function memberJoinCircle() {
        // 所属圈子信息
        $circle_array = Model()->table('circle,circle_member')->field('circle.*,circle_member.is_identity')->join('inner')->on('circle.circle_id=circle_member.circle_id')->where(['circle_member.member_id' => $_SESSION['member_id']])->select();
        Tpl::output('circle_array', $circle_array);
    }
    
    /**
     * Top Navigation
     */
    protected function sidebar_menu($sign, $child_sign = '') {
        $menu = [
            'index' => [
                'menu_name' => L('circle_basic_setting'),
                'menu_url' => 'index.php?act=manage&c_id=' . $this->c_id
            ],
            'member' => [
                'menu_name' => L('circle_member_manage'),
                'menu_url' => 'index.php?act=manage&op=member_manage&c_id=' . $this->c_id
            ],
            'applying' => [
                'menu_name' => L('circle_wait_apply'),
                'menu_url' => 'index.php?act=manage&op=applying&c_id=' . $this->c_id
            ],
            'level' => [
                'menu_name' => L('circle_member_level'),
                'menu_url' => 'index.php?act=manage_level&op=level&c_id=' . $this->c_id
            ],
            'class' => [
                'menu_name' => L('circle_tclass'),
                'menu_url' => 'index.php?act=manage&op=class&c_id=' . $this->c_id
            ],
            'inform' => [
                'menu_name' => L('circle_inform'),
                'menu_url' => 'index.php?act=manage_inform&op=inform&c_id=' . $this->c_id,
                'menu_child' => [
                    'untreated' => [
                        'name' => L('circle_inform_untreated'),
                        'url' => 'index.php?act=manage_inform&op=inform&c_id=' . $this->c_id
                    ],
                    'treated' => [
                        'name' => L('circle_inform_treated'),
                        'url' => 'index.php?act=manage_inform&op=inform&type=treated&c_id=' . $this->c_id
                    ]
                ],
            ],
            'managerapply' => [
                'menu_name' => L('circle_mapply'),
                'menu_url' => 'index.php?act=manage_mapply&c_id=' . $this->c_id
            ],
            'friendship' => [
                'menu_name' => L('fcircle'),
                'menu_url' => 'index.php?act=manage&op=friendship&c_id=' . $this->c_id
            ]
        ];
        if($this->identity == 2){
            unset($menu['index']);
            unset($menu['member']);
            unset($menu['level']);
            unset($menu['class']);
            unset($menu['friendship']);
            unset($menu['inform']['menu_child']['untreated']);
            unset($menu['managerapply']);
        }
        Tpl::output('sidebar_menu', $menu);
        Tpl::output('sidebar_sign', $sign);
        Tpl::output('sidebar_child_sign', $child_sign);
    }
}