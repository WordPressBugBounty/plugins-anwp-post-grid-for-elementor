<?php
/**
 * Plugin Name: AnWP Post Grid and Post Carousel Slider for Elementor
 * Plugin URI:  https://anwppro.userecho.com/communities/50-anwp-post-grid-for-elementor
 * Description: Easily create awesome post grids and post carousel sliders. Different widget types, powerful filters, "load more" button and many customizable options.
 * Version:     1.3.4
 * Author:      Andrei Strekozov <anwppro>
 * Author URI:  https://anwp.pro
 * License:     GPLv2+
 * Requires PHP: 5.6
 * Text Domain: anwp-post-grid
 * Domain Path: /languages
 * Elementor tested up to: 4.3
 */

/**
 * Copyright (c) 2020-2025 Andrei Strekozov <anwppro> (email: anwp.pro@gmail.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using generator-plugin-wp (https://github.com/WebDevStudios/generator-plugin-wp)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// Oldest premium release that talks to the REST routes registered by this version.
define( 'ANWP_POST_GRID_MIN_PREMIUM_VERSION', '1.0.5' );

// Check for required PHP version
if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {

	add_action( 'admin_notices', 'anwp_post_grid_requirements_not_met_notice' );

} else {

	// Require the main plugin class
	require_once plugin_dir_path( __FILE__ ) . 'class-anwp-post-grid.php';

	// Kick it off.
	add_action( 'plugins_loaded', array( anwp_post_grid(), 'hooks' ) );

	// Activation
	register_activation_hook( __FILE__, array( anwp_post_grid(), 'activate' ) );
}

/**
 * Warn when the active Premium add-on predates this version's REST routes.
 *
 * @since  1.3.4
 * @return void
 */
function anwp_post_grid_check_premium_version() {
	if ( defined( 'ANWP_POST_GRID_PREMIUM_VERSION' ) && version_compare( ANWP_POST_GRID_PREMIUM_VERSION, ANWP_POST_GRID_MIN_PREMIUM_VERSION, '<' ) ) {
		add_action( 'admin_notices', 'anwp_post_grid_premium_outdated_notice' );
	}
}

add_action( 'plugins_loaded', 'anwp_post_grid_check_premium_version' );

/**
 * Adds a notice to the dashboard if the plugin requirements are not met.
 *
 * @since  0.2.0
 * @return void
 */
function anwp_post_grid_requirements_not_met_notice() {

	$details = '';

	if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
		/* translators: %s minimum PHP version */
		$details .= '<small>' . sprintf( esc_html__( 'AnWP Post Grid cannot run on PHP versions older than %s. Please contact your hosting provider to update your site.', 'anwp-post-grid' ), '5.6.0' ) . '</small><br />';
	}

	// Output errors.
	?>
	<div id="message" class="error">
		<p><?php echo wp_kses_post( $details ); ?></p>
	</div>
	<?php
}

/**
 * Adds a notice to the dashboard if the installed Premium add-on is too old.
 *
 * Premium builds its requests from the rest_url localized here; releases
 * before ANWP_POST_GRID_MIN_PREMIUM_VERSION still call the removed admin-ajax
 * handlers, so their widgets render but stop responding.
 *
 * @since  1.3.4
 * @return void
 */
function anwp_post_grid_premium_outdated_notice() {

	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				/* translators: 1: installed premium version, 2: minimum required premium version */
				esc_html__( 'AnWP Post Grid: the Premium add-on (version %1$s) is outdated - version %2$s or higher is required. Until it is updated, premium filters, pagination and "load more" will not work.', 'anwp-post-grid' ),
				esc_html( ANWP_POST_GRID_PREMIUM_VERSION ),
				esc_html( ANWP_POST_GRID_MIN_PREMIUM_VERSION )
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Grab the AnWP_Post_Grid object and return it.
 * Wrapper for AnWP_Post_Grid::get_instance().
 *
 * @since  0.1.0
 * @return AnWP_Post_Grid  Singleton instance of plugin class.
 */
function anwp_post_grid() {
	return AnWP_Post_Grid::get_instance();
}
