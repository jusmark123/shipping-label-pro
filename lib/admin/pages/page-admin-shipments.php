<div class="wrap slp">
  <h2>Shipping Label Pro Shipments</h2>
  <table class="shipments compact display" width="100%" cellspacing="0">
    <thead>
    <th><input type="checkbox" id="select-all" checked="false" /></th>
      <th>Order#</th>
      <th>Customer</th>
      <th>Tracking#</th>
      <th>Service Method</th>
      <th>Date Shipped</th>
      <th>Tracking Status</th>
      <th>Shipping Cost</th>
      </thead>
    <tbody>
    </tbody>
    <tfoot>
    </tfoot>
  </table>
  <table>
  	<tr>
    	<td><button type="button" class="button slp_action_btn" id="update_sel">Update Selected</button></td>
    	<td><button type="button" class="button slp_action_btn" id="refresf_tbl">Refresh Shipments</button></td>
    </tr>
  </table>
</div>
<script type="text/javascript">
	(function($) {
		$(document).ready(function(e) {
			
			get_shipments();
			
			$( '#update_sel' ).on( 'click', update_tracking );
			
			$( '#select-all' ).on( 'change', function() {
				if( $(this).is(':checked') ) {
					$( '.sel_shipments' ).prop( 'checked', true );
				} else {
					$( '.sel_shipments' ).prop( 'checked', false );	
				} 
			}).prop( 'checked', false );
			
        });
		
		update_tracking = function() {
			
			if( $( '.sel_shipments' ).is( ':checked' ) ) {
				var shipments = []; 
			 	$( '.sel_shipments:checked' ).each( function() { 
					shipments.push( $(this).attr( 'id' ) );
				});
				
				update_shipments( shipments )
			} else {
				alert( 'Please select atleast one shipment to update' );	
			}
		}
		
		loadShipments = function( shipments ) {	
			shipments = $.parseJSON( shipments );		
		
		   	$('.shipments').dataTable({
			  	'data': shipments,
			  	'language': {
					'zeroRecords': 'No Shipments Found',
				  	'infoEmpty': 'No shipments found',
			  	},
			  	'order': [[1, 'desc']],
		  	});	 	 
		};
		
		shipments_updated = function( shipments ) {
			location.reload();
		}
	})(jQuery);
</script>