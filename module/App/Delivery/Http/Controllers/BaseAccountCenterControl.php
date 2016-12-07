<?php namespace App\Delivery\Http\Controllers;

/**
 * 操作中心
 * @author Administrator
 */
class  BaseAccountCenterControl extends BaseDeliveryControl {
    
    public function __construct() {
        parent::__construct();
        Tpl::setLayout('login_layout');
    }
}