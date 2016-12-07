<?php namespace App\Shop\Http\Controllers;
/**
 * Created by IntelliJ IDEA.
 * User: mo
 * Date: 16-9-30
 * Time: 上午2:19
 */

/**
 * 店铺 control新父类
 */
class BaseSellerControl extends Control {
    
    //店铺信息
    protected $store_info = [];
    //店铺等级
    protected $store_grade = [];
    
    public function __construct() {
        Language::read('common,store_layout,member_layout');
        if(!C('site_status')){
            halt(C('closed_reason'));
        }
        Tpl::setDir('seller');
        Tpl::setLayout('seller_layout');
        
        Tpl::output('nav_list', rkcache('nav', true));
        if($_GET['act'] !== 'seller_login'){
            
            if(empty($_SESSION['seller_id'])){
                @header('location: index.php?act=seller_login&op=show_login');
                die;
            }
            
            // 验证店铺是否存在
            $model_store = Model('store');
            $this->store_info = $model_store->getStoreInfoByID($_SESSION['store_id']);
            if(empty($this->store_info)){
                @header('location: index.php?act=seller_login&op=show_login');
                die;
            }
            
            // 店铺关闭标志
            if(intval($this->store_info['store_state']) === 0){
                Tpl::output('store_closed', true);
                Tpl::output('store_close_info', $this->store_info['store_close_info']);
            }
            
            // 店铺等级
            if(checkPlatformStore()){
                $this->store_grade = [
                    'sg_id' => '0',
                    'sg_name' => '自营店铺专属等级',
                    'sg_goods_limit' => '0',
                    'sg_album_limit' => '0',
                    'sg_space_limit' => '999999999',
                    'sg_template_number' => '6', // see also store_settingControl.themeOp()
                    // 'sg_template' => 'default|style1|style2|style3|style4|style5',
                    'sg_price' => '0.00',
                    'sg_description' => '',
                    'sg_function' => 'editor_multimedia',
                    'sg_sort' => '0',
                ];
            } else {
                $store_grade = rkcache('store_grade', true);
                $this->store_grade = $store_grade[$this->store_info['grade_id']];
            }
            
            if($_SESSION['seller_is_admin'] !== 1 && $_GET['act'] !== 'seller_center' && $_GET['act'] !== 'seller_logout'){
                if(!in_array($_GET['act'], $_SESSION['seller_limits'])){
                    showMessage('没有权限', '', '', 'error');
                }
            }
            
            // 卖家菜单
            Tpl::output('menu', $_SESSION['seller_menu']);
            // 当前菜单
            $current_menu = $this->_getCurrentMenu($_SESSION['seller_function_list']);
            Tpl::output('current_menu', $current_menu);
            // 左侧菜单
            if($_GET['act'] == 'seller_center'){
                if(!empty($_SESSION['seller_quicklink'])){
                    $left_menu = [];
                    foreach($_SESSION['seller_quicklink'] as $value){
                        $left_menu[] = $_SESSION['seller_function_list'][$value];
                    }
                }
            } else {
                $left_menu = $_SESSION['seller_menu'][$current_menu['model']]['child'];
            }
            Tpl::output('left_menu', $left_menu);
            Tpl::output('seller_quicklink', $_SESSION['seller_quicklink']);
            
            $this->checkStoreMsg();
        }
    }
    
    /**
     * 记录卖家日志
     * @param $content 日志内容
     * @param $state   1成功 0失败
     */
    protected function recordSellerLog($content = '', $state = 1) {
        $seller_info = [];
        $seller_info['log_content'] = $content;
        $seller_info['log_time'] = TIMESTAMP;
        $seller_info['log_seller_id'] = $_SESSION['seller_id'];
        $seller_info['log_seller_name'] = $_SESSION['seller_name'];
        $seller_info['log_store_id'] = $_SESSION['store_id'];
        $seller_info['log_seller_ip'] = getIp();
        $seller_info['log_url'] = $_GET['act'].'&'.$_GET['op'];
        $seller_info['log_state'] = $state;
        $model_seller_log = Model('seller_log');
        $model_seller_log->addSellerLog($seller_info);
    }
    
