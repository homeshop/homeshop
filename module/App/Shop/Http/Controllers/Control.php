<?php  namespace App\Shop\Http\Controllers;

/**
 * 前台control父类,店铺control父类,会员control父类
 */
class Control {
    
    /**
     * 检查短消息数量
     */
    protected function checkMessage() {
        if($_SESSION['member_id'] == ''){
            return;
        }
        //判断cookie是否存在
        $cookie_name = 'msgnewnum'.$_SESSION['member_id'];
        if(cookie($cookie_name) != null){
            $countnum = intval(cookie($cookie_name));
        } else {
            $message_model = Model('message');
            $countnum = $message_model->countNewMessage($_SESSION['member_id']);
            setNcCookie($cookie_name, "$countnum", 2 * 3600);//保存2小时
        }
        Tpl::output('message_num', $countnum);
    }
    
    /**
     *  输出头部的公用信息
     */
    protected function showLayout() {
        $this->checkMessage();//短消息检查
        $this->article();//文章输出
        
        $this->showCartCount();
        
        //热门搜索
        Tpl::output('hot_search', @explode(',', C('hot_search')));
        if(C('rec_search') != ''){
            $rec_search_list = @unserialize(C('rec_search'));
        }
        Tpl::output('rec_search_list', is_array($rec_search_list) ? $rec_search_list : []);
        
        //历史搜索
        if(cookie('his_sh') != ''){
            $his_search_list = explode('~', cookie('his_sh'));
        }
        Tpl::output('his_search_list', is_array($his_search_list) ? $his_search_list : []);
        
        $model_class = Model('goods_class');
        $goods_class = $model_class->get_all_category();
        Tpl::output('show_goods_class', $goods_class);//商品分类
        
        //获取导航
        Tpl::output('nav_list', rkcache('nav', true));
        //查询保障服务项目
        Tpl::output('contract_list', Model('contract')->getContractItemByCache());
    }
    
    /**
     * 显示购物车数量
     */
    protected function showCartCount() {
        if(cookie('cart_goods_num') != null){
            $cart_num = intval(cookie('cart_goods_num'));
        } else {
            //已登录状态，存入数据库,未登录时，优先存入缓存，否则存入COOKIE
            if($_SESSION['member_id']){
                $save_type = 'db';
            } else {
                $save_type = 'cookie';
            }
            $cart_num = Model('cart')->getCartNum($save_type, ['buyer_id' => $_SESSION['member_id']]);//查询购物车商品种类
        }
        Tpl::output('cart_goods_num', $cart_num);
    }
    
    /**
     * 输出会员等级
     * @param bool $is_return 是否返回会员信息，返回为true，输出会员信息为false
     */
    protected function getMemberAndGradeInfo($is_return = false) {
        $member_info = [];
        //会员详情及会员级别处理
        if($_SESSION['member_id']){
            $model_member = Model('member');
            $member_info = $model_member->getMemberInfoByID($_SESSION['member_id']);
            if($member_info){
                $member_gradeinfo = $model_member->getOneMemberGrade(intval($member_info['member_exppoints']));
                $member_info = array_merge($member_info, $member_gradeinfo);
                $member_info['security_level'] = $model_member->getMemberSecurityLevel($member_info);
            }
        }
        if($is_return == true){//返回会员信息
            return $member_info;
        } else {//输出会员信息
            Tpl::output('member_info', $member_info);
        }
    }
    
    /**
     * 验证会员是否登录
     */
    protected function checkLogin() {
        if($_SESSION['is_login'] !== '1'){
            if(trim($_GET['op']) == 'favoritegoods' || trim($_GET['op']) == 'favoritestore'){
                $lang = Language::getLangContent('UTF-8');
                echo json_encode(['done' => false, 'msg' => $lang['no_login']]);
                die;
            }
            $ref_url = request_uri();
            if($_GET['inajax']){
                showDialog('', '', 'js', "login_dialog();", 200);
            } else {
                @header("location: ".urlLogin('login', 'index', ['ref_url' => $ref_url]));
            }
            exit;
        }
    }
    
