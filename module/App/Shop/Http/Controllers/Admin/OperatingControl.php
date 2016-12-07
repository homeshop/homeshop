<?php namespace App\Shop\Http\Controllers\Admin;



/**
 * 运营
 */
class  OperatingControl extends SystemControl {
    
    public function __construct() {
        parent::__construct();
        //Language::read('setting');
    }
    
    public function indexOp() {
        $this->settingOp();
    }
    
    /**
     * 基本设置
     */
    public function settingOp() {
        $model_setting = Model('setting');
        if(chksubmit()){
            $obj_validate = new Validate();
            $obj_validate->validateparam = [
            
            ];
            $error = $obj_validate->validate();
            if($error != ''){
                showDialog($error);
            } else {
                $update_array = [];
                $update_array['contract_allow'] = intval($_POST['contract_allow']);
                $update_array['delivery_isuse'] = intval($_POST['delivery_isuse']);
                $result = $model_setting->updateSetting($update_array);
                if($result === true){
                    if($update_array['delivery_isuse'] == 0){
                        // 删除相关联的收货地址
                        Model('address')->delAddress(['dlyp_id' => ['neq', 0]]);
                    }
                    $this->log('编辑运营设置', 1);
                    showDialog(L('nc_common_save_succ'));
                } else {
                    showDialog(L('nc_common_save_fail'));
                }
            }
        }
        $list_setting = $model_setting->getListSetting();
        Tpl::output('list_setting', $list_setting);
        Tpl::setDirquna('shop');
        Tpl::showpage('operating.setting');
    }
}
