<?php
 /*
 * Class Name: 		SLP Ajax Functions
 * Version: 		3.1.0
 */
if ( !class_exists( 'slp_ajax_functions' ) ) : 

class slp_ajax_functions {
	
	public static $carriers = array();
	
	public static $shipper;
	
	public $shipment = array();

	/**
	 * Ajax function _construct
	 *
	 * hook ajax callback functions
	 * @return void
	 */ 	
	public function __construct() {
		
		$methods = get_shipping_methods();
				
		foreach( $methods as $key => $method ) {
			$class = 'SLP_' . strtoupper( $key );
			
			if( ! class_exists( $class ) ) {
				self::$carriers['key'] = $class;
			}
		}
		
		//ajax callback functions
		$ajax_functions = array(
			'process_shipment',
			'schedule_pickup',
			'edit_address',
			'get_shipments',
			'void_shipment',
			'track_shipment',
			'track_history',
			'update_pickup',
			'update_shipments',
			'get_packing_slip',
			'get_labels',
			'get_states',
			'get_rates',
			'repack_html',
			'reset_order'
		);
		
		//Hook ajax callback functions
		foreach( $ajax_functions as $ajax_function ) {
			add_action( 'wp_ajax_' . $ajax_function, array( $this, $ajax_function ) );
		}
	}	
	
	/**
	 * Ajax function Process Shipment
	 *
	 * Creates shipment 
	 */
	public function process_shipment() {
		
		//sanitize $_POST data before use
		$_POST = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
		
		//create instance of WooCommerce order for accessing order data
		$order = wc_get_order( $_POST['post'] );
		
		//create shipper object and get class instance													
		self::$shipper = $this->get_shipper( $order );
		
		//get shipment data
		$shipment = get_post_meta( $order->id, '_shipment', true );
		
		//confirm address has been verified by user
		switch( $_POST['call'] ) {
			case 'VerifyAddress':
				self::$shipper->verify_address( $order, $shipment );
				break;
			case 'GetRates':
				if( isset( $_POST['address'] ) ) {
					if( is_array( $_POST['address'] ) ) {
						$order->set_address( $_POST['address'], 'shipping' );
					}
					$shipment['_valid_address'] = true;
					$this->update_shipment( $order->id, $shipment );
				}
			
				if( isset( $_POST['customs'] ) ) {
					$shipment['_customs'] = $_POST['customs'];
				}
				
				self::$shipper->get_rates( $order, $shipment );
				break;
			case 'CreateShipment':
				self::$shipper->create_shipment( $order, $shipment );
				break;	
			case 'ViewShipment':
				self::confirm_shipment( $order, $shipment );	
			case 'validate_tracking':
				$this->track_shipment( $order, $shipment );
				break;
		} 	
	}	
	
   /**
	* Conducts address verfication for new shipments with specified carrier
	*
	* @params array $xml containing validated address data
 	* @params mixed $order for order we are working with
 	* @params array $shipment for order we are working with
	*
 	* @return mixed JSON Array object
 	*/
	public static function verify_address( $xml, $order, $shipment ) {
		
		$countries = new WC_Countries();
		
		if( ! valid ) {			
			//domestic array for differentiating between domestic and internation shipments
			$domestic = array( "US", "PR", "VI" );
			
			//assign returned address to variable for further processing
			$address = isset( $xml['address'] ) ? $xml['address'] : '';
							
			//store and format data 	
			$name 	 = strtoupper( $order->shipping_first_name . ' ' . $order->shipping_last_name );
			$company = strtoupper( $order->shipping_company );
			$csz = strtoupper( $order->shipping_city . ', ' . $order->shipping_state. ', ' . $order->shipping_country . ' ' . $order->shipping_postcode );
			
			//get shipment type domestic or international
			$customs = ! in_array( $order->shipping_country, $domestic );
			
			//set initial title for dialog
			$title = 'Step 1: Address Verification'; 
	
			//clear buffer for html code
			ob_start(); 
			
			include( 'templates/slp-verfiy-address.php' );
			   
			if( $customs ) { ?>
			<!---Get customs form for international shipments--> <?php
				self::get_customs_form( $order, $shipment );
			}
	
  			$xml = array( 
            	'Success'  	 	=> true,
            	'StatusMessage' => ob_get_clean(),
            	'Title'			=> $title,
				'Post' 			=> $order->id,
				'Address'		=> $address
        	);

	 		self::send_json( $xml );
		} else {
			self::$shipper->get_rates( $order, $shipment );
		}
	}
	
