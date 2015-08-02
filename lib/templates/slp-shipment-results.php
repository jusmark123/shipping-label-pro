<p>Shipment for Order <strong><?php echo $order->get_order_number(); ?></strong>. View details below:</p>
<?php
		
if( isset( $shipment['_errors'] ) ) { ?>
	<p>The following shipment errors occurred:</p><?php
	foreach( $shipment['_errors'] as $key => $error ) {?>
		<p style="background: none repeat scroll 0% 0% lightyellow;"><img src="<?php echo PLUGIN_URL; ?>/images/error.ico" width="16" height="16" /> <?php echo $error['error']; ?> <a href="#" id="shipment_error_<?php echo $key; ?>" class="fix_error">Fix</a></p><?php
	}
} else { ?>
    <section id="view_shipment"><?php
	if( isset( $shipment['_message'] ) ) { ?>
      <p><?php echo $shipment['_message']; ?></p><?php
    }?>
      <table>
        <tbody>
          <tr>
            <td>Shipping Date</td>
            <td><?php echo date( 'm/d/Y', strtotime( $shipment['_shipping_date'] ) ); ?></td>
          </tr>
          <tr>
            <td>Shipping Cost</td>
            <td><?php echo wc_price( $shipment['_shipping_cost'] ); ?></td>
          </tr>
          <tr>
            <td>Status</td>
            <td><?php echo $shipment['_shipment_status']; ?></td>
          </tr>
        </tbody>
      </table>
	</section><?php
}?>
	<section>
      <table width="100%">
        <thead>
          <tr>
            <th>Box#</th>
            <th>Tracking Number</th>
            <th>Shipping Cost</th>
            <th>Shipping Label</th>
          </tr>
        </thead>
        <tbody><?php
    foreach( $shipment['_packages'] as $key => $package ) { ?>
          <tr>
            <td><?php echo $key + 1; ?></td>
            <td><?php echo $package->id == 'No Tracking# Assigned' ? 'Not Trackable' : $package->id; ?></td>
            <td><?php echo isset( $package->ShippingCost ) ? wc_price( $package->ShippingCost ) : 'Not applicable'; ?></td>
            <td><?php echo isset( $package->ShippingLabel ) ? '<a class="slp_label" href="' . $package->ShippingLabel . '" target="_blank">View/Print Label</a>' : 'Not Available'; ?></td>
          </tr><?php
            if( isset( $package->Errors ) ) { ?>
          <tr style="background: none repeat scroll 0% 0% lightyellow;">
            <td><img src="<?php echo PLUGIN_URL; ?>/images/error.ico" width="16" height="16" /> Error:</td>
            <td colspan="2"><?php echo $package->Errors['error']; ?></td>
            <td style="text-align:left;"><a href="#" id="package-error_<?php echo $key; ?>" class="fix_error">FIX</a></td>
          </tr><?php
	 	}
  	} ?>
    	</tbody>
  	  </table>
    </section>
<style type="text/css">
	#view_shipment {
		border-radius: 5px;
		background: none repeat scroll 0% 0% #EEE;
		padding: 10px;
		box-shadow: 3px 3px 3px 2px;
	}
</style>
<script type="text/javascript">
	(function($){	
		var buttons = $( '.dialog' ).dialog( 'option', 'buttons' );				
	
		buttons['Cancel'] = function() {
			$( '.dialog' ).dialog('close');
			
			if( <?php echo (int)$reload; ?> ) {	
				window.location.reload();
			} 
		}
		
		$( '.dialog' ).dialog( 'option', 'buttons', buttons );
		
		$( '.ui-dialog-buttonset' ).children().not( ':last' ).hide();
		$( '.ui-dialog-buttonset' ).children( ':last' ).button( 'option', 'label', 'Close' );
			
		$( '#shipping_total' ).html( '<?php echo wc_price( $shipment['_shipping_cost'] ); ?>' );
		
		$( '#shipment_status' ).html( '<?php echo $shipment['_shipment_status']; ?>' );	
		
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