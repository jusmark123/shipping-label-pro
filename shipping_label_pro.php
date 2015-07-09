<?php
/**
 * Plugin Name: 	Shipping Label Pro 
 * Plugin URI:		http://jwalkerdzines.com/plugins/wordpress/slp
 * Author:			Justin Walker - Jwalkerdzines LLC
 * Author URI:		https://jwalkerdzines.com/
 * Description:		Extends UPS, USPS, and FEDEX WooCommerce Method Plugins. Automatically create and track shipments, generate labels, and schedule pickups. Requires WooCommerce Version 2.0 and higher and at least one Woocommerce Shipping Method.
 * Version: 		3.1.0
 * Copyright		2013-2014 JwalkerDzines LLC. All Rights Reserved
 * License:			GPL2
 *
 * This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 **/
if( ! defined( 'ABSPATH' ) ) exit; //exit if accessed directly

if( ! class_exists( 'Shipping_Label_Pro' ) ) :

require_once( 'woo-includes/woo-functions.php' ); 

class Shipping_Label_Pro {	 			
	
	public $version = "3.1.0";
	
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'init', array( $this, 'slp_include_lib' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'add_admin_bar_link' ) );
		add_action( 'woocommerce_view_order', 'slp_display_tracking_info' );
		add_action( 'woocommerce_email_before_order_table', 'slp_display_tracking_info' );
		$this->define_constants();
	}
	
	
	public function add_metabox() {
		add_meta_box( 'slp', __('Shipping Label Pro', 'slp'), array( $this, 'slp_metabox' ), 'shop_order', 'side', 'high' ); 	
	}
	
	public function slp_metabox() {
		include 'lib/slp-meta-box.php';
	}
	
	public function slp_include_lib() {
		include_once 'lib/admin/class-slp-shipping-method.php';
		include_once 'lib/slp-functions.php';
		include_once 'lib/admin/class-slp-admin.php';
		include_once 'lib/class-slp-ajax-functions.php';
				
		wp_enqueue_style( 'dataTables', '//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css');
		wp_enqueue_script( 'dataTables', '//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'moment', PLUGIN_URL . '/js/moment.js', array( 'jquery' ) );
	}
	
	public function add_admin_bar_link() {
		global $wp_admin_bar;

		$url =  'admin.php?page=slp-shipments';
		$wp_admin_bar->add_menu( array(
			'parent' => false,
			'id'	 => 'slp_shipments',
			'title'	 => __( 'Shipments', 'slp' ),
			'href'	 => $url,
		) );	
	}
	
	public function define_constants() {
		define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );	
		define( 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'SLP_VERSION', $this->version );
	}
}

return new Shipping_Label_Pro();

endif;
?>