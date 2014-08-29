<?php
class connect2xpp {
	public static $xpp_api_key		=	'XPP_API_KEY';
	public static $xpp_user_name	=	'XPP_USER_NAME';
	public static $xpp_email		=	'XPP_EMAIL';
	public static $xpp_home			=	'XPP_HOME';
	
	public static $xpp_error		=	'XPP_ERROR';
	
	public static $xpp_last_sync_error	=	'XPP_LAST_SYNC_ERROR';
	
	public static $_status_arr	=	array(1 => 'publish', 2 => 'trash');
	
	const XPLUSPLUS_API_URL			=	'http://localhost/xplusplus_web/index.php/wp/';
	const XPLUSPLUS_API_VERIFY		=	'verify';
	const XPLUSPLUS_API_GETUSERINFO	=	'get_user_info';
	const XPLUSPLUS_API_DELETE_KEY	=	'delete_key';
	const XPLUSPLUS_API_PUBLISH_POST	=	'publish_post';
	const XPLUSPLUS_API_DELETE_POST	=	'delete_post';
	const XPLUSPLUS_API_TRASH_POST	=	'trash_post';
	const XPLUSPLUS_API_UNTRASH_POST	=	'untrash_post';
	const XPLUSPLUS_API_GET_ALL_POST	=	'all_post_list';
	const XPLUSPLUS_API_SYNC_A_POST	=	'sync_post';
	
	
	
	private static $initiated = false;
	
	public static function init(){
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}
	
