(function($) {

	var history = new Array();
	var addons_selected = new Array();
	
	$('.login_button').on( 'click', function() {
		var data = {
			'action': 'user_login',
			'username': $('#username').val(),
			'password': $('#password').val(),
		}
		
		ajax_request( data );
	});
	
	usps_process_data = function( obj ) {
		switch( obj.call ) {
			case 'AddPostage':
				usps_add_postage( obj);
				break;
			case 'VerifyAddress':
				usps_address_check( obj );
				break;
			case 'GetRates':
				usps_confirm_rates(obj);
				break;
			case 'ConfirmShipment':
				usps_confirm_shipment(obj);
				toggle_controls();
				break;	
			case 'PostageAdded':
				usps_postage_added( obj );
				break;
			case 'PickupConfirm':
				usps_pickup_confirm( obj );
				break;
			case 'GetLabels':
				usps_get_labels( obj );
				break;
			case 'VoidShipment':
				usps_void_shipment( obj );
				break;
			case 'GetCustoms':
				usps_customs_form( obj );
				break;
		}
	}
	
	usps_postage_added = function( obj ) {
		var back = $('.dialog').html();
		var html = obj.StatusMessage;
		
		$('.dialog').html( html ).dialog( 'option', 'title', obj.Title );
		
		$('#btn_continue').click(function(e) {
			var data = {
				action: 'process_shipment',  
				post: $('#post_id').val(),
			};
	
			ajax_request( data );
		});
		
	}
	
	usps_confirm_pickup = function( obj ) {
		var title = obj.Title;
		var html = obj.StatusMessage;
		var options = {
			resizable: true,
			width: 600,
			modal: true,
			buttons: {
				'Close':function() {
					$(this).dialog( 'close' );
				}
			}
		}
	}
	
	usps_add_postage = function( obj ) {
		var html = '<div>' + obj.StatusMessage + '</br></br>';
		var account_balance = obj.PostageBalance;
		var max_postage = obj.MaxPostageBalance;
		var allowed_postage = parseFloat( max_postage - account_balance );
		var data = {};
		var options = {
			autoOpen: true,
			resizable: false,
			width: 800,
			modal: true,
			title: obj.Title,
			buttons: {
				'Purchase Postage': function() {
					var allowed_postage = obj.MaxPostageBalance - obj.AllowedPostage
					if( $('#postage_amount').val() <= obj.AllowedPostage ) {
						var data = {
							action: 'usps_ajax',
							call: 'add_postage',
							post: $('#post_id').val(),
							value: $('#postage_amount').val()
						}
						
						$('.dialog').dialog( 'option', 'title', 'Processing...Action may take upto 30 seconds.' );
						$('#dialog_message').empty();
						
						ajax_request( data );

					} else {
						
						$('#dialog_message').text( 'The Amount entered exceeds the maximum allowed postage balance of ' + obj.AllowedPostage + '. Please correct above and try again.');
					}
				},
				Cancel: function() {
					$(this).dialog( 'close' );
				}
			}
		}
		
		if( obj.status === 'Rejected' ) {
			$('#dialog_message').html( obj.StatusMessage );
			$('.dialog').dialog( 'option', 'title', obj.Title );
		} else {
			
			data['html']    = html;
			data['options'] = options;
		 
			show_dialog( data );
		}
		
		$('#postage_amount').on('blur', function(e) {
			if( ! obj.status ) {
				history = {
					'allowed_postage': allowed_postage,
					'max_postage': obj.MaxPostageBalance,
					'postage_balance': obj.PostageBalance,
				};
			}
			
			if( $(this).val() > history.allowed_postage ) {
				$(this).css('background', 'yellow' );
				$('#dialog_message').text( 'The Amount entered exceeds the maximum allowed postage balance of ' + history.allowed_postage + '. Please correct above and try again.');
			} else {
				$(this).css('background', 'white' );
				$('#dialog_message').text('');
			}
		})
	}
	
	usps_purchase_postage = function( obj ) {
		var data = {
			html: obj.StatusMessage,
			options: {
				title: 'Payment Confirmation',
				buttons: {
					Ok: function() {
						$(this).dialog('destroy').detach();
					}	
				}
			}
		}
		
		show_dialog( html, options );
	}
	
	usps_address_check = function( obj ) {
		if( obj.edit ) {
			$('.dialog').html( obj.html ).dialog( 'option', 'title', obj.Title );
	
			$('#continue').off();
			
			$('#back').one( 'click', function() {
				$('.dialog').html( history.StatusMessage ).dialog( 'option', 'title', history.Title );
			});
			
			$('#continue' ).on( 'click', function() {
				var data = {
					post: $('#post_id').val(),
					action: 'edit_address',
				}
				var address = {};
				
				$('#edit_address input').each( function(e) {
					address[$(this).attr('id')] = $(this).val();
				});
				
				address['state'] = $('#state').val();
				address['country'] = $('#country').val();
				
				data['address'] = address;
				
				ajax_request( data );
				
			});
		} else if( obj.edited ) {
			$('.dialog').html( obj.StatusMessage ).dialog( 'option', 'title', obj.Title );
		} else {
			var data = { 
				html: obj.StatusMessage,
				options: {
					autoOpen: true,
					resizable: false,
					width: 800,
					modal: true,
					title: obj.Title,
					buttons: {
						'Cancel': function() {
							$(this).dialog( 'close' );
						}
					}
				}
			}
			
			set_history( obj );
			
			show_dialog( data );
		}
		
		$('#address_edit').off();
		$('#btn_continue').off();
	
		$('#address_edit').on('click', function() {
			data = {
				action: 'edit_address',
				post: obj.post,
			}
			
			ajax_request( data );
		});		

		$('#btn_continue').on('click', function() {  
			if( $('#verified').attr('checked') ) {
				data = {
					action: 'check_rate',
					post: $('#post_id').val(),
					obj: obj
				};
				
				set_history( obj );
				
				$('#back').one( 'click', function() {
					$('.dialog').html( history.StatusMessage ).dialog( 'option', 'title', history.Title );
				});
				
				$('.dialog').dialog('option', 'title', 'Processing...Please wait');
				
				ajax_request( data );
			} else {
				$('#dialog_message').text("Please click the checkbox to confirm corrected address, and click continue to proceed." ).css('color','red');
			}
		});
		
	}	
	
	usps_confirm_rates = function( obj ) {
		var total_cost = obj.ShippingTotal;
		
		$('.dialog').html(obj.StatusMessage).dialog('option', 'title', obj.Title );
		
		$('#back').one( 'click', function() {
				$('.dialog').html( history.StatusMessage ).dialog( 'option', 'title', history.Title );
		});
		
		$('#btn_continue').off();
		
		$('#btn_continue').one( 'click', function() {
			var data = {
				action: 'usps_ajax',
				call: 'confirm_shipment',
				post: $('#post_id').val(),
				rates: obj.Rates,
			};
			
			set_history( obj );
			
			$('.dialog').dialog('option', 'title', 'Processing...Please wait' );
			
			ajax_request( data );
		});
		
	}
	
	usps_confirm_shipment = function( obj ) {
		
		$('.dialog').html( obj.StatsuMessge ).dialog( 'options', 'title', obj.Title );
		
		update_metabox( obj );
		
	}
	
	usps_pickup_confirm = function( obj ) {
		var title = obj.Title;
		var html = obj.StatusMessage;
		var options = {
			resizable: false,
			width:800,
			modal:true,
			buttons: {
				'Close': function() {
					$(this).dialog('close');
				}
			}
		}
		update_metabox( obj );
		
		show_dialog( title, html, options );	
	}
	
	usps_get_labels = function( obj ) {
		var title = 'Print Shipping Labels';
		var options = {
			resizable: false,
			width:800,
			modal:true,
			buttons: {
				'Close': function() {
					$(this).dialog('close');
				}
			}
		}
		
		var html = '<table>';
		var count = 1;
		var date = new Date();
		var cutoff = new Date()	
	
		for( var url in obj.GraphicImages ) {
			html += '<tr><td>Package ' + count + ': </td><td><a href="' + obj.GraphicImages[url] + '" target="_blank">Click to View/Print Labels</a><td><tr>';
			++count;
		}
		
		if( typeof obj.ScanformURL !== 'undefined' )
			html += '<tr><td>Scanform</td><td><a href="' + obj.ScanformURL + ' target="_blank">Click to View/Print Scanform</td><tr>';
		
		
		show_dialog( title, html, options );
		$('#dialog').dialog('open');	
	}
	
	usps_void_shipment = function(obj) {
		var title = obj.Title;
		var html = obj.StatusMessage;
		var options = {
			resizable: false,
			width:800,
			modal:true,
			buttons: {
				'Close': function() {
					$(this).dialog('close');
				}
			}
		}
		
		show_dialog( title, html, options );
		
		$('#dialog').dialog('open');
	}
	
	function usps_customs_form( obj ) {
		$('.dialog').dialog( 'option', 'width', 1000 );
		
	
			
		$('#btn_continue').one('click', function() {
			
			
			var data = {
				action: 'usps_ajax',
				call: 'confirm_shipment',
				post: $('#post_id').val(),
				rates: obj.Rates,
				customs: customs
			}
			
			ajax_request(data);
		});
	}
	
	function set_history( obj ) {
		$.each(obj, function( index, value ) {
			history[index] = value;
		});
	}
})(jQuery);