	public function get_customs_form( $order, $shipment ) {
		//get woocommerce countries/state array
		$countries = new WC_Countries(); 
        
        $settings = get_option( 'slp_general_settings' );
		
		$ob_start();
		
		include( 'templates/slp-customs.php' );       	
       	
		return ob_get_clean();
    }
	
	public static function confirm_rates( $order, $shipment ) {	
		
		self::update_shipment( $order->id, $shipment );	
				
		$packages = $shipment['_packages'];
	
		$shipping_cost = $order->get_total_shipping();
		$shipping_total = $shipment['_shipping_cost'];
		
		ob_clean();
		
		if( $shipping_total > $shipping_cost ) {
			
			$title = "Step $step: Confirm Package Detail and Rates";

			include( 'templates/slp-confirm-rates.php' );		
		} else {
			
			$title = 'Confirm Shipment Details';
			
			include( 'templates/slp-confirm-shipment.php' );
		}

		$xml = array(
			'Success'		=> true,
			'Title' 		 	=> $title,
			'StatusMessage' => ob_get_clean(),
			'Post' 			=> $_POST['post']
		);

		self::send_json( $xml );
	}
	
	public static function confirm_shipment( $order, $shipment, $reload = false ) {
		if( $shipment['_shipment_status'] !== 'DELIVERED' ) {
			self::update_shipment($order->id, $shipment);
		} 
		
		if( $reload ) {
			$order_note = $order->get_shipping_method() . ' shipment processed on ' . date( 'm/d/Y' ) . '. '; 
			$order->update_status( 'completed', $order_note );
		}
		
		ob_start();?>
		
		<?php
		
		$xml = array(
			'Success' 		=> true,
			'Title'   		=> "Shipment Details",
			'StatusMessage' => ob_get_clean(), 
			'Post' 			=> $order->id,
		);
	
		if( $reload ) {
			$xml['Reload'] = true;	
		}
		
		self::send_json( $xml );
	}
	
	public function get_boxes() {
		
		$boxes = self::$shipper->get_boxes();
		
		return $boxes;	
	}
	
	/**
	 * Ajax function voids shipment
	 *
	 * Voids Shipment and returns XML response
	 * @return mixed array
	 */
	public function void_shipment() {
		global $current_user;
						
		$order = wc_get_order( $_POST['post'] );
		
		self::$shipper = $this->get_shipper( $order );
	
		$method = self::$shipper->get_method();
		
		$void_request = self::$shipper->void_shipment();
						
		if( $void_request['ResponseStatusCode'] == 1 ) {
			$this->update_order( $order_id, $shipment );
		} 
												
		$this->send_json( $void_request );
	}

	/**
	 * Ajax function send_json
	 *
	 * Returns json encoded object to JS for processing
	 * @return mixed array
	 */
	 public function send_json( $xml_response ) {
		 		 
		echo json_encode( $xml_response ); 
		die();
	 }

	
	public function repack_html() {
		
		$count = 0;
		
		$order = wc_get_order( $_POST['post'] );
		
		$shipment = $order->_shipment;
				
		$shipment = get_post_meta( $_POST['post'], '_shipment', true );
		
		self::$shipper = $this->get_shipper( $order );
		
		$boxes = self::$shipper->get_boxes();
						
		ob_start();

		include( 'templates/slp-repack.php' );
       
		$xml = array(
			'Success' 		=> true,
			'Title' 		=> 'Repack Boxes',
			'StatusMessage' => ob_get_clean(),
			'NoContinue' 	=> true,
			'CloseButton' 	=> 'Close',
			'packages'		=> $shipment['_packages'],
			'Post'			=> $order->id
		);
		   	
		$this->send_json( $xml );
	}