    /**
     * 记录店铺费用
     * @param $cost_price  费用金额
     * @param $cost_remark 费用备注
     */
    protected function recordStoreCost($cost_price, $cost_remark) {
        // 平台店铺不记录店铺费用
        if(checkPlatformStore()){
            return false;
        }
        $model_store_cost = Model('store_cost');
        $param = [];
        $param['cost_store_id'] = $_SESSION['store_id'];
        $param['cost_seller_id'] = $_SESSION['seller_id'];
        $param['cost_price'] = $cost_price;
        $param['cost_remark'] = $cost_remark;
        $param['cost_state'] = 0;
        $param['cost_time'] = TIMESTAMP;
        $model_store_cost->addStoreCost($param);
        
        // 发送店铺消息
        $param = [];
        $param['code'] = 'store_cost';
        $param['store_id'] = $_SESSION['store_id'];
        $param['param'] = [
            'price' => $cost_price,
            'seller_name' => $_SESSION['seller_name'],
            'remark' => $cost_remark
        ];
        
        QueueClient::push('sendStoreMsg', $param);
    }
    
    protected function getSellerMenuList($is_admin, $limits) {
        $seller_menu = [];
        if(intval($is_admin) !== 1){
            $menu_list = $this->_getMenuList();
            foreach($menu_list as $key => $value){
                foreach($value['child'] as $child_key => $child_value){
                    if(!in_array($child_value['act'], $limits)){
                        unset($menu_list[$key]['child'][$child_key]);
                    }
                }
                
                if(count($menu_list[$key]['child']) > 0){
                    $seller_menu[$key] = $menu_list[$key];
                }
            }
        } else {
            $seller_menu = $this->_getMenuList();
        }
        $seller_function_list = $this->_getSellerFunctionList($seller_menu);
        return ['seller_menu' => $seller_menu, 'seller_function_list' => $seller_function_list];
    }
    
    private function _getCurrentMenu($seller_function_list) {
        $current_menu = $seller_function_list[$_GET['act']];
        if(empty($current_menu)){
            $current_menu = ['model' => 'index', 'model_name' => '首页'];
        }
        return $current_menu;
    }
    
