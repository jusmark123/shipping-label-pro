<?php
if packages are loaded process	
	if( ! empty( $packages ) ) {
		foreach( $packages as $key => $package ) { ?>
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
      </tr><?php 
	  		} ?>
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
      </tr><?php 
	  	} ?>
    </tbody>
  </table>
</div><?php
	}
}