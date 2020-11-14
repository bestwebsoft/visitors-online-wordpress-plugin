<?php
if ( ! function_exists( 'vstrsnln_count_rows' ) ) {
	function vstrsnln_count_rows( $noscript = false ) {
		global $wpdb;

		if ( false == $noscript ) {
			check_ajax_referer('bws_plugin', 'vstrsnln_ajax_nonce_field');
		}
		$upload_dir = plugin_dir_path( __FILE__ ) . 'geolite-country';
		if ( is_dir( $upload_dir ) ) {
			$file_country_name = $upload_dir . '/IpToCountry.csv';
			/* We do a breakdown of the file */
			$handle = fopen( $file_country_name, 'r' );
			if ( $handle ) {
				$file_number = 1;
				/* On how many lines in the file */
				$lines_count = ( false == $noscript ) ? 2500 : 1000;
				if ( ! is_writable($upload_dir ) ) {
					if ( false == $noscript ) {
						echo 0;
						die();
					} else {
						return 0;
					}
				}
				/* Creating an array of names of countries */
				$country_array = $array_compare = $update_ignore = $insert_ignore = $country_in_file = array();
				$countries_data = $wpdb->get_results( "SELECT `id`, `country_code` FROM {$wpdb->base_prefix}bws_list_countries" );

				if ( ! empty( $countries_data ) ) {
					foreach ( $countries_data as $data ) {
						$array_compare[$data->id] = $data->country_code;
					}
					$first_import = false;
				} else {
					$first_import = true;
				}
				$current_file_country = fopen( $file_country_name, 'r' );
				if ( $current_file_country ) {
					while ( ( $line = fgetcsv( $current_file_country, 150, ',' ) ) !== FALSE ) {
						/* The first line of the file is skipped, there is overhead */
						if ( ! (int) $line[0] ) {
							continue;
						} else {
							$line[6] = preg_replace( '#\W#', '', $line[6] );
							$country_array[ $line[0] ] = array(
								'short' => $line[4],
								'name' => $line[6]
							);
						}
					}
				} else {
					if ( false == $noscript ) {
						echo 0;
						die();
					} else {
						return 0;
					}
				}
				$current_file = fopen( $upload_dir . '/file_' . $file_number . '.csv', 'a' );
				if ( $current_file ) {
					$first_line = $i = 0;
					while ( ( $line = fgetcsv( $handle, 100, ',' ) ) !== FALSE ) {
						if ( ! (int) $line[0] ) {
							continue;
						} else {
							if ( $first_line < 2 ) {
								$first_line++;
							} else {
								if ( ! empty( $line ) ) {
									/* Start - forming a string to write to the file */
									$country_short_name = $country_full_name = $country_id = '';
									if ( is_array($country_array ) ) {
										if ( $country_array[ $line[0] ] ) {
											$line[6] = preg_replace( '#\W#', '', $line[6] );
											$country_short_name = $line[4];
											$country_full_name = $line[6];
											if ( ! empty($country_full_name ) ) {
												if ( in_array($country_short_name, $array_compare ) ) {
													$country_id = empty($array_compare) ? 1 : array_search( $country_short_name, $array_compare );
													if ( ! in_array( $country_short_name, $country_in_file ) ) {
														$update_ignore[] = "( {$country_id}, '{$country_short_name}', '{$country_full_name}' )";
														$country_in_file[] = $country_short_name;
													}
												} else {
													if ( $first_import ) {
														$country_id = empty( $array_compare ) ? 1 : max( array_keys( $array_compare ) ) + 1;
														$insert_ignore[] = "( {$country_id}, '{$country_short_name}', '{$country_full_name}' )";
													} else {
														/* add record about new country */
														$wpdb->insert(
															"{$wpdb->base_prefix}bws_list_countries",
															array(
																'country_code' => $country_short_name,
																'country_name' => $country_full_name
															)
														);
														$country_id = $wpdb->insert_id;
													}
													$array_compare[ $country_id ] = $country_short_name;
													$country_in_file[] = $country_short_name;
												}
											}
										}
									}
									
									$start_ip = $line[0];
									$end_ip = $line[1];
									$long_start_ip = long2ip( $start_ip );
									$long_end_ip = long2ip( $end_ip );
									$file_line = "'{$long_start_ip}', '{$long_end_ip}','{$start_ip}','{$end_ip}','{$country_id}'";
									/* Finish - forming a string to write to the file */
									fwrite( $current_file, $file_line . PHP_EOL );
									$i++;
									if ( $i == $lines_count ) {
										fclose( $current_file );
										if ( ! empty( $update_ignore ) ) {
											vstrsnln_insert_countries( $update_ignore );
										}
										if ( ! empty( $insert_ignore ) ) {
											vstrsnln_insert_countries( $insert_ignore );
										}
										$file_number++;
										$current_file = fopen( $upload_dir . '/file_' . $file_number . '.csv', 'a' );
										if ( $current_file ) {
											$i = 0;
										} else {
											if ( false == $noscript ) {
												echo 0;
												die();
											} else {
												return 0;
											}
										}
									}
								}
							}
						}	
					}
					fclose( $current_file );
					fclose( $handle );
					if ( ! empty( $update_ignore ) ) {
						vstrsnln_insert_countries( $update_ignore );
					}
					if ( ! empty( $insert_ignore ) ) {
						vstrsnln_insert_countries( $insert_ignore );
					}
					if ( false == $noscript ) {
						echo $file_number;
						die();
					} else {
						return $file_number;
					}
				} else {
					if ( false == $noscript ) {
						echo 0;
						die();
					} else {
						return 0;
					}
				}
			} else {
				if ( false == $noscript ) {
					echo 0;
					die();
				} else {
					return 0;
				}
			}

		}
	}
}
if ( ! function_exists( 'vstrsnln_insert_countries' ) ) {
	function vstrsnln_insert_countries( $data ) {
		global $wpdb;
		$wpdb->query(
			"INSERT INTO `{$wpdb->base_prefix}bws_list_countries`
			( `id`, `country_code`, `country_name` ) VALUES " .
			implode(",", $data) .
			"ON DUPLICATE KEY UPDATE
			`country_name` = VALUES(`country_name`);"
		);
	}
}

