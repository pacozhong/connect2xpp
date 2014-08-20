<div class="wrap">
	<h2>connect to xpp</h2>
	<div class="have-key">
		<div id="wpcom-stats-meta-box-container" class="metabox-holder">
			<script type="text/javascript">
			jQuery(document).ready( function($) {
				jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				if(typeof postboxes !== 'undefined')
					postboxes.add_postbox_toggles( 'plugins_page_akismet-key-config' );
			});
			</script>
			<div class="postbox-container" style="width: 55%;margin-right: 10px;">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div id="referrers" class="postbox ">
						<div class="handlediv" title="Click to toggle"><br></div>
						<h3 class="hndle"><span>账户信息</span></h3>
						<form name="akismet_conf" id="akismet-conf" action="<?php echo esc_url( connect2xpp_admin::get_page_url() ); ?>" method="POST">
							<div class="inside">
								<table cellspacing="0">
									<tbody>
										<tr>
											<th width="10%" align="right" scope="row">API KEY</th>
											<td width="5%"/>
											<td align="left" class="setting-item">
												<?php echo connect2xpp::get_api_key(); ?>
											</td>
										</tr>
										<tr>
											<th align="right" scope="row">用户名</th>
											<td></td>
											<td align="left" class="setting-item">
												<?php echo connect2xpp::getUserName();?>
											</td>
										</tr>
										<tr>
											<th align="right" scope="row">邮箱</th>
											<td></td>
											<td align="left" class="setting-item">
												<?php echo connect2xpp::getEmail(); ?>
											</td>
										</tr>
										<tr>
											<th align="right" scope="row">地址</th>
											<td></td>
											<td align="left" class="setting-item">
												<a href="<?php echo connect2xpp::getHome();?>"><?php echo connect2xpp::getHome();?></a>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div id="major-publishing-actions">
								<div id="delete-action">
									<a class="submitdelete deletion" href="<?php echo esc_url( connect2xpp_admin::get_page_url( 'delete_key' ) ); ?>">断开此用户</a>
								</div>
								<?php wp_nonce_field(connect2xpp_admin::NONCE) ?>
								<div id="publishing-action">
								</div>
								<div class="clear"></div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>