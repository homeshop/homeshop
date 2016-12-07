<?php namespace App\Delivery\Http\Controllers;

/**
 * 物流自提服务站首页
 */
class  IndexControl extends BaseDeliveryControl {
    
    public function __construct() {
        parent::__construct();
        @header('location: index.php?act=login');
        die;
    }
}
