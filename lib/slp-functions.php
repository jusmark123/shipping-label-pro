<?php
/*
 * File Name: slp-functions
 * Version: 3.1.0
 */
 
if( ! defined( 'ABSPATH' ) ) exit; //exit if accessed directly

function get_tracking_url( $carrier, $tracking_number, $postcode ) {
	$tracking_urls = apply_filters( 'slp_supported_providers', array( 
		'UPS' 	=> 'http://wwwapps.ups.com/WebTracking/track?track=yes&trackNums=%1$s',
		'USPS'	=> 'https://tools.usps.com/go/TrackConfirmAction_input?qtc_tLabels1=%1$s',
		'FEDEX'	=> 'http://www.fedex.com/Tracking?action=track&tracknumbers=%1$s',
	) );
	
	$track_link = sprintf( $tracking_urls[$carrier], $tracking_number, urlencode( $postcode ) );
	$track_link = sprintf( __('<a href="%s" target="_blank">%s</a>', 'slp' ), $track_link, $tracking_number );
	
	return $track_link;
}

function get_slp_settings() {
	$settings = get_option( 'shipping-label-pro-settings' );	
		
	return $settings;		
}

function curPageURL() {
	$pageURL = 'http';
  
	if( isset( $_SERVER["HTTPS"] )  && $_SERVER["HTTPS"] == "on" ) {
		$pageURL .= "s";
	}
	
	$pageURL .= "://";
	
	if( $_SERVER["SERVER_PORT"] != "80" ) {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}	
	
	return $pageURL;
}

function get_shipping_methods() {
	$plugins = get_option( 'active_plugins' );

	foreach( $plugins as $key => $value ) {
		if( substr( $value, 0, strlen('woocommerce-shipping') ) == 'woocommerce-shipping' ) {
				
			$method = substr( substr( $value, strrpos( $value, '-' )  + 1 ), 0, strpos( substr( $value, strrpos( $value, '-' )  + 1 ), '.') );
			
			$file = PLUGIN_PATH . 'lib/admin/class-slp-' . $method . '.php';
				
			if( file_exists( $file ) ) {
				$active_methods[$method]['file'] = $file;
				$supported = true;
			} else {
				$supoorted = false;
			}
			
			$active_methods[$method]['supported'] = $supported;
		}
	}
	
	return apply_filters( 'slp_get_active_shipping_methods', $active_methods );
}

function get_accounts( $shipping_method ) {
	$settings = get_slp_settings();
	
	$shipping_method =  trim( preg_replace('#[()]#', '', substr($shipping_method, strpos( $shipping_method, '(' ) ) ) );
	
	$accounts =  $settings[$shipping_method]['accounts'];
	
	$options['default'] = $settings[$shipping_method]['default_account'];
	
	foreach( $accounts as $key => $account ) {
		
		$options['options'][$key] = $shiping_method . ' - ' . $account;	
	}
	
	return $accounts;
}

function slp_display_tracking_info( $order = '' ) {
	
	//track only if order status is complete
	if ( $order->get_status() === 'completed' ) {

		//get shipment data
		$shipment = get_post_meta( $order->id, '_shipment', true );
		//get shipping method
		
		$provider = get_post_meta( $order->id, '_shipping_method', true );
		
		//format text excerpt for display
		if ( strtotime( $shipment['_shipping_date'] ) > current_time( 'time_stamp' ) ) { 
			$verb = 'is scheduled for shipment';
		} else {
			$verb = 'was shipped';
		}
					
		//format shipping date for display
		$date_shipped = ' ' . sprintf( __('on %s', 'slp'), date_i18n( __( 'l, F jS, Y', 'slp'), strtotime( $shipment['_shipping_date'], current_time( 'timestamp' ) ) ) );
		
		ob_start(); ?>
        <!--End PHP/HTML Start-->
        
        <style type="text/css">
			#slp_order_packages {
				border: 0.5px solid #555; 	
			}
			#slp_order_packages thead {
				background: #985F01;
				color: #fff;
			}
			#slp_order_packages th, #slp_order_packages td {
				padding: 0 5px;
				text-align: left;
			}
			#slp_order_packages > tbody tr {
				background: #eee;
				padding:0 5px;
			}
		</style>
        
        <p style="margin:5px 0;">The table below contains up-to-date tracking information. <!--Click the tracking numbers below to view complete tracking history.--></p>
        
		<table id="slp_order_packages" width="100%" style="margin:10px 0;"> 
        	<thead>
        		<tr> 
                	<th>Tracking Number</th>
                    <th>Status Date</th>
                    <th>Status</th>
                    <th>Description</th>
				</tr>
           	</thead>
            <tbody>
				<?php 
        foreach( $shipment['_packages'] as $key => $package ) {
			$tracking_link = get_tracking_url( $tracking_provider, $package->id, $order->shipping_postcode );?>
            <tr>
                <td><?php echo $package->id; ?></td>
                <td><?php echo $package->TrackingStatus['timestamp']; ?></td>
                <td><?php echo $package->TrackingStatus['status']; ?></td>
                <td><?php echo $package->TrackingStatus['desc']; ?></td>
            </tr><?php
		}?>
            </tbody>
        </table>
		
		<!--End HTML/PHP Start--><?php
        
		$tracking_table = ob_get_clean();
		
		$tracking_provider = ' ' . __('via ', 'slp') . '<strong>' . $order->get_shipping_method() . '</strong>';
					
		echo wpautop( sprintf( __( '<p style="margin-top:10px;">This order ' . $verb . '%s%s.</p>', 'slp'), $date_shipped, $tracking_provider ) );
		
		echo wpautop( $tracking_table );
	}
}

