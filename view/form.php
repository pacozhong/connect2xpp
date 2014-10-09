<div class="no-key config-wrap">
<p>这个插件可以同步当前独立博客的文章到您的xplusplus.cn账户</p>
<div class="activate-highlight activate-option">
	<div class="option-description">
		<strong>激活connect2xpp</strong>
		<p>登录或注册xplusplus.cn账户来获取您的API KEY。</p>
	</div>
	<form name="connect2xpp_activate" action="http://www.xplusplus.cn/" method="POST" target="_blank">
		<input type="submit" class="right button button-primary" value="获取API KEY"/>
	</form>
</div>
<div class="activate-highlight secondary activate-option">
	<div class="option-description">
		<strong>手工输入API KEY</strong>
		<p>如果您知道您的API KEY。</p>
	</div>
	<form action="<?php echo esc_url( connect2xpp_admin::get_page_url() ); ?>" method="post" id="connect2xpp-enter-api-key" class="right">
		<input id="key" name="key" type="text" size="15" value="" class="regular-text code">
		<input type="hidden" name="action" value="enter-key">
		<?php wp_nonce_field( connect2xpp_admin::NONCE ); ?>
		<input type="submit" name="submit" id="submit" class="button button-secondary" value="使用此API KEY">
	</form>
</div>
</div>