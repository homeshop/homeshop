<?php namespace App\Mobile\Http\Controllers\Article;

use App\Mobile\Http\Controllers\MobileHomeControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 文章
 **/
class ClassControl extends MobileHomeControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function indexOp() {
        $article_class_model = Model('article_class');
        $article_model = Model('article');
        $condition = [];
        $article_class = $article_class_model->getClassList($condition);
        output_data(['article_class' => $article_class]);
    }
}
