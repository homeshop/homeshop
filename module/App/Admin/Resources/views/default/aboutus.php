
<div class="ncap-about">
  <div class="left-pic"></div>
  <div class="version">
    <h4><?php echo $output['v_date'];?>版</h4>
    <hr>
    <h5>安装日期：<?php echo $output['s_date'];?></h5>
  </div>
  <div class="content">
    <div class="scroll switchbox" >
      <ul class="tema">
        <li>
        </li>
      </ul>
    </div>
    <!-- 代码结束 -->
    <div class="scrollbar switchbox" style="display: none;">
      <div class="law-notice">

      </div>
    </div>
    <div class="switchbox" style="display:none;" >
      <ul>
        <li>
          <h4><?php echo $lang['dashboard_aboutus_idea'];?></h4>
          <p><?php echo $lang['dashboard_aboutus_idea_content'];?></p>
        </li>
        <li>
          <h4>关注我们</h4>
          <p><?php echo $lang['dashboard_aboutus_website'];?>/p>
          <p><?php echo $lang['dashboard_aboutus_website_tip'];?></p>
        </li>
        <li>
          <h4><?php echo $lang['dashboard_aboutus_notice'];?></h4>
          <p><?php echo $lang['dashboard_aboutus_notice4'];?>&nbsp;:&nbsp;&nbsp;jQuery,kindeditor<?php echo $lang['dashboard_aboutus_notice5'];?>.&nbsp;<?php echo $lang['dashboard_aboutus_notice6'];?> </p>
        </li>
      </ul>
    </div>
  </div>
  <div class="btns"><a href="javascript:void(0);" onClick="about_change(0)" class="ncap-btn ncap-btn-green">开发团队</a><a href="javascript:void(0);" onClick="about_change(1)" class="ncap-btn">法律声明</a><a href="javascript:void(0);" onClick="about_change(2)" class="ncap-btn">致用户</a></div>
</div>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.scroll.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/perfect-scrollbar.min.js"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.mousewheel.js"></script>
<link href="<?php echo RESOURCE_SITE_URL;?>/js/perfect-scrollbar.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">
$(function(){
	$("div.scroll").myScroll({
		speed:30,
		rowHeight:60
	});
	$("div.scrollbar").perfectScrollbar();
});

function about_change(i) {
    $(".switchbox").hide().eq(i).show();
    $(".btns > a").removeClass("ncap-btn-green").eq(i).addClass("ncap-btn-green");
    if (i == 0) {
        $("div.scroll").myScroll({
            speed:30,
            rowHeight:60
        });
    }
}
</script>