<?php
/*
 * Class Name: 		SLP Meta Box
 * Version: 		3.1.0
 */
if( ! defined( 'ABSPATH' ) )  exit; //Exit if accessed directly

if( ! class_exists( 'SLP_Meta_Box' ) ) :

class SLP_Meta_Box {
	
	/*
	 * public function constructor
	 * return void
	 * since version 3.0.0
	 */	
	public function __construct() {
		//Global variable for accessing post data
		global $post;
		
		//get shipment data for this order
		$shipment = get_post_meta( $post->ID, '_shipment', true );

		//create instance of WooCommerce order for accessing order data
		$order = wc_get_order( $post->ID );
			
		//Update shipment structure for version compatibility
		$shipment = cleanse_shipment( $order, $shipment );
		
		if( $shipment['_shipment_status'] !== 'Not Shipped' && $shipment['_shipment_status'] !== 'DELIVERED' ) {
			
			if ( (bool)@fsockopen( 'www.canyonwerks.com', 80 ) ) {
				$shipment = slp_ajax_functions::track_shipment( $order, $shipment );
			} else {?>
				<script type="text/javascript">
					$('<div id="message" class="error fade"><p><strong>Connection Error. Please check internet connection.</strong>').insertAfter('#wpbody-content > .wrap > h2');	

				</script><?php
			}
		}
		//display SLP meta box
		$this->get_meta_box( $order, $shipment );
	}
	
	/* 
	 * public function get_meta_box
	 * return void
	 * since version 3.0.0
	 */
	public function get_meta_box( $order, $shipment ) {
		//Global variable for accessing post data
		global $post, $woocommerce;
					
		//set boolean flag for use in javascript code
		$shipped = $shipment['_shipment_status'] === 'Not Shipped' ? 0 : 1; 
		$shipment["_version"] = "3.0.0";
		?>
		
		<!--HTML meta box code start-->	
       	<div id="slp_meta_box">							
            <table>
                <tr><td class="mb_label">Shipping Method:</td></tr>
                <tr><td class="mb_value" id="shipping_method"><?php echo $order->get_shipping_method() ? $order->get_shipping_method() : 'None Selected';?></td></tr>
                <tr><td class="mb_label">Actual Shipping Cost:</td></tr>
                <tr><td class="mb_value" id="shipping_total"><?php echo is_numeric( $shipment['_shipping_cost'] ) ? wc_price( $shipment['_shipping_cost'] ) : $shipment['_shipping_cost']; ?></td></tr>
                <tr>
                	<td class="mb_label" colspan="2">Tracking Number(s): <a href="#" class="ajax_control" title="Click to repack items" id="repack">Repack Items</a></td>
                </tr>
                <tr><td class="mb_value"><?php
				
         if( isset( $shipment['_packages'] ) ) { ?>
                        <table><?php
			//parse through packages for display		
			foreach( $shipment['_packages'] as $key => $package ) { 
				//print_r( $package->TrackingStatus );
			?>
							<tr>
								<td><a href="#" class="ajax_control" id="slip" name="<?php echo $key; ?>" title="Click to view packing slip.">Box<?php echo ($key + 1 ); ?></a>:</td>
                                <td><span id="box<?php echo $key;?>"><?php echo $package->id; ?></span></td>
                            </tr><?php
                    if( isset( $package->ShippingLabel ) ) { ?>
                    		<tr>	
                                <td/>
                                <td><a class="slp_label" href="<?php echo $package->ShippingLabel; ?>" title="Click to view/print labels" target="_blank"class="slp_label"> View Label</a></td><?php
					} ?>
                                </td>
							</tr><?php
			}?> 
						</table><?php
		 } else {?>
					No Packages Found <?php
		 }?>
					</td>
				</tr>
                <tr><td class="mb_label">Shipment Status:</td></tr>
                <tr><td class="mb_value"><strong><span id="shipment_status"><?php echo $shipment['_shipment_status']; ?></span></strong></td></tr>
                <tr>
                	<td class="mb_value">
                    	<button type="button" class="ajax_control dialog_nav" style="float:left;" id="mb_button_1"></button>
                		<!--<button type="button" class="ajax_control dialog_nav" style="float:left;" id="mb_button_2">Print Invoice</button>-->
                    </td>
                </tr>
            </table>
       	</div>
        <!--End HTML/JS start-->
		<script type="text/javascript">
			(function($){
				var text;
				var title;
				var call;
				
				//post object for ajax functions
				var data = {
					post: <?php echo $post->ID; ?>
				}
				
				//set View/Ship button attributes and function
				if( <?php echo $shipped; ?> ) {
					title = 'View shipment details.';
					text  = 'View Shipment';
					call  = 'ViewShipment';
				} else {
					title = 'Ship this order';
					text  = 'Ship Order'; 
					
					if( <?php echo (int)isset( $shipment['_valid_address'] ); ?> ) {
						call = 'GetRates';
					} else {
						call = 'VerifyAddress';	
					}
				}
				
				$('#mb_button_1').text( text ).attr('title', title ).on( 'click', function() {
					data['action'] = 'process_shipment';
					data['call'] = call;
					
					$('.error').remove();
					
					show_processing(true);
					
					ajax_request(data);
				});
				
				//show processing in title meta box title
				show_processing = function( processing ) {			
					if( processing ) {
						$('#slp .hndle span').text('Processing...Please Wait');
					} else {
						$('#slp .hndle span').text('Shipping Label Pro');
					}
				}
				
				$( '#mb_button_2' ).on( 'click', function() {
					
					var newWindow = window.open();
					newWindow.document.write( <?php echo json_encode( $new_order_html ); ?> );
					newWindow.setTimeout( function() {
						newWindow.stop();
					}, 300 );
				});
				
				//set event handler for view packaging slip link
				$('#slip').on('click', function(e) {
					data['action'] = 'get_packing_slip';
					data['box'] = $(this).attr('name');
					
					ajax_request( data );
				});
				
				//set event handler for repack boxes link
				$('#repack').on('click', function() {
					data = {
						action: 'repack_html',
						post: <?php echo $post->ID; ?>
					}
					
					ajax_request( data );
					
				});
				
				$( '.slp_label' ).on( 'click', function(e) {
					e.preventDefault();	
					code = $(this).attr( 'href' ); 
					
					if( code.substring( 0, 4 ) == 'data' ) {
						show_label( code );
					} else {
						window.open( code, '_blank' );
					}
				
				});
			})(jQuery);
		</script>
        <!--End JavaScript Code--><?php
	}
}
endif;

return new SLP_Meta_Box();


