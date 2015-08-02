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
			'repack_html'
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
		
		//var_dump( $boxes );
				
		ob_start();
		
		foreach( $shipment['_packages'] as $key => $package ) {?>
           <div style="vertical-align:top; display:inline-block; margin: 0 20px;">
            	<table class="header_table" width="100%">
                	<tr>
						<td>Box<?php echo $key + 1; ?></td>
                    </tr>
                    <tr>
                    	<td>Type</td>
                        <td>
                            <select id="slp_box_select"><?php
                             foreach( $boxes as $index => $box ) {
                                $box = (object)$box;
								$dimensions = "L: $box->inner_length in W: $box->inner_width in H: $box->inner_height in ";
                                if( $package->length == $box->inner_length && $package->width == $box->inner_width && $package->height == $box->inner_height ) {
                                    $selected = 'selected="selected"';
                                    $max_weight = $box->max_weight;
									$package->box_weight = $box->box_weight;
									$box_volume = $box->inner_length * $box->inner_height * $box->inner_width;
									$package->percent = ( $package->volume/$box_volume ) * 100;
                                } else {
                                    $selected = '';
                                }?>
                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo isset( $box->name ) ? $box->name : $dimensions; ?></option><?php
                             } ?>
                            </select>
                        </td>	
                    </tr>
                    <tr>
                        <td>Dimensions</td>
						<td id="dimensions">L: <?php echo $package->length; ?> in W: <?php echo $package->width; ?> in H: <?php echo $package->height; ?>in</td>          
					</tr>
                    <tr>
                    	<td>Item Count</td>
                        <td id="item_count"><?php echo sizeof( $package->packed ); ?></td>
                    </tr>
                   	<tr>
                		<td>Max Weight</td>
                        <td id="max_weight"><?php echo number_format( $max_weight, 2 ); ?> lbs</td>
                    </tr>
                    <tr>
            			<td>Space Used</td>
						<td id="space_used"><?php echo number_format( $package->percent, 2 ); ?>%</td>
                    </tr>
                    <tr>
                    	<td>Space Free</td>
                        <td id="free_space"><?php echo number_format( 100 - $package->percent, 2 ); ?> %</td>
                    </tr>	
                    <tr>
                		<td>Total Weight</td>
						<td id="current_weight"><?php echo number_format( $package->weight, 2 ); ?> lbs</td>
                    </tr>
            	</table>
				<ul style="vertical-align:top; display:inline-block" id="sortable_<?php echo $key; ?>" class="connectedSortable"><?php
				foreach( $package->packed as $index => $item ) {
					$product = get_product( $item['meta']['id'] );?>
              	 	<li id="item_<?php echo $index; ?>" class="ui-state-default"><span><?php echo $product->get_title(); ?></span></li><?php
				} ?>
            	</ul>
			</div><?php
            $package->calc_volume = $box_volume;
		}?>
        <div class="clear"></div>
   		<p><button type="button" id="reset" class="dialog_nav" Title="Undo Changes">Undo Changes</button><button type="button"id="calculate_rate" class="dialog_nav">Calculate Shipping</button></p>
		
        <script type="text/javascript">
			( function($) {
								
				$( '.dialog' ).on( 'dialogopen', function() {
					$( '.connectedSortable' ).sortable({ connectWith: '.connectedSortable' }).disableSelection();
						
					$('.connectedSortable').on( 'sortreceive', function( event, ui ) {
						packages = $(this).box_pack( ui, packages.length > 0 ? packages : response.packages );
					});
				});
				
				$.fn.box_pack = function( ui, packages ) {
					var destin = this.attr( 'id' ). split( '_' )[1];
					var origin = ui.sender.attr( 'id' ).split( '_' )[1];
					var index = ui.item.attr( 'id' ).split( '_' )[1];
					var item = packages[origin].packed[index];
					
					packages[origin].packed = $.makeArray( packages[origin].packed );
					packages[origin].packed.splice( index, 1 );
					packages[destin].packed = $.makeArray( packages[destin].packed );
					packages[destin].packed.splice( ui.item.index(), 0, item );
					
					var boxes = {
						origin: {
							id:	origin,
							package: packages[origin],
							parent: ui.sender.parent(),
							sortable: ui.sender
						},
						destin:	{
							id: destin,
							package: packages[destin],
							parent: this.parent(),
							sortable: this
						},
					}; 
			
					for( var box in boxes ) {
						var box_volume = boxes[box].package.length * boxes[box].package.height * boxes[box].package.width;
						var calc_volume = 0;
						var calc_weight = 0 + boxes[box].package.box_weight;
						var calc_value = 0;
						var packed = boxes[box].package.packed;
						for( var i = 0; i < packed.length; ++i ) {
							$(boxes[box].sortable).children().eq(i).attr( 'id', 'item_' + i );
							calc_volume += parseFloat( packed[i].length * packed[i].width * packed[i].height );		
							calc_weight += parseFloat( packed[i].weight );	
							calc_value += parseFloat( packed[i].value );
						}
					
						var percent = parseFloat( calc_volume/box_volume );
					
						boxes[box].package.percent = percent;	
						boxes[box].package.volume = calc_volume;
						boxes[box].package.weight = calc_weight;
						
						packages[boxes[box].id] = boxes[box].package;
						
						var $parent = boxes[box].parent;
						
						$parent.find( '#item_count' ).text( boxes[box].package.packed.length ).end().find( '#space_used' ).text( parseFloat( percent * 100 ).toFixed(2)  + '%').end().find( '#free_space' ).text( 1 - percent <= 0 ? 0 + '%' : ( ( 1 - percent ) * 100 ).toFixed(2)  + '%').end().find( '#current_weight' ).text( parseFloat( calc_weight ).toFixed(2) + ' lbs');
						
						var package = boxes[box].package;
						
						if( percent > 1 || package.weight > package.max_weight ) {
							$parent.find( '#space_used' ).css( 'color', 'red' );
							$('#dialog_message').text( 'One or more boxes have exceeded its capacity. Ensure items will fit properly prior to shipping.' ); 
						} else {
							$parent.find( '#space_used' ).css( 'color', '' );
							$('#dialog_message').text('');
						}
					}
					return packages;
				}
			});
		</script>
		
		<?php
       
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
			
			ob_start(); ?>
 
            <div>
                <p>For packing purposes only. Be sure to check order for variation details.</p>
                <table style="width:100%;">    
                    <tr>
                        <td class="table_label">Package</td>
                        <td id="box_key"><?php echo $box + 1; ?></td>
                    </tr>
                        <td class="table_label">Tracking Number</td>
                        <td colspan="2" id="tracking_number"><?php echo isset( $package->id ) ? $package->id : 'Not Generated'; ?></td>
      					<td id="tracking_buttons"><button class="ajax_control dialog_nav" id="edit_tracking" name="<?php echo $box; ?>" title="Click to change tracking number.">Update Tracking Number</button></td>
                    <tr>	
                        <td class="table_label">Package Dimensions:</td>
                        <td colspan="2">L=<?php echo $package->length; ?> in W=<?php echo $package->width; ?> in H=<?php echo $package->height; ?> in Weight=<?php echo number_format( $package->weight, 2 ); ?> lbs</td>
                    </tr>
                </table>
                <table width="100%">
                    <thead>
                        <tr class="table_label">
                            <th><?php _e( 'Product ID', 'slp'); ?></th>
                            <th><?php _e( 'Product', 'slp'); ?></th>
                            <th><?php _e( 'Quantity', 'slp'); ?></th>
                            <th><?php _e( 'Unit Price', 'slp'); ?></th>
                            <th><?php _e( 'Subtotal', 'slp'); ?></th>
                        </tr>
                    </thead>
                    <tbody><?php
                    	$this->get_contents( $order, $package );?>
                    </tbody>
                </table> 
                <p><button type="button" class="dialog_nav no_continue" id="print">Print</button></p><?php
                if( sizeof( $packages ) > 1 ) { 
				?>
					<p><?php
					if( $box > 0 ) { ?>
						<button class="dialog_nav slip_nav" id="prev" value="<?php echo $box - 1; ?>">Prev Package</button><?php
					}
					if( $box < sizeof( $packages ) - 1 ) {?>
						<button class="dialog_nav slip_nav" id="next" value="<?php echo $box + 1; ?>">Next Package</button><?php
					}?> 
                    </p><?php
				} ?>
            </div>
			<script type="text/javascript">
				(function($) {;
					
					//Hide unnecessary buttons
					$( '.ui-dialog-buttonset' ).children().eq(1).hide();
					$( '.ui-dialog-buttonset' ).children().eq(2).button( 'option', 'label', 'Close' );

					//set packing slip action link event handler
					$( '.slip_nav' ).on( 'click', function() {
						var data = {
							action: 'get_packing_slip',
							box: $( this ).val().trim(),
							post: <?php echo $_POST['post']; ?>
						}
						
						ajax_request( data )
					});
					
					
					//set edit tracking button event handler
					$( '#edit_tracking' ).one( 'click', editTracking );
					
					
					//edit tracking function
					function editTracking( e ) {
						var tracking_number = $( '#tracking_number' ).text();
						var button_text = $( this ).text();
					
						$( '#tracking_number' ).html( '<input type="text" value="' + tracking_number + '" id="tracker" />' );
						$( '#tracker' ).focus().select();
						$( '<button></button>' ).appendTo( '#tracking_buttons' ).addClass( 'dialog_nav' ).text( 'Cancel' ).one( 'click', { text: button_text, tracking_number: tracking_number }, cancelUpdate );
						$( this ).text( 'Update' ).on( 'click', updateTracking );
					};
					
					
					//cancel edits function
					function cancelUpdate( e ) {
						$( '#tracking_number' ).empty().text( e.data.tracking_number );
						$( '#tracker' ).remove();
						$( this ).remove();
						$( '#edit_tracking' ).text( e.data.text ).one( 'click', editTracking );
					}
					
					//validate and set new tracking number
					function updateTracking( e ) {
						var key = parseInt( $( '#box_key' ).text() ) - 1;
						var data = {
							action: 'process_shipment',
							call: 'validate_tracking',
							post: <?php echo $order_id; ?>,
							tracking_number: $.trim( $( '#tracker' ).val() ),
							box: key
						}
						
						$.post( ajaxurl, data, function( response ) {
							response = $.parseJSON( response );
														
							if( response.Success == true ) {
								$( '#dialog_message' ).html( '<p>Tracking number has been updated. Tracking information will be updated after clicking close</p>' ).css( 'color', 'black' );
								$( '#box' + key ).text( response.TrackingNumber );	
								$( '#tracking_number' ).text( response.TrackingNumber );
								$( '#shipment_status' ).text( response.ShipmentStatus ); 
								$( '#tracker' ).remove();
								$( '#tracking_buttons' ).children().eq( 1 ).remove();
								$( '#edit_tracking' ).text( 'Update Tracking Number' ).one( 'click', editTracking );
								$( '.dialog' ).on( 'dialogclose', function() {
									window.location.reload();
								});
							} else {
								$( '#dialog_message' ).html( '<p>The tracking number entered is not valid. Please enter a valid tracking number or click cancel.</p>').css( 'color', 'red' );
							}
						});
					}
					
				})(jQuery);
			</script>
            <style>
				.tips{
					 cursor: help;
   					 text-decoration: none;
				}
				.slp_thumb img {
					padding: 1px;
					margin: 0px;
					border: 1px solid #DFDFDF;
					vertical-align: middle;
					width: 50px;
					height: 50px;
				}
				#tracker {
					width: 300px;
				}
			</style>
			<?php
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
	
	public function validate_tracking() {
		
		
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
		
		//if packages are loaded process	
		if( ! empty( $packages ) ) {
			foreach( $packages as $key => $package ) {?>
            <div>			
                <table>
                	<tbody>
                	    <tr>
                    		<td>Tracking Number</td>
							<td><?php echo $package->id ?></td>
                    	</tr>
                    	<tr>   
                    		<td>Date Shipped</td>
                    	    <td><?php echo date( 'm/d/Y', strtotime( $shipment['_shipping_date'] ) );?></td>
                    	</tr>
                    	<tr>
                    		<td>Shipping Method</td>
                    	    <td><?php echo $order->get_shipping_method(); ?></td>
                    	</tr>
                    	<tr>
                    		<td>Shipment Status</td>
                    	    <td><?php echo $shipment['_shipment_status']; ?></td>
                    	</tr><?php
						if( $shipment['_shipment_status'] === 'DELIVERED' && ! empty( $package->tracking_status[0]['signer'] ) ) {?>
						<tr>
                        	<td>Signed By</td>
                            <td><?php echo  $package->tracking_status[0]['signer'];	?></td>	 
                        </tr>
                      <?php } ?>  
                   	</tbody> 
                </table>	
            	<table style="overflow-y:scroll; max-height:400px; width:100%;">
                  <thead>
                      <tr>
                          <th>Date</th>
                          <th>Status</th>
                          <th>Description</th>
                          <th>Location</th>
                      </tr>
                  </thead>	
                  <tbody><?php
				foreach( $package->tracking_status as $tracking ) { ?>                 
                      <tr>
                          <td><?php echo $tracking['timestamp']; ?></td>
                          <td><?php echo $tracking['status']; ?></td>
                          <td><?php echo $tracking['desc']; ?></td>
                          <td><?php echo $tracking['location']; ?></td>
                      </tr>        
		 <?php } ?>  	
         		</tbody> 
            </table> 
			<div><?php
			}
		}
		
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
		$carrier = $order->get_shipping_method();
		
		//trim special characters from carrier title for use in function
		$carrier = explode( ',' , $carrier );
		
		$carrier = trim( preg_replace('#[()]#', '', substr( $carrier[0], strpos( $carrier[0], '(' ) ) ) );
		
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
}

new slp_ajax_functions();

endif;
?>