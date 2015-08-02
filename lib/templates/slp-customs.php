<?php global $shipper, $order, $shipment; ?>

<div id="customs" style="display:none;">
  <p>International shipments require completion of customs declaration where applicable. Please complete the form below and click continue.</p>
  <form id="customs_form" method="post">
    <p>
      <label for="ContentType">Contents:</label>
      <select id="ContentType">
        <?php
        foreach( self::$shipper->get_content_types() as $key => $type ) { ?>
        	<option value="<?php echo esc_attr( $key ); ?>" <?php echo $key == 1 ? 'selected="selected"' : ''; ?>><?php echo  esc_html( $type ); ?></option>
        <?php	
		} ?>
      </select>
      <label for="comments">Comments</label>
      <input type="text" id="Comments" maxlength="76"  />
    </p>
    <table id="customs_table">
      <thead>
        <tr>
          <th/>
          <th>Qty</th>
          <th>Description</th>
          <th colspan="2">Weight</th>
          <th style="padding:0 5px;">HS Tarriff</th>
          <th>Country of Orgin</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <?php
		$total_pounds = 0;
		$total_ounces = 0;
		foreach( $order->get_items() as $key => $item ) { 
			$product = wc_get_product( $item['product_id'] ); 
			$ounces = $product->get_weight() * $item['qty'];
			$pounds = $ounces * 0.0625;
			$split = number_format( ( $pounds - floor( $pounds ) ) * 16, 0 );
			$total_ounces += $ounces; ?>
        <tr class="customs_line">
          <td class="remove"></td>
          <td><input style="width:50px;" type="number" min="1" step="1" value="<?php echo isset( $item['qty'] ) ? $item['qty'] : 0; ?>" class="Quantity" /></td>
          <td><input type="text" maxlength="60" class="Description" value="<?php echo $item['name']; ?>" /></td>
          <td style="padding-right:10px;"><input style="width:50px;" type="number" min="0" max="70" step="1" value="<?php echo round( $pounds ); ?>" class="WeightLb total" />
            lbs</td>
          <td style="padding-right:10px;"><input style="width:50px;" type="number" min="0" max="15" step="1" value="<?php echo $split; ?>" maxlength="4"  class="WeightOz total"  />
            oz</td>
          <td><input style="width:80px;"type="text" maxlength="6" class="HSTariffNumber"  /></td>
          <td><select class="CountryOfOrigin">
              <?php
              foreach( $countries->get_countries() as $key => $country ) { ?>
              <option value="<?php echo esc_attr( $key ); ?>" <?php echo $settings['country'] == $key ? 'selected="selected"' : '';?>><?php echo esc_html( $country ); ?></option>
        <?php } ?>
            </select></td>
          <td><input style="width:80px;" type="text" maxlength="6" value="<?php echo isset( $item['line_total'] ) ? number_format( $item['line_total'], 2 ) : 0.00; ?>" class="Value total"  /></td>
        </tr>
        <?php
	  	} ?>
      </tbody>
    </table>
    <table width="100%">
      <tbody>
        <tr>
          <td><a href="#" id="add_line">Add Another Line</a></td>
          <td/>
          <?php
								$pounds = $total_ounces * .0625;
								$ounces = number_format( ( $pounds - floor( $pounds ) ) * 16, 1 ) ?>
          <td>Total Weight: <span id="tot_WeightLb"><?php echo floor( $pounds ); ?></span> lbs. <span id="tot_WeightOz"><?php echo $ounces; ?></span> oz.</td>
          <td/>
          <td>Total Itemized Value: $<span id="tot_value"><?php echo $order->get_subtotal(); ?></span></td>
        </tr>
      </tbody>
    </table>
  </form>
</div>
<script type="text/javascript">
	(function($) {
		$( '#add_line' ).on( 'click', function() {				
			$( '.customs_line' ).last().clone( true ).appendTo( '#customs_table' );
			$( '.customs_line' ).last().children( 'td' ).each( function() {
				$(this).children( 'input' ).val( $( this ).children( 'input' ).attr( 'min' ) )
				$(this).children('.value' ).val( '0.00' );		
			})
			$( '.remove' ).html( '<a href="#" class="remove">Remove</a>' ).on( 'click', function() { 
				$( this ).parent( 'tr' ).remove();
			
				if( $( '.customs_line' ).length < 2 ) {
					$( '.remove' ).empty();	
				}
			});
		});
		
		$( '.total' ).on( 'change', function() {
			var calc = 0;
			var element = $( this ).attr( 'class' ).split( ' ' )[0];
			
			$( '.' + element ).each( function( e ) {
				calc += parseFloat( $( this ).val() );	 
			});
			 
			if( element === 'Value' ) { 
				$( '#tot_value' ).text( calc.toFixed( 2 ) );	
			} else {
				$( '#tot_' + element ).text( calc );	
			}
		});
	})(jQuery);
</script>