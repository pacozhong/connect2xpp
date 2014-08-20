<div class="no-key config-wrap">
<p>这个插件可以让你同步文章</p>
<div class="activate-highlight activate-option">
	<div class="option-description">
		<strong>激活xplusplus.cn</strong>
		<p>登录或创建账户来获取您的API密钥。</p>
	</div>
	<form name="akismet_activate" action="http://localhost/xplusplus_web/index.php/index" method="POST" target="_blank">
		<input type="submit" class="right button button-primary" value="获取你的api密钥"/>
	</form>
</div>
<div class="activate-highlight secondary activate-option">
	<div class="option-description">
		<strong>手工输入API密钥</strong>
		<p>如果您知道您的API密钥。</p>
	</div>
	<form action="<?php echo esc_url( connect2xpp_admin::get_page_url() ); ?>" method="post" id="connect2xpp-enter-api-key" class="right">
		<input id="key" name="key" type="text" size="15" value="" class="regular-text code">
		<input type="hidden" name="action" value="enter-key">
		<?php wp_nonce_field( connect2xpp_admin::NONCE ); ?>
		<input type="submit" name="submit" id="submit" class="button button-secondary" value="使用此密钥">
	</form>
</div>
</div>