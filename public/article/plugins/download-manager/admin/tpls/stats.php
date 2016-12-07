
<div class="wrap w3eden" style="margin-top: 45px">

    <div class="panel panel-primary" id="wpdm-wrapper-panel">
        <div class="panel-heading">
            <b><i class="fa fa-bar-chart-o"></i> &nbsp; <?php echo __('Download History','wpdmpro'); ?></b>

        </div>

        <div class="tab-content" style="padding: 15px;">
<?php 

$type = WPDM_BASE_DIR."admin/tpls/stats/history.php";

include($type);

?>
</div>
</div>

    <style>
        .notice, .updated{
            display: none !important;
        }
    </style>