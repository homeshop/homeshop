<?php

/**
 * 前台分类
 */
class categoryControl extends BaseHomeControl {
    
    /**
     * 分类列表
     */
    public function indexOp() {
        Language::read('home_category_index');
        $lang = Language::getLangContent();
        //导航
        $nav_link = [
            '0' => ['title' => $lang['homepage'], 'link' => SHOP_SITE_URL],
            '1' => ['title' => $lang['category_index_goods_class']]
        ];
        Tpl::output('nav_link_list', $nav_link);
        
        Tpl::output('html_title', C('site_name').' - '.Language::get('category_index_goods_class'));
        Tpl::showpage('category');
    }
}
