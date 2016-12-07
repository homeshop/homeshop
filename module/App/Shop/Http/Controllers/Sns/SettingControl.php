<?php namespace App\Shop\Http\Controllers\Sns;

use App\Shop\Http\Controllers\BaseSNSControl;
use App\Shop\Http\Controllers\Language;
use App\Shop\Http\Controllers\Tpl;


/**
 * 图片空间操作
 */
class  SettingControl extends BaseSNSControl {
    
    public function __construct() {
        parent::__construct();
        /**
         * 读取语言包
         */
        Language::read('sns_setting');
    }
    
    public function change_skinOp() {
        Tpl::showpage('sns_changeskin', 'null_layout');
    }
    
    public function skin_saveOp() {
        $insert = [];
        $insert['member_id'] = $_SESSION['member_id'];
        $insert['setting_skin'] = $_GET['skin'];
        
        Model()->table('sns_setting')->pk(['member_id'])->insert($insert, true);
    }
}
