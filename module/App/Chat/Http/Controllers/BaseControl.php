<?php namespace App\Chat\Http\Controllers;
    
/********************************** 前台control父类 **********************************************/
class  BaseControl {
    
    public function __construct() {
        Language::read('common');
    }
}
