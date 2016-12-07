<?php namespace App\Mobile\Http\Controllers;

/**
 * 前台品牌分类
 */
class DocumentControl extends MobileHomeControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function agreementOp() {
        $doc = Model('document')->getOneByCode('agreement');
        output_data($doc);
    }
}
