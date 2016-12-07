<?php namespace App\Shop\Http\Controllers\Store;

use App\Shop\Http\Controllers\BaseSellerControl;
use App\Shop\Http\Controllers\Language;
use App\Shop\Http\Controllers\Tpl;


/**
 * 卖家商品咨询管理
 */
class  ConsultControl extends BaseSellerControl {
    
    public function __construct() {
        parent::__construct();
        Language::read('member_store_consult_index');
    }
    
    /**
     * 商品咨询首页
     */
    public function indexOp() {
        $this->consult_listOp();
    }
    
    /**
     * 商品咨询列表页
     */
    public function consult_listOp() {
        $consult = Model('consult');
        $list_consult = [];
        $where = [];
        if(trim($_GET['type']) == 'to_reply'){
            if(C('dbdriver') == 'mysqli'){
                $where['consult_reply'] = ['eq', ''];
            } else {
                $where['consult_reply'] = ['exp', 'consult_reply IS NULL'];
            }
        } elseif(trim($_GET['type'] == 'replied')) {
            if(C('dbdriver') == 'mysqli'){
                $where['consult_reply'] = ['neq', ''];
            } else {
                $where['consult_reply'] = ['exp', 'consult_reply IS NOT NULL'];
            }
        }
        if(intval($_GET['ctid']) > 0){
            $where['ct_id'] = intval($_GET['ctid']);
        }
        $where['store_id'] = $_SESSION['store_id'];
        $list_consult = $consult->getConsultList($where, '*', 0, 10);
        Tpl::output('show_page', $consult->showpage());
        Tpl::output('list_consult', $list_consult);
        
        // 咨询类型
        $consult_type = rkcache('consult_type', true);
        Tpl::output('consult_type', $consult_type);
        
        $_GET['type'] = empty($_GET['type']) ? 'consult_list' : $_GET['type'];
        self::profile_menu('consult', $_GET['type']);
        Tpl::showpage('store_consult_manage');
    }
    
    /**
     * 商品咨询删除处理
     */
    public function drop_consultOp() {
        $ids = trim($_GET['id']);
        if(!preg_match('/^[\d,]+$/i', $ids)){
            showDialog(L('para_error'), '', 'error');
        }
        $consult = Model('consult');
        $id_array = explode(',', trim($_GET['id']));
        $where = [];
        $where['store_id'] = $_SESSION['store_id'];
        $where['consult_id'] = ['in', $id_array];
        $state = $consult->delConsult($where);
        if($state){
            showDialog(Language::get('store_consult_drop_success'), 'reload', 'succ');
        } else {
            showDialog(Language::get('store_consult_drop_fail'));
        }
    }
    
    /**
     * 回复商品咨询表单页
     */
    public function reply_consultOp() {
        $consult = Model('consult');
        $list_consult = [];
        $search_array = [];
        $search_array['consult_id'] = intval($_GET['id']);
        $search_array['store_id'] = $_SESSION['store_id'];
        $consult_info = $consult->getConsultInfo($search_array);
        Tpl::output('consult', $consult_info);
        Tpl::showpage('store_consult_form', 'null_layout');
    }
    
    /**
     * 商品咨询回复内容的保存处理
     */
    public function reply_saveOp() {
        $consult_id = intval($_POST['consult_id']);
        if($consult_id <= 0){
            showDialog(L('wrong_argument'));
        }
        $consult = Model('consult');
        $update = [];
        $update['consult_reply'] = $_POST['content'];
        $condtion = [];
        $condtion['store_id'] = $_SESSION['store_id'];
        $condtion['consult_id'] = $consult_id;
        $state = $consult->editConsult($condtion, $update);
        if($state){
            $consult_info = $consult->getConsultInfo(['consult_id' => $consult_id]);
            // 发送用户消息
            $param = [];
            $param['code'] = 'consult_goods_reply';
            $param['member_id'] = $consult_info['member_id'];
            $param['param'] = [
                'goods_name' => $consult_info['goods_name'],
                'consult_url' => urlShop('member_consult', 'my_consult')
            ];
            QueueClient::push('sendMemberMsg', $param);
            
            showDialog(Language::get('nc_common_op_succ'), 'reload', 'succ', empty($_GET['inajax']) ? '' : 'CUR_DIALOG.close();');
        } else {
            showDialog(Language::get('nc_common_op_fail'));
        }
    }
    
    /**
     * 用户中心右边，小导航
     * @param string $menu_type 导航类型
     * @param string $menu_key  当前导航的menu_key
     * @param array  $array     附加菜单
     * @return
     */
    private function profile_menu($menu_type, $menu_key = '', $array = []) {
        Language::read('member_layout');
        $menu_array = [];
        switch($menu_type) {
            case 'consult':
                $menu_array = [
                    1 => [
                        'menu_key' => 'consult_list',
                        'menu_name' => Language::get('nc_member_path_all_consult'),
                        'menu_url' => 'index.php?act=store_consult&op=consult_list'
                    ],
                    2 => [
                        'menu_key' => 'to_reply',
                        'menu_name' => Language::get('nc_member_path_unreplied_consult'),
                        'menu_url' => 'index.php?act=store_consult&op=consult_list&type=to_reply'
                    ],
                    3 => [
                        'menu_key' => 'replied',
                        'menu_name' => Language::get('nc_member_path_replied_consult'),
                        'menu_url' => 'index.php?act=store_consult&op=consult_list&type=replied'
                    ]
                ];
                break;
        }
        if(!empty($array)){
            $menu_array[] = $array;
        }
        Tpl::output('member_menu', $menu_array);
        Tpl::output('menu_key', $menu_key);
    }
}
