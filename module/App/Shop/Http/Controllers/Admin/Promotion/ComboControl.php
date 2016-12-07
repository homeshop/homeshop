<?php namespace App\Shop\Http\Controllers\Admin\Promotion;

use App\Shop\Http\Controllers\Admin\SystemControl;
use App\Shop\Http\Controllers\Admin\Language;
use App\Shop\Http\Controllers\Admin\Tpl;


/**
 * 推荐组合管理
 */
class  ComboControl extends SystemControl {
    
    private $links = [
        ['url' => 'act=promotion_combo&op=index', 'text' => '商品列表'],
        ['url' => 'act=promotion_combo&op=combo_quota_list', 'text' => '套餐列表'],
        ['url' => 'act=promotion_combo&op=combo_setting', 'text' => '设置']
    ];
    
    public function __construct() {
        parent::__construct();
        //检查审核功能是否开启
        if(intval($_GET['promotion_allow']) !== 1 && intval(C('promotion_allow')) !== 1){
            $url = [
                [
                    'url' => 'index.php?act=setting',
                    'msg' => L('close'),
                ],
                [
                    'url' => 'index.php?act=promotion_combo&promotion_allow=1',
                    'msg' => L('open'),
                ]
            ];
            showMessage('商品促销功能尚未开启', $url, 'html', 'succ', 1, 6000);
        }
    }
    
    /**
     * 默认Op
     */
    public function indexOp() {
        //自动开启优惠套装
        if(intval($_GET['promotion_allow']) === 1){
            $model_setting = Model('setting');
            $update_array = [];
            $update_array['promotion_allow'] = 1;
            $model_setting->updateSetting($update_array);
        }
        $this->combo_goods_listOp();
    }
    
    /**
     * 活动商品列表
     */
    public function combo_goods_listOp() {
        Tpl::output('top_link', $this->sublink($this->links, 'index'));
        Tpl::setDirquna('shop');
        Tpl::showpage('promotion_combo.goods');
    }
    
