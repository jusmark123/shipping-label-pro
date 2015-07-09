// JavaScript Document
(function($) {
	$(document).ready(function(e) {
		
		$( '.chosen_select' ).chosen();
		
		$('.timepicker').timepicker({ 'step': 30, 'minTime' : '9:00am', 'maxTime' : '5:00pm' });
		
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
})(jQuery);