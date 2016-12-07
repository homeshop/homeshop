<?php namespace App\Member\Http\Controllers;

class  BaseArticleControl extends Control {
    
    /**
     * 构造函数
     */
    public function __construct() {
        /**
         * 读取通用、布局的语言包
         */
        Language::read('common,core_lang_index');
        /**
         * 设置布局文件内容
         */
        Tpl::setLayout('article_layout');
        /**
         * 获取导航
         */
        Tpl::output('nav_list', rkcache('nav', true));
        /**
         *  输出头部的公用信息
         */
        $this->showLayout();
        /**
         * 文章
         */
        $this->article();
    }
}