<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wpgodspeed.io
 * @since             1.0.0
 * @package           WP_Godspeed
 *
 * @wordpress-plugin
 * Plugin Name:       WP Godspeed
 * Plugin URI:        https://wpgodspeed.io
 * Description:       The premiere free CDN plugin for WordPress.
 * Version:           0.9.7
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-godspeed
 * GitHub Plugin URI: https://github.com/wpgodspeed/wp-godspeed
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently pligin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_GODSPEED_VERSION', '0.9.7' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-godspeed-activator.php
 */
function activate_wp_godspeed() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-godspeed-activator.php';
	WP_Godspeed_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-godspeed-deactivator.php
 */
function deactivate_wp_godspeed() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-godspeed-deactivator.php';
	WP_Godspeed_Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstall.
 * This action is documented in includes/class-wp-godspeed-uninstall.php
 */
function uninstall_wp_godspeed() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-godspeed-uninstall.php';
    WP_Godspeed_Uninstall::uninstall();
}

register_activation_hook( __FILE__, 'activate_wp_godspeed' );
register_deactivation_hook( __FILE__, 'deactivate_wp_godspeed' );
register_uninstall_hook( __FILE__, 'uninstall_wp_godspeed' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-godspeed.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_godspeed() {

	$plugin = new WP_Godspeed();
	$plugin->run();

}
run_wp_godspeed();
