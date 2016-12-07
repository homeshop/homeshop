<?php namespace App\Chain\Http\Controllers;

/**
 * 物流自提服务站首页
 */
class  IndexControl extends BaseChainCenterControl {
    public function __construct() {
        parent::__construct();
        redirect('index.php?act=goods');
    }
}
