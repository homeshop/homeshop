<?php
/**
 * Created by PhpStorm.
 * User: shahnuralam
 * Date: 11/9/15
 * Time: 7:44 PM
 */

namespace WPDM\admin\menus;


class Stats
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'Menu'));
    }

    function Menu()
    {
        add_submenu_page('edit.php?post_type=wpdmpro', __('History &lsaquo; Download Manager', "wpdmpro"), __('History', "wpdmpro"), WPDM_MENU_ACCESS_CAP, 'wpdm-stats', array($this, 'UI'));
    }

    function UI()
    {
        include(WPDM_BASE_DIR."admin/tpls/stats.php");
    }


}