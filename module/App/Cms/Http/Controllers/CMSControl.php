<?php namespace App\Cms\Http\Controllers;

/********************************** 前台control父类 **********************************************/
class  CMSControl {
    
    //文章状态草稿箱
    const ARTICLE_STATE_DRAFT = 1;
    //文章状态待审核
    const ARTICLE_STATE_VERIFY = 2;
    //文章状态已发布
    const ARTICLE_STATE_PUBLISHED = 3;
    //文章状态回收站
    const ARTICLE_STATE_RECYCLE = 4;
    //推荐
    const COMMEND_FLAG_TRUE = 1;
    //文章评论类型
    const ARTICLE = 1;
    const PICTURE = 2;
    //用户中心文章列表页
    const CMS_MEMBER_ARTICLE_URL = 'index.php?act=member_article&op=article_list';
    const CMS_MEMBER_PICTURE_URL = 'index.php?act=member_picture&op=picture_list';
    protected $publisher_name  = '';
    protected $publisher_id    = 0;
    protected $publisher_type  = 0;
    protected $attachment_path = '';
    protected $publish_state;
    
    /**
     * 构造函数
     */
    public function __construct() {
        /**
         * cms开关判断
         */
        if(intval(C('cms_isuse')) !== 1){
            header('location: ' . SHOP_SITE_URL);
            die;
        }
        /**
         * 读取通用、布局的语言包
         */
        Language::read('common');
        Language::read('cms');
        /**
         * 设置布局文件内容
         */
        Tpl::setLayout('cms_layout');
        /**
         * 转码
         */
        if($_GET['column'] && strtoupper(CHARSET) == 'GBK'){
            $_GET = Language::getGBK($_GET);
        }
        /**
         * 获取导航
         */
        Tpl::output('nav_list', rkcache('nav', true));
        /**
         * 系统状态检查
         */
        if(!C('site_status')){
            halt(C('closed_reason'));
        }
        /**
         * seo
         */
        Tpl::output('html_title', C('cms_seo_title') . '-' . C('site_name') . '');
        Tpl::output('seo_keywords', C('cms_seo_keywords'));
        Tpl::output('seo_description', C('cms_seo_description'));
        /**
         * 判断是不是管理员
         */
        if(!empty($_SESSION['member_name'])){
            $this->publisher_name = $_SESSION['member_name'];
            $this->publisher_id = $_SESSION['member_id'];
            //早期有后台管理员直接发布功能，由于权限判断过于复杂现在已经取消，目前为固定值1
            $this->publisher_type = 1;
            $this->publisher_avatar = $_SESSION['avatar'];
            $this->attachment_path = $_SESSION['member_id'];
        }
        //发布状态，管理员直接发布，投稿如果后台开启审核未待审核状态
        if(intval(C('cms_submit_verify_flag')) === 1){
            $this->publish_state = self::ARTICLE_STATE_VERIFY;
        } else {
            $this->publish_state = self::ARTICLE_STATE_PUBLISHED;
        }
    }
    
    protected function check_login() {
        if(!isset($_SESSION['is_login'])){
            $ref_url = CMS_SITE_URL . request_uri();
            @header("location: " . urlLogin('login', 'index', ['ref_url' => getRefUrl()]));
            die;
        }
    }
    
    /**
     * 获取文章状态列表
     */
    protected function get_article_state_list() {
        $array = [];
        $array[ self::ARTICLE_STATE_DRAFT ] = Language::get('cms_text_draft');
        $array[ self::ARTICLE_STATE_VERIFY ] = Language::get('cms_text_verify');
        $array[ self::ARTICLE_STATE_PUBLISHED ] = Language::get('cms_text_published');
        $array[ self::ARTICLE_STATE_RECYCLE ] = Language::get('cms_text_recycle');
        return $array;
    }
    
    /**
     * 获取文章相关文章
     */
    protected function get_article_link_list($article_link) {
        $article_link_list = [];
        if(!empty($article_link)){
            $model_article = Model('cms_article');
            $condition = [];
            $condition['article_id'] = ['in', $article_link];
            $condition['article_state'] = self::ARTICLE_STATE_PUBLISHED;
            $article_link_list = $model_article->getList($condition, null, 'article_id desc');
        }
        return $article_link_list;
    }
    
