<?php namespace App\Circle\Http\Controllers;



/********************************** 前台control父类 **********************************************/
class  BaseCircleControl  {
    
    protected $identity   = 0;    // 身份 0游客 1圈主 2管理 3成员 4申请中 5申请失败 6禁言
    protected $c_id       = 0;        // 圈子id
    protected $cm_info    = [];   // Members of the information
    protected $m_readperm = 0;  // Members read permissions
    protected $super      = 0;
    
    /**
     * 构造函数
     */
    public function __construct() {
        /**
         * 验证圈子是否开启
         */
        if(C('circle_isuse') != '1'){
            @header('location: ' . SHOP_SITE_URL);
            die;
        }
        /**
         * 读取通用、布局的语言包
         */
        Language::read('common');
        /**
         * 设置布局文件内容
         */
        Tpl::setLayout('circle_layout');
        /**
         * 查询是否是超管
         */
        $this->checkSuper();
        /**
         * 获取导航
         */
        Tpl::output('nav_list', rkcache('nav', true));
    }
    
    private function checkSuper() {
        if($_SESSION['is_login']){
            $super = Model('circle_member')->getSuperInfo(['member_id' => $_SESSION['member_id']]);
            $this->super = empty($super) ? 0 : 1;
        }
        Tpl::output('super', $this->super);
    }
    
    /**
     * 圈子信息
     */
    protected function circleInfo() {
        $this->circle_info = Model()->table('circle')->where(['circle_id' => $this->c_id])->find();
        if(empty($this->circle_info)){
            showMessage(L('circle_group_not_exists'), '', '', 'error');
        }
        Tpl::output('circle_info', $this->circle_info);
    }
    
    /**
     * 圈主和管理信息
     */
    protected function manageList() {
        $prefix = 'circle_managelist';
        $info = rcache($this->c_id, $prefix);
        if(empty($info)){
            $manager_list = Model()->table('circle_member')->where([
                'circle_id' => $this->c_id,
                'is_identity' => ['in', [1, 2]]
            ])->select();
            $manager_list = array_under_reset($manager_list, 'is_identity', 2);
            $manager_list[2] = array_under_reset($manager_list[2], 'member_id', 1);
            $info['info'] = serialize($manager_list);
            wcache($this->c_id, $info, $prefix, 60);
        }
        $manager_list = unserialize($info['info']);
        Tpl::output('creator', $manager_list[1][0]);
        Tpl::output('manager_list', $manager_list[2]);
    }
    
    /**
     * 会员信息
     */
    protected function memberInfo() {
        if($_SESSION['is_login']){
            $this->cm_info = Model()->table('circle_member')->where([
                'circle_id' => $this->c_id,
                'member_id' => $_SESSION['member_id']
            ])->find();
            if(!empty($this->cm_info)){
                switch(intval($this->cm_info['cm_state'])) {
                    case 1:
                        $this->identity = intval($this->cm_info['is_identity']);
                        break;
                    case 0:
                        $this->identity = 4;
                        break;
                    case 2:
                        $this->identity = 5;
                        break;
                }
                // 禁言
                if($this->cm_info['is_allowspeak'] == 0){
                    $this->identity = 6;
                }
            }
            Tpl::output('cm_info', $this->cm_info);
        }
        Tpl::output('identity', $this->identity);
    }
    
    /**
     * sidebar相关信息
     */
    protected function sidebar() {
        $prefix = 'circle_sidebar';
        $data = rcache($this->c_id, $prefix);
        if(empty($data)){
            // 圈子所属分类
            $data['class_info'] = Model()->table('circle_class')->where(['class_id' => $this->circle_info['class_id']])->find();
            // 明星圈友
            $data['star_member'] = Model()->table('circle_member')->where([
                'cm_state' => 1,
                'circle_id' => $this->c_id,
                'is_star' => 1
            ])->order('rand()')->limit(5)->select();
            // 最新加入
            $data['newest_member'] = Model()->table('circle_member')->where([
                'cm_state' => 1,
                'circle_id' => $this->c_id
            ])->order('cm_jointime desc')->limit(5)->select();
            // 友情圈子
            $data['friendship_list'] = Model()->table('circle_fs')->where([
                'circle_id' => $this->c_id,
                'friendship_status' => 1
            ])->order('friendship_sort asc')->select();
        }
        Tpl::output('class_info', $data['class_info']);
        Tpl::output('star_member', $data['star_member']);
        Tpl::output('newest_member', $data['newest_member']);
        Tpl::output('friendship_list', $data['friendship_list']);
    }
    
    /**
     * 最新话题/热门话题/人气回复
     */
    protected function themeTop() {
        $prefix = 'circle_themetop';
        $info = rcache('circle', $prefix);
        if(empty($info)){
            $model = Model();
            // 最新话题
            $data['new_themelist'] = $model->table('circle_theme')->where(['is_closed' => 0])->order('theme_id desc')->limit(10)->select();
            // 热门话题
            $data['hot_themelist'] = $model->table('circle_theme')->where(['is_closed' => 0])->order('theme_browsecount desc')->limit(10)->select();
            // 人气回复
            $data['reply_themelist'] = $model->table('circle_theme')->where(['is_closed' => 0])->order('theme_commentcount desc')->limit(10)->select();
            $info['info'] = serialize($data);
            wcache('circle', $info, $prefix, 60);
        }
        $data = unserialize($info['info']);
        Tpl::output('new_themelist', $data['new_themelist']);
        Tpl::output('hot_themelist', $data['hot_themelist']);
        Tpl::output('reply_themelist', $data['reply_themelist']);
    }
    
    /**
     * SEO
     */
    protected function circleSEO($title = '') {
        Tpl::output('html_title', $title . ' ' . C('circle_seotitle'));
        Tpl::output('seo_keywords', C('circle_seokeywords'));
        Tpl::output('seo_description', C('circle_seodescription'));
    }
    
    /**
     * Read permissions
     */
    protected function readPermissions($cm_info) {
        $data = rkcache('circle_level', true);
        $rs = [];
        $rs[0] = 0;
        $rs[0] = L('circle_no_limit');
        foreach($data as $v){
            $rs[ $v['mld_id'] ] = $v['mld_name'];
        }
        switch($cm_info['is_identity']) {
            case 1:
            case 2:
                $rs['255'] = L('circle_administrator');
                $this->m_readperm = 255;
                return $rs;
                break;
            case 3:
                $rs = array_slice($rs, 0, intval($cm_info['cm_level']) + 1, true);
                $this->m_readperm = $cm_info['cm_level'];
                return $rs;
                break;
        }
    }
    
    /**
     * breadcrumb navigation
     */
    protected function breadcrumd($param = '') {
        $crumd = [
            0 => [
                'link' => CIRCLE_SITE_URL,
                'title' => L('nc_index')
            ],
            1 => [
                'link' => CIRCLE_SITE_URL . '/index.php?act=group&c_id=' . $this->c_id,
                'title' => $this->circle_info['circle_name']
            ],
        ];
        if(!empty($this->theme_info)){
            $crumd[2] = [
                'link' => CIRCLE_SITE_URL . '/index.php?act=theme&op=theme_detail&c_id=' . $this->c_id . '&t_id=' . $this->t_id,
                'title' => $this->theme_info['theme_name']
            ];
        }
        if(empty($param)){
            unset($crumd[ (count($crumd) - 1) ]['link']);
        } else {
            $crumd[]['title'] = $param;
        }
        Tpl::output('breadcrumd', $crumd);
    }
}



