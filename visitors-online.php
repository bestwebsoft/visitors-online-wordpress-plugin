<?php
/*
Plugin Name: Visitors Online by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/
Description: Plugin allows to see how many users, guests and bots are online on the website.
Author: BestWebSoft
Text Domain: visitors-online
Domain Path: /languages
Version: 0.4
Author URI: http://bestwebsoft.com/
License: GPLv3 or later
*/

/*  © Copyright 2015  BestWebSoft  ( http://support.bestwebsoft.com )

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Include files on import of table and declare the prefix */
global $vstrsnln_prefix, $vstrsnln_prefix_bws, $wpdb;
$vstrsnln_prefix		= $wpdb->base_prefix . 'vstrsnln_';
$vstrsnln_prefix_bws	= $wpdb->base_prefix . 'bws_';		
$vstrsnln_fpath = dirname( __FILE__ ) . '/import-country.php';
if ( file_exists( $vstrsnln_fpath ) ) {
	include $vstrsnln_fpath;
}

/* Function for adding menu and submenu */
if ( ! function_exists( 'vstrsnln_admin_menu' ) ) {
	function vstrsnln_admin_menu() {
		bws_add_general_menu( plugin_basename( __FILE__ ) );
		$settings = add_submenu_page( 'bws_plugins', 'Visitors Online', 'Visitors Online', 'manage_options', 'visitors-online.php', 'vstrsnln_settings_page' );		
		add_action( 'load-' . $settings, 'vstrsnln_add_tabs' );
	}
}

if ( ! function_exists( 'vstrsnln_plugins_loaded' ) ) {
	function vstrsnln_plugins_loaded() {
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'visitors-online', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists( 'vstrsnln_plugin_init' ) ) {
	function vstrsnln_plugin_init() {
		global $vstrsnln_plugin_info;

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );

		if ( empty( $vstrsnln_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$vstrsnln_plugin_info = get_plugin_data( __FILE__ );
		}
		/* Function check if plugin is compatible with current WP version */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $vstrsnln_plugin_info, '3.8', '3.4' );
		
		/* Get/Register and check settings for plugin */
		vstrsnln_default_options();
				
		vstrsnln_write_user_base();
	}
}

/* Function to add plugin version. */
if ( ! function_exists ( 'vstrsnln_plugin_admin_init' ) ) {
	function vstrsnln_plugin_admin_init() {
		global $bws_plugin_info, $vstrsnln_plugin_info, $bws_shortcode_list;
		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) ) {
			$bws_plugin_info = array( 'id' => '213', 'version' => $vstrsnln_plugin_info["Version"] );
		}
		/* add Visitors Online to global $bws_shortcode_list  */
		$bws_shortcode_list['vstrsnln'] = array( 'name' => 'Visitors Online' );
	}
}

/* Set default settings */
if ( ! function_exists ( 'vstrsnln_default_options' ) ) {
	function vstrsnln_default_options() {
		global $vstrsnln_plugin_info, $vstrsnln_user_interval, $vstrsnln_options;
		/* Add options to database */		
		$db_version	= "1.0";
		
		$vstrsnln_option_defaults = array(
			'check_user_interval'		=> 15,
			'check_browser'				=> 0,
			'check_country'				=> 0,
			'plugin_option_version'		=> $vstrsnln_plugin_info["Version"],
			'plugin_db_version'			=> $db_version,
			'display_settings_notice'	=> 1,
			'first_install'				=> strtotime( "now" )
		);

		if ( ! get_option( 'vstrsnln_options' ) )	
			add_option( 'vstrsnln_options', $vstrsnln_option_defaults );

		$vstrsnln_options = get_option( 'vstrsnln_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $vstrsnln_options['plugin_option_version'] ) || $vstrsnln_options['plugin_option_version'] != $vstrsnln_plugin_info["Version"] ) {
			$vstrsnln_option_defaults['display_settings_notice'] = 0;
			$vstrsnln_options = array_merge( $vstrsnln_option_defaults, $vstrsnln_options );
			$vstrsnln_options['plugin_option_version'] = $vstrsnln_plugin_info["Version"];
			/* show pro features */
			$vstrsnln_options['hide_premium_options'] = array();

			$update_option = true;
		}
		/* Update plugin database */
		if ( ! isset( $vstrsnln_options['plugin_db_version'] ) || $vstrsnln_options['plugin_db_version'] != $db_version ) {
			vstrsnln_install_base();
			$vstrsnln_options['plugin_db_version'] = $db_version;
			$update_option = true;
		}
		if ( isset( $update_option ) )
			update_option( 'vstrsnln_options', $vstrsnln_options );

		$vstrsnln_user_interval = $vstrsnln_options['check_user_interval'];		
	}
}

