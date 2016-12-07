<?php namespace App\Circle\Http\Controllers;




class  BaseCirclePersonalControl extends BaseCircleControl {
    
    protected $m_id = 0;   // memeber ID
    
    public function __construct() {
        parent::__construct();
        if(!$_SESSION['is_login']){
            @header("location: " . CIRCLE_SITE_URL);
        }
        $this->m_id = $_SESSION['member_id'];
        // member information
        $this->circleMemberInfo();
    }
    
    /**
     * member information
     */
    protected function circleMemberInfo() {
        // member information list
        $circlemember_list = Model()->table('circle_member')->where(['member_id' => $this->m_id])->select();
        $data = [];
        $data['cm_thcount'] = 0;
        $data['cm_comcount'] = 0;
        $data['member_id'] = $_SESSION['member_id'];
        $data['member_name'] = $_SESSION['member_name'];
        if(!empty($circlemember_list)){
            foreach($circlemember_list as $val){
                $data['cm_thcount'] += $val['cm_thcount'];
                $data['cm_comcount'] += $val['cm_comcount'];
            }
        }
        Tpl::output('cm_info', $data);
    }
}