function vstrsnln_check_country_js() {
	(function($) {
		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			async: false,
			data: { 
				'action': 'vstrsnln_check_country',
				vstrsnln_ajax_nonce_field: vstrsnln_ajax.vstrsnln_nonce
			}
		});
	})(jQuery);
}