	/**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;
	
		add_action( 'connect2xpp_sync', array( 'connect2xpp', 'sync' ) );
		
		if ( function_exists('wp_next_scheduled') && function_exists('wp_schedule_event') ) {
			// WP 2.1+: delete old comments daily
			if ( !wp_next_scheduled( 'connect2xpp_sync' ) )
				wp_schedule_event( time(), 'hourly', 'connect2xpp_sync' );
		}
		elseif ( (mt_rand(1, 10) == 3) ) {
			// WP 2.0: run this one time in ten
			self::sync();
		}
	}
	
	public static function getXppError(){
		return get_option(self::$xpp_error);
	}
	public static function setXppError($errorInfo){
		update_option( self::$xpp_error, $errorInfo );
	}
	
	public static function getLastSyncError(){
		return get_option(self::$xpp_last_sync_error);
	}
	
	public static function setLastSyncError($error){
		update_option(self::$xpp_last_sync_error, $error);
	}
	
	public static function setUserNameAndEmailAndHome($user_name, $email, $home){
		update_option( self::$xpp_user_name, $user_name );
		update_option( self::$xpp_email, $email);
		update_option( self::$xpp_home, $home);
	}
	
	public static function getUserName(){
		return get_option(self::$xpp_user_name);
	}
	
	public static function getEmail(){
		return get_option(self::$xpp_email);
	}
	
	public static function getHome(){
		return get_option(self::$xpp_home);
	}
	
	public static function get_api_key(){
		return get_option(self::$xpp_api_key);	
	}
	
	public static function save_api_key($key){
		update_option( self::$xpp_api_key, $key );
	}
	
	
	public static function sync(){
		if(! self::get_api_key()) return false;
		$post_to_sync = self::get_all_post_to_sync();
		$last_sync_error = self::getLastSyncError();
		if(null === $post_to_sync) {
			$last_sync_error[1] = 'post to sync is null';
			self::setLastSyncError($last_sync_error);
			return false;
		}
		unset($last_sync_error[1]);
		self::setLastSyncError($last_sync_error);
		$post_sync = self::get_all_sync_post();
		if(false === $post_sync) return false;
		self::log('post to sync:' . var_export($post_to_sync, true));
		self::log('post sync:' . var_export($post_sync, true));
		$new_post_sync = array();
		foreach ($post_sync as $post) {
			$new_post_sync[$post['wp_id']] = $post;
		}
		foreach ($post_to_sync as $wp_post){
			if($new_post_sync[$wp_post->ID]){
				//check if need update
				if($wp_post->post_modified > $new_post_sync[$wp_post->ID]['mod_timestamp']){
					//update
					self::sync_a_post($wp_post->ID);
				}
			}else {
				//add post to xplusplus
				self::sync_a_post($wp_post->ID);
			}
			unset($new_post_sync[$wp_post->ID]);
		}
		//delete post not exist in wordpress
		foreach ($new_post_sync as $post_id => $post_info){
			self::delete_post_sync($post_id);
		}
	}
	
	
	
	private static function get_all_post_to_sync(){
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare("SELECT ID, post_status, post_modified FROM {$wpdb->posts} WHERE post_type='post' AND post_status IN ('publish', 'trash') "));
	}
	
	private static function sync_a_post($post_id){
		$post_obj = WP_Post::get_instance($post_id);
		$last_sync_error = self::getLastSyncError();
		if(null === $post_obj) {
			$last_sync_error[2] = 'can not get a instance from post id:' . $post_id;
			self::setLastSyncError($last_sync_error);
			return false;
		}
		unset($last_sync_error[2]);
		self::setLastSyncError($last_sync_error);
		
		$request_data = array('id' => $post_id, 'title' => $post_obj->post_title, 'content' => $post_obj->post_content,
				'add_timestamp' => $post_obj->post_modified, 'status' => $post_obj->post_status, 'tags' =>  implode(',', self::get_tags_arr($post_id))
		);
		$ret_data = self::http_post($request_data, self::XPLUSPLUS_API_SYNC_A_POST);
		$error_info = self::getXppError();
		if(false !== $ret_data && $ret_data['code'] == 1600){
			if($error_info[$post_id]){
				unset($error_info[$post_id]);
				self::setXppError($error_info);
			}
			return true;
		}else if(false === $ret_data)
			$error_info[$post_id] = array('code' => -1 , 'msg'=> '其他错误');
		else if($ret_data['code'] != 1600){
			$error_info[$post_id] = array('code' => $ret_data['code'] , 'msg'=> $ret_data['msg']);
		}
		self::setXppError($error_info);
		return false;
	}
	
	private static function get_all_sync_post(){
		$error_info = self::getXppError();
		$ret_data = self::http_post(array(), self::XPLUSPLUS_API_GET_ALL_POST);
		$last_sync_error = self::getLastSyncError();
		if(false != $ret_data && $ret_data['code'] == 1600){
			unset($last_sync_error[3]);
			self::setLastSyncError($last_sync_error);
			return $ret_data['data'];
		}
		
		$last_sync_error[3] = 'requet to xpp error, interface:' . self::XPLUSPLUS_API_GET_ALL_POST;
		self::setLastSyncError($last_sync_error);
		return false;
	}
	
	private static function get_tags_arr($post_id){
		$tags = array();
		$tag_arr = wp_get_post_tags($post_id);
		if(! empty($tag_arr)){
			foreach ($tag_arr as $tag){
				$tags [] = $tag->name;
			}
		}
		return $tags;
	}
	
	public static function publish_post($id, $post){
		$error_info = self::getXppError();
		$ret_data = self::http_post(array('id' => $id,
				'title' => $post->post_title, 'content' => $post->post_content,
				'add_timestamp' => $post->post_date, 'tags' => implode(',', self::get_tags_arr($id))
		), self::XPLUSPLUS_API_PUBLISH_POST);
		if(false != $ret_data && $ret_data['code'] == 1600){
			if($error_info[$id]){
				unset($error_info[$id]);
				self::setXppError($error_info);
			}
			return true;
		}else if(false === $ret_data)
			$error_info[$id] = array('code' => -1 , 'msg'=> '其他错误');
		else if($ret_data['code'] != 1600){
			$error_info[$id] = array('code' => $ret_data['code'] , 'msg'=> $ret_data['msg']);
		}
		self::setXppError($error_info);
		return false;
	}
	
	public static function trash_post($id){
		$error_info = self::getXppError();
		$ret_data = self::http_post(array('id' => $id), self::XPLUSPLUS_API_TRASH_POST);
		if(false != $ret_data && $ret_data['code'] == 1600){
			if($error_info[$id]){
				unset($error_info[$id]);
				self::setXppError($error_info);
			}
			return true;
		}else if(false === $ret_data)
			$error_info[$id] = array('code' => -1 , 'msg'=> '其他错误');
		else if($ret_data['code'] != 1600){
			$error_info[$id] = array('code' => $ret_data['code'] , 'msg'=> $ret_data['msg']);
		}
		self::setXppError($error_info);
		return false;
	}
	
	public static function untrash_post($id){
		$error_info = self::getXppError();
		$ret_data = self::http_post(array('id' => $id), self::XPLUSPLUS_API_UNTRASH_POST);
		if(false != $ret_data && $ret_data['code'] == 1600){
			if($error_info[$id]){
				unset($error_info[$id]);
				self::setXppError($error_info);
			}
			return true;
		}else if(false === $ret_data)
			$error_info[$id] = array('code' => -1 , 'msg'=> '其他错误');
		else if($ret_data['code'] != 1600){
			$error_info[$id] = array('code' => $ret_data['code'] , 'msg'=> $ret_data['msg']);
		}
		self::setXppError($error_info);
		return false;
	}
	
	public static function delete_post($id){
		$error_info = self::getXppError();
		$ret_data = self::http_post(array('id' => $id), self::XPLUSPLUS_API_DELETE_POST);
		if(false != $ret_data && $ret_data['code'] == 1600){
			if($error_info[$id]){
				unset($error_info[$id]);
				self::setXppError($error_info);
			}
			return true;
		}else if(false === $ret_data)
			$error_info[$id] = array('code' => -1 , 'msg'=> '其他错误');
		else if($ret_data['code'] != 1600){
			$error_info[$id] = array('code' => $ret_data['code'] , 'msg'=> $ret_data['msg']);
		}
		self::setXppError($error_info);
		return false;
	}
	
	public static function delete_post_sync($id){
		$ret_data = self::http_post(array('id' => $id), self::XPLUSPLUS_API_DELETE_POST);
		$error_info = self::getXppError();
		if(false != $ret_data && $ret_data['code'] == 1600){
			if($error_info[$id]){
				unset($error_info[$id]);
				self::setXppError($error_info);
			}
			return true;
		}else if(false === $ret_data)
			$error_info[$id] = array('code' => -1 , 'msg'=> '其他错误');
		else if($ret_data['code'] != 1600){
			$error_info[$id] = array('code' => $ret_data['code'] , 'msg'=> $ret_data['msg']);
		}
		self::setXppError($error_info);
		return false;
	}
	
	public static function check_user_info(){
		$key = self::get_api_key();
		if(! $key ){
			return false;
		}
		$user_data = self::http_post(array('key' => $key), self::XPLUSPLUS_API_GETUSERINFO);
		if(false === $user_data){
			connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_HTTP_REQ_ERROR;
			return false;
		}
		if($user_data['code'] != 1600){
			connect2xpp_admin::setReturnCodeAndMsg($user_data['code'], $user_data['msg']);
			if($user_data['code'] == 1601 || count($user_data['data']) == 0){
				connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_XPP_GET_USER_NON;
			}else connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_XPP_OTHER_ERROR;
			return false;
		}
		if($user_data['data']['wp_switch'] != 2){
			connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_XPP_GET_USER_SWICH_OFF;
		}
		self::setUserNameAndEmailAndHome($user_data['data']['first_name'] . $user_data['data']['second_name'], $user_data['data']['email'], $user_data['data']['home']);
		return true;
	}
	
	public static function verify_key($key){
		$ret_data = self::http_post(array('key' => $key), self::XPLUSPLUS_API_VERIFY);
		if(false === $ret_data){
			connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_HTTP_REQ_ERROR;
			return false;
		}
		if($ret_data['code'] != 1600){
			connect2xpp_admin::setReturnCodeAndMsg($ret_data['code'], $ret_data['msg']);
			if($ret_data['code'] == 1601)
				connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_XPP_CODE_NON;
			else if($ret_data['code'] == 1602)
				connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_XPP_SWITCH_OFF;
			else if($ret_data['code'] == 1607)
				connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_XPP_MAIL_STATUS_BAD;
			else if($ret_data['code'] == 1608){
				connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_XPP_CODE_USED;
			}else connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_XPP_OTHER_ERROR;
			return false;
		}
		return $ret_data['data'];
	}
	
	public static function delete_key(){
		$key = self::get_api_key();
		$ret_data = self::http_post(array('key' => $key), self::XPLUSPLUS_API_DELETE_KEY);
		if(false === $ret_data){
			connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_HTTP_REQ_ERROR;
			return false;
		}
		if($ret_data['code'] != 1600){
			connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_XPP_OTHER_ERROR;
			return false;
		}
		return true;
	}
	
	
	public static function view( $name, array $args = array() ) {
	
		foreach ( $args AS $key => $val ) {
			$$key = $val;
		}
	
		$file = CONNECT2XPP__PLUGIN_DIR . 'view/'. $name . '.php';
	
		include( $file );
	}
	
	/**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {
		if ( version_compare( $GLOBALS['wp_version'], CONNECT2XPP__MINIMUM_WP_VERSION, '<' ) ) {
				
			$message = '<strong>'.sprintf( 'connect2xpp %s requires WordPress %s or higher.' , 'akismet', AKISMET_VERSION, AKISMET__MINIMUM_WP_VERSION ).'</strong> '.sprintf('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Akismet plugin</a>.', 'akismet', 'https://codex.wordpress.org/Upgrading_WordPress', 'http://wordpress.org/extend/plugins/akismet/download/');
	
			connect2xpp::bail_on_activation( $message );
		}
	}
	
	private static function bail_on_activation( $message, $deactivate = true ) {
		?>
	<!doctype html>
	<html>
	<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<style>
	* {
		text-align: center;
		margin: 0;
		padding: 0;
		font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
	}
	p {
		margin-top: 1em;
		font-size: 18px;
	}
	</style>
	<body>
	<p><?php echo  $message ; ?></p>
	</body>
	</html>
	<?php
			if ( $deactivate ) {
				$plugins = get_option( 'active_plugins' );
				$connect2xpp = plugin_basename(CONNECT2XPP__PLUGIN_DIR . 'connect2xpp.php' );
				$update  = false;
				foreach ( $plugins as $i => $plugin ) {
					if ( $plugin === $connect2xpp ) {
						$plugins[$i] = false;
						$update = true;
					}
				}
	
				if ( $update ) {
					update_option( 'active_plugins', array_filter( $plugins ) );
				}
			}
			exit;
		}
	
	
	/**
	 * Removes all connection options
	 * @static
	 */
	public static function plugin_deactivation( ) {
		//delete key
		if(self::get_api_key())
			self::delete_key();
		//delete options
		delete_option(self::$xpp_api_key);
		delete_option(self::$xpp_email);
		delete_option(self::$xpp_error);
		delete_option(self::$xpp_home);
		delete_option(self::$xpp_last_sync_error);
		delete_option(self::$xpp_user_name);
		
		//delete cron
		wp_clear_scheduled_hook('connect2xpp_sync');
		
	}
	
	
	public static function http_post( $data, $interface) {
		$connect2xpp_ua = sprintf( 'WordPress/%s | connect2xpp/%s', $GLOBALS['wp_version'], CONNECT2XPP_VERSION );
		
		if(! array_key_exists('key', $data)){
			$api_key   = self::get_api_key();
			if($api_key){
				$data['key'] = $api_key;
			}
		}
		$data['home'] = get_option('home');
		$requestBody = http_build_query($data);
		$content_length = strlen( $requestBody );
		
		$http_args = array(
				'body' => $requestBody,
				'headers' => array(
						'Content-Type' => 'application/x-www-form-urlencoded; charset=' . get_option( 'blog_charset' ),
						'User-Agent' => $connect2xpp_ua,
				),
				'timeout' => 15
		);
	
		$connect2xpp_url = self::XPLUSPLUS_API_URL . $interface;
		$response = wp_remote_post( $connect2xpp_url, $http_args );
		self::log('url:' . $connect2xpp_url );
		self::log('req:' . var_export($http_args, true));
		self::log('reponse:' . var_export($response, true));
		if ( is_wp_error( $response ) ){
			self::log('http response is WPError');
			return false;
		}
		if($response['response']['code'] != 200){
			self::log('http response code is not 200');
			return false;
		}
		if(! $response['body']){
			self::log('http response body empty');
			return false;
		}
		$retData = json_decode($response['body'], true);
		if(false == $retData){
			self::log('http reponse body decode error');
			return false;
		}
		return $retData;
	}
	
	public static function log( $msg ) {
		if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG )
			error_log( $msg ); //send message to debug.log when in debug mode
	}
}