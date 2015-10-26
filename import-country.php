<?php
/* Count the number of lines in the file GeoIPCountryWhois.csv */
if ( ! function_exists( 'vstrsnln_count_rows' ) ) {
    function vstrsnln_count_rows( $noscript = false ) {
        if ( false == $noscript )
            check_ajax_referer( 'bws_plugin', 'vstrsnln_ajax_nonce_field' );

        $handle = fopen( plugin_dir_path( __FILE__ ) . 'GeoIPCountryWhois.csv', 'r' );
        if ( $handle ) {
            $file_number = 1;
            /* On how many lines in the file */
            $vstrsnln_lines_count = ( $noscript == false ) ? 3000 : 1000;
            if ( ! is_writable( plugin_dir_path( __FILE__ ) ) ) {
                return 0;
                exit;
            }
            $current_file = fopen( plugin_dir_path( __FILE__ ) . 'file_' . $file_number . '.csv', 'a' );
            if ( $current_file ) {
                $i = 0;
                while ( !feof( $handle ) ) {
                    $line = fgets( $handle );
                    fwrite( $current_file, $line );
                    $i++;
                    if ( $i == $vstrsnln_lines_count ) {
                        fclose( $current_file );
                        $file_number++;
                        $current_file = fopen( plugin_dir_path( __FILE__ ) . 'file_' . $file_number . '.csv', 'a' );
                        if ( $current_file ) {
                            $i = 0;
                        } else {
                            if ( $noscript == false ) {
                                echo 0;
                            } else {
                                return 0;
                            }
                        }                                            
                    }
                }
                fclose( $current_file );
                fclose( $handle );
                if ( $noscript == false ) {
                    echo $file_number;
                } else {
                    return $file_number;
                }
            } else {
                if ( $noscript == false ) {
                    echo 0;
                } else {
                    return 0;
                }
            }                
        } else {
            if ( $noscript == false ) {
                echo 0;
            } else {
                return 0;
            }
        }
        /* This is required to terminate immediately and return a proper response */
        wp_die();
    }
}

