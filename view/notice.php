<?php 
/**
 * type
 * code
 * msg
 */
if ( $type == connect2xpp_admin::NOTICE_XPP_PLUGIN ) {?>
<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">
	<style type="text/css">
.xplusplus_activate{
	min-width:825px;
	border:1px solid #111111;
	padding:5px;
	margin:15px 0;
	background:#317ef3;
	background: -webkit-gradient(linear,left bottom,right top,color-stop(0%,#317ef3),color-stop(100%,#111111));
	background: -o-linear-gradient(45deg, #317ef3 0, #111111 100%);
    background: -ms-linear-gradient(45deg,#317ef3 0,#111111 100%);
    background: linear-gradient(45deg,#317ef3 0,#111111 100%);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#317ef3', endColorstr='#111111', GradientType=1);
    webkit-box-shadow: inset 0 3px 7px rgba(0,0,0,.2),inset 0 -3px 7px rgba(0,0,0,.2);
    moz-box-shadow: inset 0 3px 7px rgba(0,0,0,.2),inset 0 -3px 7px rgba(0,0,0,.2);
    box-shadow: inset 0 3px 7px rgba(0,0,0,.2),inset 0 -3px 7px rgba(0,0,0,.2);
	-moz-border-radius:3px;border-radius:3px;
	-webkit-border-radius:3px;
	position:relative;overflow:hidden
}
.xplusplus_activate .aa_a{
	position:absolute;
	top:-5px;
	right:10px;
	font-size:140px;
	color:#317ef3;
	font-family:Georgia, "Times New Roman", Times, serif;z-index:1
}
.xplusplus_activate .aa_button{
	font-weight:bold;
	border-top:1px solid #2d6ca2;
	font-size:15px;
	text-align:center;
	padding:9px 0 8px 0;
	color:#FFF;
	background:#428bca;
	background-image:-webkit-gradient(linear,0% 0,0% 100%,from(#428bca),to(#2d6ca2));
	background-image:-moz-linear-gradient(0% 100% 90deg,#428bca,#2d6ca2);
	-moz-border-radius:2px;
	border-radius:2px;
	-webkit-border-radius:2px
}
.xplusplus_activate .aa_button:hover{
	text-decoration:none !important;
	border:1px solid #029DD6;
	border-bottom:1px solid #00A8EF;
	font-size:15px;
	text-align:center;
	padding:9px 0 8px 0;
	color:#F0F8FB;
	background:#0079B1;
	background-image:-webkit-gradient(linear,0% 0,0% 100%,from(#0079B1),to(#0092BF));
	background-image:-moz-linear-gradient(0% 100% 90deg,#0092BF,#0079B1);
	-moz-border-radius:2px;border-radius:2px;-webkit-border-radius:2px
}
.xplusplus_activate .aa_button_border{
	border:1px solid #006699;
	-moz-border-radius:2px;
	border-radius:2px;
	-webkit-border-radius:2px;
	background:#029DD6;
	background-image:-webkit-gradient(linear,0% 0,0% 100%,from(#029DD6),to(#0079B1));
	background-image:-moz-linear-gradient(0% 100% 90deg,#0079B1,#029DD6)
}
.xplusplus_activate .aa_button_container{
	cursor:pointer;
	display:inline-block;
	background:#DEF1B8;
	padding:5px;
	-moz-border-radius:2px;
	border-radius:2px;
	-webkit-border-radius:2px;width:266px
}
.xplusplus_activate .aa_description{
	position:absolute;
	top:22px;
	left:285px;
	margin-left:25px;
	color:#E5F2B1;
	font-size:15px;z-index:1000
}
.xplusplus_activate .aa_description strong{
	color:#FFF;font-weight:normal
}
</style>
	<form name="xplusplus_activate" action="<?php echo esc_url( connect2xpp_admin::get_page_url() ); ?>" method="POST">
		<div class="xplusplus_activate">
			<div class="aa_a">x</div>
			<div class="aa_button_container" onclick="document.xplusplus_activate.submit();">
				<div class="aa_button_border">
					<div class="aa_button">设置connect2xpp</div>
				</div>
			</div>
			<div class="aa_description">设置API KEY，和xplusplus.cn同步，把技术文章分享给更多的IT技术爱好者！</div>
		</div>
	</form>
</div>
<?php
} else if($type == connect2xpp_admin::NOTICE_SET_KEY_SUS){
?>
<div class="wrap alert active">
<h3 class="key-status">设置API KEY成功!</h3>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_NEW_KEY_EMPTY){?>
<div class="wrap alert critical">
<h3 class="key-status failed">API KEY为空！</h3>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_NEW_KEY_EQUAL_OLD_KEY){?>
<div class="wrap alert critical">
<h3 class="key-status failed">输入的API KEY和已有的相同！</h3>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_HTTP_REQ_ERROR){?>
<div class="wrap alert critical">
<h3 class="key-status failed">请求xplusplus.cn失败！</h3>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_XPP_CODE_NON){?>
<div class="wrap alert critical">
<h3 class="key-status failed">API KEY错误！</h3>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_XPP_MAIL_STATUS_BAD){?>
<div class="wrap alert critical">
<h3 class="key-status failed">xplusplus.cn注册邮箱未激活！</h3>
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
<?php }else if($type == connect2xpp_admin::NOTICE_XPP_GET_USER_NON){?>
<div class="wrap alert critical">
<h3 class="key-status failed">API KEY异常！</h3>
	<p class="description">
		请确认是否在xplusplus.cn更新过API KEY！
	</p>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_XPP_GET_USER_SWICH_OFF){?>
<div class="wrap alert critical">
<h3 class="key-status failed">请在xplusplus.cn端打开开关！</h3>
</div>
<?php }else if($type == connect2xpp_admin::NOTICE_XPP_CODE_USED){?>
<div class="wrap alert critical">
<h3 class="key-status failed">该API KEY已被使用！</h3>
	<p class="description">
		请在xplusplus.cn刷新API KEY，或者在使用该API KEY的独立博客中点击“断开此用户”，停止使用该API KEY！
	</p>
</div>
<?php }?>