<?php namespace App\Member\Http\Controllers;

/**
 * 默认展示页面
 */
class  IndexControl extends BaseLoginControl {
    
    public function __construct() {
        @header("location: " . urlMember('member_information'));
    }
}
