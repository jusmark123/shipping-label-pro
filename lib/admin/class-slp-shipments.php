<?php 
/**
 * Class Name: 	SLP Shipments
 * Package: 	Shipping Label Pro
 * Version: 	3.0.0
 */
 
 if( ! defined( 'ABSPATH' ) ) exit;
 
 if( ! class_exists( 'SLP_Shipments' ) ) :
 
 class SLP_Shipments {
	 
	 public $shipments = array();	
	 
	 public $pages;
	
	 public function __construct() {
		add_action( 'slp_load_shipments', array( $this, 'load_shipments' ) );
		add_action( 'slp_table_footer', array( $this, 'table_footer' ) );
		
		include_once( 'pages/page-admin-shipments.php' ); 
		
	 }	

 }
 
 endif;
 
 new SLP_Shipments(); 