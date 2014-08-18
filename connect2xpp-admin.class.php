<?php
class connect2xpp_admin{
	
	const NONCE = 'connect2xpp-update-key';
	const ADMIN_PAGE_NAME = 'connect_to_xpp';
	const ADMIN_PAGE_TITLE = '连接到xplusplus.cn';
	const ADMIN_PAGE_TAB_NAME = 'connect2xpp';
	
	public static $initiated = false;
	public static $notice = '';
	public static $return_code = 1600;
	public static $return_msg = '';
	
	const NOTICE_NEW_KEY_EMPTY = 'new_key_empty';
	const NOTICE_NEW_KEY_EQUAL_OLD_KEY = 'new_key_equal_old_key';
	const NOTICE_HTTP_REQ_ERROR = 'http_req_error';
	const NOTICE_XPP_CODE_NON	=	'xpp_code_non';//key 不存在
	const NOTICE_XPP_SWITCH_OFF	=	'xpp_switch_off';//xpp端开关未打开
	const NOTICE_XPP_MAIL_STATUS_BAD = 'xpp_mail_status_bad';//xpp端邮箱状态不对
	const NOTICE_XPP_OTHER_ERROR	=	'xpp_other_error';//xpp端其他错误
	const NOTICE_SET_KEY_SUS	=	'set_key_sus';
	
	public static function setReturnCodeAndMsg($code, $msg){
		self::$return_code = $code;
		self::$return_msg = $msg;
	}
	
