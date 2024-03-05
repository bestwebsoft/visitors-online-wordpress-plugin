<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! class_exists( 'Vstrsnln_Settings_Tabs' ) ) {
	class Vstrsnln_Settings_Tabs extends Bws_Settings_Tabs {
		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename Plugin basename.
		 */
		public function __construct( $plugin_basename ) {
			global $vstrsnln_options, $vstrsnln_plugin_info;
			$tabs = array(
				'settings'    => array( 'label' => __( 'Settings', 'visitors-online' ) ),
				'misc'        => array( 'label' => __( 'Misc', 'visitors-online' ) ),
				'custom_code' => array( 'label' => __( 'Custom Code', 'visitors-online' ) ),
				'license'     => array( 'label' => __( 'License Key', 'visitors-online' ) ),
			);

			parent::__construct(
				array(
					'plugin_basename'    => $plugin_basename,
					'plugins_info'       => $vstrsnln_plugin_info,
					'prefix'             => 'vstrsnln',
					'default_options'    => vstrsnln_get_options_default(),
					'options'            => $vstrsnln_options,
					'is_network_options' => is_network_admin(),
					'tabs'               => $tabs,
					'wp_slug'            => 'visitors-online',
					'link_key'           => '1b01d30e84bb97b2afecb5f34c43931d',
					'link_pn'            => '216',
					'doc_link'           => 'https://docs.google.com/document/d/1FaTnRsYs64adPiRz_REGH9u0pOPz2flCL4gi49qdfaw',
				)
			);

			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );

			$this->all_plugins = get_plugins();
		}

		/**
		 * Save plugin options to the database
		 *
		 * @access public
		 * @return array The action results
		 */
		public function save_options() {
			global $wpdb;

			$message = '';
			$notice  = '';
			$error   = '';

			if ( isset( $_POST['vstrsnln_save_options'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vstrsnln_save_options'] ) ), 'vstrsnln_save_options' ) ) {
				if ( isset( $_POST['vstrsnln_button_clean'] ) ) {
					$wpdb->query( 'TRUNCATE `' . $wpdb->base_prefix . 'vstrsnln_general`;' );
					$wpdb->query( 'TRUNCATE `' . $wpdb->base_prefix . 'vstrsnln_detailing`;' );
					$vstrsnln_number_general = $wpdb->get_var(
						'
											SELECT count( * )
											FROM `' . $wpdb->base_prefix . 'vstrsnln_general`
											LIMIT 1'
					);
					if ( empty( $vstrsnln_number_general ) ) {
						$message = __( 'Statistics was successfully cleared', 'visitors-online' );
					}
				} else {
					/* Save data for settings page */
					if ( isset( $_REQUEST['vstrsnln_check_user_interval'] ) ) {
						if ( absint( $_REQUEST['vstrsnln_check_user_interval'] ) !== $this->options['check_user_interval'] ) {
							/* Add the planned hook - check users online */
							wp_clear_scheduled_hook( 'vstrsnln_check_users' );
							if ( ! wp_next_scheduled( 'vstrsnln_check_users' ) ) {
								wp_schedule_event( time(), 'vstrsnln_interval', 'vstrsnln_check_users' );
							}
						}
						if ( empty( $_REQUEST['vstrsnln_check_user_interval'] ) ) {
							$error = __( 'Please fill The time period. The settings are not saved', 'visitors-online' );
						} elseif ( empty( $_REQUEST['vstrsnln_structure_pattern'] ) ) {
							$error = __( 'Please fill The data structure. The settings are not saved', 'visitors-online' );
						} else {
							$this->options['check_user_interval'] = isset( $_REQUEST['vstrsnln_check_user_interval'] ) ? absint( $_REQUEST['vstrsnln_check_user_interval'] ) : 1;
							$this->options['structure_pattern']   = wp_kses_post( wp_unslash( $_REQUEST['vstrsnln_structure_pattern'] ) );

							update_option( 'vstrsnln_options', $this->options );
							$message = __( 'Settings saved', 'visitors-online' );
						}
					}
				}
			}
			return compact( 'message', 'notice', 'error' );
		}

		/**
		 * Display tab "Settings"
		 *
		 * @access public
		 */
		public function tab_settings() { ?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Visitors Online Settings', 'visitors-online' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<form class="bws_form" method="post" action="admin.php?page=visitors-online.php">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'No Activity Threshold', 'visitors-online' ); ?></th>
						<td>
							<input type="number" min="1" max="60" name="vstrsnln_check_user_interval" value="<?php echo esc_attr( $this->options['check_user_interval'] ); ?>" />
							<?php esc_html_e( 'min', 'visitors-online' ); ?><br />
							<span class="bws_info"><?php esc_html_e( 'Set the maximum time during which the visitor is not performing any activity, but is still considered as an online visitor.', 'visitors-online' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Data Structure', 'visitors-online' ); ?></th>
						<td>
							<textarea id="vstrsnln_structure_builder" class="regular-text" name="vstrsnln_structure_pattern" required="required"><?php echo esc_attr( $this->options['structure_pattern'] ); ?></textarea>
							<div class="bws_info">
								<?php esc_html_e( 'Allowed Variables', 'visitors-online' ); ?>:<br />
								<?php
								$shortcodes = array(
									'TOTAL'      => __( 'Total number of current visitors', 'visitors-online' ),
									'USERS'      => __( 'Current number of registred users', 'visitors-online' ),
									'GUESTS'     => __( 'Current number of guests', 'visitors-online' ),
									'BOTS'       => __( 'Current number of bots', 'visitors-online' ),
									'MAX-DATE'   => __( 'The date of the highest visitors attendance', 'visitors-online' ),
									'MAX-TOTAL'  => __( 'Total number of visitors at the highest attendance date', 'visitors-online' ),
									'MAX-USERS'  => __( 'The number of registered users at the highest attendance date', 'visitors-online' ),
									'MAX-GUESTS' => __( 'The number of guests at the highest attendance date', 'visitors-online' ),
									'MAX-BOTS'   => __( 'The number of bots at the highest attendance date', 'visitors-online' ),
								);

								$blog_id       = get_current_blog_id();
								$table_general = vstrsnln_get_table_general( $blog_id );

								if ( ! empty( $table_general->country ) ) {
									$shortcodes['COUNTRY'] = __( 'Country', 'visitors-online' ) . ' (' . __( 'Data is collected for 1 day and then displayed', 'visitors-online' ) . ')';
								}

								if ( ! empty( $table_general->browser ) ) {
									$shortcodes['BROWSER'] = __( 'Browser', 'visitors-online' ) . ' (' . __( 'Data is collected for 1 day and then displayed', 'visitors-online' ) . ')';
								}
								foreach ( $shortcodes as $shortcode => $description ) {
									echo wp_kses_post( '{' . $shortcode . '} - ' . $description . '<br />' );
								}
								?>
							</div>
						</td>
					</tr>
				</table>
				<?php if ( ! $this->hide_pro_tabs ) { ?>
					<div class="bws_pro_version_bloc">
						<div class="bws_pro_version_table_bloc">
							<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'rating-bws' ); ?>"></button>
							<div class="bws_table_bg"></div>
							<table class="form-table bws_pro_version">
								<tr valign="top">
									<th scope="row">
										<?php esc_html_e( 'Update GeoIP', 'visitors-online' ); ?>
									</th>
									<td>
										<?php esc_html_e( 'every', 'visitors-online' ); ?>
										<input type="number" class="vstrsnln_input_value" disabled name="vstrsnln_loading_country" value="3" />
										<?php esc_html_e( 'months', 'visitors-online' ); ?>
										<input type="submit" id="bwscntrtbl_button_import" disabled="disabled" name="bwscntrtbl_button_import" class="button bwsplgns_need_disable" value="Update now">
										</div><br>
										<span class="bws_info">
											<?php esc_html_e( 'This option allows you to download lists with registered IP addresses all over the world to the database (from', 'visitors-online' ); ?>&nbsp;<a href="https://www.software77.net" target="_blank">https://www.software77.net</a>).
											<br>
											<?php esc_html_e( 'Hence you will receive the information about each IP address, and the country it belongs to. You can select the desired frequency for IP database updating', 'visitors-online' ); ?>.
										</span>
									</td>
								</tr>
								<tr valign="top">
									<th>
										<label for="vstrsnln_checkbox_info_users">
											<?php esc_html_e( 'User Data', 'visitors-online' ); ?>
										</label>
									</th>
									<td>
										<label for="vstrsnln_checkbox_info_users">
											<input type="checkbox" value="1" name="vstrsnln_checkbox_info_users" id="vstrsnln_checkbox_info_users" disabled="disabled" <?php checked( 1 ); ?> />
											<span class="bws_info"><?php esc_html_e( 'Enable to display the list of users and information about each of them.', 'visitors-online' ); ?></span>
										</label>
									</td>
								</tr>
							</table>
						</div>
						<?php $this->bws_pro_block_links(); ?>
					</div>
				<?php } ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Clear the Statistics', 'visitors-online' ); ?></th>
						<td>
							<input type="submit" name="vstrsnln_button_clean" class="button" value=<?php esc_html_e( 'Clear', 'visitors-online' ); ?> />
							<?php wp_nonce_field( 'vstrsnln_save_options', 'vstrsnln_save_options' ); ?>
						</td>
					</tr>
				</table>
			</form>
			<?php vstrsnln_form_import_country( 'admin.php?page=visitors-online.php' ); ?>
			<?php
		}

		/**
		 * Display custom metabox
		 *
		 * @access public
		 */
		public function display_metabox() {
			?>
			<div class="postbox">
				<h3 class="hndle">
					<?php esc_html_e( 'Visitors Online Shortcode', 'visitors-online' ); ?>
				</h3>
				<div class="inside">
					<?php if ( ! $this->is_network_options ) { ?>
						<p><?php printf( esc_html__( 'Real-time statistics can be viewed on the %1$sDashboard%2$s.', 'visitors-online' ), '<a href="' . esc_url( admin_url() ) . '">', '</a>' ); ?></p>
					<?php } ?>
				</div>
				<div class="inside">
					<p><?php printf( esc_html__( 'Add a real-time online visitors counter to your posts, pages or custom post types using %1$swidgets%2$s or the following shortcode:', 'visitors-online' ), '<a href="' . esc_url( admin_url( 'widgets.php' ) ) . '">', '</a>' ); ?></p>
					<?php bws_shortcode_output( '[vstrsnln_info]' ); ?>
				</div>
				<?php if ( ! $this->hide_pro_tabs ) { ?>
					<div class="bws_pro_version_bloc">
						<div class="bws_pro_version_table_bloc">
							<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'rating-bws' ); ?>"></button>
							<div class="bws_table_bg" style="margin-top: -15px;"></div>
							<div class="inside">
								<p><?php esc_html_e( 'Add online-users to your posts, pages, custom post types or widgets by using the following shortcode:', 'visitors-online' ); ?></p>
								<?php bws_shortcode_output( '[vstrsnln_online_users]' ); ?>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
			<?php
		}
	}
}
