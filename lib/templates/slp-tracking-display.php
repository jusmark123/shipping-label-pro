<?php 
	$shipment = get_post_meta( $order->id, '_shipment', true );
	$date_shipped = $shipment['_shipping_date'];
	$tracking_provider =  $shipment['_shipping_method'];
	$postcode = get_post_meta( $order_id, '_shipping_postcode', true );
	$packages = $shipment['_packages'];
	
	if ( $date_shipped ) {
		$date_shipped = ' ' . sprintf( __('on %s', 'shipping_label_pro'), date_i18n( __( 'l, F jS, Y', 'shipping_label_pro'), strtotime($date_shipped ) ) );
	}
	
	foreach( $packages as $package ) {
		
		if ( $tracking_provider ) {
						
			$link_format = '';
			
			$providers = get_tracking_url();
			
			$link_format = $providers['tracking_provider'];
				
			if ($link_format ) {
				$link = sprintf( $link_format, $package->id, urlencode( $postcode ) );	
				$links[] = sprintf( '<a href="%s">%s</a>', $link, $package->id );
			}
		}	
	}
	
	$tracking_provider = ' ' . __('via ', 'slp') . '<strong>' . $tracking_provider . '</strong>';
	
	if( sizeof( $links ) > 1 ) {
		$tracking_link = 's ' . implode( ',', $links );
	} else {
		$tracking_link = ' ' . $links;
	}
?>
<p><?php sprintf( __('Your order was shipped%s%s. Tracking number%s. You can check the tracking status of your shipment below if avaliable.', 'shipping_label_pro'), $date_shipped, $tracking_provider, $tracking_link ); ?></p>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
    	<tr>
        	<th scope="col1" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Date', 'slp' ); ?></th>
            <th scope="col1" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Event', 'slp');?></th>
            <th scope="col1" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Description', 'slp');?></th>
		</tr>
    </thead>
<?php
	foreach( $packages as $package ) {
		if( $tracking = $package->tracking_status ) {
			foreach( $tracking as $event ) {?>
			<tr>
            	<td><?php date( 'm/d/Y h:i', strtotime( $event['timestamp'] ) ); ?></td>
                <td><?php _e( $event['status'], 'slp' ); ?></td>
                <td><?php _e( $event['location'], 'slp' ); ?></td>
                <td><?php _e( $event['desc'], 'slp' ); ?></td>	
            </tr>				
	<?php	}
		} else { ?>
			<tr><td>No tracking informaiton available. Please check back later.</td></tr>        	
<?php	}
	}
    
    
   