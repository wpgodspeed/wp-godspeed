<?php

/**
 * Fired during plugin deactivation.
 *
 * @link       https://wpgodspeed.io
 * @since      1.0.0
 *
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/includes
 * @author     WP Godspeed <hello@wpgodspeed.io>
 */
class WP_Godspeed_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		update_option( 'wpgods_cdn_enabled', 0 );
		wp_clear_scheduled_hook( 'wp_godspeed_cron', array( 'check_cdn_status' ) );
	}

}
