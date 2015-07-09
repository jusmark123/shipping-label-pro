<?php

/**
 * Class Name: 	SLP Shipping Method
 * Package: 	Shipping Label Pro
 * Author: 		JwalkerDzines LLC - Justin Walker
 * Version: 	3.0.0
 */
 
class SLP_Shipping_Method {
	 
	public $method_id = '';
		
	public $label = '';	 
	 
	public $method_settings = array();
	
	public $plugin_settings = array();

	public $has_settings = true;
	
	public $order_id;
	
	public function init_settings() {
		$settings = get_option( 'slp_' . $this->method_id . '_settings' );
		
		$method_settings = $settings ? get_option( 'slp_' . $this->method_id . '_settings' ) : get_option( 'woocommerce_' . $this->method_id . '_settings' );
		
		return $method_settings;
	}
 }
 
 