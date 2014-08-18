<?php 
/**
 * type
 * code
 * msg
 */
if($type == connect2xpp_admin::NOTICE_SET_KEY_SUS){
?>
<div class="wrap alert active">
<h3 class="key-status">设置xplusplus.cn的api key成功!</h3>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_NEW_KEY_EMPTY){?>
<div class="wrap alert critical">
<h3 class="key-status failed">api key为空！</h3>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_NEW_KEY_EQUAL_OLD_KEY){?>
<div class="wrap alert critical">
<h3 class="key-status failed">输入的api key和已有的相同！</h3>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_HTTP_REQ_ERROR){?>
<div class="wrap alert critical">
<h3 class="key-status failed">请求xplusplus.cn失败！</h3>
	<p class="description">
		错误码：<?php echo $code;?><BR>
		错误信息:<?php echo $msg;?>
	</p>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_XPP_CODE_NON){?>
<div class="wrap alert critical">
<h3 class="key-status failed">api key错误！</h3>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_XPP_MAIL_STATUS_BAD){?>
<div class="wrap alert critical">
<h3 class="key-status failed">邮箱未激活！</h3>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_XPP_SWITCH_OFF){?>
<div class="wrap alert critical">
<h3 class="key-status failed">没有在xplusplus.cn端打开开关！</h3>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_XPP_OTHER_ERROR){?>
<div class="wrap alert critical">
<h3 class="key-status failed">其他错误！</h3>
	<p class="description">
		错误码：<?php echo $code;?><BR>
		错误信息:<?php echo $msg;?>
	</p>
</div>
<?php }?>