function objectToArray( $object ) {
        
	if( !is_object( $object ) && !is_array( $object ) ) {
		return $object;
	}
	
	if( is_object( $object ) ) {
		$object = get_object_vars( $object );
	}
	
	return array_map( 'objectToArray', $object );
}

/*
 * Check shipment array structure and update if needed to be compatible with current SLP version
 *
 * @params mixed $order, mixed $shipment
 * 
 */

function cleanse_shipment( $order, $shipment = array() ) {
	
	//Check if version number is present and is current, return if is upto date
	if( isset( $shipment['_version'] ) && $shipment['_version'] === SLP_VERSION ) return $shipment;
	
	//Check if shipping cost has been calculated
	if( ! isset( $shipment['_shipping_cost'] ) ) {
		
		//Check for shipping cost in order meta first
		if( $shipping_total = get_post_meta( $order->id, '_shipping_cost', true ) ) {
			
			//set shipping cost
			$shipping_cost = $shipping_total;	
		} else {
			//set inital shipment status
			$shipment['_shipment_status'] = 'Not Shipped';
			
			//set shipping cost text
			$shipping_cost = '<span>Shipping Cost Not Calculated</span>';
		}
		
		//add to shipment
		$shipment['_shipping_cost'] = $shipping_cost;
	}
	
	//Get shipment package data
	$shipment = slp_ajax_functions::get_packages( $order, $shipment );
		
	//cleanse shipment if not compatible
	if( ! isset( $shipment['_shipment_status'] ) || $shipment['_shipment_status'] != 'Not Shipped' ) {
	
		//parse through packages
		foreach( $shipment['_packages'] as $key => $package ) {
			
			//try to find tracking number if set in packages
			if( ! isset( $package->id ) ) {
				
				//set tracking number if exists
				if( array_key_exists( '_tracking_numbers', $shipment ) ) {
					$package->id = $shipment['_tracking_numbers'][$key];
					
				//check older versions	
				} else if( $tracking_number = get_post_meta( $order->id, '_tracking_number', true ) ) {
					$package->id =  $tracking_number;
				} else if( $tracking_number = get_post_meta( $order->id, '_tracking_numbers', true ) ) {
					$package->id = $tracking_number[$key];
					
				//default if no tracking number found
				} else {
					$package->id = 'Not Trackable';
				}
			}
			
			//remove tracking_status after update to package data
			if( isset( $package->tracking_status ) )
				unset( $package->tracking_status );
			
			//store shipping labels shipping labels if they exists
			if( array_key_exists( '_shipping_labels', $shipment ) ) {
			
				//check if shipping label is base 64 gif code and format otherwise store as is
				$package->ShippingLabel = preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $shipment['_shipping_labels'][$key] ) ? 'data:image/gif;base64,' . $shipment['_shipping_labels'][$key] : $shipment['_shipping_labels'][$key];
				
			}
		}
		
		//array of keys that will be removed from shipment and post for normalization
		$cleanse = array(
			'_shipment_id',
			'_tracking_numbers',
			'_shipping_labels',
			'_total_weight',
			'_package_count',
			'_service_code',
			'_country_code',
			'_pickup_date',
			'_no_pickup',
			'_number_of_packages',
			'_total_weight',
			'_shipping_cost',
			'_shipping_digest',
			'_tracking_number',
			'_shipment_status',
			'_pickup_date',
			'_shipping_label',
			'_shipment_status',
		);
		
		//remove specified items from shipment and order
		foreach( $cleanse as $index ) {
			if( array_key_exists( $index, $shipment ) ){
				if( ! $index === '_shipping_cost' || ! $index === '_shipment_status' ) {
					unset( $shipment[$index] );	
				}
			}
			delete_post_meta( $order->id, $index );
		}
	}

	//update shipment verision 
	$shipment['_version'] = SLP_VERSION;
	
	//save changes to DB
	update_post_meta( $order->id, '_shipment', $shipment );
	
	//return shipment for further processing
	return $shipment;
}