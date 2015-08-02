
<?php global $address, $order, $xml;?>

<!-- HTML Start --->

<div id="verify_address">
  <form id="address_select">
    <p id="slp_shipment_message"><?php echo $xml['message']; ?></p>
    
    <!---Original address block-->
    
    <div id="original_address" class="address_block">
      <p class="slp_shipment_message">Original Address</p>
      <table class="slp_address">
        <tr>
          <td><input type="radio" value="-1" name="selected" checked="checked" /></td>
          <td><table>
              <tr>
                <td><?php echo strtoupper( $order->shipping_address_1 ); ?></td>
              </tr>
              <?php if( ! isset( $address_2 ) ) { ?>
              <tr>
                <td><?php echo strtoupper( $order->shipping_address_2 );  ?></td>
              </tr>
              <?php } ?>
              <tr>
                <td><?php echo strtoupper( $csz ); ?></td>
              </tr>
            </table></td>
        </tr>
      </table>
    </div>
    <?php
				 
			if( ! empty( $address ) ) { ?>
    
    <!---Returned address block-->
    
    <div id="returned_address" class="address_block">
      <p class="slp_shipment_message"><?php echo sizeof( $xml['address'] ) > 1  ? sizeof( $xml['address'] ) . ' Results. Scroll to view all.' : 'Updated Address'; ?></p>
      <?php
				foreach( $xml['address'] as $key => $address ) {
				$address_1 = $address['address_1'];
				$address_2 = isset( $address['address_2'] ) ? $address['address_2'] : '';
				$csz = $address['city'] . ', ' . $address['state'] . ', ' . $address['country'] . ' ' . $address['postcode'];?>
      <table class="slp_address">
        <tr>
          <td><input type="radio" value="<?php echo $key; ?>" <?php echo $key == 0 ? 'checked="checked"' : '';?> name="selected" /></td>
          <td><table>
              <tr>
                <td id="address_1"><?php echo $address_1; ?></td>
              </tr>
              <?php 		if( ! empty( $address_2 ) ) { ?>
              <tr>
                <td id="address_2"><?php echo $address_2;  ?></td>
              </tr>
              <?php 		} ?>
              <tr>
                <td id="csz"><?php echo $csz; ?></td>
              </tr>
            </table></td>
        </tr>
      </table>
      <?php
				} ?>
    </div>
    <?php
			} ?>
  </form>
  <p id="dialog_buttons">
    <button type="button" class="dialog_nav" id="address_edit">Edit Address</button>
  </p>
</div>

<!---Edit Address HMTL-->

<div id="edit_address" style="display:none;">
  <p>Make changes to this order's shipping address below then click continue to proceed.</p>
  <form id="edit_address" action="" method="post">
    <table>
      <tr>
        <td><label for="first_name">First Name</label></td>
        <td><input type="text" id="first_name" value="<?php echo  $order->shipping_first_name; ?>" /></td>
        <td><label for="last_name">Last Name</label></td>
        <td><input type="text" id="last_name" value="<?php echo $order->shipping_last_name; ?>" /></td>
      </tr>
      <tr>
        <td><label for="company">Company</label></td>
        <td><input type="text" id="company" value="<?php echo $order->shipping_company; ?>" /></td>
      </tr>
      <tr>
        <td><label for="address1">Address</label></td>
        <td><input type="text" id="address_1" value="<?php echo $order->shipping_address_1; ?>" /></td>
        <td><label for="address2">Address 2</label></td>
        <td><input type="text" id="address_2" value="<?php echo $order->shipping_address_2;?>" /></td>
      </tr>
      <tr>
        <td><label for="city">City</label></td>
        <td><input type="text" id="city" value="<?php echo $order->shipping_city; ?>" /></td>
        <td><label for="country">Country</label></td>
        <td><select id="country">
            <?php
								foreach( $countries->get_countries() as $key => $country ) {?>
            <option value="<?php echo esc_attr( $key );?>" <?php echo $order->shipping_country === $key ? 'selected="selected"' : ''; ?>><?php echo $country; ?> </option>
            <?php
								} ?>
          </select></td>
      </tr>
      <tr>
        <td><label for="state">State</label></td>
        <td><select id="state">
            <?php
								foreach( $countries->get_states( $order->shipping_country ) as $key => $state ) {?>
            <option value="<?php echo esc_attr( $key ); ?>"<?php echo $order->shipping_state === $key ? 'selected="selected"' : ''; ?>><?php echo esc_html( strtoupper( $state ) ); ?> </option>
            <?php
								} ?>
          </select></td>
        <td><label for="postcode">ZipCode</label></td>
        <td><input type="text" id="postcode" style="width:75px;" value="<?php echo substr( $order->shipping_postcode, 0, 5 ); ?>" />
          <?php if( sizeof( $order->shipping_postcode ) == 4 ) {?>
          <span> - </span>
          <input type="text" id="zipcode_4" style="width:82px;" value="<?php echo substr( $order->shipping_postcode, 8 ); ?>" />
          <?php } ?></td>
      </tr>
    </table>
  </form>
</div>
<!---Style Declaration Start/HTML End-->

<style type="text/css">
	.address_block {
		padding: 0 20px;
		display: inline-block;
		vertical-align:	top;
	}
	#original_address {
		padding-right:0;
	}
	#returned_address {
		height:155px;
		width: 415px;
		overflow-y: scroll;
		border-left: 1px solid #838181;
	}
	.slp_address, .selected_address {
		border-radius: 5px;
		padding: 10px;
		background: #EEE;
		box-shadow: 3px 3px 3px 1px;
	}