	public static function init(){
		if(! self::$initiated) {
			self::init_hooks();
		}
		
		if(isset($_POST['action'])){
			if( $_POST['action'] == 'enter_key'){
				self::save_key();
			}else if($_POST['action'] == 'sync_all'){
				self::sync_all();
			}
		}
		if ( isset( $_GET['action'] ) ) {
			if ( $_GET['action'] == 'delete-key' ) {
				if ( isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], self::NONCE ) ){
					delete_option( connect2xpp::$xpp_api_key );
					delete_option(connect2xpp::$xpp_email);
					delete_option(connect2xpp::$xpp_home);
					delete_option(connect2xpp::$xpp_user_name);
				}
			}
		}
	}
	
	public static function init_hooks(){
		
		self::$initiated = true;
		
		/**
		 * publish_post (not deprecated)
		 * Runs when a post is published, or if it is edited and its status is changed to "published".
		 * This action hook conforms to the (status)_(post_type) action hook type. Action function arguments:
		 * post ID, $post object. (See also Post Status Transitions.)
		 */
		add_action( 'publish_post', array('connect2xpp_admin', 'publish_post'), 10, 2);
		
		/**
		 * trashed_post
		 * Runs just after a post or page is trashed. Action function arguments: post or page ID.
		 */
		add_action( 'trashed_post', array('connect2xpp_admin', 'trashed_post'), 10, 1);
		
		/**
		 * untrashed_post
		 * Runs just after undeletion, when a post or page is restored. Action function arguments: post or page ID.
		 */
		add_action( 'untrashed_post', array('connect2xpp_admin', 'untrashed_post'), 10, 1);
		
		add_action( 'admin_menu', array('connect2xpp_admin', 'plugin_menu'));
		
		add_action( 'admin_enqueue_scripts', array( 'connect2xpp_admin', 'load_resources' ) );
	}
	
	public static function sync_all(){
		
	}
	
	public static function save_key(){
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die('cheating!');
		
		if ( !wp_verify_nonce( $_POST['_wpnonce'], self::NONCE ) )
			return false;
		$new_key = trim($_POST['key']);
		if(! $new_key){
			self::$notice = self::NOTICE_NEW_KEY_EMPTY;
			return false;
		}
		$old_key = connect2xpp::get_api_key();
		if($new_key == $old_key){
			self::$notice = self::NOTICE_NEW_KEY_EQUAL_OLD_KEY;
			return false;
		}
		$verify_ret_data = connect2xpp::verify_key($new_key);
		if(false == $verify_ret_data){
			return false;
		}
		connect2xpp::save_api_key($new_key);
		connect2xpp::setUserNameAndEmailAndHome($verify_ret_data['first_name'] . $verify_ret_data['second_name'], 
							$verify_ret_data['email'], $verify_ret_data['home']);
		self::$notice = self::NOTICE_SET_KEY_SUS;
		return true;
	}
	
	public static function get_page_url( $page = 'form' ) {
	
		$args = array( 'page' => self::ADMIN_PAGE_NAME );
		
		if ( $page == 'delete_key' )
			$args = array( 'page' => self::ADMIN_PAGE_NAME, 'action' => 'delete-key', '_wpnonce' => wp_create_nonce( self::NONCE ) );
		
		$url = add_query_arg( $args, admin_url( 'options-general.php' ) );
	
		return $url;
	}
	
	public static function load_resources() {
		global $hook_suffix;
	
		if ( in_array( $hook_suffix, array(
				//'index.php', # dashboard
				//'edit-comments.php',
				//'comment.php',
				//'post.php',
				'settings_page_' . self::ADMIN_PAGE_NAME,
				//'jetpack_page_akismet-key-config',
		) ) ) {
			wp_register_style( 'connect2xpp.css', CONNECT2XPP__PLUGIN_URL . '_inc/connect2xpp.css', array(), CONNECT2XPP_VERSION );
			wp_enqueue_style( 'connect2xpp.css');
	
			wp_register_script( 'akismet.js', CONNECT2XPP__PLUGIN_URL . '_inc/connect2xpp.js', array('jquery','postbox'), CONNECT2XPP_VERSION );
			wp_enqueue_script( 'akismet.js' );
			/*wp_localize_script( 'akismet.js', 'WPAkismet', array(
			'comment_author_url_nonce' => wp_create_nonce( 'comment_author_url_nonce' ),
			'strings' => array(
			'Remove this URL' => __( 'Remove this URL' , 'akismet'),
			'Removing...'     => __( 'Removing...' , 'akismet'),
			'URL removed'     => __( 'URL removed' , 'akismet'),
			'(undo)'          => __( '(undo)' , 'akismet'),
			'Re-adding...'    => __( 'Re-adding...' , 'akismet'),
			)
			) );*/
		}
	}
	
	public static function plugin_menu() {
		add_options_page( self::ADMIN_PAGE_TITLE, self::ADMIN_PAGE_TAB_NAME, 'manage_options', self::ADMIN_PAGE_NAME, array('connect2xpp_admin', 'plugin_options') );
	}
	
	public static function plugin_options() {
		if( connect2xpp::get_api_key()){
			self::display_stat();
		}else{
			self::display_form();
		}
	}
	
	public static function display_form(){
		if(self::$notice == '')
			echo '<h2 class="ak-header">xplusplus.cn</h2>';
		connect2xpp::view('notice', array('type' => self::$notice, 'code' => self::$return_code, 'msg' => self::$return_msg));
		connect2xpp::view('form');
	}
	
	public static function display_stat(){
		connect2xpp::view('notice', array('type' => self::$notice, 'code' => self::$return_code, 'msg' => self::$return_msg));
		connect2xpp::view('stat');
	}
	
	/**
	 * @param unknown $ID
	 * @param unknown $post
	 *
	 * 1.检查用户，验证
	 * 2.检查用户是否有这篇文章
	 * 2.1.如果没有，新增文章
	 * 2.2.如果有，更新文章
	 *
	 */
	function publish_post($ID, $post){
		$data = array('id' => $ID,
				'title' => $post->post_title,
				'content' => $post->post_content,
				'add_timestamp' => $post->post_date);
	
		$url = 	'http://localhost/xplusplus_web/index.php/tool/test_wp';
	
		$ret = wp_safe_remote_post($url, array('body' => json_encode($data)));
		file_put_contents('D:\\log.txt', var_export($ret, true), FILE_APPEND);
	}
	
	function trashed_post($ID){
	
	}
	
	function untrashed_post($ID){
	
	}
}