<?php

/**
 * Fired during plugin uninstall.
 *
 * @link       https://wpgodspeed.io
 * @since      1.0.0
 *
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/includes
 */

/**
 * Fired during plugin uninstall.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/includes
 * @author     WP Godspeed <hello@wpgodspeed.io>
 */
class WP_Godspeed_Uninstall {

	public static function uninstall()
	{
		global $wpdb;
		global $wp_version;

		$auth_token = get_option( 'wpgods_auth_token' );
		if ( ! empty( $auth_token ) )
		{
			$args = [
				'timeout'     => 5,
				'redirection' => 5,
				'httpversion' => '1.0',
				'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
				'blocking'    => TRUE,
				'headers'     => array( 'Authorization' => 'Bearer ' . base64_encode( $auth_token ) ),
				'cookies'     => array(),
				'body'        => NULL,
				'compress'    => FALSE,
				'decompress'  => TRUE,
				'sslverify'   => TRUE,
				'stream'      => FALSE,
				'filename'    => NULL
			];
			wp_remote_get( 'https://api.godspeedcdn.com/cdn/uninstall', $args );
		}
		$sqlquery = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wpgods_%'" );
	}


}