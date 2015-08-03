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
  <p>
    <button type="button" class="dialog_nav no_continue" id="print">Print</button>
  </p><?php
     if( sizeof( $packages ) > 1 ) { ?>
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