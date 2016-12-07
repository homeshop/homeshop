<?php
/**
 * 商品管理
 */


defined('IN_HOMESHOP') or exit ('Access Invalid!');

class store_goods_offlineControl extends BaseSellerControl {
    
    public function __construct() {
        parent::__construct();
        Language::read('member_store_goods_index');
    }
    
    public function indexOp() {
        $this->goods_storageOp();
    }
    
    /**
     * 仓库中的商品列表
     */
    public function goods_storageOp() {
        $model_goods = Model('goods');
        
        $where = [];
        $where['store_id'] = $_SESSION['store_id'];
        if(intval($_GET['stc_id']) > 0){
            $where['goods_stcids'] = ['like', '%,'.intval($_GET['stc_id']).',%'];
        }
        if(trim($_GET['keyword']) != ''){
            switch($_GET['search_type']) {
                case 0:
                    $where['goods_name'] = ['like', '%'.trim($_GET['keyword']).'%'];
                    break;
                case 1:
                    $where['goods_serial'] = ['like', '%'.trim($_GET['keyword']).'%'];
                    break;
                case 2:
                    $where['goods_commonid'] = intval($_GET['keyword']);
                    break;
            }
        }
        if(intval($_GET['sup_id']) > 0){
            $where['sup_id'] = intval($_GET['sup_id']);
        }
        
        //权限组对应分类权限判断
        if(!$_SESSION['seller_gc_limits'] && $_SESSION['seller_group_id']){
            $gc_list = Model('seller_group_bclass')->getSellerGroupBclasList(['group_id' => $_SESSION['seller_group_id']], '', '', 'gc_id', 'gc_id');
            $where['gc_id'] = ['in', array_keys($gc_list)];
        }
        
        switch($_GET['type']) {
            // 违规的商品
            case 'lock_up':
                $this->profile_menu('goods_lockup');
                $goods_list = $model_goods->getGoodsCommonLockUpList($where);
                break;
            // 等待审核或审核失败的商品
            case 'wait_verify':
                $this->profile_menu('goods_verify');
                if(isset($_GET['verify']) && in_array($_GET['verify'], ['0', '10'])){
                    $where['goods_verify'] = $_GET['verify'];
                }
                $goods_list = $model_goods->getGoodsCommonWaitVerifyList($where);
                break;
            // 仓库中的商品
            default:
                $this->profile_menu('goods_storage');
                $goods_list = $model_goods->getGoodsCommonOfflineList($where);
                break;
        }
        
        Tpl::output('show_page', $model_goods->showpage());
        Tpl::output('goods_list', $goods_list);
        
        // 计算库存
        $storage_array = $model_goods->calculateStorage($goods_list);
        Tpl::output('storage_array', $storage_array);
        
        // 商品分类
        $store_goods_class = Model('store_goods_class')->getClassTree([
            'store_id' => $_SESSION['store_id'],
            'stc_state' => '1'
        ]);
        Tpl::output('store_goods_class', $store_goods_class);
        
        // 供货商
        $supplier_list = Model('store_supplier')->getStoreSupplierList(['sup_store_id' => $_SESSION['store_id']]);
        Tpl::output('supplier_list', $supplier_list);
        
        switch($_GET['type']) {
            // 违规的商品
            case 'lock_up':
                Tpl::showpage('store_goods_list.offline_lockup');
                break;
            // 等待审核或审核失败的商品
            case 'wait_verify':
                Tpl::output('verify', ['0' => '未通过', '10' => '等待审核']);
                Tpl::showpage('store_goods_list.offline_waitverify');
                break;
            // 仓库中的商品
            default:
                Tpl::showpage('store_goods_list.offline');
                break;
        }
    }
    
    /**
     * 商品上架
     */
    public function goods_showOp() {
        $commonid = $_GET['commonid'];
        if(!preg_match('/^[\d,]+$/i', $commonid)){
            showDialog(L('para_error'), '', 'error');
        }
        if($this->store_info['store_state'] != 1){
            showDialog(L('store_goods_index_goods_show_fail').'，店铺正在审核中或已经关闭', '', 'error');
        }
        $commonid_array = explode(',', $commonid);
        $result = Logic('goods')->goodsShow($commonid_array, $this->store_info['store_id'], $_SESSION['seller_id'], $_SESSION['seller_name']);
        if($result['state']){
            showDialog(L('store_goods_index_goods_show_success'), 'reload', 'succ');
        } else {
            showDialog(L('store_goods_index_goods_show_fail'), '', 'error');
        }
    }
    
    /**
     * 用户中心右边，小导航
     * @param string $menu_key 当前导航的menu_key
     * @return
     */
    private function profile_menu($menu_key = '') {
        $menu_array = [
            [
                'menu_key' => 'goods_storage',
                'menu_name' => L('nc_member_path_goods_storage'),
                'menu_url' => urlShop('store_goods_offline', 'index')
            ],
            [
                'menu_key' => 'goods_lockup',
                'menu_name' => L('nc_member_path_goods_state'),
                'menu_url' => urlShop('store_goods_offline', 'index', ['type' => 'lock_up'])
            ],
            [
                'menu_key' => 'goods_verify',
                'menu_name' => L('nc_member_path_goods_verify'),
                'menu_url' => urlShop('store_goods_offline', 'index', ['type' => 'wait_verify'])
            ]
        ];
        Tpl::output('member_menu', $menu_array);
        Tpl::output('menu_key', $menu_key);
    }
}