/* Fill in the table of country */
if ( ! function_exists( 'vstrsnln_insert_rows' ) ) {
	function vstrsnln_insert_rows( $number_file = false, $noscript = false ) {
		global $wpdb, $wp_filesystem;
		if ( false == $noscript ) {
			check_ajax_referer( 'bws_plugin', 'vstrsnln_ajax_nonce_field' );
		}
		$vstrsnln_access_type = get_filesystem_method();
		if ( 'direct' == $vstrsnln_access_type ) {
			$vstrsnln_creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, array() );
			if ( ! WP_Filesystem( $vstrsnln_creds ) ) {
				if ( false == $number_file ) {
					echo false;
				} else {
					return false;
				}
			}
			if ( false == $number_file ) {
				if ( isset( $_POST['count'] ) && file_exists( plugin_dir_path( __FILE__ ) . 'geolite-country/file_' . $_POST['count'] . '.csv' ) ) {
					$filename = plugin_dir_path( __FILE__ ) . 'file_' . $_POST['count'] . '.csv';
					$data_array = $wp_filesystem->get_contents_array( $filename );
					if ( false !== $data_array && is_array( $data_array ) && ! empty( $data_array ) ) {
						$sql = "INSERT IGNORE INTO `" . $wpdb->base_prefix . "bws_list_ip`
							(  `ip_from`, `ip_to`, `ip_from_int`, `ip_to_int`, `country_id`  )
							VALUES ( " . implode( " ) , ( ", $data_array ) . " );";
						$result = $wpdb->query( $sql );
						unlink( $filename );
						echo $result;
					}
				}
			} else {
				if ( $number_file > 0 && file_exists( plugin_dir_path( __FILE__ ) . 'geolite-country/file_' . $number_file . '.csv' ) ) {
					$filename   = plugin_dir_path( __FILE__ ) . 'geolite-country/file_' . $number_file . '.csv';
					$data_array = $wp_filesystem->get_contents_array( $filename );
					if ( false !== $data_array && is_array( $data_array ) && ! empty( $data_array ) ) {
						$sql = "INSERT IGNORE INTO `" . $wpdb->base_prefix . "bws_list_ip`
							( `ip_from`, `ip_to`, `ip_from_int`, `ip_to_int`, `country_id`)
							VALUES ( " . implode( " ) , ( ", $data_array ) . " );";
						$result = $wpdb->query( $sql );
						unlink( $filename );
						return $result;
					}
				}
			}
		}
		/* This is required to terminate immediately and return a proper response */
		wp_die();
	}
}

/* Importing countries with javascript disabled */
if ( ! function_exists( 'vstrsnln_import_noscript' ) ) {
	function vstrsnln_import_noscript( $count_files ) {
		for ( $count = 1; $count <= $count_files ; $count++ ) {
			$result = vstrsnln_insert_rows( $count, true );
		}
		if ( empty( $result ) ) {
			$result = true;
		}
		return $result;
	}
}

