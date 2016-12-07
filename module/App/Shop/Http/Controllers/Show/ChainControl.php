<?php namespace App\Shop\Http\Controllers\Show;

use App\Shop\Http\Controllers\BaseChainControl;
use App\Shop\Http\Controllers\Language;
use App\Shop\Http\Controllers\Tpl;


/**
 * 会员店铺
 */
class  ChainControl extends BaseChainControl {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * 展示门店
     */
    public function indexOp() {
        $chain_id = intval($_GET['chain_id']);
        $chain_info = Model('chain')->getChainInfo(['chain_id' => $chain_id]);
        Tpl::output('chain_info', $chain_info);
        Tpl::showpage('show_chain');
    }
}
