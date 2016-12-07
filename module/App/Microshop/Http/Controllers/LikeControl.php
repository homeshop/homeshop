<?php namespace App\Microshop\Http\Controllers;

/**
 * 微商城喜欢
 */
class  LikeControl extends MircroShopControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 喜欢保存
     **/
    public function like_saveOp() {
        $data = [];
        $data['result'] = 'true';
        $data['message'] = Language::get('microshop_like_success');
        $like_id = intval($_GET['like_id']);
        $like_type = self::get_channel_type($_GET['type']);
        if($like_id <= 0 || empty($like_type)){
            $data['result'] = 'false';
            $data['message'] = Language::get('wrong_argument');
            self::echo_json($data);
        }
        if(!empty($_SESSION['member_id'])){
            $param = [];
            $param['like_type'] = $like_type['type_id'];
            $param["like_object_id"] = $like_id;
            $param['like_member_id'] = $_SESSION['member_id'];
            $model_like = Model('micro_like');
            $is_exist = $model_like->isExist($param);
            if(!$is_exist){
                $param['like_time'] = time();
                $result = $model_like->save($param);
                if($result){
                    //喜欢计数加1
                    $model = Model();
                    $update = [];
                    $update['like_count'] = ['exp', 'like_count+1'];
                    $condition = [];
                    $condition[ $like_type['type_key'] ] = $like_id;
                    $model->table("micro_{$_GET['type']}")->where($condition)->update($update);
                    //返回信息
                    $data['result'] = 'true';
                } else {
                    $data['result'] = 'false';
                    $data['message'] = Language::get('nc_common_save_fail');
                }
            } else {
                $data['result'] = 'false';
                $data['message'] = Language::get('microshop_like_fail');
            }
        } else {
            $data['result'] = 'false';
            $data['message'] = Language::get('no_login');
        }
        self::echo_json($data);
    }
    
    /**
     * 喜欢删除
     **/
    public function like_dropOp() {
        $data['result'] = 'false';
        $data['message'] = Language::get('nc_common_del_fail');
        $like_id = intval($_GET['like_id']);
        if($like_id > 0){
            $model_like = Model('micro_like');
            $like_info = $model_like->getOne(['like_id' => $like_id]);
            if($like_info['like_member_id'] == $_SESSION['member_id']){
                $result = $model_like->drop(['like_id' => $like_id]);
                if($result){
                    $data['result'] = 'true';
                    $data['message'] = Language::get('nc_common_del_succ');
                }
            }
        }
        self::echo_json($data);
    }
}
