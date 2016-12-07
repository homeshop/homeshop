<?php namespace App\Chain\Http\Controllers;

/**
 * 操作中心
 * @author Administrator
 */
class  BaseAccountCenterControl extends BaseChainControl {
    public function __construct() {
        parent::__construct();
        Tpl::setLayout('login_layout');
    }
}
