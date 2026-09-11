<?php
/**
 * Plugin Name: Frontend Dashboard Extra
 * Plugin URI: https://buffercode.com/plugin/frontend-dashboard-extra
 * Description: Front end dashboard provide high flexible way to customize the user dashboard on front end rather than
 * WordPress wp-admin dashboard.
 * Version: 3.0.0
 * Author: vinoth06
 * Author URI: http://buffercode.com/
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fed
 *
 * @package frontend-dashboard-extra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$fed_check = get_option( 'fed_plugin_version' );

require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( $fed_check && is_plugin_active( 'frontend-dashboard/frontend-dashboard.php' ) ) {

	/**
	 * Version Number
	 */
	define( 'BC_FED_EXTRA_PLUGIN_VERSION', '3.0.0' );

	/**
	 * App Name
	 */
	define( 'BC_FED_EXTRA_APP_NAME', 'Frontend Dashboard Extra' );

	/**
	 * Root Path
	 */
	define( 'BC_FED_EXTRA_PLUGIN', __FILE__ );
	/**
	 * Plugin Base Name
	 */
	define( 'BC_FED_EXTRA_PLUGIN_BASENAME', plugin_basename( BC_FED_EXTRA_PLUGIN ) );
	/**
	 * Plugin Name
	 */
	define( 'BC_FED_EXTRA_PLUGIN_NAME', trim( dirname( BC_FED_EXTRA_PLUGIN_BASENAME ), '/' ) );
	/**
	 * Plugin Directory
	 */
	define( 'BC_FED_EXTRA_PLUGIN_DIR', untrailingslashit( dirname( BC_FED_EXTRA_PLUGIN ) ) );


	require_once BC_FED_EXTRA_PLUGIN_DIR . '/menu/FEDE_Menu.php';
	require_once BC_FED_EXTRA_PLUGIN_DIR . '/fields/FEDEFormWPEditor.php';
	require_once BC_FED_EXTRA_PLUGIN_DIR . '/functions.php';
	require_once BC_FED_EXTRA_PLUGIN_DIR . '/menu/fields/fede_files.php';
	require_once BC_FED_EXTRA_PLUGIN_DIR . '/menu/fields/fede_label.php';
	require_once BC_FED_EXTRA_PLUGIN_DIR . '/menu/fields/fede_table.php';
	require_once BC_FED_EXTRA_PLUGIN_DIR . '/menu/fields/fede_color.php';

	/**
	 * Deprecation Notice: Frontend Dashboard Extra is now built directly into Frontend Dashboard Core (v3.0.0+).
	 */
	function fed_extra_deprecation_admin_notice() {
		$deactivate_url = wp_nonce_url(
			admin_url( 'plugins.php?action=deactivate&plugin=' . urlencode( BC_FED_EXTRA_PLUGIN_BASENAME ) ),
			'deactivate-plugin_' . BC_FED_EXTRA_PLUGIN_BASENAME
		);
		?>
		<div class="notice notice-info is-dismissible" style="border-left-color: #4f46e5; padding: 12px 16px;">
			<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
				<div>
					<p style="margin: 0 0 4px 0; font-size: 13px; font-weight: 700; color: #1e293b;">
						<span class="dashicons dashicons-info" style="color: #4f46e5; margin-right: 4px; vertical-align: middle;"></span>
						<?php esc_html_e( 'Frontend Dashboard Extra has been integrated into Core!', 'frontend-dashboard' ); ?>
					</p>
					<p style="margin: 0; font-size: 12px; color: #475569;">
						<?php esc_html_e( 'Date & Time pickers, File uploads, Color pickers, WP Editor (TinyMCE), Custom Labels, and Table Grids are now built directly into Frontend Dashboard Core (v3.0.0+). You can safely deactivate and remove this add-on.', 'frontend-dashboard' ); ?>
					</p>
				</div>
				<div>
					<a href="<?php echo esc_url( $deactivate_url ); ?>" class="button button-primary" style="background-color: #4f46e5; border-color: #4338ca; text-shadow: none;">
						<?php esc_html_e( 'Deactivate Add-on', 'frontend-dashboard' ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'fed_extra_deprecation_admin_notice' );
} else {
	add_action( 'admin_notices', 'fed_global_admin_notification_extra' );
	function fed_global_admin_notification_extra() {
		?>
		<div class="notice notice-warning">
			<p>
				<b>
					<?php
					_e(
						'Please install <a href="https://buffercode.com/plugin/frontend-dashboard">Frontend Dashboard</a> to use this plugin [Frontend Dashboard Extra]',
						'frontend-dashboard-extra'
					);
					?>
				</b>
			</p>
		</div>
		<?php
	}
}
