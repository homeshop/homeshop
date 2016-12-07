<?php namespace App\Delivery\Http\Controllers;

/**
 * 操作中心
 * @author Administrator
 */
class  BaseDeliveryCenterControl extends BaseDeliveryControl {
    
    public function __construct() {
        parent::__construct();
        if($_SESSION['delivery_login'] != 1){
            @header('location: index.php?act=login');
            die;
        }
    }
}