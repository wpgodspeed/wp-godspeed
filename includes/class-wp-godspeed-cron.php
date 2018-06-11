<?php

/**
 * The main cron class.
 *
 * @link       https://wpgodspeed.io
 * @since      1.0.0
 *
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/cron
 */

/**
 * The main cron class.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/cron
 * @author     WP Godspeed <hello@wpgodspeed.io>
 */
class WP_Godspeed_Cron extends WP_Godspeed_Admin {

	/**
	 * The cron action name
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $action    The cron action name
	 */
	private $action;

	/**
	 * The authentication token
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $auth_token    The authentication token
	 */
	public $auth_token;

	/**
	 * The default argument list for remote requests
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $args    The default argument list for remote requests
	 */
	public $args;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct()
	{
		$this->load_cron_hooks();
	}

	public function load_cron_hooks()
	{
		add_action( 'wp_godspeed_cron', array( $this, 'process' ) );
	}

	public function process( $action )
	{
		global $wp_version;
		$auth_token   = get_option( $this->option_name . '_auth_token' );
		$this->action = $action;
		if ( ! empty( $auth_token ) )
		{
			$this->auth_token = $auth_token;
			$this->args       = array(
				'timeout'     => 5,
				'redirection' => 5,
				'httpversion' => '1.0',
				'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
				'blocking'    => TRUE,
				'headers'     => array( 'Authorization' => 'Bearer ' . base64_encode( $this->auth_token ) ),
				'cookies'     => array(),
				'body'        => NULL,
				'compress'    => FALSE,
				'decompress'  => TRUE,
				'sslverify'   => TRUE,
				'stream'      => FALSE,
				'filename'    => NULL
			);
			$this->$action();
		}
	}

	public function create_new_dist()
	{
		$response = wp_remote_post( 'https://api.godspeedcdn.com/cdn/create', $this->args );
		if ( is_array( $response ) )
		{
			$body        = $response['body'];
			$result      = json_decode( $body, TRUE );
			$dist_status = $result['result'];
			update_option( $this->option_name . '_cdn_status', $dist_status );
		}
	}

	public function check_new_dist_status()
	{
		$starttime = get_option( $this->option_name . '_cdn_processing_start_time' );
		$response  = wp_remote_get( 'https://api.godspeedcdn.com/cdn/status', $this->args );

		if ( is_array( $response ) )
		{
			$body        = $response['body'];
			$result      = json_decode( $body, TRUE );
			$status      = $result['result'];
			$now         = date( 'H:i' );
			$runningtime = microtime( TRUE );
			$timediff    = $runningtime - $starttime;
			$elapsed     = floor( round( $timediff ) / 60 );
			update_option( $this->option_name . '_cdn_status_waiting', $elapsed );

			if ( $status == 'deployed' )
			{
				//update a few options & we're done
				$status    = $result['result'];
				$dist_id   = $result['distribution_id'];
				$uid       = $result['uid'];
				$subdomain = $result['subdomain'];
				$cdnstatus = $result['cdn_status'];

				delete_option( $this->option_name . '_cdn_status_waiting'        );
				delete_option( $this->option_name . '_cdn_processing'            );
				delete_option( $this->option_name . '_cdn_processing_start_time' );
				update_option( $this->option_name . '_distribution_id', $dist_id   );
				update_option( $this->option_name . '_uid',             $uid       );
				update_option( $this->option_name . '_subdomain',       $subdomain );
				update_option( $this->option_name . '_cdn_status',      $cdnstatus );
				$this->check_cdn_status();
			}
		}
		else
		{
			$this->args['body'] = json_encode( array( 'fn' => "cron->$this->action : no response", 'payload' => "$response" ) );
			wp_remote_post( 'https://api.godspeedcdn.com/usage/plugin/debug', $this->args );
		}
	}

	public function check_cdn_status()
	{
		$dist_id = get_option( $this->option_name . '_distribution_id' );
		if ( $dist_id )
		{
			$response = wp_remote_get( 'https://api.godspeedcdn.com/usage/stats', $this->args );

			if ( is_array( $response ) )
			{
				$body      = $response['body'];
				$result    = json_decode( $body, TRUE );
				$cdnstatus = $result['cdn_status'];
				$plan_name = $result['name'];
				$plan_data = $result['plan_data'];

				if ( $cdnstatus == 'disabled' )
				{
					update_option( $this->option_name . '_cdn_status', 'disabled' );
					update_option( $this->option_name . '_needs_plan_upgrade', 1  );
					update_option( $this->option_name . '_cdn_enabled', 0         );
					update_option( $this->option_name . '_plan', $plan_name       );
					update_option( $this->option_name . '_plan_data', $plan_data  );
					$this->args['body'] = json_encode( array( 'deactivated' => TRUE ) );
					wp_remote_post( 'https://api.godspeedcdn.com/usage/plugin/confirm', $this->args );
					return;
				}

				update_option( $this->option_name . '_cdn_status', $cdnstatus );
				update_option( $this->option_name . '_plan', $plan_name       );
				update_option( $this->option_name . '_plan_data', $plan_data  );

				$this->args['body'] = json_encode( array( 'deactivated' => FALSE ) );
				wp_remote_post( 'https://api.godspeedcdn.com/usage/plugin/confirm', $this->args );
			}
			else
			{
				$this->args['body'] = json_encode( array( 'fn' => "cron->$this->action : no response", 'payload' => "$response" ) );
				wp_remote_post( 'https://api.godspeedcdn.com/usage/plugin/debug', $this->args );
			}
		}
	}


}
