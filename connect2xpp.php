<?php
/**
 * @package connect2xpp
 * @version 1.0
 */
/*
Plugin Name: connect2xpp
Plugin URI: http://www.xplusplus.cn/doc/connect2xpp
Description: connect2xpp可以把独立技术博客聚合到<a href="http://www.xplusplus.cn/">xplusplus.cn</a>。xplusplus.cn是IT技术精英的知识分享社区，聚合了<a href="http://www.xplusplus.cn/connect2xpp#top-user">众多技术大牛的独立博客</a>。
Author: 钟志勇
Version: 1.0
Author URI: http://www.xplusplus.cn/doc/connect2xpp
Text Domain: connect2xpp
*/

if ( !function_exists( 'add_action' ) ) {
	echo '请不要直接调用！';
	exit;
}

define( 'CONNECT2XPP_VERSION', '1.0.0' );
define( 'CONNECT2XPP__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CONNECT2XPP__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'connect2xpp', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'connect2xpp', 'plugin_deactivation' ) );

require_once( CONNECT2XPP__PLUGIN_DIR . 'connect2xpp.class.php' );

add_action( 'init', array( 'connect2xpp', 'init' ) );

if ( is_admin() ) {
	require_once( CONNECT2XPP__PLUGIN_DIR . 'connect2xpp-admin.class.php' );
	add_action( 'init', array( 'connect2xpp_admin', 'init' ) );
}
