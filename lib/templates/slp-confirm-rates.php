<span>Service Method: <?php echo $order->get_shipping_method(); ?></span>
<table width="100%" id="rate_confirm">
  <thead>
    <tr>
      <th>Box#</th>
      <th>Package Type</th>
      <th colspan="4">Dimenisions</th>
      <th>View</th>
    </tr>
  </thead>
  <tbody><?php
foreach(  $packages as $key => $package ) { ?>
    <tr style="text-align:center;">
      <td>Box <?php echo $key + 1; ?></td>
      <td><?php echo $package->PackageType; ?></td>
      <td>L= <?php echo $package->length; ?> in</td>
      <td>W= <?php echo $package->width; ?> in</td>
      <td>H= <?php echo $package->height; ?> in</td>
      <td>Weight= <?php echo number_format( $package->weight, 2 ); ?> lbs</td>
      <td><a href="#" class="view_contents">View Contents</a></td>
    </tr>
    <tr style="display:none;">
      <td/>
      <td colspan="7"><table class="contents" width="90%" style="border-top:1px solid #555;">
          <thead>
            <tr>
              <th>View</th>
              <th>Product</th>
              <th>Qty</th>
              <th>Unit Price</th>
              <th>Line Total</th>
            </tr>
          </thead>
          <tbody>
            <?php self::get_contents( $order, $packages[$key] ); ?>
          </tbody>
        </table></td>
    </tr><?php	
} ?>
    <tr>
      <td colspan="6" style="text-align:right; border-top:2px solid #000;">Total Shipping Charges</td>
      <td style="border-top:2px solid #000; text-align:right;"><?php echo wc_price( $shipping_total ); ?></td>
    </tr>
    <tr>
      <td colspan="6" style="text-align:right;">Customer Paid</td>
      <td style="text-align:right;"><?php echo wc_price( $shipping_cost ); ?></td>
    </tr>
    <tr>
      <td colspan="6" style="text-align:right;">You <?php echo ( $shipping_cost > $shipping_total ? 'Save' : 'Pay' ); ?></td>
      <td style="text-align:right;"><?php echo wc_price( $shipping_cost - $shipping_total ); ?></td>
    </tr>
    <tr>
      <td><label for="shippingInstructions">Shipping Instructions</label></td>
      <td colspan="6"><textarea id="shippingInstructions" placeholder="Enter Shipping Instructions" style="width;100%;"><?php echo $order->customer_note; ?></textarea></td>
    </tr>
  </tbody>
</table>
<style type="text/css">
	#rate_confirm {
		border-radius: 5px;
		background: #EEE;
		padding: 5px;
		box-shadow: 3px 3px 3px 2px;
	}
</style>
<script type="text/javascript">
	(function($){
		$( '#dialog_message' ).text( '' );
		
		$( '.view_contents' ).on( 'click', function() {
			$(this).closest( 'tr' ).next().slideToggle();		
		});
		
		var buttons = $( '.dialog' ).dialog( 'option', 'buttons' );
						
		buttons['Continue'] = function() {
			var data = {
				action: 'process_shipment',
				call: 'CreateShipment',
				post: <?php echo $order->id; ?>,
				memo: $( '#shippingInstructions' ).html()
			}
			
			ajax_request( data );
		}
		
		$('.dialog' ).dialog( 'option', 'buttons', buttons );
	})(jQuery);
</script>