    private function _getMenuList() {
        $menu_list = [
            'goods' => [
                'name' => '商品',
                'child' => [
                    ['name' => '商品发布', 'act' => 'store_goods_add', 'op' => 'index'],
                    ['name' => '淘宝CSV导入', 'act' => 'taobao_import', 'op' => 'index'],
                    ['name' => '出售中的商品', 'act' => 'store_goods_online', 'op' => 'index'],
                    ['name' => '仓库中的商品', 'act' => 'store_goods_offline', 'op' => 'index'],
                    ['name' => '预约/到货通知', 'act' => 'store_appoint', 'op' => 'index'],
                    ['name' => '关联版式', 'act' => 'store_plate', 'op' => 'index'],
                    ['name' => '商品规格', 'act' => 'store_spec', 'op' => 'index'],
                    ['name' => '图片空间', 'act' => 'store_album', 'op' => 'album_cate'],
                ]
            ],
            'order' => [
                'name' => '订单物流',
                'child' => [
                    ['name' => '实物交易订单', 'act' => 'store_order', 'op' => 'index'],
                    ['name' => '虚拟兑码订单', 'act' => 'store_vr_order', 'op' => 'index'],
                    ['name' => '发货', 'act' => 'store_deliver', 'op' => 'index'],
                    ['name' => '发货设置', 'act' => 'store_deliver_set', 'op' => 'daddress_list'],
                    ['name' => '运单模板', 'act' => 'store_waybill', 'op' => 'waybill_manage'],
                    ['name' => '评价管理', 'act' => 'store_evaluate', 'op' => 'list'],
                    ['name' => '物流工具', 'act' => 'store_transport', 'op' => 'index'],
                    ['name' => '来单提醒', 'act' => 'order_call', 'op' => 'index'],
                ]
            ],
            'promotion' => [
                'name' => '促销',
                'child' => [
                    ['name' => '抢购管理', 'act' => 'store_groupbuy', 'op' => 'index'],
                    ['name' => '加价购', 'act' => 'store_promotion_cou', 'op' => 'cou_list'],
                    ['name' => '限时折扣', 'act' => 'store_promotion_xianshi', 'op' => 'xianshi_list'],
                    ['name' => '满即送', 'act' => 'store_promotion_mansong', 'op' => 'mansong_list'],
                    ['name' => '优惠套装', 'act' => 'store_promotion_bundling', 'op' => 'bundling_list'],
                    ['name' => '推荐展位', 'act' => 'store_promotion_booth', 'op' => 'booth_goods_list'],
                    ['name' => '预售商品', 'act' => 'store_promotion_book', 'op' => 'index'],
                    ['name' => 'F码商品', 'act' => 'store_promotion_fcode', 'op' => 'index'],
                    ['name' => '推荐组合', 'act' => 'store_promotion_combo', 'op' => 'index'],
                    ['name' => '手机专享', 'act' => 'store_promotion_sole', 'op' => 'index'],
                    ['name' => '代金券管理', 'act' => 'store_voucher', 'op' => 'templatelist'],
                    ['name' => '活动管理', 'act' => 'store_activity', 'op' => 'store_activity'],
                ]
            ],
            'store' => [
                'name' => '店铺',
                'child' => [
                    ['name' => '店铺设置', 'act' => 'store_setting', 'op' => 'store_setting'],
                    ['name' => '店铺装修', 'act' => 'store_decoration', 'op' => 'decoration_setting'],
                    ['name' => '店铺导航', 'act' => 'store_navigation', 'op' => 'navigation_list'],
                    ['name' => '店铺动态', 'act' => 'store_sns', 'op' => 'index'],
                    ['name' => '店铺信息', 'act' => 'store_info', 'op' => 'bind_class'],
                    ['name' => '店铺分类', 'act' => 'store_goods_class', 'op' => 'index'],
                    ['name' => '品牌申请', 'act' => 'store_brand', 'op' => 'brand_list'],
                    ['name' => '供货商', 'act' => 'store_supplier', 'op' => 'sup_list'],
                    ['name' => '实体店铺', 'act' => 'store_map', 'op' => 'index'],
                    ['name' => '消费者保障服务', 'act' => 'store_contract', 'op' => 'index'],
                ]
            ],
            'consult' => [
                'name' => '售后服务',
                'child' => [
                    ['name' => '咨询管理', 'act' => 'store_consult', 'op' => 'consult_list'],
                    ['name' => '投诉管理', 'act' => 'store_complain', 'op' => 'list'],
                    ['name' => '退款记录', 'act' => 'store_refund', 'op' => 'index'],
                    ['name' => '退货记录', 'act' => 'store_return', 'op' => 'index'],
                ]
            ],
            'statistics' => [
                'name' => '统计结算',
                'child' => [
                    ['name' => '店铺概况', 'act' => 'statistics_general', 'op' => 'general'],
                    ['name' => '商品分析', 'act' => 'statistics_goods', 'op' => 'goodslist'],
                    ['name' => '运营报告', 'act' => 'statistics_sale', 'op' => 'sale'],
                    ['name' => '行业分析', 'act' => 'statistics_industry', 'op' => 'hot'],
                    ['name' => '流量统计', 'act' => 'statistics_flow', 'op' => 'storeflow'],
                    ['name' => '实物结算', 'act' => 'store_bill', 'op' => 'index'],
                    ['name' => '虚拟结算', 'act' => 'store_vr_bill', 'op' => 'index'],
                ]
            ],
            'message' => [
                'name' => '客服消息',
                'child' => [
                    ['name' => '客服设置', 'act' => 'store_callcenter', 'op' => 'index'],
                    ['name' => '系统消息', 'act' => 'store_msg', 'op' => 'index'],
                    ['name' => '聊天记录查询', 'act' => 'store_im', 'op' => 'index'],
                ]
            ],
            'account' => [
                'name' => '账号',
                'child' => [
                    ['name' => '账号列表', 'act' => 'store_account', 'op' => 'account_list'],
                    ['name' => '账号组', 'act' => 'store_account_group', 'op' => 'group_list'],
                    ['name' => '账号日志', 'act' => 'seller_log', 'op' => 'log_list'],
                    ['name' => '店铺消费', 'act' => 'store_cost', 'op' => 'cost_list'],
                    ['name' => '门店账号', 'act' => 'store_chain', 'op' => 'index'],
                ]
            ]
            //临时注释
            /*'webchat' => array('name' => '微信', 'child' => array(
                array('name' => '微信接口管理', 'act'=>'seller_wechat', 'op'=>'index'),
                array('name' => '关注自动回复', 'act'=>'seller_wechat_follow', 'op'=>'follow_index'),
                array('name' => '关键词自动回复', 'act'=>'seller_wechat_keyword', 'op'=>'keyword_index'),
                array('name' => '消息自动回复', 'act'=>'seller_wechat_message', 'op'=>'message_index'),
                array('name' => '自定义菜单', 'act'=>'seller_wechat_menu', 'op'=>'index'),
            ))*/
        ];
        return $menu_list;
    }
    
