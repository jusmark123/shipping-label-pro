<?php

/**
 * Class Name: 	SLP General Settings
 * Package: 	Shipping Label Pro
 * Version: 	3.0.0
 */

 if( ! defined( 'ABSPATH' ) ) exit; //exit if accessed directly
 
 if( ! class_exists( 'SLP_General_Settings' ) ) :
 
 class SLP_General_Settings extends SLP_Settings_Page {
	 
	 public function __construct() {

		 $this->id = 'general';
		 $this->label = __( 'General Settings', 'slp' );
		 
		 add_filter( 'slp_settings_tabs_array', array( $this, 'add_settings_page'), 20 );
		 add_action( 'slp_settings_' . $this->id, array( $this, 'output' ) );
		 add_action( 'slp_settings_save_' . $this->id, array( $this, 'save' ) );
	 }
	 
	 public function get_settings() {
		$settings = get_option( 'slp_general_settings' );
		$countries = new WC_Countries();
		
		return apply_filters( 'slp_general_settings', array(	
			array(
				'id'		=> 'slp_location_settings',
				'title' 	=> __( 'Location Settings', 'slp' ),
				'type'		=> 'title',
				'desc'		=> __( 'Tell us about your business', 'slp' ),
				'autoload'  => false,
			),
			array(
				'id'		=> 'company_name',
				'title'		=> __( 'Company Name', 'slp' ),
				'type'		=> 'text',
				'default'	=> '',
				'value'		=> isset( $settings['company_name'] ) ? $settings['company_name'] : '',
				'autoload'	=> false,
			),
			array( 
				'id'		=> 'address_1',
				'title' 	=> __( 'Address 1', 'slp' ),
				'type' 		=> 'text',
				'desc' 		=> __( 'Street Address or PO Box', 'slp' ),
				'default' 	=> '',
				'value'		=> isset( $settings['address_1'] ) ? $settings['address_1'] : '',
				'autoload'  => false,
			),
			array( 
				'id'		=> 'address_2',
				'title' 	=> __( 'Address 2', 'slp' ),
				'type'		=> 'text',
				'desc'		=> __( 'Suite, unit, etc', 'slp' ),
				'default'	=> '',
				'value'		=> isset( $settings['address_2'] ) ? $settings['address_2'] : '',
				'autoload'  => false,
			),
			array( 
				'id'		=> 'address_3',
				'title' 	=> __( 'Address 3', 'slp' ),
				'type'		=> 'text',
				'desc'		=> __( 'Floor, room, ect', 'slp' ),
				'default'	=> '',
				'value'		=> isset( $settings['address_3'] ) ? $settings['address_3'] : '',
				'autoload'  => false,
			),
			array( 
				'id'		=> 'city',
				'title' 	=> __( 'City', 'slp' ),
				'type'		=> 'text',
				'desc'		=> '',
				'default'	=> '',
				'value'		=> isset( $settings['city'] ) ? $settings['city'] : '',
				'autoload'  => false,
			),
			array( 
				'id'		=> 'country',
				'title'		=> __( 'Country', 'slp' ),
				'type'		=> 'select',
				'css'		=> 'width: 200px;',
				'class'		=> 'chosen_select',
				'default'	=> 'US',
				'value'		=> isset( $settings['country'] ) ? $settings['country'] : 'US',
				'settings'	=> $countries->get_countries(),
				'autoload'  => false,
			),
			array( 
				'id'		=> 'state',
				'title'		=> __( 'State', 'slp' ),
				'type'		=> 'select',
				'css'		=> 'width: 200px;',
				'class'		=> 'chosen_select',
				'default'	=> 'AL',
				'value'		=> isset( $settings['state'] ) ? $settings['state'] : '',
				'settings'	=> $countries->get_states( isset( $settings['country'] ) ? $settings['country'] : 'US' ),
				'autoload'  => false,
			),
			array( 
				'id'		=> 'zipcode',
				'title'		=> __( 'Zip Code', 'slp' ),
				'type'		=> 'text',
				'desc'		=> '5 digit zipcode',
				'default'	=> '',
				'tooltip'	=> false,
				'value'		=> isset( $settings['zipcode'] ) ? $settings['zipcode'] : '',
				'autoload'  => false,
			),	
			array( 				
				'id'		=> 'phone',
				'title' 	=> __( 'Phone Number', 'slp' ),
				'type'		=> 'text',
				'desc'		=> '',
				'value'		=> isset( $settings['phone'] ) ? $settings['phone'] : '',
				'autoload'  => false,
			),
			array( 
				'type'		=> 'sectionend',
				'id'		=> 'slp_location_settings',
			),	
		) );  
	 }
	 
	 public function save() {
		$settings = $this->get_settings();
		
		SLP_Admin_Settings::save_fields( $settings ); 
	 }
 }
 endif;
 
 return new SLP_General_Settings();