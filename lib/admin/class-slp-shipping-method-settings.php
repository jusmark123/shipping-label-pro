<?php

/**
 * Class Name: 	SLP shipping Method Settings
 * Package: 	Shipping Label Pro
 * Version: 	3.0.0
 */
 
 if( ! defined( 'ABSPATH' ) ) exit; //exit if accessed directly
 
 if( ! class_exists( 'SLP_Shipping_Method_Settings' ) ) :
 
 class SLP_Shipping_Method_Settings extends SLP_Settings_Page {
	 
	public $methods = array();
	 
	public function __construct() {
		
		$this->id = 'shipping_methods';
	 	$this->label = __( 'Shipping Method Settings', 'slp' );
		$this->load_methods();
		
		add_filter( 'slp_settings_tabs_array', array( $this, 'add_settings_page'), 20 );
	 	add_action( 'slp_settings_' . $this->id, array( $this, 'output' ) );
	 	add_action( 'slp_settings_save_' . $this->id, array( $this, 'save' ) );
	 	add_action( 'slp_sections_' . $this->id, array( $this, 'output_sections' ) ); 
	}
	
	public function load_methods() {
		$active_methods = get_shipping_methods();
		foreach( $active_methods as $key => $method ) {
			if( $method['supported'] ) {
				$class = 'SLP_' . strtoupper( $key );
				
				if( ! class_exists( $class ) ) {
					include_once( $method['file'] );
				}
				
				$this->methods[$key] = new $class; 
			}
		}
	}

	
	public function get_sections() {
		$sections = array();
			
		foreach( $this->methods as $method ) {
			$sections[sanitize_title( $method->method_id )] = $method->label;
		}
		
		return apply_filters( 'slp_get_sections_' . $this->id, $sections );
	 }
	 
	 public function get_settings() {
		
		return apply_filters( 'slp_shipping_method_settings', array(	
	
		) );  
	 }
	 
	 public function output() {
		global $current_section;
		
		reset( $this->methods );
		
		$current_tab 	 = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] );
		$current_section = empty( $_REQUEST['section'] ) ? key( $this->methods ) : sanitize_title( $_REQUEST['section'] );
		
		foreach( $this->methods as $method ) {
			if( $current_section == $method->method_id ) {
				add_action( 'slp_settings_save_' . $this->id, array( $this, 'output' ) );

				SLP_Admin_Settings::output_fields( $method->get_settings() );	
			}
		}
	 }
	 
	 public function save() {
		global $current_section; 
		
		if( $current_section ) {
			$method = $this->methods[$current_section];
			$settings = $method->get_settings();
		}
		
		SLP_Admin_Settings::save_fields( $settings );
	 }
 }
 endif;
 
 return new SLP_Shipping_Method_Settings();