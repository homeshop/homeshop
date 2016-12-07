<?php namespace App\Chain\Http\Controllers;

/**
 * 物流自提服务站父类
 */
class  BaseChainControl {
    /**
     * 构造函数
     */
    public function __construct() {
        /**
         * 读取通用、布局的语言包
         */
        Language::read('common');
        /**
         * 设置布局文件内容
         */
        Tpl::setLayout('chain_layout');
        /**
         * SEO
         */
        $this->SEO();
        /**
         * 获取导航
         */
        Tpl::output('nav_list', rkcache('nav', true));
    }
    
    /**
     * SEO
     */
    protected function SEO() {
        Tpl::output('html_title', '门店系统      ' . C('site_name') . '');
        Tpl::output('seo_keywords', '');
        Tpl::output('seo_description', '');
    }
}



