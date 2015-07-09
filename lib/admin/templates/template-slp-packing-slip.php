<?php 
global $post;

$order = new WC_Order( $post->ID );
$shipment = get_post_meta( $order->id, '_shipment', true );
$packages = $shipment['packages'];
?>
<form id="packing_slip" action="" method="post">
  <h3>For packing purposes only. Be sure to check order for variation details.</h3>
  <table border="1" style="width:100%; clear:both;">
    <tbody>	
    <tr>
      <td colspan="5" style="text-align:center; font-size:20px;">Package Details</td>
    </tr><?php
    foreach(  $packages as $key => $package ) { ?>
   	<tr style="color:#FFF; background-color:#666;">
      <td colspan="5" style="text-align:center;">Package <?php echo ( $key + 1 ); ?></td>
    </tr> 
    <tr>
      <td>Tracking Number</td>
      <td><?php echo $package->id; ?></td>
    </tr>
    <tr>
      <td colspan="1" style=" text-align:center; padding:0 5px;">Box Size</td>
      <td colspan="4" style="padding:5px;">L=<?php echo $package->packed->length; ?>  W=<?php echo $package->packed->width; ?>  H=<?php echo $package->packed->height; ?> Weight=<?php echo wc_get_dimension( $package->packed->weight, 'lbs' ); ?> lbs</td>
    </tr>
    <tr style="text-align:center"><td>Product ID</td><td>Product</td><td>Item Quantity</td><td>Unit Price</td><td>Total</td></tr><?php
  		$items = $package->packed->meta;
    
    <tr style="text-align:center;">
      <td style="text-align:center;"> + items[item].id + </td>
      <td style="padding:5px;"> + items[item].name + </td>
      <td> + items[item].qty + </td>
      <td> + (items[item].price).toFixed(2) + </td>
      <td> + items[item].total + </td>
    </tr>
    }
    ++count;
    }
    
    
    </tbody>
    
    
  </table>
  
  <button class="no_print" type="button" style="width: 150px; height: 30px; float: right; margin-bottom: 10px; margin-top: -50px;" onClick="print();">Print Packing Slip</button>
</form>
