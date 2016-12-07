<?php namespace App\Cms\Http\Controllers;

class  CMSHomeControl extends CMSControl {
    
    public function __construct() {
        parent::__construct();
        $model_navigation = Model('cms_navigation');
        $navigation_list = $model_navigation->getList(true, null, 'navigation_sort asc');
        Tpl::output('navigation_list', $navigation_list);
        $model_article_class = Model('cms_article_class');
        $article_class_list = $model_article_class->getList(true, null, 'class_sort asc');
        $article_class_list = array_under_reset($article_class_list, 'class_id');
        Tpl::output('article_class_list', $article_class_list);
        $model_picture_class = Model('cms_picture_class');
        $picture_class_list = $model_picture_class->getList(true, null, 'class_sort asc');
        $picture_class_list = array_under_reset($picture_class_list, 'class_id');
        Tpl::output('picture_class_list', $picture_class_list);
        Tpl::output('index_sign', 'index');
        Tpl::output('top_function_block', true);
    }
    
    /**
     * 推荐文章
     */
    protected function get_article_comment() {
        $model_article = Model('cms_article');
        $condition = [];
        $condition['article_commend_flag'] = 1;
        $article_commend_list = $model_article->getListWithClassName($condition, null, 'article_id desc', '*', 9);
        Tpl::output('article_commend_list', $article_commend_list);
    }
}