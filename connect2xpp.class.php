<?php
class connect2xpp {
	public static $xpp_api_key		=	'XPP_API_KEY';
	public static $xpp_user_name	=	'XPP_USER_NAME';
	public static $xpp_email		=	'XPP_EMAIL';
	public static $xpp_home			=	'XPP_HOME';
	
	public static $xpp_error		=	'XPP_ERROR';
	
	const XPLUSPLUS_API_URL			=	'http://localhost/xplusplus_web/index.php/wp/';
	const XPLUSPLUS_API_VERIFY		=	'verify';
	const XPLUSPLUS_API_GETUSERINFO	=	'get_user_info';
	const XPLUSPLUS_API_DELETE_KEY	=	'delete_key';
	const XPLUSPLUS_API_PUBLISH_POST	=	'publish_post';
	const XPLUSPLUS_API_DELETE_POST	=	'delete_post';
	const XPLUSPLUS_API_TRASH_POST	=	'trash_post';
	const XPLUSPLUS_API_UNTRASH_POST	=	'untrash_post';
	const XPLUSPLUS_API_UPDATE_POST	=	'update_post';
	
	
	public static function init(){
		
	}
	
	public static function getXppError(){
		return get_option(self::$xpp_error);
	}
	public static function setXppError($errorInfo){
		update_option( self::$xpp_error, $errorInfo );
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
	
	
	
	public static function publish_post($id, $post){
		//get tags
		$tags = array();
		$tag_arr = wp_get_post_tags($id);
		if(! empty($tag_arr)){
			foreach ($tag_arr as $tag){
				$tags [] = $tag->name;
			}
		}
		$error_info = self::getXppError();
		$ret_data = self::http_post(array('id' => $id,
				'title' => $post->post_title, 'content' => $post->post_content,
				'add_timestamp' => $post->post_date, 'tags' => implode(',', $tags)
		), self::XPLUSPLUS_API_PUBLISH_POST);
		if(false != $ret_data && $ret_data['code'] == 1600){
			if($error_info[$id]){
				unset($error_info[$id]);
				self::setXppError($error_info);
			}
			return true;
		}
		if(false === $ret_data){
			$error_info [$id] = '-1';
		}else if($ret_data['code'] != 1600){
			$error_info [$id] = $ret_data['code'];
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
		}
		if(false === $ret_data){
			$error_info [$id] = '-1';
		}else if($ret_data['code'] != 1600){
			$error_info [$id] = $ret_data['code'];
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
		}
		if(false === $ret_data){
			$error_info [$id] = '-1';
		}else if($ret_data['code'] != 1600){
			$error_info [$id] = $ret_data['code'];
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
		}
		if(false === $ret_data){
			$error_info [$id] = '-1';
		}else if($ret_data['code'] != 1600){
			$error_info [$id] = $ret_data['code'];
		}
		self::setXppError($error_info);
		return false;
	}
	
	public static function update_post($id, $post){
		echo 'test add tags';
		$tags = var_export(wp_get_post_tags($id), true);
		self::log('tags info:' . $tags);
	}
	
	public static function add_tags(){
		
	}
	
	public static function delete_tags(){
		
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
		//tidy up
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