    /**
     * 活动商品管理XML
     */
    public function get_combo_goods_xmlOp() {
        $condition = [];
        if($_POST['query'] != ''){
            $condition[ $_POST['qtype'] ] = ['like', '%' . $_POST['query'] . '%'];
        }
        $order = '';
        $param = ['goods_id'];
        if(in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], ['asc', 'desc'])){
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        $page = $_POST['rp'];
        
        $goods_list = [];
        $model_combo_goods = Model('p_combo_goods');
        $combo_list = $model_combo_goods->getComboGoodsList($condition, 'distinct goods_id', $page, $order);
        if(!empty($combo_list)){
            $goodsid_array = [];
            foreach($combo_list as $val){
                $goodsid_array[] = $val['goods_id'];
            }
            $goods_list = Model('goods')->getGoodsList(['goods_id' => ['in', $goodsid_array]], '*', '', $order);
        }
        $flipped_own_shop_ids = array_flip(Model('store')->getOwnShopIds());
        
        $data = [];
        $data['now_page'] = $model_combo_goods->shownowpage();
        $data['total_num'] = $model_combo_goods->gettotalnum();
        foreach($goods_list as $value){
            $param = [];
            $operation = "<a class='btn red' href='javascript:;' onclick=\"fg_del('" . $value['goods_id'] . "')\"><i class='fa fa-trash-o'></i>删除</a><a class='btn green' target='_blank' href='" . urlShop('goods', 'index', ['goods_id' => $value['goods_id']]) . "'><i class='fa fa-list-alt'></i>查看</a>";
            $param['operation'] = $operation;
            $param['goods_id'] = $value['goods_id'];
            $param['goods_name'] = $value['goods_name'];
            $param['goods_price'] = ncPriceFormat($value['goods_price']);
            $param['store_id'] = $value['store_id'];
            $param['store_name'] = "<a target='_blank' href='" . urlShop('show_store', 'index', ['store_id' => $value['store_id']]) . "'>" . $value['store_name'] . "</a>";
            if(isset($flipped_own_shop_ids[ $value['store_id'] ])){
                $param['store_name'] .= '<span class="ownshop">[自营]</span>';
            }
            $data['list'][ $value['goods_id'] ] = $param;
        }
        echo Tpl::flexigridXML($data);
        exit();
    }
    
    /**
     * 删除订金预售商品活动
     */
    public function del_combo_goodsOp() {
        $id = intval($_GET['id']);
        if($id > 0){
            $state = Model('p_combo_goods')->delComboGoodsByGoodsId($id);
            $this->log('删除推荐组合商品活动，商品ID' . $id);
            exit(json_encode(['state' => true, 'msg' => '删除成功']));
        } else {
            exit(json_encode(['state' => false, 'msg' => '删除失败']));
        }
    }
    
    /**
     * 套餐列表
     */
    public function combo_quota_listOp() {
        Tpl::output('top_link', $this->sublink($this->links, 'combo_quota_list'));
        Tpl::setDirquna('shop');
        Tpl::showpage('promotion_combo.quota');
    }
    
    /**
     * 套餐列表XML
     */
    public function get_quota_xmlOp() {
        $model_combo = Model('p_combo_quota');
        $condition = [];
        if($_POST['query'] != ''){
            $condition[ $_POST['qtype'] ] = ['like', '%' . $_POST['query'] . '%'];
        }
        $order = '';
        $param = ['store_id', 'store_name', 'cq_starttime', 'cq_endtime'];
        if(in_array($_POST['sortname'], $param) && in_array($_POST['sortorder'], ['asc', 'desc'])){
            $order = $_POST['sortname'] . ' ' . $_POST['sortorder'];
        }
        $page = $_POST['rp'];
        $quota_list = $model_combo->getComboQuotaList($condition, '*', $page, $order);
        
        $data = [];
        $data['now_page'] = $model_combo->shownowpage();
        $data['total_num'] = $model_combo->gettotalnum();
        foreach($quota_list as $value){
            $param = [];
            $param['operation'] = '--';
            $param['store_id'] = $value['store_id'];
            $param['store_name'] = "<a target='_blank' href='" . urlShop('show_store', 'index', ['store_id' => $value['store_id']]) . "'>" . $value['store_name'] . "</a>";
            if(isset($flipped_own_shop_ids[ $value['store_id'] ])){
                $param['store_name'] .= '<span class="ownshop">[自营]</span>';
            }
            $param['cq_starttime'] = date('Y-m-d H:i:s', $value['cq_starttime']);
            $param['cq_endtime'] = date('Y-m-d H:i:s', $value['cq_endtime']);
            $data['list'][ $value['cg_id'] ] = $param;
        }
        echo Tpl::flexigridXML($data);
        exit;
    }
    
    /**
     * 设置
     */
    public function combo_settingOp() {
        // 实例化模型
        $model_setting = Model('setting');
        
        if(chksubmit()){
            // 验证
            $obj_validate = new Validate();
            $obj_validate->validateparam = [
                [
                    "input" => $_POST["promotion_combo_price"],
                    "require" => "true",
                    'validator' => 'Number',
                    "message" => '请填写套餐价格'
                ],
            ];
            $error = $obj_validate->validate();
            if($error != ''){
                showMessage($error);
            }
            
            $data['promotion_combo_price'] = intval($_POST['promotion_combo_price']);
            
            $return = $model_setting->updateSetting($data);
            if($return){
                $this->log(L('nc_set') . '推荐组合');
                showMessage(L('nc_common_op_succ'));
            } else {
                showMessage(L('nc_common_op_fail'));
            }
        }
        
        // 查询setting列表
        $setting = $model_setting->GetListSetting();
        Tpl::output('setting', $setting);
        
        Tpl::output('top_link', $this->sublink($this->links, 'combo_setting'));
        Tpl::setDirquna('shop');
        Tpl::showpage('promotion_combo.setting');
    }
}
