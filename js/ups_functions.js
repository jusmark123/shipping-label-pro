(function($) {

ups_process_data = function( obj ) {
	switch( obj.Call ) {	
		case 'ProcessShipment':
			ups_process_shipment( obj );
			break;	
		case 'VoidShipment':
			ups_void_shipment( obj );
			break;
		case 'ShippingLabels':
			ups_get_labels( obj );
			break;
		case 'CheckRate':
			ups_check_rate( obj );
			break;
		case 'TrackShipment':
			ups_track_shipment( obj );
			break;
		case 'GetLabels':
			ups_get_labels( obj );
			break;
	}
}

ups_process_shipment = function( obj ) {
			
	if( obj.ResponseStatusCode == 1 ) {
			
		update_metabox( obj );
		
		toggle_controls();
	}
	message_display( obj.StatusMessage, obj.ResponseStatusCode );
	
}

ups_track_shipment = function( obj ) {		
	
	if( obj.ResponseStatusCode == 1 ) {	
		$('#shipment_status').val( obj.ShipmentStatus );
		$('#tracking_status').val( obj.TrackingStatus );
	} else {
		$('#shipment_status').val(obj.ShipmentStatus);
	}	
	
	message_display( obj.StatusMessage, obj.ResponseStatusCode );
	
}

function ups_check_rate( obj )  {
	var title = 'Rate Discrepancy Nofitication';
	var html  = '<p>The rate returned from UPS is greater than the rate offered to the customer.</p></br></br>';
		html +=		'<table>';
		html +=			'<tr><td>Customer Paid:</td><td>obj.Paid</td></tr>';
		html +=			'<tr><td>Returned Rate:</td><td>obj.Rate</td></tr>';
		html +=			'<tr><td>Total Discrepancy</td>' + parseFloat(obj.Paid - obj.Rate).toFixed(2) + '</td></tr>'
		html +=		'</table>'; 
	var options = {
		
		
	}
	
	show_dialog( title, html, options );
	$('#dialog').dialog( 'show' );
		
}

function message_display( message, status ) {
	var html;
	if( status == 1 ) {
		if( !$('#slp_message').hasClass('updated') )
			$('#slp_message').toggleClass( 'updated' );
	} else {
		if( !$('#slp_message').hasClass('error') )
			$('#slp_message').toggleClass('error');
	}
	
	$('#slp_message').text( message );
	alert( message );
}
		
function ups_void_shipment( obj ) {
	if( obj.ResponseStatusCode == 1 ) {
		$('.ajax_editable').val('');
		$('#shipment_status').val( obj.ShipmentStatus ); 
		toggle_controls();
	}
	
	message_display( obj.StatusMessage, obj.ResponseStatusCode );
	alert( obj.StatusMessage );
}

ups_get_labels = function( obj ) {
		var labels = obj.GraphicImages;
		var label_count = labels.length;
		var curr_label = 0;
		var style_dec = '<style type="text/css">@media print { .no_print{ display:none;} #viewer{ min-width:300px; min-height:450px; border:nonel;}}</style>';
		var newWindow = window.open("","label_viewer");
			newWindow.document.write( style_dec );
			newWindow.document.write('<input type="button" value="Previous Label" id="prev" class="no_print" />');
			newWindow.document.write('<input type="button" value="Print Label" id="print" class="no_print"/>');
			newWindow.document.write('<input type="button" value="Next Label" id="next" class="no_print"/>');
			
		var prev = newWindow.document.getElementById( 'prev' );
		var next = newWindow.document.getElementById( 'next' );
		var btnPrint = newWindow.document.getElementById( 'print' );
		
		button_display();
		
		update_src( labels[0] ); 
		
		prev.onclick = function() {
			if ( curr_label > 0)
				--curr_label;
			label = update_src( labels[curr_label] );
		};
			
		next.onclick = function() {
			if ( curr_label < label_count - 1 )
				++curr_label;	
			label = update_src( labels[curr_label] );
		};
			
		btnPrint.onclick = function() {
 			newWindow.print();
		}
		
		function button_display() {
			if ( curr_label <= 0 ) {
				prev.style.display = 'none';
				if( label_count == 1 || curr_label >= label_count - 1 ) {
					next.style.display = 'none';
				}
			} else {
				prev.style.display = 'inline';
				next.style.display = 'inline';
			}
		}
					
		function update_src( label ) {
			var img = new Image;
				img.width = 600;
				img.style.border = 'none';
				img.src = 'data:image/gif;base64,' + label;	
				img.onload = function() {
					newWindow.document.write( '</br>' + img.outerHTML );
					newWindow.stop();
				}
		}
	}
})(jQuery);