</style>

<!--End Style Declaration/JavaScript Start--> 

<script type="text/javascript">
	( function( $ ) {				
		var options = {};
		var buttons = $( '.dialog' ).dialog( 'option', 'buttons' );
			buttons['Continue'] = function(){
				address_selected();
			};
			
			$( '.dialog' ).dialog( 'option', 'buttons', buttons );
		
		function address_selected() {
			
			var $selected = $( '#address_select input[type="radio"]:checked' );
			var index =  $selected.index();
			var address;
			var message = '';
			
			$( '.dialog' ).dialog( 'option', 'title',  'Step 1: Address Verfication' );					
			
			$selected.closest( '.slp_address' ).toggleClass( 'selected_address' ).removeClass( 'slp_address' );

			$( '.address_block' ).not( $selected.closest( '.address_block' ) ).css( 'display', 'none' ).end().css('border', 'none').css( 'border-left', '1px solid' );
			$( '#returned_address' ).css( 'height', '200px' );

			var value = $selected.val();
			
			$selected.prop('checked', false ).focus().addClass( 'verified' ).get(0).type = 'checkbox';
			
			if( value >= 0 ) {
				message = 'You have selected the below address. Please confirm by checking the box below and click continue to proceed.';
									
				address = $.makeArray( <?php echo json_encode( $address ); ?> );	
				address = address[index];
				$( '#slp_shipment_message' ).css( 'color', 'green' );
			} else {
				message = "You have selected a non-validated address. Delivery cannot be guaranteed. Please confirm by checking the box below and click continue to proceed. The carrier is not responsible for undelivered or mis-delivered packages. Also note a fee may be assessed if address is not valid.";
				address = 'original';
				$( '#slp_shipment_message' ).css( 'color', 'red' );
			}	
			
			$selected.parent().next().children().prepend( '<tr><td><?php echo $name;?></td></tr><tr><td><?php echo $company;?></td></tr>' );
			
			$( '.slp_address' ).hide();
			
			$( '#slp_shipment_message' ).text( message ) 
			
			buttons = $( '.dialog' ).dialog( 'option', 'buttons' );
			
			buttons['Continue'] = function() {
				get_rates( address );
			}
			
			options = {
				title: 'Step 1: Confirm Address Selection',
				buttons: buttons
			}
			
			dialog_options( options );
		}
		
		function get_customs( data ) {  					
			buttons = $( '.dialog' ).dialog( 'option', 'buttons' );
			
			$( '#customs' ).toggle();
			$( '#verify_address' ).toggle();
			 
			$( '#address_edit' ).hide();
			
			options = {
				width: 1000,
				title: 'Step 1: Customs Form - CN22'
			}
			
			buttons['Continue'] = function() {
				var customs = {};
				var lines = [];
			
				customs['ContentType'] = $( '#ContentType' ).val();
				customs['Comments'] = $( '#Comments' ).val();
					
				$('.customs_line').each(function() {
					var line = {};
					$(this).children('td').children().each(function(){
						line[$(this).attr('class').split(' ')[0]] = $(this).val(); 	
					});
					
					lines.push(line)
				});
				
				customs['CustomsLines'] = lines;
				
				data['customs'] = customs;
				
				clear_history();	
				
				ajax_request( data );				
			}
			
			options['buttons'] = buttons;
			
			dialog_options( options );
		}
				
		function get_rates( address ) {
			if( $( '.verified' ).length ) {
				if( $( '.verified' ).is( ':checked' ) ) {
					var data = {
						action: 'process_shipment',
						post: <?php echo $order->id; ?>,
						address: address,
						call: 'GetRates'
					}

					$( '#dialog_message' ).text( '' );
				
					$( '#edit_address' ).hide();

					if( <?php echo (int)$customs; ?> ) {
						get_customs( data );
					} else {
						clear_history();
						ajax_request( data );
					}
				} else {
					$( '#dialog_message' ).text( 'Please confirm the address below by checking the box below and click continue to proceed.' );
				}
			} else {
				$( '.ui-dialog-buttonset' ).children().eq(0).hide();	
				address_selected();
			}
		}

		$('#address_edit').one('click', function() {
			set_history();
			$( '#dialog_message' ).text( '' );

			var prev_title = $('.dialog').dialog( 'option', 'title' );

			$('#verify_address').toggle(); 
			$('#edit_address').toggle();

			$('.dialog').dialog( 'option', 'title', 'Edit Address' );
			
			buttons['Continue'] = function() {
				var data = {
					action: 'process_shipment',
					call: 'VerifyAddress',
					post: '<?php echo $order->id; ?>'
				};
				
				if( $('#edit_address').length ) {
					var address = {};
					$('#edit_address input, #edit_address select').each( function(e) {
						address[$(this).attr('id')] = $(this).val();
					});
				  
					data['address'] = address,
					
					ajax_request( data );
				}
			};
			
			$( '.dialog' ).dialog( 'option', 'buttons', buttons );
			
			$('#country').on('change', function() {
				var data = {
					action: 'get_states',
					country:$(this).val(),
				}
			
				$.post( ajaxurl, data, function(response) {
					if( response = $.parseJSON(response)) {
						$('#state').empty();
						$.each(response, function( key, value) {
							$('#state').append($('<option></option>').val(key).html(value));
						});
					}
				});		
			});
		});
	})( jQuery );
</script>
<?php 



