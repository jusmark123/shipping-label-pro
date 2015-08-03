<?php
foreach( $shipment['_packages'] as $key => $package ) {?>

<div style="vertical-align:top; display:inline-block; margin: 0 20px;">
  <table class="header_table" width="100%">
    <tr>
      <td>Box<?php echo $key + 1; ?></td>
    </tr>
    <tr>
      <td>Type</td>
      <td><select id="slp_box_select"><?php
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
        </select></td>
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
  <ul style="vertical-align:top; display:inline-block" id="sortable_<?php echo $key; ?>" class="connectedSortable">
    <?php
	foreach( $package->packed as $index => $item ) {
		$product = get_product( $item['meta']['id'] );?>
    <li id="item_<?php echo $index; ?>" class="ui-state-default"><span><?php echo $product->get_title(); ?></span></li><?php
	} ?>
  </ul>
</div><?php
    $package->calc_volume = $box_volume;
}?>
<div class="clear"></div>
<p>
  <button type="button" id="reset" class="dialog_nav" Title="Undo Changes">Undo Changes</button>
  <button type="button"id="calculate_rate" class="dialog_nav">Calculate Shipping</button>
</p>
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