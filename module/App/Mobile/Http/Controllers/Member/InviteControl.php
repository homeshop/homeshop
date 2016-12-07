<?php namespace App\Mobile\Http\Controllers\Member;

use App\Mobile\Http\Controllers\MobileMemberControl;
use App\Mobile\Http\Controllers\Language;
use App\Mobile\Http\Controllers\Tpl;

/**
 * 我的余额
 */
class InviteControl extends MobileMemberControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 添加返利
     */
    public function indexOp() {
        $member_id = $this->member_info['member_id'];
        $encode_member_id = base64_encode(intval($member_id) * 1);
        $myurl = BASE_SITE_URL . "/#V5" . $encode_member_id;
        $str_member = "memberqr_" . $member_id;
        $myurl_src = UPLOAD_SITE_URL . DS . "shop" . DS . "member" . DS . $str_member . '.png';
        $imgfile = BASE_UPLOAD_PATH . DS . "shop" . DS . "member" . DS . $str_member . '.png';
        if(!file_exists($imgfile)){
            require_once(BASE_RESOURCE_PATH . DS . 'phpqrcode' . DS . 'index.php');
            $PhpQRCode = new PhpQRCode();
            $PhpQRCode->set('pngTempDir', BASE_UPLOAD_PATH . DS . "shop" . DS . "member" . DS);
            $PhpQRCode->set('date', $myurl);
            $PhpQRCode->set('pngTempName', $str_member . '.png');
            $PhpQRCode->init();
        }
        $member_info = [];
        $member_info['user_name'] = $this->member_info['member_name'];
        $member_info['avator'] = getMemberAvatarForID($this->member_info['member_id']);
        $member_info['point'] = $this->member_info['member_points'];
        $member_info['predepoit'] = $this->member_info['available_predeposit'];
        $member_info['available_rc_balance'] = $this->member_info['available_rc_balance'];
        $member_info['myurl'] = $myurl;
        $member_info['myurl_src'] = $myurl_src;
        //下载连接
        $mydownurl = BASE_SITE_URL . "/index.php?act=invite&op=downqrfile&id=" . $member_id;
        $member_info['mydownurl'] = $mydownurl;
        output_data(['member_info' => $member_info]);
    }
}