    //文章输出
    protected function article() {
        
        if(C('cache_open')){
            if($article = rkcache("index/article")){
                Tpl::output('show_article', $article['show_article']);
                Tpl::output('article_list', $article['article_list']);
                return;
            }
        } else {
            if(file_exists(BASE_DATA_PATH.'/cache/index/article.php')){
                include(BASE_DATA_PATH.'/cache/index/article.php');
                Tpl::output('show_article', $show_article);
                Tpl::output('article_list', $article_list);
                return;
            }
        }
        
        $model_article_class = Model('article_class');
        $model_article = Model('article');
        $show_article = [];//商城公告
        $article_list = [];//下方文章
        $notice_class = ['notice'];
        $code_array = ['member', 'payment', 'sold', 'service'];
        $notice_limit = 4;
        $faq_limit = 5;
        
        $class_condition = [];
        $class_condition['home_index'] = 'home_index';
        $class_condition['order'] = 'ac_sort asc';
        $article_class = $model_article_class->getClassList($class_condition);
        $class_list = [];
        if(!empty($article_class) && is_array($article_class)){
            foreach($article_class as $key => $val){
                $ac_code = $val['ac_code'];
                $ac_id = $val['ac_id'];
                $val['list'] = [];//文章
                $class_list[$ac_id] = $val;
            }
        }
        
        $condition = [];
        $condition['article_show'] = '1';
        $condition['field'] = 'article.article_id,article.ac_id,article.article_url,article_class.ac_code,article.article_position,article.article_title,article.article_time,article_class.ac_name,article_class.ac_parent_id';
        $condition['order'] = 'article_sort asc,article_time desc';
        $condition['limit'] = '300';
        $article_array = $model_article->getJoinList($condition);
        if(!empty($article_array) && is_array($article_array)){
            foreach($article_array as $key => $val){
                if($val['ac_code'] == 'notice' && !in_array($val['article_position'], [
                        ARTICLE_POSIT_SHOP,
                        ARTICLE_POSIT_ALL
                    ])
                ){
                    continue;
                }
                $ac_id = $val['ac_id'];
                $ac_parent_id = $val['ac_parent_id'];
                if($ac_parent_id == 0){//顶级分类
                    $class_list[$ac_id]['list'][] = $val;
                } else {
                    $class_list[$ac_parent_id]['list'][] = $val;
                }
            }
        }
        if(!empty($class_list) && is_array($class_list)){
            foreach($class_list as $key => $val){
                $ac_code = $val['ac_code'];
                if(in_array($ac_code, $notice_class)){
                    $list = $val['list'];
                    array_splice($list, $notice_limit);
                    $val['list'] = $list;
                    $show_article[$ac_code] = $val;
                }
                if(in_array($ac_code, $code_array)){
                    $list = $val['list'];
                    $val['class']['ac_name'] = $val['ac_name'];
                    array_splice($list, $faq_limit);
                    $val['list'] = $list;
                    $article_list[] = $val;
                }
            }
        }
        if(C('cache_open')){
            wkcache('index/article', ['show_article' => $show_article, 'article_list' => $article_list,]);
        } else {
            $string = "<?php\n\$show_article=".var_export($show_article, true).";\n";
            $string .= "\$article_list=".var_export($article_list, true).";\n?>";
            file_put_contents(BASE_DATA_PATH.'/cache/index/article.php', ($string));
        }
        
        Tpl::output('show_article', $show_article);
        Tpl::output('article_list', $article_list);
    }
    
    /**
     * 自动登录
     */
    protected function auto_login() {
        $data = cookie('auto_login');
        if(empty($data)){
            return false;
        }
        $model_member = Model('member');
        if($_SESSION['is_login']){
            $model_member->auto_login();
        }
        $member_id = intval(decrypt($data, MD5_KEY));
        if($member_id <= 0){
            return false;
        }
        $member_info = $model_member->getMemberInfoByID($member_id);
        $model_member->createSession($member_info);
    }
}




