    /**
     * 返回json状态
     */
    protected function return_json($message, $result = 'true') {
        $data = [];
        $data['result'] = $result;
        $data['message'] = $message;
        self::echo_json($data);
    }
    
    protected function echo_json($data) {
        if(strtoupper(CHARSET) == 'GBK'){
            $data = Language::getUTF8($data);//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
        }
        echo json_encode($data);
        die;
    }
    
    /**
     * 获取主域名
     */
    protected function get_url_domain($url) {
        $url_parse_array = parse_url($url);
        $host = $url_parse_array['host'];
        $host_names = explode(".", $host);
        $bottom_host_name = $host_names[ count($host_names) - 2 ] . "." . $host_names[ count($host_names) - 1 ];
        return $bottom_host_name;
    }
    
    //获得分享列表
    protected function get_share_app_list() {
        $app_shop = [];
        $app_array = [];
        if(C('share_isuse') == 1 && isset($_SESSION['member_id'])){
            //站外分享接口
            $model = Model('sns_binding');
            $app_array = $model->getUsableApp($_SESSION['member_id']);
        }
        Tpl::output('app_arr', $app_array);
    }
    
    protected function share_app_publish($publish_info = []) {
        $param = [];
        $param['comment'] = "'" . $_SESSION['member_name'] . "'" . Language::get('cms_text_zai') . C('cms_seo_title') . Language::get('share_article');
        $param['title'] = "'" . $_SESSION['member_name'] . "'" . Language::get('cms_text_zai') . C('cms_seo_title') . Language::get('share_article');
        $param['url'] = $publish_info['url'];
        $param['title'] = $publish_info['share_title'];
        $param['image'] = $publish_info['share_image'];
        $param['content'] = self::get_share_app_content($param);
        $param['images'] = '';
        //分享应用
        $app_items = [];
        foreach($_POST['share_app_items'] as $val){
            if($val != ''){
                $app_items[ $val ] = true;
            }
        }
        if(C('share_isuse') == 1 && !empty($app_items)){
            $model = Model('sns_binding');
            //查询该用户的绑定信息
            $bind_list = $model->getUsableApp($_SESSION['member_id']);
            //商城
            if(isset($app_items['shop'])){
                $model_member = Model('member');
                $member_info = $model_member->getMemberInfoByID($_SESSION['member_id']);
                $tracelog_model = Model('sns_tracelog');
                $insert_arr = [];
                $insert_arr['trace_originalid'] = '0';
                $insert_arr['trace_originalmemberid'] = '0';
                $insert_arr['trace_memberid'] = $_SESSION['member_id'];
                $insert_arr['trace_membername'] = $_SESSION['member_name'];
                $insert_arr['trace_memberavatar'] = $member_info['member_avatar'];
                $insert_arr['trace_title'] = $publish_info['commend_message'];
                $insert_arr['trace_content'] = $param['content'];
                $insert_arr['trace_addtime'] = time();
                $insert_arr['trace_state'] = '0';
                $insert_arr['trace_privacy'] = 0;
                $insert_arr['trace_commentcount'] = 0;
                $insert_arr['trace_copycount'] = 0;
                $insert_arr['trace_from'] = '4';
                $result = $tracelog_model->tracelogAdd($insert_arr);
            }
            //腾讯微博
            if(isset($app_items['qqweibo']) && $bind_list['qqweibo']['isbind'] == true){
                $model->addQQWeiboPic($bind_list['qqweibo'], $param);
            }
            //新浪微博
            if(isset($app_items['sinaweibo']) && $bind_list['sinaweibo']['isbind'] == true){
                $model->addSinaWeiboUpload($bind_list['sinaweibo'], $param);
            }
        }
    }
    
    //CMSsns内容结构
    protected function get_share_app_content($info) {
        $content_str = "
            <div class='fd-media'>
            <div class='goodsimg'><a target=\"_blank\" href=\"{$info['url']}\"><img src=\"" . $info['image'] . "\" onload=\"javascript:DrawImage(this,120,120);\"></a></div>
            <div class='goodsinfo'>
            <dl>
            <dt><a target=\"_blank\" href=\"{$info['url']}\">{$info['title']}</a></dt>
            <dd>{$info['comment']}<a target=\"_blank\" href=\"{$info['url']}\">" . Language::get('nc_common_goto') . "</a></dd>
            </dl>
            </div>
            </div>
            ";
        return $content_str;
    }
}




