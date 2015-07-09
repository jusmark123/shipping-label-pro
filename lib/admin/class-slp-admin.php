<?
/**
 * Class Name: 	SLP Admin
 * Package: 	Shipping Label Pro
 * Version: 	3.0.0
 */

class SLP_Admin {	
	public $shipping_methods = array();
	
	public function __construct() {	
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_head', array( $this, 'alter_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}
	
	public function admin_menu() {
		add_menu_page( __( 'Shipping Label Pro', 'slp' ), __( 'Shipping Label Pro', 'slp'), 'manage_options', 'slp_admin', array( $this, 'options_page'), PLUGIN_URL . 'images/icon.png' );
		add_submenu_page( 'slp_admin', __( 'Shipments', 'slp' ), __( 'Shipments', 'slp' ), 'manage_options', 'slp-shipments', array( $this, 'shipments_page') );
		add_submenu_page( 'slp_admin', __( 'Shipping Label Pro Settings', 'slp' ), __( 'Settings', 'slp' ), 'manage_options', 'slp-settings', array( $this, 'setting_page') );
		add_submenu_page( 'slp_admin', __( 'Reports', 'slp' ), __( 'Reports', 'slp' ), 'manage_options', 'slp-reports', array( $this, 'reports_page') );
	}
	
	public function setting_page() {
		include_once( 'class-slp-admin-settings.php' );	
		SLP_Admin_Settings::output();	
	}
	
	public function shipments_page() {
		include_once( 'class-slp-shipments.php' );	
	}
	
	public function reports_page() {
		include_once( 'class-slp-reports.php' );	
	}

	public function enqueue_scripts() {
		
		wp_enqueue_script( 'jquery-timepicker', PLUGIN_URL . 'js/jquery-timepicker-master/jquery.timepicker.js', array( 'jquery') );
		wp_enqueue_script( 'chosen', PLUGIN_URL . 'js/chosen/chosen.jquery.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'admin_functions', PLUGIN_URL . 'js/admin_functions.js', array( 'jquery' ) );
		wp_enqueue_script( 'slp_ajax_functions', PLUGIN_URL . 'js/slp_ajax_functions.js',  array( 'jquery', 'jquery-ui-dialog', 'jquery-ui-button', 'jquery-ui-tabs', 'jquery-ui-datepicker', 'chosen' ) );
		wp_enqueue_script( 'slp_ups_ajax_functions', PLUGIN_URL . 'js/ups_functions.js', array( 'jquery' ) );
		wp_enqueue_script( 'slp_usps_ajax_functions', PLUGIN_URL . 'js/usps_functions.js', array( 'jquery' ) );
		wp_enqueue_script( 'slp_countries-state', PLUGIN_URL . '/js/countries.js', array( 'jquery' ) );
		
		wp_enqueue_style( 'admin_styles', PLUGIN_URL . 'css/slp_admin_css.css' );		
		wp_enqueue_style( 'ui_styles', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'jquery-timepicker', PLUGIN_URL . 'js/jquery-timepicker-master/jquery.timepicker.css' );
		wp_enqueue_style( 'chosen', PLUGIN_URL . 'js/chosen/chosen.min.css' );
	}
	
	public function alter_menu() {
		global $menu, $submenu, $parent_file, $submenu_file, $self, $post_type, $taxonomy;
		
		if ( isset( $submenu['slp_admin'] ) && isset( $submenu['slp_admin'][1] ) ) {
			$submenu['slp_admin'][0] = $submenu['slp_admin'][1];
			unset( $submenu['slp_admin'][1] );
		}
	}
}

return new SLP_Admin();
?>