/* Function to add script and styles to the admin panel. */
if ( ! function_exists ( 'vstrsnln_admin_head' ) ) {
	function vstrsnln_admin_head() {
		if ( isset( $_REQUEST['page'] ) && 'visitors-online.php' == $_REQUEST['page'] ) {
			wp_enqueue_style( 'vstrsnln_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'vstrsnln_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_script( 'vstrsnln_country_script', plugins_url( 'js/import-country.js', __FILE__ ), array( 'jquery' ) );
			$vstrsnln_var = array( 
				'notice_finish' 	=> __( 'Import was finished', 'visitors-online' ),
				'notice_false'	 	=> __( 'Not enough rights to import from the GeoIPCountryWhois.csv file, import is impossible', 'visitors-online' ),
				'vstrsnln_nonce'	=> wp_create_nonce( 'bws_plugin', 'vstrsnln_ajax_nonce_field' )
			);
			wp_localize_script( 'vstrsnln_country_script', 'vstrsnln_var', $vstrsnln_var );
			wp_localize_script( 'vstrsnln_script', 'vstrsnln_ajax', array( 'vstrsnln_nonce' => wp_create_nonce( 'bws_plugin', 'vstrsnln_ajax_nonce_field' ) ) );
		}
	}
}

/* Function sets crowns events */
if ( ! function_exists( 'vstrsnln_install' ) ) {
	function vstrsnln_install() {
		vstrsnln_install_base();
		/* Add the planned hook - check users online */
		if ( ! wp_next_scheduled( 'vstrsnln_check_users' ) ) {
			$vstrsnln_time = time() + 60;
			wp_schedule_event( $vstrsnln_time, 'vstrsnln_interval', 'vstrsnln_check_users' );
		}
		/* Add the planned hook - record of the day with the maximum number of visits */
		if ( ! wp_next_scheduled( 'vstrsnln_count_visits_day' ) ) {
			$vstrsnln_time_daily = strtotime( date( 'Y-m-d', strtotime( ' +1 day' ) ) . ' 00:00:59' );
			wp_schedule_event( $vstrsnln_time_daily, 'daily', 'vstrsnln_count_visits_day' );
		}
	}
}

/* Function to create a new tables in database, sets crowns events, settings defaults */
if ( ! function_exists( 'vstrsnln_install_base' ) ) {
	function vstrsnln_install_base() {
		global $wpdb, $vstrsnln_prefix, $vstrsnln_prefix_bws;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/* Data users connect : connection time, country, browser, etc. */
		$sql = "CREATE TABLE IF NOT EXISTS `" . $vstrsnln_prefix . "detailing` (
			`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`date_connection` DATE NOT NULL,
			`time_on` INT( 10 ),
			`time_off` INT( 10 ),
			`user_type` CHAR( 5 ),
			`browser` CHAR( 100 ),
			`country_id` INT( 10 ),
			`ip_user` CHAR( 16 ),
			`user_cookie` CHAR( 32 ),
			`blog_id` CHAR( 5 ),
			PRIMARY KEY ( `id` )
			) ENGINE = InnoDB DEFAULT CHARSET = utf8;";
		dbDelta( $sql );
		/* Data about day wiht a number of connections */
		$sql = "CREATE TABLE IF NOT EXISTS `" . $vstrsnln_prefix . "general` (
			`id` INT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
			`date_connection` DATE NOT NULL,
			`number_users` INT,
			`number_bots` INT,
			`number_guests` INT,
			`number_visits` INT,
			`blog_id` CHAR( 5 ),
			`country` CHAR( 60 ),
			`browser` CHAR( 70 ),
			PRIMARY KEY ( `id` )
			) ENGINE = InnoDB DEFAULT CHARSET = utf8;";
		dbDelta( $sql );
		$wpdb->query( "ALTER TABLE `" . $vstrsnln_prefix . "general` 
			ADD UNIQUE ( `date_connection` ,`blog_id` )" );
		/* Identification of the country by IP */
		$sql = "CREATE TABLE IF NOT EXISTS `" . $vstrsnln_prefix_bws . "country` (
			`id_country` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`ip_from` CHAR( 15 ),
			`ip_to` CHAR( 15 ),
			`ip_from_int` BIGINT( 12 ) UNIQUE,
			`ip_to_int` BIGINT( 12 ) UNIQUE,
			`short_country` CHAR( 2 ),
			`name_country` CHAR( 30 ),
			PRIMARY KEY ( `id_country` )
			) ENGINE = InnoDB DEFAULT CHARSET = utf8;";
		dbDelta( $sql );
	}
}

/* Add or changes the data a user in the table "Detailing" */
if ( ! function_exists( 'vstrsnln_write_user_base' ) ) {
	function vstrsnln_write_user_base() {
		global $wpdb, $vstrsnln_user_interval, $vstrsnln_prefix, $vstrsnln_prefix_bws;
		/* If this event crowns back */
		if ( defined( 'DOING_CRON' ) && DOING_CRON )
			return;
		
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$vstrsnln_user_agent = $_SERVER['HTTP_USER_AGENT'];
		}

		$vstrsnln_blog_id		= get_current_blog_id();
		$vstrsnln_record_table	= 0;

		if ( isset( $_COOKIE['vstrsnln'] ) ) {
			$vstrsnln_record_table = (int) $wpdb->get_var( "
				SELECT count( * )
				FROM `" . $vstrsnln_prefix . "detailing`
				WHERE `time_off` IS NULL
					AND `user_cookie` = '" . $_COOKIE['vstrsnln'] . "'
				LIMIT 1"
			);
		}
		$vstrsnln_guest_admin = ( is_admin() ) ? false : true;

		if ( isset( $_COOKIE['vstrsnln'] ) && $vstrsnln_record_table > 0 ) {
			$vstrsnln_user = $vstrsnln_guest = false;			
			/* Сheck bot */
			if ( true == vstrsnln_list_bots( $vstrsnln_user_agent ) ) {
				$vstrsnln_user_type = "bot";				
			} else {
				$vstrsnln_current_user = wp_get_current_user();
				/* If not bot, check guest */
				$vstrsnln_current_id = $vstrsnln_current_user->ID;
				if ( false == $vstrsnln_guest_admin ) {
					$vstrsnln_user		= true;
					$vstrsnln_user_type = "user";					
				} else {
					if ( $vstrsnln_current_id == 0 ) {
						$vstrsnln_guest		= true;
						$vstrsnln_user_type = "guest";
					} else {
					/* Check user */
						$vstrsnln_user		= true;
						$vstrsnln_user_type = "user";
					}
				}
			}			
			/* Update record database table */
			setcookie( 'vstrsnln', $_COOKIE['vstrsnln'], time() + $vstrsnln_user_interval * 60, "/" );
			$wpdb->update( $vstrsnln_prefix . 'detailing', 
				array( 
					'time_on'			=> time(), 
					'date_connection'	=> date( 'Y.m.d' ),
					'user_type' 		=> $vstrsnln_user_type,
				 	'blog_id' 			=> $vstrsnln_blog_id
				), array(
					'user_cookie' => $_COOKIE['vstrsnln'] )			
			);
		} else {
			/* Сreate a new record of the database table */
			$vstrsnln_bot = $vstrsnln_user = $vstrsnln_guest = false;

			if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
				$vstrsnln_user_agent = $_SERVER['HTTP_USER_AGENT'];
				/* Detects the browser and its version */
				preg_match( '/(Firefox|Opera|Chrome|MSIE|OPR|Trident|Avant|Acoo|Iron|Orca|Lynx|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/', $vstrsnln_user_agent, $vstrsnln_browser_info );
				list( , $browser, $version )	= $vstrsnln_browser_info;
				$vstrsnln_browser 			= $browser . ' ' . $version;
				if ( preg_match( '/Opera ( [0-9.]+ ) /i', $vstrsnln_user_agent, $opera ) ) {
					if ( $opera[1] )
						$vstrsnln_browser = 'Opera ' . $opera[1];
					else
						$vstrsnln_browser = 'Opera ';
				}
				if ( $browser == 'MSIE' ) {
					preg_match( '/( Maxthon|Avant Browser|MyIE2 )/i', $vstrsnln_user_agent, $ie );
					$vstrsnln_browser = ( $ie && $ie[1] ) ? $ie[1] . ' based on IE ' . $version : 'IE '. $version;
				}
				if ( $browser == 'Firefox' ) {
					preg_match( '/( Flock|Navigator|Epiphany)\/([0-9.]+ ) /', $vstrsnln_user_agent, $ff );
					if ( $ff )
						$vstrsnln_browser = ( $ff[1] && $ff[2] ) ? $ff[1] . ' ' . $ff[2] : '';
				}
				if ( $browser == 'Opera' && $version == '9.80' )
					$vstrsnln_browser = 'Opera ' . substr( $vstrsnln_user_agent,-5 );
				if ( $browser == 'Version' )
					$vstrsnln_browser = 'Safari ' . $version;
				if ( ! $browser && strpos( $vstrsnln_user_agent, 'Gecko' ) )
					$vstrsnln_browser = 'Browser based on Gecko';
				/* Сheck bot */
				if ( true == vstrsnln_list_bots( $vstrsnln_user_agent ) ) {
					$vstrsnln_user_type = "bot";
					$vstrsnln_bot		= true;
				}
			} else {
				$vstrsnln_browser = "";
			}
			if ( true !== $vstrsnln_bot ) {
				$vstrsnln_current_user = wp_get_current_user();
				/* If not bot, check guest */
				$vstrsnln_current_id = $vstrsnln_current_user->ID;
				if ( false == $vstrsnln_guest_admin ) {
					$vstrsnln_user 		= true;
					$vstrsnln_user_type = "user";					
				} else {
					if ( $vstrsnln_current_id == 0 ) {
						$vstrsnln_guest 	= true;
						$vstrsnln_user_type = "guest";
					} else {
					/* Check user */
						$vstrsnln_user 		= true;
						$vstrsnln_user_type = "user";
					}
				}
			}
			/* Set a cookie */
			$vstrsnln_cookie_value = md5( 'vstrsnln' . date( 'H:i:s' ) );
			setcookie( 'vstrsnln', $vstrsnln_cookie_value, time() + $vstrsnln_user_interval * 60, "/" );
			/* Detects the IP */
			$ip = '';
			if ( isset( $_SERVER ) ) {
				$sever_vars = array( 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
				foreach ( $sever_vars as $var ) {
					if ( isset( $_SERVER[ $var ] ) && ! empty( $_SERVER[ $var ] ) ) {
						if ( filter_var( $_SERVER[ $var ], FILTER_VALIDATE_IP ) ) {
							$ip = $_SERVER[ $var ];
							break;
						} else { /* if proxy */
							$ip_array = explode( ',', $_SERVER[ $var ] );
							if ( is_array( $ip_array ) && ! empty( $ip_array ) && filter_var( $ip_array[0], FILTER_VALIDATE_IP ) ) {
								$ip = $ip_array[0];
								break;
							}
						}
					}
				}
			}
			if ( ! empty( $ip ) ) {
				/* Detects the country */
				$vstrsnln_country 	= $wpdb->get_var( "
					SELECT `id_country`
					FROM `" . $vstrsnln_prefix_bws . "country`
					WHERE `ip_from_int` <= '" . sprintf( '%u', ip2long( $ip ) ) . "'
						AND `ip_to_int` >= '" . sprintf( '%u', ip2long( $ip ) ) . "'
					LIMIT 1"
				);
				$wpdb->insert( $vstrsnln_prefix . 'detailing',
					array(
						'date_connection'	=> date( 'Y.m.d' ),
						'time_on'			=> time(),
						'user_type'			=> $vstrsnln_user_type,
						'browser'			=> $vstrsnln_browser,
						'country_id'		=> $vstrsnln_country,
						'ip_user'			=> $ip,
						'user_cookie'		=> $vstrsnln_cookie_value,
						'blog_id'			=> $vstrsnln_blog_id
					)
				);
			}
		}
	}
}

/* We do not check the elapsed time during which the user is considered online */
if ( ! function_exists( 'vstrsnln_check_user' ) ) {
	function vstrsnln_check_user() {
		global $wpdb, $vstrsnln_user_interval, $vstrsnln_prefix;
		$vstrsnln_blog_id	= get_current_blog_id();
		$time 				= time() - $vstrsnln_user_interval * 60;
		$vstrsnln_all_users = $wpdb->get_results( "
			SELECT `id`
			FROM `" . $vstrsnln_prefix . "detailing`
			WHERE `time_off` IS NULL
				AND `time_on` <= " . $time . "
				AND `blog_id` = '" . $vstrsnln_blog_id . "'"
		);
		if ( is_array( $vstrsnln_all_users ) ) {
			foreach ( $vstrsnln_all_users as $vstrsnln_user ) {
				$wpdb->update( $vstrsnln_prefix . 'detailing', array(
					'time_off'	=> time()
				 ), array(
				 	'id' 		=> $vstrsnln_user->id,
				 	'blog_id' 	=> $vstrsnln_blog_id )			 
				);				
			}
		}
	}
}

/* Work on the settings page */
if ( ! function_exists( 'vstrsnln_settings_page' ) ) {
	function vstrsnln_settings_page() {
		global $wpdb, $vstrsnln_options, $vstrsnln_plugin_info, $vstrsnln_prefix, $wp_version;
		$message = $error = '';
		
		if ( isset( $_REQUEST['vstrsnln_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'vstrsnln_nonce_name' ) ) {
			if ( isset( $_POST['bws_hide_premium_options'] ) ) {
				$hide_result = bws_hide_premium_options( $vstrsnln_options );
				$vstrsnln_options = $hide_result['options'];
			}

			/* Pressing the clear statistics */
			if ( isset( $_POST['vstrsnln_button_clean'] ) ) {
				$wpdb->query( "TRUNCATE `" . $vstrsnln_prefix . "general`;" );
				$wpdb->query( "TRUNCATE `" . $vstrsnln_prefix . "detailing`;" );
				$vstrsnln_number_general = $wpdb->get_var( "
					SELECT count( * )
					FROM `" . $vstrsnln_prefix . "general`
					LIMIT 1"
				);
				if ( $vstrsnln_number_general == 0 )
					$message = __( 'Statistics was cleared successfully', 'visitors-online' );
			} else {
				
				/* Save data for settings page */				
				if ( $vstrsnln_options['check_user_interval'] != $_REQUEST['vstrsnln_check_user_interval'] ) {
					/* Add the planned hook - check users online */
					wp_clear_scheduled_hook( 'vstrsnln_check_users' );
					if ( ! wp_next_scheduled( 'vstrsnln_check_users' ) ) {
						wp_schedule_event( time(), 'vstrsnln_interval', 'vstrsnln_check_users' );
					}
				}
				if ( isset( $_REQUEST['vstrsnln_check_user_interval'] ) ) {
					if ( empty( $_REQUEST['vstrsnln_check_user_interval'] ) ) {
						$error = __( 'Please fill The time period. The settings are not saved', 'visitors-online' );
					} else {
						$vstrsnln_options['check_user_interval']	= isset( $_REQUEST['vstrsnln_check_user_interval'] ) ? ( $_REQUEST['vstrsnln_check_user_interval'] ) : 1;
						$vstrsnln_options['check_browser']			= isset( $_REQUEST['check_browser'] ) ? 1 : 0;
						$vstrsnln_options['check_country']			= isset( $_REQUEST['check_country'] ) ? 1 : 0;
						update_option( 'vstrsnln_options', $vstrsnln_options );
						$message = __( 'Settings saved', 'visitors-online' );
					}
				}
			}			
		}
		/* Pressing the 'Import Country' */
		$vstrsnln_result_downloaded = vstrsnln_press_buttom_import();
		$result = $vstrsnln_result_downloaded['result'];
		if ( 0 !== $result ) {
			$message = $vstrsnln_result_downloaded['message'];
			$error = $vstrsnln_result_downloaded['error'];
		}
       	if ( true == $result ) {
        	vstrsnln_check_country( true );
        }

        $bws_hide_premium_options_check = bws_hide_premium_options_check( $vstrsnln_options );

		/* GO PRO */
		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
			$go_pro_result = bws_go_pro_tab_check( plugin_basename( __FILE__ ), 'vstrsnln_options' );
			if ( ! empty( $go_pro_result['error'] ) )
				$error = $go_pro_result['error'];
			elseif ( ! empty( $go_pro_result['message'] ) )
				$message = $go_pro_result['message'];
		} ?>		
		<div class="wrap">
			<h2><?php _e( 'Visitors Online Settings', 'visitors-online' ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab <?php if ( ! isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=visitors-online.php"><?php _e( 'Settings', 'visitors-online' ); ?></a>
				<a class="nav-tab bws_go_pro_tab<?php if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=visitors-online.php&amp;action=go_pro"><?php _e( 'Go PRO', 'visitors-online' ); ?></a>
			</h2>
			<div class="updated fade" <?php if ( '' == $message || '' != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div class="error" <?php if ( '' == $error ) echo 'style="display:none"'; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php if ( ! empty( $hide_result['message'] ) ) { ?>
				<div class="updated fade"><p><strong><?php echo $hide_result['message']; ?></strong></p></div>
			<?php }			
			if ( ! isset( $_GET['action'] ) ) {
				bws_show_settings_notice(); ?>
				<br/>
				<div>
					<?php printf( 
						__( 'You can add the counter of Visitors Online by clicking on %s button.', 'visitors-online' ), 
						'<span class="bws_code"><img style="vertical-align: sub;" src="' . plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ ) . '" alt=""/></span>'
					); ?>
					<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help">
						<div class="bws_hidden_help_text" style="min-width: 180px;">
							<?php printf( 
								__( "You can add the counter of Visitors Online to your content by clicking on %s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %s", 'visitors-online' ), 
								'<code><img style="vertical-align: sub;" src="' . plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ ) . '" alt="" /></code>',
								'<code>[vstrsnln_info]</code>'
							); ?>
						</div>
					</div>
					<br>
					<?php _e( 'You can also add a widget', 'visitors-online' ); ?>: <strong>Visitors Online</strong>
				</div>
				<p><?php _e( 'Statistics can be viewed on the Dashboard.', 'visitors-online' ); ?></p>
				<form class="bws_form" method="post" action="admin.php?page=visitors-online.php">
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e( 'The time period when the user is online, without making any actions', 'visitors-online' ); ?></th>
							<td>
								<input type="number" min="1" max="60" name="vstrsnln_check_user_interval" value="<?php echo $vstrsnln_options['check_user_interval']; ?>" />
								<?php _e( 'min', 'visitors-online' ); ?>
							</td>						
						</tr>
					</table>
					<?php if ( ! $bws_hide_premium_options_check ) { ?>
						<div class="bws_pro_version_bloc">
							<div class="bws_pro_version_table_bloc">	
								<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'visitors-online' ); ?>"></button>
								<div class="bws_table_bg"></div>											
								<table class="form-table bws_pro_version">
									<tr valign="top">
										<th scope="row">
											<?php _e( 'Update GeoIP', 'visitors-online' ); ?>										
										</th>
										<td>
											<?php _e( 'every', 'visitors-online' ); ?>
											<input type="number" disabled name="vstrsnln_loading_country" value="0" />
											<?php _e( 'months', 'visitors-online' ); ?> <br>
											<span class="bws_info">
												<?php _e( 'This option allows you to download lists with registered IP addresses all over the world to the database (from', 'visitors-online' ); ?>&nbsp;<a href="https://www.maxmind.com" target="_blank">https://www.maxmind.com</a>).
												<br>
												<?php _e( 'With this, you receive an information about each IP address, and to which country it belongs to. You can select the desired frequency for IP database updating', 'visitors-online' ); ?>.
											</span>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" colspan="2">
											* <?php _e( 'If you upgrade to Pro version all your settings will be saved.', 'visitors-online' ); ?>
										</th>
									</tr>
								</table>	
							</div>
							<div class="bws_pro_version_tooltip">
								<div class="bws_info">
									<?php _e( 'Unlock premium options by upgrading to Pro version', 'visitors-online' ); ?>
								</div>
								<a class="bws_button" href="http://bestwebsoft.com/products/visitors-online/?k=1b01d30e84bb97b2afecb5f34c43931d&pn=216&v=<?php echo $vstrsnln_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Visitors Online Pro"><?php _e( 'Learn More', 'visitors-online' ); ?></a>
								<div class="clear"></div>
							</div>
						</div>
					<?php } ?>
					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php _e( 'Clear the statistics', 'visitors-online' ); ?></th>
							<td>
								<input type="submit" name="vstrsnln_button_clean" class="button" value=<?php _e( 'Clear', 'visitors-online' ); ?> />
							</td>
						</tr>
					</table>
					<p class="submit">
						<input type="hidden" name="vstrsnln_submit" value="submit" />
						<input id="bws-submit-button" type="submit" name="vstrsnln_button_save" class="button-primary" value="<?php _e( 'Save Changes', 'visitors-online' ) ?>" />
					</p>
					<?php wp_nonce_field( plugin_basename( __FILE__ ), 'vstrsnln_nonce_name' ); ?>
				</form>
				<?php vstrsnln_form_import_country( "admin.php?page=visitors-online.php" );
			} elseif ( 'go_pro' == $_GET['action'] ) {
				bws_go_pro_tab_show( $bws_hide_premium_options_check, $vstrsnln_plugin_info, plugin_basename( __FILE__ ), 'visitors-online.php', 'visitors-online-pro.php', 'visitors-online-pro/visitors-online-pro.php', 'visitors-online', '1b01d30e84bb97b2afecb5f34c43931d', '216', isset( $go_pro_result['pro_plugin_is_activated'] ) );
			} 
			bws_plugin_reviews_block( $vstrsnln_plugin_info['Name'], 'visitors-online' ); ?>				
		</div>
	<?php }	
}

/* Checking whether the maximum number of users yesterday */
if ( ! function_exists( 'vstrsnln_write_max_visits' ) ) {
	function vstrsnln_write_max_visits() {
		global $wpdb, $vstrsnln_prefix, $vstrsnln_prefix_bws;

		$vstrsnln_blog_id = get_current_blog_id();
		/* Date two days ago, to clean the table detailing */
		$vstrsnln_date_delete = date( 'Y-m-d', time() - 172800 );
		/* Determine the last day for which the processed statistics */
		$general_last_day = $wpdb->get_var( "
			SELECT `date_connection`
			FROM `" . $vstrsnln_prefix . "general`
			WHERE `blog_id` = '" . $vstrsnln_blog_id . "'
			ORDER BY `date_connection` DESC 
			LIMIT 1"
		);
 		if ( empty( $general_last_day ) ) {
			$daterange = new DateTime( 'yesterday' );
		} else {
			$begin = new DateTime( $general_last_day );
			$begin = $begin->modify( '+1 day' );
			$end = new DateTime( '-1 day' );
			$interval = new DateInterval( 'P1D' );
			$daterange = new DatePeriod( $begin, $interval ,$end );
		}
		
		foreach ( $daterange as $key => $date ) {			
			if ( is_object( $date ) ) {
				$vstrsnln_date_yesterday = $date->format( 'Y-m-d' );
			} else {
				$vstrsnln_date_yesterday = date( 'Y-m-d', strtotime( $date ) );
			}
			$vstrsnln_number_visits_detailing		= $wpdb->get_var( "				
				SELECT count( * )
				FROM `" . $vstrsnln_prefix . "detailing`
				WHERE `date_connection` = '" . $vstrsnln_date_yesterday . "'
					AND `blog_id` = '" . $vstrsnln_blog_id . "'
				LIMIT 1"
			);	
			if ( $vstrsnln_number_visits_detailing != 0 ) {
				$vstrsnln_type_user	= $wpdb->get_row( "
					SELECT COUNT( * ) AS 'guest',
						( SELECT COUNT( * ) FROM `" . $vstrsnln_prefix . "detailing` 
							WHERE `date_connection` = '" . $vstrsnln_date_yesterday . "'
								AND `user_type` = 'bot'
								AND `blog_id` = '" . $vstrsnln_blog_id . "'
						) AS 'bot',
						( SELECT COUNT( * ) FROM `" . $vstrsnln_prefix . "detailing` 
							WHERE `date_connection` = '" . $vstrsnln_date_yesterday . "'
								AND `user_type` = 'user'
								AND `blog_id` = '" . $vstrsnln_blog_id . "'
						) AS 'user'
					FROM `" . $vstrsnln_prefix . "detailing`
					WHERE `date_connection` = '" . $vstrsnln_date_yesterday . "'
					AND `user_type` = 'guest'
					AND `blog_id` = '" . $vstrsnln_blog_id . "'
					LIMIT 1"
				);
				
				$vstrsnln_number_bots	= $vstrsnln_type_user->bot;
				$vstrsnln_number_guests	= $vstrsnln_type_user->guest;
				$vstrsnln_number_users	= $vstrsnln_type_user->user;

				/* We determine which country had the maximum number of connections */
				$vstrsnln_country_max_connections	= $wpdb->get_results( "
					SELECT count( * ) as max_count, `name_country`
					FROM `" . $vstrsnln_prefix . "detailing`
					LEFT JOIN " . $vstrsnln_prefix_bws . "country ON `" . $vstrsnln_prefix . "detailing`.country_id = " . $vstrsnln_prefix_bws . "country.id_country
					WHERE `date_connection` = '" . $vstrsnln_date_yesterday . "'
					GROUP BY `" . $vstrsnln_prefix . "detailing`.country_id
					ORDER BY count( * )
					DESC LIMIT 3"
				);
				$vstrsnln_number_connections 	= 0;
				$vstrsnln_name_country 			= "";
				if ( $vstrsnln_country_max_connections ) {
					foreach ( $vstrsnln_country_max_connections as $vstrsnln_country ) {
						if ( $vstrsnln_number_connections <= $vstrsnln_country->max_count ) {
							$vstrsnln_number_connections 	= $vstrsnln_country->max_count;
							$vstrsnln_name_country .= $vstrsnln_country->name_country . ' ';
						}
					}
				}
				/* We determine which browser had the maximum number of connections */
				$vstrsnln_browser_max_connections = $wpdb->get_results( "
					SELECT count( * ) as max_count, `browser`
					FROM `" . $vstrsnln_prefix . "detailing`
					WHERE `date_connection` = '" . $vstrsnln_date_yesterday . "' 
						AND `browser` != '' 
					GROUP BY browser 
					ORDER BY count( * ) 
					DESC LIMIT 3"
				);
				$vstrsnln_number_connections 	= 0;
				$vstrsnln_name_browser 			= "";
				if ( $vstrsnln_browser_max_connections ) {
					foreach ( $vstrsnln_browser_max_connections as $vstrsnln_browser ) {
						if ( $vstrsnln_number_connections <= $vstrsnln_browser->max_count ) {
							$vstrsnln_number_connections 	= $vstrsnln_browser->max_count;
							$vstrsnln_name_browser 	.= $vstrsnln_browser->browser . ' ';
						}
					}
				}						
				$wpdb->insert( $vstrsnln_prefix . 'general', 
					array(
						'date_connection'	=> $vstrsnln_date_yesterday,
						'number_users'		=> $vstrsnln_number_users,
						'number_bots'		=> $vstrsnln_number_bots,
						'number_guests'		=> $vstrsnln_number_guests,
						'number_visits'		=> $vstrsnln_number_visits_detailing,
						'blog_id'			=> $vstrsnln_blog_id,
						'country'			=> $vstrsnln_name_country,
						'browser'			=> $vstrsnln_name_browser
					)
				);
			}
			if( !is_object( $date ) )	
				break;
		}
		/* Keep records for two days, delete the remaining */
		$wpdb->query( "DELETE
			FROM `" . $vstrsnln_prefix . "detailing`
			WHERE `date_connection` <= '" . $vstrsnln_date_delete . "'
				AND `blog_id` = '" . $vstrsnln_blog_id . "'"
		);
	}
}

/* Create an interval for checking user online */
if ( ! function_exists( 'vstrsnln_add_user_interval' ) ) {
	function vstrsnln_add_user_interval( $schedules ) {
		global $vstrsnln_options;

		if ( ! $vstrsnln_options )
			vstrsnln_default_options();
		
		$vstrsnln_user_interval 		= $vstrsnln_options['check_user_interval'];
		$vstrsnln_display 				= $vstrsnln_user_interval . __( 'min', 'visitors-online' );
		$schedules['vstrsnln_interval']	= array(
			'interval'	=> 60 * $vstrsnln_user_interval,
			'display'	=> $vstrsnln_display
		);
		return $schedules;
	}
}

/* List of bots */
if ( ! function_exists( 'vstrsnln_list_bots' ) ) {
	function vstrsnln_list_bots( $vstrsnln_user_agent ) {
		$vstrsnln_array_bots = array(
			'AbachoBOT', 'accoona', 'AdsBot-Google', 'agama', 'alexa.com', 'AltaVista', 'aport/',
			'ask.com', 'ASPSeek', 'bing.com', 'Baiduspider/', 'Copyscape.com', 'crawler@fast', 'CrocCrawler',
			'Dumbot', 'FAST-WebCrawler', 'GeonaBot', 'gigabot', 'Gigabot', 'googlebot/', 'Googlebot/',
			'ia_archiver', 'igde.ru', 'liveinternet.ru', 'Lycos/', 'mail.ru', 'MantraAgent', 'metadatalabs.com',
			'msnbot/', 'MSRBOT', 'Nigma.ru', 'qwartabot', 'Robozilla', 'sape.bot', 'sape_context',
			'scooter/', 'Scrubby', 'snapbot', 'Slurp', 'Teoma_agent', 'WebAlta', 'WebCrawler',
			'YandexBot', 'yaDirectBot', 'yahoo/', 'yandexSomething', 'yanga.co.uk', 'ZyBorg'
		);
		$vstrsnln_current_user_bot = false;
		foreach ( $vstrsnln_array_bots as $vstrsnln_bot_name ) {
			if ( false !== stripos( $vstrsnln_user_agent, $vstrsnln_bot_name ) ) {
				$vstrsnln_current_user_bot = true;
				break;
			}
		}
		return $vstrsnln_current_user_bot;
	}
}

/* Display information about users online */
if ( ! function_exists( 'vstrsnln_info_display' ) ) {
	function vstrsnln_info_display( $is_widget = false ) {
		global $wpdb, $vstrsnln_prefix;
		$vstrsnln_blog_id 		= get_current_blog_id();
		$vstrsnln_content		= '<div class="visitors-online-block">';
		$vstrsnln_date_today	= date( 'Y-m-d', time() );
		$vstrsnln_type_user	= $wpdb->get_row( "
			SELECT COUNT( * ) AS 'guest',
				( SELECT COUNT( * ) FROM `" . $vstrsnln_prefix . "detailing` 
					WHERE `date_connection` = '" . $vstrsnln_date_today . "'
						AND `user_type` = 'bot'
						AND `time_off` IS NULL
						AND `blog_id` = '" . $vstrsnln_blog_id . "'
				) AS 'bot',
				( SELECT COUNT( * ) FROM `" . $vstrsnln_prefix . "detailing` 
					WHERE `date_connection` = '" . $vstrsnln_date_today . "'
						AND `user_type` = 'user'
						AND `time_off` IS NULL
						AND `blog_id` = '" . $vstrsnln_blog_id . "'
				) AS 'user'
			FROM `" . $vstrsnln_prefix . "detailing`
			WHERE `date_connection` = '" . $vstrsnln_date_today . "'
			AND `user_type` = 'guest'
			AND `time_off` IS NULL
			AND `blog_id` = '" . $vstrsnln_blog_id . "'
			LIMIT 1"
		);
		$vstrsnln_number_bots	= $vstrsnln_type_user->bot;
		$vstrsnln_number_guests	= $vstrsnln_type_user->guest;
		$vstrsnln_number_users	= $vstrsnln_type_user->user;

		$vstrsnln_number_visits = $vstrsnln_number_bots + $vstrsnln_number_users + $vstrsnln_number_guests;

		$vstrsnln_content .= __( 'Visitors online', 'visitors-online' ) . '&#032;&#150;&#032;' . $vstrsnln_number_visits . '&#058;<br />';
		$vstrsnln_content .= __( 'users', 'visitors-online' ) . '&#032;&#150;&#032;' .	$vstrsnln_number_users . '<br />';
		$vstrsnln_content .= __( 'guests', 'visitors-online' ) . '&#032;&#150;&#032;' . $vstrsnln_number_guests . '<br />';
		$vstrsnln_content .= __( 'bots', 'visitors-online' ) . '&#032;&#150;&#032;' . $vstrsnln_number_bots;
		
		/* Print data from general a table */
		$table_general = $wpdb->get_row( "
			SELECT *
			FROM `" . $vstrsnln_prefix . "general`
			WHERE `blog_id` = '" . $vstrsnln_blog_id . "'
			ORDER BY `number_visits` DESC"
		);
		if ( ! empty( $table_general ) ) {
			$vstrsnln_content .= '<br /><br />' . __( 'The maximum number of visits was', 'visitors-online' ) . '&#032;&#150;&#032;' . $table_general->date_connection . '&#058;<br />';
			$vstrsnln_content .= __( 'all visits', 'visitors-online' ) . '&#032;&#150;&#032;' .	$table_general->number_visits . '&#058;<br />';			
			$vstrsnln_content .= __( 'users', 'visitors-online' ) . '&#032;&#150;&#032;' .	$table_general->number_users . '<br />';
			$vstrsnln_content .= __( 'guests', 'visitors-online' ) . '&#032;&#150;&#032;' . $table_general->number_guests . '<br />';
			$vstrsnln_content .= __( 'bots', 'visitors-online' ) . '&#032;&#150;&#032;' . $table_general->number_bots;
			if ( ! empty( $table_general->country ) ) {
				$vstrsnln_content .= '<br />' . __( 'country', 'visitors-online' ) . '&#032;&#150;&#032;' . $table_general->country;
			}
			if ( ! empty( $table_general->browser ) ) {
				$vstrsnln_content .= '<br />' . __( 'browser', 'visitors-online' ) . '&#032;&#150;&#032;' . $table_general->browser;				
			}			
		}
		$vstrsnln_content .= '</div>';
		if ( is_admin() || true == $is_widget )
			echo $vstrsnln_content;
		else
			return $vstrsnln_content;
	}
}

/* add shortcode content  */
if ( ! function_exists( 'vstrsnln_shortcode_button_content' ) ) {
	function vstrsnln_shortcode_button_content( $content ) { ?>
		<div id="vstrsnln" style="display:none;">
			<fieldset>
				<?php _e( 'Add the counter of Visitors Online to your website', 'visitors-online' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[vstrsnln_info]" />
			<div class="clear"></div>
		</div>
	<?php }
}

/* Display information about users online to dashboard */
if ( ! function_exists( 'vstrsnln_dashboard_widget' ) ) {
	function vstrsnln_dashboard_widget() {
		add_meta_box( 'vstrsnln_dashboard', 'Visitors Online', 'vstrsnln_info_display', 'dashboard', 'side', 'default' );
	}
}

/* Creation Widget */
if ( ! class_exists( 'vstrsnln_widget' ) ) {
	class vstrsnln_widget extends WP_Widget {
		/* Instantiate the parent object */
		function vstrsnln_widget() {
			parent::__construct( false, 'Visitors Online', array(
				'classname'		=> 'visitors-online',
				'description'	=> __( 'This Widget shows the number of active visitors on the site, including users, guests and bots.', 'visitors-online' )
			) );
		}

		function widget( $args, $instance ) {
			echo $args['before_widget'];
			if ( ! empty( $instance['vstrsnln_widget_title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['vstrsnln_widget_title'] ) . $args['after_title'];
			}
			vstrsnln_info_display( true );
			echo $args['after_widget'];
		}
		/* Save widget options */
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['vstrsnln_widget_title'] = $new_instance['vstrsnln_widget_title'];
			return $instance;
		}
		/* Output admin widget options form */
		function form( $instance ) {
			$default_widget_args = array(
				'vstrsnln_widget_title'	=>	''
			);
			$instance = wp_parse_args( ( array ) $instance, $default_widget_args ); ?>
			<div class='vstrsnln_widget_settings'>
				<p>
					<label>
						<?php _e( 'Title', 'visitors-online' ) . '&#058;&#032;'; ?>
					</label>
					<input type="text" <?php echo $this->get_field_id( 'vstrsnln_widget_title' ); ?> name="<?php echo $this->get_field_name( 'vstrsnln_widget_title' ); ?>" value="<?php echo $instance['vstrsnln_widget_title']; ?>" class='widefat' />
				</p>
			</div>
		<?php }
	}
}

/* Add widget */
if ( ! function_exists( 'vstrsnln_register_widget' ) ) {
	function vstrsnln_register_widget() {
		register_widget( 'vstrsnln_widget' );
	}
}

/* Uninstall plugin, drop tables, delete options. */
if ( ! function_exists( 'vstrsnln_uninstall' ) ) {
	function vstrsnln_uninstall() {
		global $wpdb, $vstrsnln_prefix, $vstrsnln_prefix_bws;

		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$all_plugins = get_plugins();

		if ( ! array_key_exists( 'visitors-online-pro/visitors-online-pro.php', $all_plugins ) ) {
			$wpdb->query( "
				DROP TABLE `" . $vstrsnln_prefix_bws . "country`,
					 `" . $vstrsnln_prefix . "general`,
				 	`" . $vstrsnln_prefix . "detailing`;
				" );
		}

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				delete_option( 'vstrsnln_options' );
				wp_clear_scheduled_hook( 'vstrsnln_check_users' );
				wp_clear_scheduled_hook( 'vstrsnln_count_visits_day' );
			}
			switch_to_blog( $old_blog );
		} else {
			delete_option( 'vstrsnln_options' );			
			wp_clear_scheduled_hook( 'vstrsnln_check_users' );
			wp_clear_scheduled_hook( 'vstrsnln_count_visits_day' );
		}
	}
}

/* Add "Settings", "FAQ", "Support" Links On The Plugin Page */
if ( ! function_exists( 'vstrsnln_register_plugin_links' ) ) {
	function vstrsnln_register_plugin_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[] = '<a href="admin.php?page=visitors-online.php">' . __( 'Settings', 'visitors-online' ) . '</a>';
			$links[] = '<a href="http://wordpress.org/plugins/visitors-online/faq/" target="_blank">' . __( 'FAQ', 'visitors-online' ) . '</a>';
			$links[] = '<a href="http://support.bestwebsoft.com">' . __( 'Support', 'visitors-online' ) . '</a>';
		}
		return $links;
	}
}

/* Add "Settings" Link On The Plugin Action Page */
if ( ! function_exists( 'vstrsnln_plugin_action_links' ) ) {
	function vstrsnln_plugin_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			/* Static so we don't call plugin_basename on every plugin row. */
			static $this_plugin;
			if ( ! $this_plugin )
				$this_plugin = plugin_basename( __FILE__ );
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=visitors-online.php">' . __( 'Settings', 'visitors-online' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

/* add help tab  */
if ( ! function_exists( 'vstrsnln_add_tabs' ) ) {
	function vstrsnln_add_tabs() {
		$screen = get_current_screen();
		$args = array(
			'id' 			=> 'vstrsnln',
			'section' 		=> '201089295'
		);
		bws_help_tab( $screen, $args );
	}
}

/* Сheck whether there are users with an undefined country, if there is something we define */
if ( ! function_exists( 'vstrsnln_check_country' ) ) {
	function vstrsnln_check_country( $noscript = false ) {
		global $wpdb, $vstrsnln_prefix, $vstrsnln_prefix_bws;
		if ( false == $noscript )
			check_ajax_referer( 'bws_plugin', 'vstrsnln_ajax_nonce_field' );		
		
		$vstrsnln_country_undefined	= $wpdb->get_results( "
			SELECT `ip_user`, `id`
			FROM `" . $vstrsnln_prefix . "detailing`
			WHERE `country_id` = 0"
		);
		
		if ( $vstrsnln_country_undefined ) {
			foreach ( $vstrsnln_country_undefined as $vstrsnln_visitors ) {
				$vstrsnln_user_ip 	= $vstrsnln_visitors->ip_user;
				/* Detects the country */
				$vstrsnln_country 	= $wpdb->get_var( "
					SELECT `id_country`
					FROM `" . $vstrsnln_prefix_bws . "country`
					WHERE `ip_from_int` <= '" . sprintf( '%u', ip2long( $vstrsnln_user_ip ) ) . "'
						AND `ip_to_int` >= '" . sprintf( '%u', ip2long( $vstrsnln_user_ip ) ) . "'
					LIMIT 1"
				);
				$wpdb->update( $vstrsnln_prefix . 'detailing',
					array(
						'country_id'	=> $vstrsnln_country
					),
					array( 'id' => $vstrsnln_visitors->id )	
				);
			}
		}
	}
}

if ( ! function_exists ( 'vstrsnln_plugin_banner' ) ) {
	function vstrsnln_plugin_banner() {
		global $hook_suffix;	
		if ( 'plugins.php' == $hook_suffix ) {
			global $vstrsnln_plugin_info, $vstrsnln_options;

			if ( isset( $vstrsnln_options['first_install'] ) && strtotime( '-1 week' ) > $vstrsnln_options['first_install'] )
				bws_plugin_banner( $vstrsnln_plugin_info, 'vstrsnln', 'visitors-online', 'ac4699da21e7e6d6238f373bc0065912', '213', '//ps.w.org/visitors-online/assets/icon-128x128.png' );
			if ( ! is_network_admin() )
				bws_plugin_banner_to_settings( $vstrsnln_plugin_info, 'vstrsnln_options', 'visitors-online', 'admin.php?page=visitors-online.php' );
		}
	}
}

register_activation_hook( __FILE__, 'vstrsnln_install' );

add_action( 'admin_menu', 'vstrsnln_admin_menu' );
add_action( 'init', 'vstrsnln_plugin_init' );
add_action( 'admin_init', 'vstrsnln_plugin_admin_init' );
add_action( 'plugins_loaded', 'vstrsnln_plugins_loaded' );

add_action( 'admin_enqueue_scripts', 'vstrsnln_admin_head' );
/* Add the function to the specified hook */
add_action( 'vstrsnln_check_users', 'vstrsnln_check_user' );
/* Add the function to the specified hook - record of the day with the maximum number of visits*/
add_action( 'vstrsnln_count_visits_day', 'vstrsnln_write_max_visits' );
/* Register a user interval */
add_filter( 'cron_schedules', 'vstrsnln_add_user_interval' );
add_shortcode( 'vstrsnln_info', 'vstrsnln_info_display' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'vstrsnln_shortcode_button_content' );

add_action( 'wp_dashboard_setup', 'vstrsnln_dashboard_widget' );
add_action( 'widgets_init', 'vstrsnln_register_widget' );

/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'vstrsnln_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'vstrsnln_register_plugin_links', 10, 2 );
/* Hooks for ajax */
add_action( 'wp_ajax_vstrsnln_count_rows', 'vstrsnln_count_rows' );
add_action( 'wp_ajax_vstrsnln_insert_rows', 'vstrsnln_insert_rows' ); 
add_action( 'wp_ajax_vstrsnln_check_country', 'vstrsnln_check_country' );

add_action( 'admin_notices', 'vstrsnln_plugin_banner' );
register_uninstall_hook( __FILE__, 'vstrsnln_uninstall' );