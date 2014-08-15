<?php
/**
 * @package Hello_Xpp
 * @version 1.6
 */
/*
Plugin Name: Hello Xpp
Plugin URI: http://localhost/xplusplus_web/index.php/index
Description: www.xplusplus.cn的插件
Author: 钟志勇
Version: 1.6
Author URI: http://ma.tt/
*/

if ( !function_exists( 'add_action' ) ) {
	echo '请不要直接调用！';
	exit;
}

define( 'CONNECT2XPP_VERSION', '1.0.0' );
//define( 'CONNECT2XPP__MINIMUM_WP_VERSION', '3.0' );
define( 'CONNECT2XPP__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CONNECT2XPP__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
//define( 'CONNECT2XPP_DELETE_LIMIT', 100000 );

register_activation_hook( __FILE__, array( 'connect2xpp', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'connect2xpp', 'plugin_deactivation' ) );

require_once( CONNECT2XPP__PLUGIN_DIR . 'connect2xpp.class.php' );

add_action( 'init', array( 'connect2xpp', 'init' ) );

if ( is_admin() ) {
	require_once( CONNECT2XPP__PLUGIN_DIR . 'connect2xpp-admin.class.php' );
	add_action( 'init', array( 'connect2xpp_admin', 'init' ) );
}
