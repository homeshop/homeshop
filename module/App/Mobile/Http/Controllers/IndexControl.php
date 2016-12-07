<?php namespace App\Mobile\Http\Controllers;

/**
 * 手机端首页控制
 */
class IndexControl extends MobileHomeControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 首页
     */
    public function indexOp() {
        $model_mb_special = Model('mb_special');
        $data = $model_mb_special->getMbSpecialIndex();
        $this->_output_special($data, $_GET['type']);
    }
    
    /**
     * 专题
     */
    public function specialOp() {
        $model_mb_special = Model('mb_special');
        $info = $model_mb_special->getMbSpecialInfoByID($_GET['special_id']);
        $list = $model_mb_special->getMbSpecialItemUsableListByID($_GET['special_id']);
        $data = array_merge($info, ['list' => $list]);
        $this->_output_special($data, $_GET['type'], $_GET['special_id']);
    }
    
    /**
     * 输出专题
     */
    private function _output_special($data, $type = 'json', $special_id = 0) {
        $model_special = Model('mb_special');
        if($_GET['type'] == 'html'){
            $html_path = $model_special->getMbSpecialHtmlPath($special_id);
            if(!is_file($html_path)){
                ob_start();
                Tpl::output('list', $data);
                Tpl::showpage('mb_special');
                file_put_contents($html_path, ob_get_clean());
            }
            header('Location: ' . $model_special->getMbSpecialHtmlUrl($special_id));
            die;
        } else {
            output_data($data);
        }
    }
    
    /**
     * android客户端版本号
     */
    public function apk_versionOp() {
        $version = C('mobile_apk_version');
        $url = C('mobile_apk');
        if(empty($version)){
            $version = '';
        }
        if(empty($url)){
            $url = '';
        }
        output_data(['version' => $version, 'url' => $url]);
    }
    
    /**
     * 默认搜索词列表
     */
    public function search_key_listOp() {
        $list = @explode(',', C('hot_search'));
        if(!$list || !is_array($list)){
            $list = [];
        }
        if($_COOKIE['hisSearch'] != ''){
            $his_search_list = explode('~', $_COOKIE['hisSearch']);
        }
        if(!$his_search_list || !is_array($his_search_list)){
            $his_search_list = [];
        }
        output_data(['list' => $list, 'his_list' => $his_search_list]);
    }
    
    /**
     * 热门搜索列表
     */
    public function search_hot_infoOp() {
        if(C('rec_search') != ''){
            $rec_search_list = @unserialize(C('rec_search'));
        }
        $rec_search_list = is_array($rec_search_list) ? $rec_search_list : [];
        $result = $rec_search_list[ array_rand($rec_search_list) ];
        output_data(['hot_info' => $result ? $result : []]);
    }
    
    /**
     * 高级搜索
     */
    public function search_advOp() {
        $area_list = Model('area')->getAreaList(['area_deep' => 1], 'area_id,area_name');
        if(C('contract_allow') == 1){
            $contract_list = Model('contract')->getContractItemByCache();
            $_tmp = [];
            $i = 0;
            foreach($contract_list as $k => $v){
                $_tmp[ $i ]['id'] = $v['cti_id'];
                $_tmp[ $i ]['name'] = $v['cti_name'];
                $i++;
            }
        }
        output_data(['area_list' => $area_list ? $area_list : [], 'contract_list' => $_tmp]);
    }
}
