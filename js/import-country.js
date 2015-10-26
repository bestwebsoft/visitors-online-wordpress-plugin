( function( $ ) {
	$( document ).ready( function() {
		/* Pressing the 'Import Table' */
		$( '#vstrsnln_button_import' ).click( function() {
			$( '#vstrsnln_img_loader' ).css( 'display', 'inline-block' );
			$( '#vstrsnln_button_import' ).attr( 'disabled', true );
			var number_records_file;
			var number_records_file_text;
			var count_rows 		= 1;
			var data_count_rows = {
				'action': 'vstrsnln_count_rows',
				vstrsnln_ajax_nonce_field: vstrsnln_var.vstrsnln_nonce
			};
			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				data: data_count_rows,
				success: function( count_rows_in_file ) {
					number_records_file = count_rows_in_file;
					if ( number_records_file != 0 ) {
						$( '#vstrsnln_message' ).css( 'display', 'block' );
						$( '#vstrsnln_loaded_rows' ).text( number_records_file );
					}
					insert_data( count_rows, number_records_file );
				},
				error : function ( xhr, ajaxOptions, thrownError ) {
					alert( xhr.status );
					alert( thrownError );
				}
			});
			return false;
		});
	});
	function insert_data( gl_count_rows, gl_number_records_file ) {
		if ( gl_count_rows != false ) {
			count_rows 				= gl_count_rows;
			number_records_file 	= gl_number_records_file
		};	
		if ( number_records_file != 0 ) {
			var ajax_result			= 0;
			var error_insert	 	= 0;
			var data_insert_rows 	= {
				'action': 'vstrsnln_insert_rows',
				vstrsnln_ajax_nonce_field: vstrsnln_var.vstrsnln_nonce,
				'count': count_rows
			};
			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				data: data_insert_rows,
				success: function( result_insert_rows ) {
					if ( result_insert_rows != 0 ) {
						if ( count_rows <= number_records_file ) {
							$( '#vstrsnln_loaded_files' ).text( count_rows );
							count_rows++;
							error_insert = 0;
							insert_data( false, false );
						}
					} else {					
						if ( error_insert < 3 ) {						
							error_insert++;
							if ( count_rows <= number_records_file ) {
								insert_data( false, false );
								$( '#vstrsnln_loaded_files' ).text( count_rows );
								count_rows++;
							} else {
								ajax_result = 1;
							}
						} else {
							error_insert = 0;
							if ( count_rows <= number_records_file ) {
								insert_data( false, false );
								$( '#vstrsnln_loaded_files' ).text( count_rows );
								count_rows++;
							} else {
								ajax_result = 1;
							}
						}
						if ( ajax_result == 1 ) {
							/* Сheck whether there are users with an undefined country, if there is something we define */
							if ( $.isFunction( vstrsnln_check_country_js ) ) {
								vstrsnln_check_country_js();
							}
							$( '#vstrsnln_message' ).css( 'display', 'none' );
							$( '#vstrsnln_img_loader' ).hide();
							$( '#vstrsnln_settings_notice p' ).html( '<strong>' + vstrsnln_var.notice_finish + '</strong>' );
							$( '#vstrsnln_settings_notice' ).css( 'display', 'block' );
							$( '#vstrsnln_button_import' ).attr( 'disabled', false );
						}
					}
				},
				error : function ( xhr, ajaxOptions, thrownError ) {
					alert( xhr.status );
					alert( thrownError );
				}
			});
		} else {
			$( '#vstrsnln_img_loader' ).hide();
			$( '#vstrsnln_button_import' ).attr( 'disabled', false );
			$( '.error p' ).html( '<strong>' + vstrsnln_var.notice_false + '</strong>' );
			$( '.error' ).css( 'display', 'block' );
		}
	}
})(jQuery);