/* The conclusion to the settings page of information on imports of table */
if ( ! function_exists( 'vstrsnln_form_import_country' ) ) {
	function vstrsnln_form_import_country( $page_url ) {
		global $wpdb;
		/* Table exists */
		if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $wpdb->base_prefix . "bws_list_ip'" ) == $wpdb->base_prefix . 'bws_list_ip' ) {
			$vstrsnln_table_full = $wpdb->get_var( "
				SELECT `id`
				FROM `" . $wpdb->base_prefix . "bws_list_ip`
				LIMIT 1"
			);
		} else {
			$vstrsnln_table_full = 0;
		}?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Import Database', 'visitors-online' ); ?></th>
                <td>
                    <form id="vstrsnln_import_block" method="post" action="<?php echo $page_url; ?>">
                        <div class="vstrsnln-info">
                            <?php $vstrsnln_file_there = 1;
                            if ( file_exists( plugin_dir_path( __FILE__ ) . 'geolite-country/IpToCountry.csv' ) ) {
                                /* Open the file in read mode */
                                $current_file = fopen( plugin_dir_path( __FILE__ ) . 'geolite-country/IpToCountry.csv', 'r' );
                                if ( $current_file ) {
                                    $vstrsnln_content = __( 'To collect statistics on the country for the day with the highest number of visits, you need to import the information on the countries to the database.', 'visitors-online' ) . '<br />';
                                } else {
                                    $vstrsnln_content = __( 'You do not have permission to access the file', 'visitors-online' ) . '&#032;&#032;' . plugin_dir_path( __FILE__ ) . 'geolite-country/IpToCountry.csv' .
                                    '&#044;&#032;&#032;' . __( 'cannot be imported', 'visitors-online' );
                                    $vstrsnln_file_there = 0;
                                }
                                echo $vstrsnln_content;
                            } else {
                                $vstrsnln_content = plugin_dir_path( __FILE__ ) . 'geolite-country/IpToCountry.csv' . '&#032;&#032;' . __( 'the file is not found, import is impossible', 'visitors-online' );
                                echo $vstrsnln_content;
                                $vstrsnln_file_there = 0;
                            } ?>
                            <div class="vstrsnln_clear"></div>
                            <?php if ( $vstrsnln_table_full > 0 ) {
                                _e( 'The table is already loaded, you can also update the data using this ', 'visitors-online' );
                            } else {
                                _e( 'Instructions on downloading and updating the country table', 'visitors-online' );
                            } ?>
                             <a href="https://docs.google.com/document/d/1sxxeDleJdPS8HvRdYwYSABQ586t1s-Z8r6wy55iXJCM/" target="_blank" style="word-break: break-word;"> <?php _e( 'here', 'visitors-online' ); ?></a>.
                        </div>
                        <div class="vstrsnln_clear"></div>
                        <?php if ( ! empty( $vstrsnln_file_there ) ) {
                            if ( $vstrsnln_table_full > 0 ) { ?>
                                <input type="submit" name="vstrsnln_button_import" class="button" value="<?php _e( 'Update', 'visitors-online' ); ?>" />
                            <?php } else {?>
                                <input type="submit" name="vstrsnln_button_import" class="button" value="<?php _e( 'Import', 'visitors-online' ); ?>" />
                            <?php }
                        } ?>
                        <?php wp_nonce_field( plugin_basename( __FILE__ ), 'vstrsnln_nonce_name' ); ?>
                        <input type="hidden" name="vstrsnln_import" value="submit" />
                        <div id="vstrsnln_img_loader"><img src="<?php echo plugins_url( 'images/ajax-loader.gif', __FILE__ ); ?>" alt="" /></div>
                        <div id="vstrsnln_message">
                            <?php _e( 'Number of loaded files', 'visitors-online' ); ?>: <span id='vstrsnln_loaded_rows'></span>
                            <?php _e( 'Number of a loading file', 'visitors-online' ); ?>: <span id='vstrsnln_loaded_files'></span>
                        </div>
                    </form>
                </td>
            </tr>
        </table>
	<?php }
}

/* Pressing the 'Import Country' */
if ( ! function_exists( 'vstrsnln_press_buttom_import' ) ) {
	function vstrsnln_press_buttom_import() {
		$message = $error = '';
		if ( isset( $_REQUEST['vstrsnln_button_import'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'vstrsnln_nonce_name' ) ) {
			$vstrsnln_count_files = vstrsnln_count_rows( true );
			if ( empty( $vstrsnln_count_files ) ) {
				$error = __( 'Not enough rights to import from the IpToCountry.csv file, import is impossible', 'visitors-online' );
				$result = false;
			} else {
				$vstrsnln_result = vstrsnln_import_noscript( $vstrsnln_count_files );
				if ( true == $vstrsnln_result ) {
					$message = __( 'Import was finished', 'visitors-online' );
					$result = true;
				} else {
					$error = __( 'Not enough rights to import from the IpToCountry.csv file, import is impossible', 'visitors-online' );
					$result = false;
				}
			}
		} else {
			$result = 0;
		}
		return array(
			'result'    => $result,
			'error'     => $error,
			'message'   => $message );
	}
}
