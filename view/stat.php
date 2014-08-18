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
								<table cellspacing="0" class="akismet-settings">
									<tbody>
										<tr>
											<th width="10%" align="left" scope="row">API KEY</th>
											<td width="5%"/>
											<td align="left">
												<span class="api-key"><?php echo connect2xpp::get_api_key() ?></span>
											</td>
										</tr>
										<tr>
											<th align="left" scope="row">用户名</th>
											<td></td>
											<td align="left">
												<p>
													<?php echo connect2xpp::getUserName();?>
												</p>
											</td>
										</tr>
										<tr>
											<th class="strictness" align="left" scope="row">邮箱</th>
											<td></td>
											<td align="left">
												<p><?php echo connect2xpp::getEmail(); ?></p>
											</td>
										</tr>
										<tr>
											<th class="strictness" align="left" scope="row">地址</th>
											<td></td>
											<td align="left">
												<p><a href="<?php echo connect2xpp::getHome();?>"><?php echo connect2xpp::getHome();?></a></p>
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