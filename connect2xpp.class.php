<?php
class connect2xpp {
	public static $xpp_api_key = 'XPP_API_KEY';
	public static $xpp_user_name = 'XPP_USER_NAME';
	public static $xpp_email = 'XPP_EMAIL';
	public static $xpp_home = 'XPP_HOME';
	
	const XPLUSPLUS_API_URL = 'http://localhost/xplusplus_web/index.php/wp/';
	const XPLUSPLUS_API_VERIFY = 'verify';
	const XPLUSPLUS_API_GETUSERINFO = 'get_user_info';
	
	public static function init(){
		
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
		update_option( self::$xpp_api_key, key );
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
			else connect2xpp_admin::$notice = connect2xpp_admin::NOTICE_XPP_OTHER_ERROR;
			return false;
		}
		return $ret_data['data'];
		
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
			load_plugin_textdomain( 'akismet' );
				
			$message = '<strong>'.sprintf(esc_html__( 'Akismet %s requires WordPress %s or higher.' , 'akismet'), AKISMET_VERSION, AKISMET__MINIMUM_WP_VERSION ).'</strong> '.sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Akismet plugin</a>.', 'akismet'), 'https://codex.wordpress.org/Upgrading_WordPress', 'http://wordpress.org/extend/plugins/akismet/download/');
	
			Akismet::bail_on_activation( $message );
		}
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
		self::log( var_export(compact( '$connect2xpp_url', 'http_args', 'response' ), true ) );
		if ( is_wp_error( $response ) ){
			self::log('http response is WPError');
			return false;
		}
		if($response['headers']['code'] != 200){
			self::log('http response code is not 200');
			return false;
		}
		if(! $response['body']){
			self::log('http response body empty');
			return false;
		}
		$retData = json_decode($response['body']);
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