	/**
	 * Ajax function get_packing_slip
	 *
	 * Returns packing slip for display
	 * @return mixed array
	 */
	public function get_packing_slip() {
		
		$order_id = $_POST['post'];
		
		$order = wc_get_order( $order_id );
		
		$box = $_POST['box'];
					
		$shipment = get_post_meta( $order_id, '_shipment', true );
			
		$packages = $shipment['_packages'];
		
		if( $packages ) {
		
			$package = $packages[$box];
			
			ob_start();
			
			include( 'templates/slp-packing-slip.php' );
			
		} else {
			self::error_handler( __FUNCTION__, __CLASS__, __LINE__, 'Package Info Not Found' );	
		}
		
		$response = array(
			'Success' 	 	=> true,
			'StatusMessage' => ob_get_clean(),
			'Title'			=> 'Packing Slip for Order# ' . $order_id,
			'Post' 			=> $_POST['post'],
			'CloseButton'	=> 'Close',
			'NoContinue'	=> true
		);

		$this->send_json( $response );
	}
	
	public function get_shipments() {
		global $wpdb, $woocommerce;
			
		$shipments = array();
		
		$posts = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type LIKE %s AND post_status LIKE %s ORDER BY ID", 'shop_order', 'wc-completed' ) );
				
		foreach( $posts as $key => $post) {
			
			$order = wc_get_order( $post->ID );
			
			$shipment = get_post_meta( $order->id, '_shipment', true );
			
			$shipment = cleanse_shipment( $order, $shipment );
					
			$order_number = get_post_meta( $order->id, '_order_number' ) ? get_post_meta( $order->id, '_order_number', true ) : $order->id;

			$service = $order->get_shipping_method();
						
			$carrier = explode( ',' , $service );
			
			$carrier = trim( preg_replace('#[()]#', '', substr( $carrier[0], strpos( $carrier[0], '(' ) ) ) );	

			$order_link = '<a href="' . admin_url( 'post.php?post=' . $order->id . '&action=edit' ) . '">' . $order_number . '</a>';
			
			foreach( $shipment['_packages'] as $key => $package ) {
				$trackingNumbers = get_tracking_url( $carrier, $package->id, $order->shipping_postcode );
			}
			
			if( is_array( $shipment['_shipment_status'] ) ) {
				$shipment['_shipment_status'] = $shipment['_shipment_status'][0];
				self::update_shipment( $order-id, $shipment );
			}
			
			/*if( ! isset( $status['status'] ) || ( ! $status['status'] == "DELIVERED" && ! $status['status'] == 'Not Trackable' ) ){
				$shipment = self::track_shipment( $order->id, $shipment );	
			}*/
						
			array_push( $shipments, array(
				'<input type="checkbox" class="sel_shipments" id="' . $order->id . '"/>', 
				$order_link,
				ucwords( $order->billing_first_name ) . ' ' . ucwords( $order->billing_last_name ),
				$trackingNumbers,
				$service,
				$shipment['_shipping_date'],
				$shipment['_shipment_status'],
				wc_price( $shipment['_shipping_costs'] )
			));
		}
		
		echo json_encode( $shipments );
		die();
	}
	
	public function get_contents( $order, $package ) {
		$totals = 0;	
		
		foreach( $package->packed as $key => $item ) {
			$item_ids[] = $item['meta']['id'];
		}
		
		$items = $order->get_items();
		$counts = array_count_values( $item_ids );
		
		foreach( $counts as $key => $qty ) {
			$product = wc_get_product( $key );
			$parent = wc_get_product( $product->parent->post->ID );
			$item = get_post_meta( $key );
			
			$price = $product->price;
			$total = $price * $qty; 
			$totals += $total;?>
        
			<tr>
            	<td class="slp_thumb"><?php 
			if( $product ) { ?>
				<a href="<?php echo esc_url( admin_url( 'post.php?post=' . absint( $product->id ) . '&action=edit' ) ); ?>" class="tips" data-tip="<?php

				echo '<strong>' . __( 'Product ID:', 'slp' ) . '</strong> ' . absint( $product->id );

				if ( $product->variation_id && 'product_variation' === get_post_type( $product->variation_id ) ) {
					echo '<br/><strong>' . __( 'Variation ID:', 'slp' ) . '</strong> ' . absint( $product->variation_id);
				} elseif ( $product->variation_id ) {
					echo '<br/><strong>' . __( 'Variation ID:', 'slp' ) . '</strong> ' . absint( $product->variation_id ) . ' (' . __( 'No longer exists', 'slp' ) . ')';
				}

				if ( $product && $product->get_sku() ) {
					echo '<br/><strong>' . __( 'Product SKU:', 'slp' ).'</strong> ' . esc_html( $product->get_sku() );
				}

				if ( $product && isset( $product->variation_data ) ) {
					echo '<br/>' . wc_get_formatted_variation( $product->variation_data, true );
				}?>">

			 	<?php echo $product->get_image( 'shop_thumbnail', array( 'title' => '' ) ); ?></a><?php
			} else { 
				 echo wc_placeholder_img( 'shop_thumbnail' );
            }?>
				<td><a href="<?php echo admin_url( "post.php?post=$product->id&action=edit" ); ?>"><?php echo $product->sku ? $product->sku . ' - ' . $product->post->post_title: $product->post->post_title; ?></a></td>
				<td><?php echo $qty; ?></td>
				<td style="text-align:right;"><?php echo wc_price( $price ); ?></td>
				<td style="text-align:right;"><?php echo wc_price( $total ); ?></td>
			</tr><?php
		} ?>
       <tr>
          <td colspan="4" style="border-top:1px solid #999; text-align:right;"><?php _e( 'Package Value' , 'slp' ); ?></td>
          <td style="border-top:1px solid #999; text-align:right;"><?php echo wc_price( $totals ); ?></td>
      </tr>
      <tr/><?PHP
	}
	
				
	
