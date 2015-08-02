<?php global $order, $shipment, $xml; ?>

<div id="confirm-shipment">
  <h3>Please confirm shipment details and click continue below to process this shipment.</h3>
  <section id="address">
    <h4>Customer Information</h4>
    <table>
      <tr>
        <td><p>Billing Address</p>
          <p><?php echo $order->formatted_billing_address(); ?></p></td>
        <td><p>Shipping Address</p>
          <p><?php echo $order->formatted_shipping_address(); ?></p></td>
      </tr>
    </table>
  </section>
  <section id="rates">
    <h4>Rate Details</h4>
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
      <tbody>
        <?php
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
        </tr>
        <?php	
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
  </section>
</div>
<style type="text/css">

</style>
<script>
	(function($) {
		$(document).ready(function(e) {
            
        });
	})(jQuery);
</script>
