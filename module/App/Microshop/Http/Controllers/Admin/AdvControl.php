<?php namespace App\Microshop\Http\Controllers\Admin;

/**
 * 微商城
 */
class AdvControl extends SystemControl {
    
    const MICROSHOP_CLASS_LIST = 'index.php?act=goods_class&op=goodsclass_list';
    const GOODS_FLAG           = 1;
    const PERSONAL_FLAG        = 2;
    const ALBUM_FLAG           = 3;
    const STORE_FLAG           = 4;
    
    public function __construct() {
        parent::__construct();
        Language::read('store');
        Language::read('microshop');
    }
    
    public function indexOp() {
        $this->adv_manageOp();
    }
    
    /**
     * 广告管理
     */
    public function adv_manageOp() {
        $model_personal = Model('micro_adv');
        $condition = [];
        if(!empty($_GET['adv_type'])){
            $condition['adv_type'] = ['like', '%' . trim($_GET['adv_type']) . '%'];
        }
        if(!empty($_GET['adv_name'])){
            $condition['adv_name'] = ['like', '%' . trim($_GET['adv_name']) . '%'];
        }
        $list = $model_personal->getList($condition, 10, '', '*');
        Tpl::output('show_page', $model_personal->showpage(2));
        Tpl::output('list', $list);
        $this->get_adv_type_list();
        $this->show_menu_adv('adv_manage');
        Tpl::setDirquna('microshop');
        Tpl::showpage('microshop_adv.manage');
    }
    
    /**
     * 微商城广告添加
     **/
    public function adv_addOp() {
        $this->get_adv_type_list();
        $this->show_menu_adv('adv_add');
        Tpl::setDirquna('microshop');
        Tpl::showpage('microshop_adv.add');
    }
    
    public function adv_editOp() {
        $adv_id = intval($_GET['adv_id']);
        if(empty($adv_id)){
            showMessage(Language::get('param_error'), '', '', 'error');
        }
        $model_adv = Model("micro_adv");
        $condition = [];
        $condition['adv_id'] = $adv_id;
        $adv_info = $model_adv->getOne($condition);
        Tpl::output('adv_info', $adv_info);
        $this->get_adv_type_list();
        $this->show_menu_adv('adv_add');
        Tpl::setDirquna('microshop');
        Tpl::showpage("microshop_adv.add");
    }
    
    public function adv_saveOp() {
        $obj_validate = new Validate();
        $validate_array = [
            [
                'input' => $_POST['adv_sort'],
                'require' => 'true',
                'validator' => 'Range',
                'min' => 0,
                'max' => 255,
                'message' => Language::get('class_sort_error')
            ],
        ];
        $obj_validate->validateparam = $validate_array;
        $error = $obj_validate->validate();
        if($error != ''){
            showMessage(Language::get('error') . $error, '', '', 'error');
        }
        $param = [];
        $param['adv_type'] = trim($_POST['adv_type']);
        $param['adv_name'] = trim($_POST['adv_name']);
        $param['adv_url'] = trim($_POST['adv_url']);
        $param['adv_sort'] = intval($_POST['adv_sort']);
        if(!empty($_FILES['adv_image']['name'])){
            $upload = new UploadFile();
            $upload->set('default_dir', ATTACH_MICROSHOP . DS . 'adv');
            $result = $upload->upfile('adv_image');
            if(!$result){
                showMessage($upload->error);
            }
            $param['adv_image'] = $upload->file_name;
            //删除老图片
            if(!empty($_POST['old_adv_image'])){
                $old_image = BASE_UPLOAD_PATH . DS . ATTACH_MICROSHOP . DS . 'adv' . DS . $_POST['old_adv_image'];
                if(is_file($old_image)){
                    unlink($old_image);
                }
            }
        } else {
            if(empty($_POST['adv_id'])){
                showMessage(Language::get('microshop_adv_image_error'), '', '', 'error');
            }
        }
        $model_adv = Model("micro_adv");
        if(isset($_POST['adv_id']) && intval($_POST['adv_id']) > 0){
            $result = $model_adv->modify($param, ['adv_id' => $_POST['adv_id']]);
        } else {
            $result = $model_adv->save($param);
        }
        if($result){
            showMessage(Language::get('nc_common_save_succ'), "index.php?act=adv&op=adv_manage");
        } else {
            showMessage(Language::get('nc_common_save_fail'), "index.php?act=adv&op=adv_manage", '', 'error');
        }
    }
    
    /**
     * 广告删除
     */
    public function adv_dropOp() {
        $model = Model('micro_adv');
        $condition = [];
        $condition['adv_id'] = ['in', trim($_REQUEST['adv_id'])];
        //删除图片
        $list = $model->getList($condition);
        if(!empty($list)){
            foreach($list as $adv_info){
                //删除原始图片
                $image_name = BASE_UPLOAD_PATH . DS . ATTACH_MICROSHOP . DS . 'adv' . DS . $adv_info['adv_image'];
                if(is_file($image_name)){
                    unlink($image_name);
                }
            }
        }
        $result = $model->drop($condition);
        if($result){
            showMessage(Language::get('nc_common_del_succ'), '');
        } else {
            showMessage(Language::get('nc_common_del_fail'), '', '', 'error');
        }
    }
    
    /**
     * 广告排序
     */
    public function adv_sort_updateOp() {
        if(intval($_GET['id']) <= 0){
            echo json_encode(['result' => false, 'message' => Language::get('param_error')]);
            die;
        }
        $new_sort = intval($_GET['value']);
        if($new_sort > 255){
            echo json_encode(['result' => false, 'message' => Language::get('class_sort_error')]);
            die;
        } else {
            $model_class = Model("micro_adv");
            $result = $model_class->modify(['adv_sort' => $new_sort], ['adv_id' => $_GET['id']]);
            if($result){
                echo json_encode(['result' => true, 'message' => '']);
                die;
            } else {
                echo json_encode(['result' => false, 'message' => '']);
                die;
            }
        }
    }
    
    //微商城广告类型列表
    private function get_adv_type_list() {
        $adv_type_list = [];
        $adv_type_list['index'] = Language::get('microshop_adv_type_index');
        $adv_type_list['store_list'] = Language::get('microshop_adv_type_store_list');
        Tpl::output('adv_type_list', $adv_type_list);
    }
    
    private function show_menu_adv($menu_key) {
        $menu_array = [
            'adv_manage' => [
                'menu_type' => 'link',
                'menu_name' => Language::get('nc_manage'),
                'menu_url' => 'index.php?act=adv&op=adv_manage'
            ],
            'adv_add' => [
                'menu_type' => 'link',
                'menu_name' => Language::get('nc_new'),
                'menu_url' => 'index.php?act=adv&op=adv_add'
            ],
        ];
        if($menu_key == 'adv_edit'){
            $menu_array['adv_edit'] = [
                'menu_type' => 'link',
                'menu_name' => Language::get('nc_edit'),
                'menu_url' => '###'
            ];
            unset($menu_array['adv_add']);
        }
        $menu_array[ $menu_key ]['menu_type'] = 'text';
        Tpl::output('menu', $menu_array);
    }
}
