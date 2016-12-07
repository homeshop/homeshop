<?php namespace App\Chain\Http\Controllers;

/**
 * 操作中心
 * @author Administrator
 */
class  BaseChainCenterControl extends BaseChainControl {
    public function __construct() {
        parent::__construct();
        if($_SESSION['chain_login'] != 1){
            @header('location: index.php?act=login');
            die;
        }
    }
}