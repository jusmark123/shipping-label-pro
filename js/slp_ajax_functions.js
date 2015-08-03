/**
 * File Name:  SLP Ajax Functions (JavaScript)
 * Version:		3.0.0
 */
var history_cache = [];
(function($) {
	$(document).ready(function(e) {	
		
		ajax_request = function( data )  {		
			$.post( ajaxurl, data, function( response ) {
				response = $.parseJSON( response );
				show_processing( false);
				
				if( response.reload ) {
					location.reload;
				} else {
					show_dialog( response );
				}
			});
		}
		
		show_dialog = function( response ) {
			var data = {};
				
			if( $('.dialog').length == 0 ) {
				var options =  {
					autoOpen: false,
					resizable: false,
					width: 800,
					modal: true,
					title: response.Title,
					close: function( ev, ui) { $(this).remove(); packages = {} },	
					buttons: {
						'Go Back': function() {
							go_back();
						},
						'Continue': function() {
						},
						'Cancel': function() {
							$( '.dialog' ).dialog( 'close' );
						}
					}
				}
								
				$( '#wpbody' ).append( '<div class="dialog"></div>' );		
				$( '.dialog' ).dialog( options );	
				$( '<p></p>' ).prependTo( '.dialog' ).attr( 'id', 'dialog_message' ).css( 'font-weight', 'bold' ).css( 'color', 'red' );
				$( '<div></div>') .appendTo( '.dialog' ).addClass( 'dialog_content' ).html( response.StatusMessage );
				$( '.dialog' ).dialog( 'open' );
				$( '.ui-dialog-buttonset' ).children().eq(0).hide();
			} else {
				$( '.dialog_content' ).html( response.StatusMessage )
				$( '.dialog' ).dialog( 'option', 'title', response.Title )
				
				if( response.Buttons ) {
					$( '.dialog' ).dialog( 'option', 'buttons', response.Buttons );
				}
				
				if( history_cache.length < 1 ) {
					$( '.ui-dialog-buttonset' ).children().eq(0).hide();	
				}
			}	
		}
		
		dialog_options = function( options ) {
			$.each( options, function( i, v ) {
				$('.dialog').dialog( 'option', i, v );
			});
		}
		
		set_history = function() {
			var history = {
				StatusMessage: $( '.dialog_content' ).html(),
				Title: $( '.dialog' ).dialog( 'option', 'title' ),
				Buttons: $( '.dialog' ).dialog( 'option', 'buttons' ),
			}

			if( history_cache.length > 1 ) {
				$( '.ui-dialog-buttonset' ).children().eq(0).show();
			}
			
			history_cache.push( history );	
		}
		
		clear_history = function() {
			history_cache = [];
			$( '.ui-dialog-buttonset' ).children().eq(0).hide();	
		}
		
		go_back = function() {
			$( '#dialog_message' ).text( '' );
			
			show_dialog( history_cache.pop() );
		}
		
		get_shipments = function( filters ) {
			var data = {
				action: 'get_shipments',
				filter: filters,
			}
			
			$.post( ajaxurl, data, function( response ) {
				if( response ) {
					loadShipments( response );
				}
			});	
		}
		
		update_shipments = function( shipments ) {
			var data = {
				action: 'update_shipments',
				shipments: shipments
			}
			
			$.post( ajaxurl, data, function( response ) {
				if( response ) {
					shipments_updated( response ); 	
				}
			})
			
		}
		
		show_label = function( code ) {
			var style_dec = '<style type="text/css">\
				@media print {\
					.no_print{\
						display:none;\
					}\
					#viewer{\
						min-width:300px;\
						min-height:450px;\
						border:nonel;\
					}\
				}\
			</style>';
			
			var newWindow = window.open();
			newWindow.document.write( style_dec );
			
			var img = new Image;
			img.width = 800;
			img.style.border = 'none';
			img.style.marginTop = '200px';
			img.style.transform = 'rotate(90deg)';
			img.style.position = 'relative';
			img.style.right = '150px';
			img.src = code;	
			img.onload = function() {
				newWindow.document.write( '</br>' + img.outerHTML );
				newWindow.stop();
			}
		}
	});
})(jQuery);