/* Fill in the table of country */
if ( ! function_exists( 'vstrsnln_insert_rows' ) ) {
    function vstrsnln_insert_rows( $number_file = false, $noscript = false ) {
        global $wpdb, $wp_filesystem;
        if ( false == $noscript )
            check_ajax_referer( 'bws_plugin', 'vstrsnln_ajax_nonce_field' );
        $prefix_bws             = $wpdb->base_prefix . 'bws_';
        $vstrsnln_access_type = get_filesystem_method();
        if ( $vstrsnln_access_type == 'direct' ) {
            $vstrsnln_creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, array() );
            if ( ! WP_Filesystem( $vstrsnln_creds ) ) {
                if ( $number_file == false ) {
                    echo false;
                } else {
                    return false;
                }
            }
            if ( $number_file == false ) {
                if ( isset( $_POST['count'] ) && file_exists( plugin_dir_path( __FILE__ ) . 'file_' . $_POST['count'] . '.csv' ) ) {
                    $filename = plugin_dir_path( __FILE__ ) . 'file_' . $_POST['count'] . '.csv';
                    $data_array = $wp_filesystem->get_contents_array( $filename );
                    if ( false !== $data_array && is_array( $data_array ) && ! empty( $data_array ) ) {
                        $sql = "INSERT IGNORE INTO `" . $prefix_bws . "country`
                            ( `ip_from`, `ip_to`, `ip_from_int`, `ip_to_int`, `short_country`, `name_country` )
                            VALUES ( " . implode( " ) , ( ", $data_array ) . " );";
                        $result = $wpdb->query( $sql );
                        unlink( $filename );
                        echo $result;
                    }
                }
            } else {
                if ( $number_file > 0 && file_exists( plugin_dir_path( __FILE__ ) . 'file_' . $number_file . '.csv' ) ) {
                    $filename   = plugin_dir_path( __FILE__ ) . 'file_' . $number_file . '.csv';
                    $data_array = $wp_filesystem->get_contents_array( $filename );
                    if ( false !== $data_array && is_array( $data_array ) && ! empty( $data_array ) ) {
                        $sql = "INSERT IGNORE INTO `" . $prefix_bws . "country`
                            ( `ip_from`, `ip_to`, `ip_from_int`, `ip_to_int`, `short_country`, `name_country` )
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
        if ( 0 == $result )
            $result = true;
        return $result;
    }
}

/* The conclusion to the settings page of information on imports of table */
if ( ! function_exists( 'vstrsnln_form_import_country' ) ) {
    function vstrsnln_form_import_country( $page_url ) {
        global $wpdb;
        $prefix_bws = $wpdb->base_prefix . 'bws_';
        $vstrsnln_table_name = $prefix_bws . 'country';
        /* Table exists */
        if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $prefix_bws . "country'" ) == $vstrsnln_table_name ) {
            $vstrsnln_table_full = $wpdb->get_var( "
                SELECT count( * )
                FROM `" . $prefix_bws . "country`
                LIMIT 1"
            );
        } else {
            $vstrsnln_table_full = 0;
        }?>
        <form id="vstrsnln_import_block" method="post" action="<?php echo $page_url; ?>">
            <div class="vstrsnln-info">
                <?php $vstrsnln_file_there = 1;
                if ( file_exists( plugin_dir_path( __FILE__ ) . 'GeoIPCountryWhois.csv' ) ) {
                    /* Open the file in read mode */
                    $current_file = fopen( plugin_dir_path( __FILE__ ) . 'GeoIPCountryWhois.csv', 'r' ); 
                    if ( $current_file ) {
                        $vstrsnln_content = __( 'To collect statistics on the country for the day with the highest number of visits, you need to import the information on the countries to the database.', 'visitors-online' ) . '<br />';
                        $vstrsnln_content .= __( 'Importing country information from the GeoIPCountryWhois.csv file', 'visitors-online' );
                    } else {
                        $vstrsnln_content = __( 'You do not have permission to access the file', 'visitors-online' ) . '&#032;&#032;' . plugin_dir_path( __FILE__ ) . 'GeoIPCountryWhois.csv' .
                        '&#044;&#032;&#032;' . __( 'cannot be imported', 'visitors-online' );
                        $vstrsnln_file_there = 0;
                    }
                    echo $vstrsnln_content;
                } else {
                    $vstrsnln_content = plugin_dir_path( __FILE__ ) . 'GeoIPCountryWhois.csv' . '&#032;&#032;' . __( 'the file is not found, import is impossible', 'visitors-online' );
                    echo $vstrsnln_content;
                    $vstrsnln_file_there = 0;
                } ?>
                <div class="vstrsnln_clear"></div>
                <?php if ( $vstrsnln_table_full > 0 ) {
                    _e( 'The table is already loaded, you can also update the data using this instructions', 'visitors-online' );
                } else {
                    _e( 'Instructions on downloading and updating the country table', 'visitors-online' );
                } ?>
                 <a href="https://docs.google.com/document/d/1sxxeDleJdPS8HvRdYwYSABQ586t1s-Z8r6wy55iXJCM/edit" target="_blank" style="word-break: break-word;">https://docs.google.com/document/d/1sxxeDleJdPS8HvRdYwYSABQ586t1s-Z8r6wy55iXJCM/edit</a>
            </div>
            <div class="vstrsnln_clear"></div>
            <?php if ( 1 == $vstrsnln_file_there ) {
                if ( $vstrsnln_table_full > 0 ) { ?>
                    <input type="submit" name="vstrsnln_button_import" class="button-primary" value="<?php _e( 'Update', 'visitors-online' ); ?>" />
                <?php } else {?>
                    <input type="submit" name="vstrsnln_button_import" class="button-primary" value="<?php _e( 'Import', 'visitors-online' ); ?>" />
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
    <?php }
}

/* Pressing the 'Import Country' */
if ( ! function_exists( 'vstrsnln_press_buttom_import' ) ) {
    function vstrsnln_press_buttom_import() {
        $message = $error = '';
        if ( isset( $_REQUEST['vstrsnln_button_import'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'vstrsnln_nonce_name' ) ) {
            $vstrsnln_count_files = vstrsnln_count_rows( true );
            if ( $vstrsnln_count_files == 0 ) {
                $error = __( 'Not enough rights to import from the GeoIPCountryWhois.csv file, import is impossible', 'visitors-online' );
                $result = false;
            } else {
                $vstrsnln_result = vstrsnln_import_noscript( $vstrsnln_count_files );
                if ( $vstrsnln_result == true ) {
                    $message = __( 'Import was finished', 'visitors-online' );
                    $result = true;
                } else {
                    $error = __( 'Not enough rights to import from the GeoIPCountryWhois.csv file, import is impossible', 'visitors-online' );
                    $result = false;
                }
            }
        } else {
            $result = 0;
        }
        return array( 
            'result'    => $result, 
            'error'     => $error,
            'message'   => $message);        
    }
} ?>