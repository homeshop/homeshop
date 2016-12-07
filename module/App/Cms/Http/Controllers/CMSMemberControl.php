<?php namespace App\Cms\Http\Controllers;

class  CMSMemberControl extends CMSControl {
    
    public function __construct() {
        parent::__construct();
        if(empty($this->publisher_name)){
            @header('Location: index.php');
            die;
        }
        //发布人信息
        Tpl::output('publisher_info', [
            'name' => $this->publisher_name,
            'id' => $this->publisher_id,
            'type' => $this->publisher_type,
            'avatar' => $this->publisher_avatar,
        ]);
    }
    
    protected function check_article_auth($article_id) {
        if($article_id > 0){
            $model_article = Model('cms_article');
            $article_detail = $model_article->getOne(['article_id' => $article_id]);
            if(!empty($article_detail)){
                if($article_detail['article_publisher_id'] == $this->publisher_id){
                    return $article_detail;
                }
            }
        }
        return false;
    }
    
    protected function check_picture_auth($picture_id) {
        if($picture_id > 0){
            $model_picture = Model('cms_picture');
            $picture_detail = $model_picture->getOne(['picture_id' => $picture_id]);
            if(!empty($picture_detail)){
                if($picture_detail['picture_publisher_id'] == $this->publisher_id){
                    return $picture_detail;
                }
            }
        }
        return false;
    }
    
    /**
     * 删除图片
     */
    protected function drop_image($attachment_path, $image_name) {
        $image = BASE_UPLOAD_PATH . DS . ATTACH_CMS . DS . $attachment_path . DS . $image_name;
        if(is_file($image)){
            unlink($image);
        }
    }
}