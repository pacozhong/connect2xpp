<?php
class connect2xpp {
	public static $xpp_api_key = 'XPP_API_KEY';
	
	public static function init(){
		
	}
	
	public static function get_api_key(){
		return get_option(self::$xpp_api_key);	
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
}