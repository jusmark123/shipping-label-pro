<?php

/**
 * Class Name: 	SLP Settings Page
 * Package: 	Shipping Label Pro
 * Version: 	3.0.0
 */
 
 if( ! defined( 'ABSPATH' ) ) exit; //exit if accessed directly
 
 if( ! class_exists( 'SLP_Settings_Page' ) ) :
 
 class SLP_Settings_Page {
	
	public $id = '';
	public $label = '';
	
	public function add_settings_page( $pages ) {
		$pages[$this->id] = $this->label;
	
		return $pages;
	}
	
	public function get_settings() {
		return apply_filters( 'slp_get_settings_' . $this->id, array() );	
	}
	
	public function get_sections() {
		return apply_filters( 'slp_get_sections_' . $this->id, array() );	
	}
	
	public function output_sections() {
		global $current_section;
		
		$sections = $this->get_sections();
		
		if( empty( $sections ) ) {
			return;
		}
		
		echo '<ul class="subsubsub">';
		
		$array_keys = array_keys( $sections );
		
		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . admin_url( 'admin.php?page=slp-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . '</li>'; 
		
		}
		
		echo '</ul><br class="clear" />';
	}
	
	public function output() {
		$settings = $this->get_settings();
		
		SLP_Admin_Settings::output_fields( $settings );
	}
	
	public function save() {
		global $current_section;
		
		$settings = $this->get_settings();
		
		SLP_Admin_Settings::save_fields( $settings );
		
		if( $current_section ) {
			do_action( 'slp_update_options_' . $this->id . '_' . $current_section );
		}
	}
 }
 endif;