	public static function error_handler( $function, $class, $line, $order, $error, $detail = " " ) { ?>
		
		<script type="text/javascript">
			(function($) {
				$('.dialog').dialog('destroy');
				$('<div id="message" class="error fade"><p><strong><?php echo $error . ' order# ' . $order->id . ' - ' . $detail; ?></strong>').insertAfter('#wpbody-content > .wrap > h2');	
			})(jQuery);
		</script><?php
		
		die();
	}
	
	
	/**
	 * Ajax function update_shipment
	 *
	 * Updates order shipment meta as needed during code execution
	 * @return void
	 */
	public static function track_shipment( $order, $shipment ) {
		if( ! is_object( $order ) ) {
			$order = wc_get_order( $order );
		}
		
		//create carrier class instance only if not already created
		if( ! isset( $shipper ) || ! in_array( $shipper, self::$carriers ) ) {
			$shipper = self::get_shipper( $order );
		}
	
		//get shipment tracking								
		$shipment = $shipper->track_shipment( $shipment, $order );
	
	//create order note when tracking status changes
		if( $shipment['_shipment_status'] != $status ) {
			$order->add_order_note( 'USPS Shipment Status Updated: ' . $status, 'shipping_label_pro' );
		}
	
		//save updates
		self::update_shipment( $order->id, $shipment );
	
		return $shipment;
	}	 

	public function update_shipments( $orders ) {
		$orders = $_POST['shipments'];
		
		//var_dump( $_POST['shipments'] ) ;

		foreach( $orders as $post ) {
			$shipment = get_post_meta( $post, '_shipment', true );	
			$order = wc_get_order( $post );
			$shipper = self::get_shipper( $order );
			$shipment = self::track_shipment( $order, $shipment );
			$shipments[] = $post;
		}
	
		echo $shipments;
		die();
	}
	
	public function track_history() {
		$shipment = get_post_meta( $_POST['post'], '_shipment', true );
		$order = wc_get_order( $_POST['post'] );
		
		//get packages
		$packages = $shipment['_packages'];
		
		$shipper = get_post_meta( $_POST['post'], '_shipping_method', true );
		
		ob_start();
		
		include( 'templates/slp-tracking-info.php' );
				
		$xml = array(
			'Succes' 		=> true,
			'StatusMessage'	=> ob_get_clean(),
			'options' 		=> array(
				'modal'		=> true,
				'title' 	=> 'Shipping Label Pro Shipment Tracking',
				'width' 	=> 1000,
				'autoOpen' 	=> true,
			),
			'Post' 			=> $_POST['post']
		);
		
		echo json_encode( $xml );
		die();
	}
	
	private function get_shipper( $order ) {
		 
		//get shipping carrier from order data
		$carrier = $order->get_shipping_methods();
		$carrier = $carrier[key($carrier)]['item_meta']['method_id'][0];

		//trim special characters from carrier title for use in function
		$carrier = substr( $carrier, 0, strpos( $carrier, ':' ) );
		
		//locate carrier in carrier array and create instance of class if needed
		if( ! array_keys( self::$carriers, $carrier ) ) {
			$class = 'SLP_' . $carrier;

			if( ! class_exists( $class ) ) {
				include( 'admin/class-slp-' . strtolower( $carrier ) . '.php' );
				
				self::$carriers[$carrier] = new $class();
			}
		}
		
		//update post meta with cleansed carrier title
		update_post_meta( $order->id, '_shipping_method', $carrier );
		
		//return instance of carrier class
		return self::$carriers[$carrier];	
		
	}
	
   /**
	* Retrieves order items and packs them into boxes by dimensions
	*
 	* @params mixed $order for order we are working with
 	* @params array $shipment for order we are working with
	*
 	* @return mixed array
 	*/
	public static function get_packages( $order, $shipment ) {
		global $woocommerce;
		
		//load box packer if not already loaded
		if( !class_exists( 'WC_Boxpack' ) ) {
			include_once( WP_CONTENT_DIR . '/plugins/woocommerce-shipping-usps/includes/box-packer/class-wc-boxpack.php' );
		}
		
		//pack items if not set
		if( ! isset( $shipment['_packages'] ) ) {							
			$boxpack = new WC_Boxpack();
			
		 	self::$shipper = self::get_shipper( $order );
			
			//get boxes from shipper class settings
			$boxes = self::$shipper->get_boxes();
					
			//Add Standard and Custom Boxes
			if ( ! empty( $boxes ) ) {
				foreach( $boxes as $key => $box ) {
					$newbox = $boxpack->add_box( $box['outer_length'], $box['outer_width'], $box['outer_height'], $box['box_weight'] );
					$newbox->set_inner_dimensions( $box['inner_length'], $box['inner_width'], $box['inner_height'] );
					$newbox->set_max_weight( $box['max_weight'] );
					$newbox->set_id = $key;
				} 
			}
			
			//retrieve order items for packing
			$items = $order->get_items();
			
			//add order items
			foreach( $items as $key => $item ) {
				$product = $order->get_product_from_item( $item );
				$item_key = $key;
				
				$dim = explode( ' ', str_replace( ' x ', ' ', $product->get_dimensions() ) );
				
				for( $i = 0; $i < $item['qty']; ++$i ) {
					$boxpack->add_item(
						number_format( wc_get_dimension( $dim[0], 'in'), 2 ), 
						number_format( wc_get_dimension( $dim[1], 'in'), 2 ),
						number_format( wc_get_dimension( $dim[2], 'in'), 2 ),
						number_format( wc_get_weight( $product->get_weight(), 'lbs' ), 2 ),
						$product->get_price(),
						array(
							'id' => $item['variation_id'] ? $item['variation_id'] : $item['product_id'],
						)
					);
				}
			}
			//Pack Items into boxes & return
			$boxpack->pack();
		
			//get packed items
			$shipment['_packages'] = $boxpack->get_packages();
		} 
			
		//normalize array for later use
		if( ! is_array( $shipment['_packages'][0]->packed[0] ) ) {
			
			//Parse through items and convert std objects to arrays
			foreach( $shipment['_packages'] as $package ) {
				
				//if no tracking number set 
				if( ! isset( $package->id ) || empty( $package->id ) ) 
					$package->id = 'No Tracking# Assigned';
				
				//remove unnecessay data
				unset( $package->unpacked );	
				
				//convert WC_Item to array
				foreach( $package->packed as $key => $line ) {
					$line = (array) $line;
					array_shift( $line );
					$package->packed[$key] = $line;
				}
			}
			
			//save changes to DB
			//self::update_shipment( $order->id, $shipment );
		}
		
		//return shipment for further processing
		return $shipment;
	}
	
	
	public function update_shipment( $post_id, $shipment ) {
		
		update_post_meta( $post_id, '_shipment', $shipment );
		
	}
	
	public function get_states() {
		$country = isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : '';
		$countries = new WC_Countries();
		$states = $countries->get_states( $country );
		
		$this->send_json( $states ); 
			
	}
	
	public function reset_order() {
		if( isset( $_POST['post_id'] ) ) {
			$sucess = delele_post_meta( $_POST['post_id'], '_shipment' );	
		}
		
		$response = array(
			'Success' => $success,
			'reload'  => $success
		);
		
		echo json_encode( $response );
		die();
	}
}

new slp_ajax_functions();

endif;
?>