    private function _getSellerFunctionList($menu_list) {
        $format_menu = [];
        foreach($menu_list as $key => $menu_value){
            foreach($menu_value['child'] as $submenu_value){
                $format_menu[$submenu_value['act']] = [
                    'model' => $key,
                    'model_name' => $menu_value['name'],
                    'name' => $submenu_value['name'],
                    'act' => $submenu_value['act'],
                    'op' => $submenu_value['op'],
                ];
            }
        }
        return $format_menu;
    }
    
    /**
     * 自动发布店铺动态
     * @param array  $data 相关数据
     * @param string $type 类型 'new','coupon','xianshi','mansong','bundling','groupbuy'
     *                     所需字段
     *                     new       goods表'
     *                     goods_id,store_id,goods_name,goods_image,goods_price,goods_freight xianshi
     *                     p_xianshi_goods表'
     *                     goods_id,store_id,goods_name,goods_image,goods_price,goods_freight,xianshi_price mansong
     *                     p_mansong表'         mansong_name,start_time,end_time,store_id bundling  p_bundling表'
     *                     bl_id,bl_name,bl_img,bl_discount_price,bl_freight_choose,bl_freight,store_id groupbuy
     *                     goods_group表'
     *                     group_id,group_name,goods_id,goods_price,groupbuy_price,group_pic,rebate,start_time,end_time
     *                     coupon在后台发布
     */
    public function storeAutoShare($data, $type) {
        $param = [3 => 'new', 4 => 'coupon', 5 => 'xianshi', 6 => 'mansong', 7 => 'bundling', 8 => 'groupbuy'];
        $param_flip = array_flip($param);
        if(!in_array($type, $param) || empty($data)){
            return false;
        }
        
        $auto_setting = Model('store_sns_setting')->getStoreSnsSettingInfo(['sauto_storeid' => $_SESSION ['store_id']]);
        $auto_sign = false; // 自动发布开启标志
        
        if($auto_setting['sauto_'.$type] == 1){
            $auto_sign = true;
            if(CHARSET == 'GBK'){
                foreach((array)$data as $k => $v){
                    $data[$k] = Language::getUTF8($v);
                }
            }
            $goodsdata = addslashes(json_encode($data));
            if($auto_setting['sauto_'.$type.'title'] != ''){
                $title = $auto_setting['sauto_'.$type.'title'];
            } else {
                $auto_title = 'nc_store_auto_share_'.$type.rand(1, 5);
                $title = Language::get($auto_title);
            }
        }
        if($auto_sign){
            // 插入数据
            $stracelog_array = [];
            $stracelog_array['strace_storeid'] = $this->store_info['store_id'];
            $stracelog_array['strace_storename'] = $this->store_info['store_name'];
            $stracelog_array['strace_storelogo'] = empty($this->store_info['store_avatar']) ? '' : $this->store_info['store_avatar'];
            $stracelog_array['strace_title'] = $title;
            $stracelog_array['strace_content'] = '';
            $stracelog_array['strace_time'] = TIMESTAMP;
            $stracelog_array['strace_type'] = $param_flip[$type];
            $stracelog_array['strace_goodsdata'] = $goodsdata;
            Model('store_sns_tracelog')->saveStoreSnsTracelog($stracelog_array);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 商家消息数量
     */
    private function checkStoreMsg() {//判断cookie是否存在
        $cookie_name = 'storemsgnewnum'.$_SESSION['seller_id'];
        if(cookie($cookie_name) != null && intval(cookie($cookie_name)) >= 0){
            $countnum = intval(cookie($cookie_name));
        } else {
            $where = [];
            $where['store_id'] = $_SESSION['store_id'];
            $where['sm_readids'] = [
                'exp',
                'sm_readids NOT LIKE \'%,'.$_SESSION['seller_id'].',%\' OR sm_readids IS NULL'
            ];
            if($_SESSION['seller_smt_limits'] !== false){
                $where['smt_code'] = ['in', $_SESSION['seller_smt_limits']];
            }
            $countnum = Model('store_msg')->getStoreMsgCount($where);
            setNcCookie($cookie_name, intval($countnum), 2 * 3600);//保存2小时
        }
        Tpl::output('store_msg_num', $countnum);
    }
    
}