<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpgodspeed.io/
 * @since      1.0.0
 *
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/admin
 * @author     WP Godspeed <hello@wpgodspeed.io>
 */
class WP_Godspeed_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The option name in this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $version    The public option name in this plugin.
	 */
	public $option_name = 'wpgods';

	/**
	 * The list of options in this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $version    The public list of options in this plugin.
	 */
	public $option_list = array(
		'_cdn_status',
		'_cdn_status_waiting',
		'_cdn_enabled',
		'_cdn_processing',
		'_cdn_processing_start_time',
		'_data_usage',
		'_distribution_id',
		'_needs_plan_upgrade',
		'_notice_distribution_dismissed',
		'_notice_registration_dismissed',
		'_subdomain',
		'_uid',
		'_auth_token',
		'_billing_end',
		'_billing_start',
		'_lazyload_images',
		'_lazyload_iframes',
		'_lazyload_youtube',
		'_plan',
		'_plan_data',
		'_status',
	);

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		new WP_Godspeed_CDN_Stack;
		new WP_Godspeed_Lazyload;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Godspeed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Godspeed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-godspeed-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'fontawesome', plugin_dir_url( __DIR__ ) . 'vendor/components/font-awesome/css/font-awesome.min.css', array(), '4.7.0', 'all' );

		if ( ! preg_match( "/$this->plugin_name/", $hook ) )
		{
			return;
		}

		if ( preg_match( "/$this->plugin_name_$this->plugin_name-setup/", $hook ) )
		{
			wp_enqueue_style( 'jqueryui-css', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css', array(), '1.12.1', 'all' );
			wp_enqueue_style( 'jqueryui-editable-css', plugin_dir_url( __FILE__ ) . 'css/jqueryui-editable.css', array(), '1.5.1', 'all' );
		}

		//bootstrap css affects all of wordpress admin
		wp_enqueue_style( 'bootstrap4-css', plugin_dir_url( __DIR__ ) . 'vendor/twbs/bootstrap/dist/css/bootstrap.min.css', array(), '4.1.2', 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Godspeed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Godspeed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-godspeed-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'bootstrap4-js', plugin_dir_url( __DIR__ ) . 'vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js', false, '4.1.2', false );

		$processing = get_option( $this->option_name . '_cdn_processing' );
		$status     = get_option( $this->option_name . '_status' );
		$waiting    = get_option( $this->option_name . '_cdn_status_waiting' );
		$dist_id    = get_option( $this->option_name . '_distribution_id' );
		if ( ! $processing )
		{
			$processing = FALSE;
		}
		$args = array(
			'hook'           => $hook,
			'cdn_processing' => $processing,
		);
		if ( ( $status == FALSE || $status == 'not registered' ) && $dist_id == FALSE )
		{
			$args['reg'] = TRUE;
			$args['cdn'] = FALSE;
			$args['rdy'] = FALSE;
		}
		if ( $status == 'registered' && $dist_id == FALSE )
		{
			$args['reg'] = FALSE;
			$args['cdn'] = TRUE;
			$args['rdy'] = FALSE;
		}
		if ( $status == 'registered' && $dist_id !== FALSE )
		{
			$args['reg'] = FALSE;
			$args['cdn'] = FALSE;
			$args['rdy'] = TRUE;
		}
		if ( $status == 'not registered' )
		{
			$args['stats'] = FALSE;
		}
		else
		{
			$args['stats'] = TRUE;
		}

		$m1 = date('M');
		$y1 = date('Y');
		$m2 = date('M', strtotime('-1 months'));
		$y2 = date('Y', strtotime('-1 months'));
		$m3 = date('M', strtotime('-2 months'));
		$y3 = date('Y', strtotime('-2 months'));
		$m4 = date('M', strtotime('-3 months'));
		$y4 = date('Y', strtotime('-3 months'));
		$m5 = date('M', strtotime('-4 months'));
		$y5 = date('Y', strtotime('-4 months'));
		$m6 = date('M', strtotime('-5 months'));
		$y6 = date('Y', strtotime('-5 months'));

		$m1d = get_option( $this->option_name . '_data_usage' );
		$m2d = get_option( $this->option_name . '_usage_history_' . $m2 . '_' . $y2 );
		$m3d = get_option( $this->option_name . '_usage_history_' . $m3 . '_' . $y3 );
		$m4d = get_option( $this->option_name . '_usage_history_' . $m4 . '_' . $y4 );
		$m5d = get_option( $this->option_name . '_usage_history_' . $m5 . '_' . $y5 );
		$m6d = get_option( $this->option_name . '_usage_history_' . $m6 . '_' . $y6 );

		if ( $m1d )
		{
			$args['m1'] = array(
				'name' => $m1,
				'data' => $m1d,
			);
		}
		if ( $m2d )
		{
			$args['m2'] = array(
				'name' => $m2,
				'data' => $m2d,
			);
		}
		if ( $m3d )
		{
			$args['m3'] = array(
				'name' => $m3,
				'data' => $m3d,
			);
		}
		if ( $m4d )
		{
			$args['m4'] = array(
				'name' => $m4,
				'data' => $m4d,
			);
		}
		if ( $m5d )
		{
			$args['m5'] = array(
				'name' => $m5,
				'data' => $m5d,
			);
		}
		if ( $m6d )
		{
			$args['m6'] = array(
				'name' => $m6,
				'data' => $m6d,
			);
		}

		wp_localize_script( $this->plugin_name, 'ajax_vars', $args );

		if ( ! preg_match( "/$this->plugin_name/", $hook ) )
		{
			return;
		}

		wp_enqueue_script( 'chart-js', plugin_dir_url( __FILE__ ) . 'js/chart.js', array(), '2.4.0', false );

		if ( preg_match( "/$this->plugin_name_$this->plugin_name-setup/", $hook ) )
		{
			wp_enqueue_script( 'jqueryui-editable-js', plugin_dir_url( __FILE__ ) . 'js/jqueryui-editable.min.js', array(), '1.5.1', false );
		}

	}

	/**
	 * Do this
	 *
	 * @since  1.0.0
	 */
	public function cron_schedule()
	{
		$args = array( 'check_cdn_status' );
		if ( ! wp_next_scheduled( 'wp_godspeed_cron', $args ) )
		{
			wp_schedule_event( time(), 'daily', 'wp_godspeed_cron', $args );
		}
	}

	/**
	 * Do this
	 *
	 * @since  1.0.0
	 */
	public function cron_schedule_5min()
	{
		$args = array( 'check_cdn_status' );
		if ( ! wp_next_scheduled( 'wp_godspeed_cron', $args ) )
		{
			wp_schedule_event( time(), 'wp_godspeed_interval_5min', 'wp_godspeed_cron', $args );
		}
	}

	/**
	 * Do this
	 *
	 * @since  1.0.0
	 */
	public function create_admin_menus()
	{

		//fa5free-cloud viewbox: 0 0 640 512
		//M537.585 226.56C541.725 215.836 544 204.184 544 192c0-53.019-42.981-96-96-96-19.729 0-38.065 5.954-53.316 16.159C367.042 64.248 315.288 32 256 32c-88.366 0-160 71.634-160 160 0 2.728.07 5.439.204 8.133C40.171 219.845 0 273.227 0 336c0 79.529 64.471 144 144 144h368c70.692 0 128-57.308 128-128 0-61.93-43.983-113.586-102.415-125.44z

		//fa5pro-cloud viewbox: 0 0 640 512
		//M272 80c53.473 0 99.279 32.794 118.426 79.363C401.611 149.793 416.125 144 432 144c35.346 0 64 28.654 64 64 0 11.829-3.222 22.9-8.817 32.407A96.998 96.998 0 0 1 496 240c53.019 0 96 42.981 96 96s-42.981 96-96 96H160c-61.856 0-112-50.144-112-112 0-56.428 41.732-103.101 96.014-110.859-.003-.381-.014-.76-.014-1.141 0-70.692 57.308-128 128-128m0-48c-84.587 0-155.5 59.732-172.272 139.774C39.889 196.13 0 254.416 0 320c0 88.374 71.642 160 160 160h336c79.544 0 144-64.487 144-144 0-61.805-39.188-115.805-96.272-135.891C539.718 142.116 491.432 96 432 96c-7.558 0-15.051.767-22.369 2.262C377.723 58.272 328.091 32 272 32z

		//fa5pro-tachometer viewbox: 0 0 576 512
		//M75.694 480a48.02 48.02 0 0 1-42.448-25.571C12.023 414.3 0 368.556 0 320 0 160.942 128.942 32 288 32s288 128.942 288 288c0 48.556-12.023 94.3-33.246 134.429A48.018 48.018 0 0 1 500.306 480H75.694zm291.419-350.921c-12.659-3.928-26.105 3.148-30.035 15.808L282.659 320.24C249.814 322.955 224 350.454 224 384c0 35.346 28.654 64 64 64s64-28.654 64-64c0-19.976-9.155-37.809-23.494-49.546l54.416-175.34c3.929-12.659-3.15-26.107-15.809-30.035z

		//fa5pro-bolt viewbox: 0 0 320 512
		//M186.071 48l-38.666 144H272L120 464l54.675-208H48L67.72 48h118.351m0-48H67.72C42.965 0 22.271 18.825 19.934 43.469l-19.716 208C-2.453 279.642 19.729 304 48.004 304h64.423l-38.85 147.79C65.531 482.398 88.788 512 119.983 512c16.943 0 33.209-9.005 41.919-24.592l151.945-271.993C331.704 183.461 308.555 144 271.945 144h-61.951l22.435-83.552C240.598 30.026 217.678 0 186.071 0z

		//fa5pro-fighter-jet viewbox: 0 0 640 512
		//M519.953 181.397l-107.935-12.336L370.219 152H359.07l-70.571-88.214c22.23-1.229 39.503-7.698 39.503-15.786 0-9-21.383-16-47.189-16H128.002v32H144v63.531L119.476 96H45.185L8 133.185v62.12l-8 .971v119.447l8 .971v62.12L45.185 416h74.291L144 384.469V448h-15.998v32h152.811c25.806 0 47.189-7 47.189-16 0-8.088-17.273-14.558-39.503-15.786L359.07 360h11.149l41.8-17.061 107.935-12.336C580.922 317.055 640.297 308.569 640 256c.298-52.759-59.534-61.154-120.047-74.603zM512 283.2L400 296l-39.2 16H336l-96 120h-48V296h-40l-56 72H65.067L56 358.933V304h8v-16h40v-8l-56-6.8v-34.4l56-6.8v-8H64v-16h-8v-54.933L65.067 144H96l56 72h40V80h48l96 120h24.8l39.2 16 112 12.8c81.6 18.133 80 22.596 80 27.2s1.6 9.067-80 27.2z

		//fa5pro-fire viewbox: 0 0 384 512
		//M216 24.008c0-23.802-31.165-33.106-44.149-13.038C76.549 158.254 200 238.729 200 288c0 22.056-17.944 40-40 40s-40-17.944-40-40V182.126c0-19.392-21.856-30.755-37.731-19.684C30.754 198.379 0 257.279 0 320c0 105.869 86.131 192 192 192s192-86.131 192-192c0-170.29-168-192.853-168-295.992zM192 464c-79.402 0-144-64.598-144-144 0-28.66 8.564-64.709 24-88v56c0 48.523 39.477 88 88 88s88-39.477 88-88c0-64.267-88-120-64-208 40 88 152 121.771 152 240 0 79.402-64.598 144-144 144z

		//fa5pro-checkered-flag: 0 0 512 512
		//M160 112.71v70.38c-27.96 3.85-56.53 13.43-72 22.87v-69.33c17.18-10.48 43.3-21.14 72-23.92zm144 28.56c-25.921-4.31-48.847-12.909-72-19.65v66.62c23.842 6.27 46.477 15.161 72 20.52v-67.49zm-144 41.82v69.06c30.531-3.016 51.364-1.255 72 3.09v-67c-28.52-7.492-48.717-8.342-72-5.15zm72 139.64c25.944 4.314 48.857 12.914 72 19.65v-66.62c-23.657-6.212-46.507-15.174-72-20.52v67.49zM88 336.76c21.71-6.95 47.24-11.89 72-14.52v-70.09c-24.28 2.38-48.01 7.61-72 15.28v69.33zm360-216.68c-21.23 8.13-46.68 15.82-72 20.19v70.59c25.04-4.04 48.57-12.48 72-21.45v-69.33zm0 207.29v-69.33c-15.47 9.44-44.04 19.02-72 22.87v70.38c28.7-2.78 54.82-13.44 72-23.92zM304 208.76v67c28.52 7.492 48.717 8.342 72 5.15v-70.05c-23.832 3.83-46.524 3.263-72-2.1zM445.096 51.819C476.736 38.169 512 61.478 512 95.937v239.918c0 16.196-8.154 31.306-21.713 40.164C463.711 393.383 420.218 416 361.739 416c-68.608 0-112.781-32-161.913-32-56.567 0-89.957 11.28-127.826 28.557V496c0 8.837-7.163 16-16 16H40c-8.837 0-16-7.163-16-16V89.562C9.657 81.262 0 65.764 0 48 0 20.431 23.242-1.71 51.201.104c22.966 1.49 41.865 19.471 44.48 42.336a48.038 48.038 0 0 1-2.142 20.727C115.958 54.482 142.96 48 174.261 48c68.608 0 112.781 32 161.913 32 35.467 0 73.019-12.691 108.922-28.181zM464 96c-31.507 14.634-84.555 32-127.826 32-59.911 0-101.968-32-161.913-32C132.824 96 93.784 112.588 72 128v232c31.447-14.597 84.465-24 127.826-24 59.911 0 101.968 32 161.913 32 41.437 0 80.478-16.588 102.261-32V96z

		$icon_svg = 'data:image/svg+xml;base64,' .
			base64_encode('<?xml version="1.0" encoding="utf-8"?><svg width="20" height="20" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg"><path fill="black" d="M186.071 48l-38.666 144H272L120 464l54.675-208H48L67.72 48h118.351m0-48H67.72C42.965 0 22.271 18.825 19.934 43.469l-19.716 208C-2.453 279.642 19.729 304 48.004 304h64.423l-38.85 147.79C65.531 482.398 88.788 512 119.983 512c16.943 0 33.209-9.005 41.919-24.592l151.945-271.993C331.704 183.461 308.555 144 271.945 144h-61.951l22.435-83.552C240.598 30.026 217.678 0 186.071 0z"/></svg>');

		add_menu_page(
			__( 'WP Godspeed', 'wp-godspeed'),
			__( 'WP Godspeed', 'wp-godspeed'),
			'manage_options',
			'wp-godspeed',
			array( $this, 'display_setup_page' ),
			$icon_svg,
			NULL );

		add_submenu_page(
			'wp-godspeed',
			__( 'WP Godspeed Setup', 'wp-godspeed'),
			__( 'Setup', 'wp-godspeed-setup'),
			'manage_options',
			'wp-godspeed-setup',
			array( $this, 'display_setup_page' )
		);

		add_submenu_page(
			'wp-godspeed',
			__( 'WP Godspeed Options', 'wp-godspeed'),
			__( 'Options', 'wp-godspeed-options'),
			'manage_options',
			'wp-godspeed-options',
			array( $this, 'display_options_page' )
		);

		add_submenu_page(
			'wp-godspeed',
			__( 'WP Godspeed Status', 'wp-godspeed'),
			__( 'Status', 'wp-godspeed-status'),
			'manage_options',
			'wp-godspeed-status',
			array( $this, 'display_status_page' )
		);

		remove_submenu_page('wp-godspeed', 'wp-godspeed');

	}

	/**
	 * Do this
	 *
	 * @since  1.0.0
	 */
	public function display_setup_page()
	{
		include_once 'partials/wp-godspeed-admin-setup.php';
	}

	/**
	 * Do this
	 *
	 * @since  1.0.0
	 */
	public function display_status_page()
	{
		include_once 'partials/wp-godspeed-admin-status.php';
	}

	/**
	 * Do this
	 *
	 * @since  1.0.0
	 */
	public function display_options_page()
	{
		include_once 'partials/wp-godspeed-admin-options.php';
	}

	/**
	 * Do this
	 *
	 * @since  1.0.0
	 */
	public function registration_callback()
	{
		$data          = $_POST['data'];
		$status        = $data['status'];
		$auth_token    = $data['auth_token'];
		$plan          = $data['plan'];
		$plan_data     = $data['plan_data'];
		$data_usage    = $data['data_usage'];
		$billing_start = $data['billing_start'];
		$billing_end   = $data['billing_end'];

		update_option( $this->option_name . '_status',        $status        );
		update_option( $this->option_name . '_auth_token',    $auth_token    );
		update_option( $this->option_name . '_plan',          $plan          );
		update_option( $this->option_name . '_plan_data',     $plan_data     );
		update_option( $this->option_name . '_data_usage',    $data_usage    );
		update_option( $this->option_name . '_billing_start', $billing_start );
		update_option( $this->option_name . '_billing_end',   $billing_end   );

		echo json_encode( array( 'result' => 'ok' ) );

		wp_die();
	}

	public function wpgods_create_dist()
	{
		update_option( $this->option_name . '_cdn_processing', 1 );
		update_option( $this->option_name . '_cdn_processing_start_time', microtime( TRUE ) );

		$wpgods_cron = new WP_Godspeed_Cron();
		$wpgods_cron->process('create_new_dist');

		$waiting = get_option( $this->option_name . '_cdn_status_waiting' );
		echo json_encode( array( 'result' => 'got it', 'waiting' => "$waiting" ));
		wp_die();
	}

	public function wpgods_get_dist_state()
	{
		$wpgods_cron = new WP_Godspeed_Cron();
		$wpgods_cron->process('check_new_dist_status');
		$waiting    = get_option( $this->option_name . '_cdn_status_waiting' );
		$processing = get_option( $this->option_name . '_cdn_processing' );
		if ( ! $processing )
		{
			$cdn_id    = get_option( $this->option_name . '_distribution_id' );
			$cdn_uid   = get_option( $this->option_name . '_uid' );
			$cdn_alias = get_option( $this->option_name . '_subdomain' ) . '.godspeedcdn.com';
			echo json_encode( array( 'result' => 'got it', 'waiting' => "$waiting", 'processing' => $processing, 'cdn_id' => $cdn_id, 'cdn_uid' => $cdn_uid, 'cdn_alias' => $cdn_alias ) );
			wp_die();
		}
		echo json_encode( array( 'result' => 'got it', 'waiting' => "$waiting", 'processing' => $processing ) );
		wp_die();
	}

	public function wpgods_dist_update_stats_cb()
	{
		$data          = $_POST['data'];
		$data_usage    = $data['data_usage'];
		$plan_name     = $data['name'];
		$plan_data     = $data['plan_data'];
		$billing_start = $data['billing_start'];
		$billing_end   = $data['billing_end'];
		$cdnstatus     = $data['cdn_status'];

		if ( ! empty( $data['usage_history'] ) )
		{
			foreach ( $data['usage_history'] as $u )
			{
				$y = date( 'Y', strtotime( $u['billing_start'] ) );
				$m = date( 'M', strtotime( $u['billing_start'] ) );
				$d = $u['data_usage'];
				update_option( $this->option_name . '_usage_history_' . $m . '_' . $y, $d );
			}
		}

		$current = get_option( $this->option_name . '_plan_name' );
		if ($current !== $plan_name)
		{
			update_option( $this->option_name . '_needs_plan_upgrade', 0 );
		}

		update_option( $this->option_name . '_data_usage',    $data_usage    );
		update_option( $this->option_name . '_plan',          $plan_name     );
		update_option( $this->option_name . '_plan_data',     $plan_data     );
		update_option( $this->option_name . '_billing_start', $billing_start );
		update_option( $this->option_name . '_billing_end',   $billing_end   );
		update_option( $this->option_name . '_cdn_status',    $cdnstatus     );

		wp_die();
	}

	/**
	 * Do this
	 *
	 * @since  1.0.0
	 */
	public function notice_registration_complete()
	{
		$status    = get_option( $this->option_name . '_status' );
		$dismissed = get_option( $this->option_name . '_notice_registration_dismissed' );
		if ( $status == 'registered' && ( ! $dismissed || $dismissed == 0 ) )
		{
		?>
			<div class="notice notice-success is-dismissible notice-registration">
				<p><?php _e( 'Authorization token granted.', 'wp-godspeed' ); ?></p>
			</div>
		<?php
		}
	}

	/**
	 * Do this
	 *
	 * @since  1.0.0
	 */
	public function notice_registration_incomplete()
	{
		$status         = get_option( $this->option_name . '_status' );
		$current_screen = get_current_screen();
		if ( $status == 'not registered' )
		{
			if ( $current_screen->id == 'dashboard' || $current_screen->id == 'wp-godspeed_page_wp-godspeed-options' || $current_screen->id == 'wp-godspeed_page_wp-godspeed-status' )
			{
			?>
				<div class="notice notice-info notice-registration-incomplete">
					<p><?php _e( '<h4>WP Godspeed</h4>The Godspeed CDN setup process is incomplete. <a href="admin.php?page=' . $this->plugin_name . '-setup">Register your site to get started</a>.', 'wp-godspeed' ); ?></p>
				</div>
			<?php
			}
			if ( $current_screen->id == 'wp-godspeed_page_wp-godspeed-setup' )
			{
			?>
				<div class="notice notice-info notice-registration-incomplete">
					<p><?php _e( '<h4>WP Godspeed</h4>The Godspeed CDN setup process is incomplete, but this is where it all begins :)', 'wp-godspeed' ); ?></p>
				</div>
			<?php
			}
		}
	}

	/**
	 * Do this
	 *
	 * @since  1.0.0
	 */
	public function notice_registration_dismissed()
	{
		update_option( $this->option_name . '_notice_registration_dismissed', 1 );
	}

	/**
	 * Do this
	 *
	 * @since  1.0.0
	 */
	public function notice_distribution_complete()
	{
		$dist_id   = get_option( $this->option_name . '_distribution_id' );
		$dismissed = get_option( $this->option_name . '_notice_distribution_dismissed' );
		if ( $dist_id && $dismissed != 1 )
		{
			$this->notice_registration_dismissed();
			?>
				<div class="notice notice-success is-dismissible notice-distribution">
					<p><h3>WP Godspeed</h3><?php _e( 'Success! Your CDN has been setup and is <a href="admin.php?page=' . $this->plugin_name . '-options">ready for activation</a>.', 'wp-godpseed' ); ?></p>
				</div>
			<?php
		}
	}

	public function notice_distribution_dismissed()
	{
		update_option( $this->option_name . '_notice_distribution_dismissed', 1 );
	}

	public function notice_low_data_warning()
	{
		$needs_upgrade = get_option( $this->option_name . '_needs_plan_upgrade' );
		$cdnstatus     = get_option( $this->option_name . '_cdn_status' );
		if ( $needs_upgrade && ( !empty( $cdnstatus ) && $cdnstatus != 'disabled' ) )
		{
		?>
			<div class="notice notice-warning">
				<!-- <div class="godspeed-data-warning-container">
					<div class="godspeed-data-warning-content-left">
						<?php //echo '<img src="' . plugins_url( 'img/bolt.png', __FILE__ ) . '" style="height:50px; width:50px;" > '; ?>
					</div>
					<div class="godspeed-data-warning-content-right"> -->
							<h3><?php _e( "WP Godspeed CDN Data Usage Warning", 'wp-godpseed' ); ?></h3>
							<p><?php _e( "You're running low on data! Prevent your CDN service from being interrupted - upgrade ASAP.", 'wp-godpseed' ); ?></p>
					<!-- </div>
				</div> -->
			</div>
		<?php
		}
	}

	public function notice_cdn_disabled()
	{
		$cdnstatus = get_option( $this->option_name . '_cdn_status' );
		if ( ! empty( $cdnstatus ) && $cdnstatus == 'disabled' )
		{
			$plan = get_option( $this->option_name . '_plan' );
			if ( is_ssl() )
			{
			?>
				<div class="notice notice-error">
					<!-- <div class="godspeed-cdn-warning-container">
						<div class="godspeed-cdn-warning-content-left">
							<?php //echo '<img src="' . plugins_url( 'img/bolt.png', __FILE__ ) . '" style="height:50px; width:50px;" > '; ?>
						</div>
						<div class="godspeed-cdn-warning-content-right"> -->
								<h3><?php _e( "WP Godspeed CDN Disabled", 'wp-godpseed' ); ?></h3>
								<p><?php _e( "You've reached your maximum CDN usage for the current tier. Keep your site optimized - <a href='admin.php?page=" . $this->plugin_name . "-status'>click here to upgrade now</a>.", 'wp-godpseed' ); ?></p>
						<!-- </div>
					</div> -->
				</div>
			<?php
			}
			else
			{
				if ($plan == 'free')
				{
					$upgrade_to = 'starter';
				}
				if ($plan == 'starter')
				{
					$upgrade_to = 'business';
				}
				if ($plan == 'business')
				{
					$upgrade_to = 'pro1';
				}
				if ($plan == 'pro1')
				{
					$upgrade_to = 'pro2';
				}
				if ($plan == 'pro2')
				{
					$upgrade_to = 'pro3';
				}
				if ($plan == 'pro3')
				{
					$upgrade_to = 'pro4';
				}
				if ($plan == 'pro4')
				{
					$upgrade_to = 'pro5';
				}
				?>
					<div class="notice notice-error">
						<h3><?php _e( "WP Godspeed CDN Disabled", 'wp-godpseed' ); ?></h3>
						<p><?php _e( "You've reached your maximum CDN usage for the current tier. Keep your site optimized - <a href='https://api.godspeedcdn.com/billing/payment/$upgrade_to/$email'>click here to upgrade now</a>.", 'wp-godpseed' ); ?></p>
					</div>
				<?php
			}
		}
	}

	/**
	 * Do this
	 *
	 * @since  1.0.0
	 */
	public function register_setting()
	{
		add_settings_section(
			$this->option_name . '_general',
			__( '', 'wp-godspeed' ),
			array( $this, $this->option_name . '_general_cb' ),
			$this->plugin_name . '-options'
		);

		add_settings_section(
			$this->option_name . '_status',
			__( '', 'wp-godspeed' ),
			array( $this, $this->option_name . '_status_cb' ),
			$this->plugin_name . '-status'
		);

		add_settings_field(
			$this->option_name . '_cdn_enabled',
			__( 'Godspeed CDN', 'wp-godspeed' ),
			array( $this, $this->option_name . '_cdn_cb' ),
			$this->plugin_name . '-options',
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_cdn_enabled' )
		);

		add_settings_field(
			$this->option_name . '_lazyload_enabled',
			__( 'LazyLoading', 'wp-godspeed' ),
			array( $this, $this->option_name . '_lazyload_cb' ),
			$this->plugin_name . '-options',
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_lazyload_enabled' )
		);

		register_setting( $this->plugin_name, $this->option_name . '_cdn_enabled', 'intval' );
		register_setting( $this->plugin_name, $this->option_name . '_lazyload_enabled', 'intval' );
		$status = get_option( $this->option_name . '_status' );
		if ( ! $status )
		{
			update_option( $this->option_name . '_status', 'not registered' );
		}

	}

	public function wpgods_status_cb()
	{
		$status        = get_option( $this->option_name . '_status' );
		$auth_token    = get_option( $this->option_name . '_auth_token' );
		$plan          = get_option( $this->option_name . '_plan' );
		$plan_data     = get_option( $this->option_name . '_plan_data' );
		$billing_start = get_option( $this->option_name . '_billing_start' );
		$billing_end   = get_option( $this->option_name . '_billing_end' );
		$data_usage    = get_option( $this->option_name . '_data_usage' );
		$cdnstatus     = get_option( $this->option_name . '_cdn_status' );

		if ( ! $auth_token )
		{
			$auth_token = 'not authorized';
		}
		else
		{
			$auth_token = 'valid';
		}
		if ( ! $status )
		{
			$status = 'not registered';
		}
		if ( ! $plan )
		{
			$plan = 'no plan';
		}
		if ( ! $billing_start )
		{
			$billing_start = '0000-00-00';
		}
		if ( ! $billing_end )
		{
			$billing_end = '0000-00-00';
		}
		if ( ! $plan_data )
		{
			$plan_data = 5.00;
		}
		if ( ! $data_usage )
		{
			$data_usage = '0.00';
		}
		if ( ! $cdnstatus )
		{
			$cdnstatus = 'disconnected';
		}
		$data_usage_remaining = number_format($plan_data, 2) - number_format($data_usage, 2);
		$data_usage_rate      = $data_usage / number_format(date('d'), 2);
		$percent_used         = number_format($data_usage, 2) / number_format($plan_data, 0) * 100;
		$percent_used         = number_format($percent_used, 0);
		$daily_usage          = number_format($data_usage, 2) / (int) date('d');
		if ( ! $daily_usage )
		{
			$daily_usage = '0.00';
		}
		if ( $percent_used > 90 )
		{
			update_option( $this->option_name . '_needs_plan_upgrade', 1 );
			wp_clear_scheduled_hook( 'wp_godspeed_cron', array( 'check_cdn_status' ) );
			$this->cron_schedule_5min();
		}
		else
		{
			wp_clear_scheduled_hook( 'wp_godspeed_cron', array( 'check_cdn_status' ) );
			$this->cron_schedule();
			//update_option( $this->option_name . '_needs_plan_upgrade', 0 );
		}
		$version_info_title       = 'Version';
		$version_info_body        = ( $this->version !== '0.7.5' ? 'Please update the plugin at your earliest convenience.' : 'No updates available.' );
		$token_info_title         = 'Auth Token';
		$token_info_body          = ( $auth_token == 'not authorized' ? 'Please complete the setup process.' : 'Current Auth Token is valid.' );
		$status_info_title        = 'Status';
		$status_info_body         = ( $status == 'not registered' ? 'Please complete the setup process.' : 'All systems go.' );
		$plan_info_title          = 'Plan';
		$plan_info_body           = ( $plan !== 'no plan' ? "You are currently on the " . ucfirst($plan) . " plan." : 'You have to complete the registration process.' );
		$billing_start_info_title = 'Billing Start';
		$billing_start_info_body  = ( $billing_start !== '0000-00-00' ? "The current billing period began on $billing_start." : 'You have to complete the registration process.' );
		$billing_end_info_title   = 'Billing End';
		$billing_end_info_body    = ( $billing_end !== '0000-00-00' ? "The current billing period will end on $billing_end." : 'You have to complete the registration process.' );
		$usage_info_title         = 'Data Usage';
		$usage_info_body          = ( $data_usage > 8 ? 'You will need to upgrade to the Starter plan for CDN service to remain active.' : 'Data usage is looking good.' );
		$remaining_info_title     = 'Data Remaining';
		$remaining_info_body      = ( $data_usage_remaining < 2 ? "You will need to upgrade to your " . ucfirst($plan) . " plan for CDN service to remain active." : 'Data usage is looking good.' );

		$ttu  = $plan_data * 1 / $daily_usage; //days left to upgrade
		$ttu  = number_format( $ttu, 0 );
		$ldom = ( int ) date( 't' ); //last day of month
		$td   = ( int ) date( 'd' ); //day of month today
		$dlim = $ldom - $td; //days left in month
		if ( $dlim > $ttu )
		{
			//upgrade x days from now
			$upgrade_date = date( 'jS', strtotime( "+$ttu day" ) ) . ' of ' . date( 'F' );
		}

		$daily_usage_info_title   = 'Avg. Daily Usage';
		$daily_usage_info_body    = ( ! empty( $upgrade_date ) ? "At the current rate of $daily_usage GB/day, you will need to upgrade to your " . ucfirst($plan) . " plan on the $upgrade_date for CDN service to remain active." : 'Avg. Daily Usage is lookin\' good!' );
		$cdnstatus_info_title     = 'CDN Status';
		$cdnstatus_info_body      = ( $cdnstatus !== 'enabled' ? "You will need to upgrade your " . ucfirst($plan) . " plan in order to resume CDN service." : 'Everything is smooth sailing.' );

		echo '<p>' . __( '
			<div id="stats-display">
				<table>
					<tbody>
						<tr><td><label>Godspeed CDN Data Usage </label></td>
						<td><div class="progress" style="height: 40px; width: 310px; margin-bottom:10px;">
						<div class="progress-bar'. ($percent_used >= 80 && $percent_used < 90 ? ' bg-warning' : ($percent_used >= 90 ? ' bg-danger' : NULL ) ) .'" role="progressbar" style="width: ' . $percent_used . '%;" aria-valuenow="' . $percent_used . '" aria-valuemin="0" aria-valuemax="100">' . $percent_used . '%</div>
						</div></td>
						</tr>
					</tbody>
				</table>
				<br>
				<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapsestats" aria-expanded="false" aria-controls="collapsestats">Toggle Details</button>
				<div class="collapse" id="collapsestats">
					<div class="card card-body">
						<canvas id="myChart"></canvas>
						<table>
							<tbody>
								<tr>
								<td><span class="vpad">' . $version_info_title . '</span></td>
								<td align="right"><span class="bracket">[</span>
								<span class="dataitem">' . $this->version . '</span>
								<span class="bracket">]</span>
								<i class="fa fa-lg fa-info-circle"
									aria-hidden="true"
									data-trigger="hover"
									data-toggle="popover"
									title="' . $version_info_title . '"
									data-content="' . $version_info_body . '">
								</i></td>
								</tr>
								<tr>
								<td><span class="lpad">' . $token_info_title . '</span></td>
								<td align="right"><span class="bracket">[</span>
								<span class="dataitem">' . $auth_token . '</span>
								<span class="bracket">]</span>
								<i class="fa fa-lg fa-info-circle"
									aria-hidden="true"
									data-trigger="hover"
									data-toggle="popover"
									title="' . $token_info_title . '"
									data-content="' . $token_info_body . '">
								</i></td>
								</tr>
								<tr>
								<td><span class="spad">' . $status_info_title . '</span></td>
								<td align="right"><span class="bracket">[</span>
								<span class="dataitem">' . $status . '</span>
								<span class="bracket">]</span>
								<i class="fa fa-lg fa-info-circle"
									aria-hidden="true"
									data-trigger="hover"
									data-toggle="popover"
									title="' . $status_info_title . '"
									data-content="' . $status_info_body . '">
								</i></td>
								</tr>
								<tr>
								<td><span class="ppad">' . $plan_info_title . '</span></td>
								<td align="right"><span class="bracket">[</span>
								<span id="pn" class="dataitem">' . $plan . '</span>
								<span class="bracket">]</span>
								<i class="fa fa-lg fa-info-circle"
									aria-hidden="true"
									data-trigger="hover"
									data-toggle="popover"
									title="' . $plan_info_title . '"
									data-content="' . $plan_info_body . '">
								</i></td>
								</tr>
								<tr>
								<td><span class="ppad">' . $billing_start_info_title . '</span></td>
								<td align="right"><span class="bracket">[</span>
								<span class="dataitem">' . $billing_start . '</span>
								<span class="bracket">]</span>
								<i class="fa fa-lg fa-info-circle"
									aria-hidden="true"
									data-trigger="hover"
									data-toggle="popover"
									title="' . $billing_start_info_title . '"
									data-content="' . $billing_start_info_body . '">
								</i></td>
								</tr>
								<tr>
								<td><span class="ppad">' . $billing_end_info_title . '</span></td>
								<td align="right"><span class="bracket">[</span>
								<span class="dataitem">' . $billing_end . '</span>
								<span class="bracket">]</span>
								<i class="fa fa-lg fa-info-circle"
									aria-hidden="true"
									data-trigger="hover"
									data-toggle="popover"
									title="' . $billing_end_info_title . '"
									data-content="' . $billing_end_info_body . '">
								</i></td>
								</tr>
								<tr>
								<td><span class="dpad">' . $usage_info_title . '</span></td>
								<td align="right"><span class="bracket">[</span>
								<span id="du" class="dataitem">' . number_format($data_usage, 2) . 'GB</span>
								<span class="bracket">]</span>
								<i class="fa fa-lg fa-info-circle"
									aria-hidden="true"
									data-trigger="hover"
									data-toggle="popover"
									title="' . $usage_info_title . '"
									data-content="' . $usage_info_body . '">
								</i></td>
								</tr>
								<tr>
								<td><span class="rpad">' . $remaining_info_title . '</span></td>
								<td align="right"><span class="bracket">[</span>
								<span id="dr" class="dataitem">' . number_format($data_usage_remaining, 2) . 'GB</span>
								<span class="bracket">]</span>
								<i class="fa fa-lg fa-info-circle"
									aria-hidden="true"
									data-trigger="hover"
									data-toggle="popover"
									title="' . $remaining_info_title . '"
									data-content="' . $remaining_info_body . '">
								</i></td>
								</tr>
								<tr>
								<td><span class="rpad">' . $daily_usage_info_title . '</span></td>
								<td align="right"><span class="bracket">[</span>
								<span id="dr" class="dataitem">' . number_format($daily_usage, 2) . 'GB</span>
								<span class="bracket">]</span>
								<i class="fa fa-lg fa-info-circle"
									aria-hidden="true"
									data-trigger="hover"
									data-toggle="popover"
									title="' . $daily_usage_info_title . '"
									data-content="' . $daily_usage_info_body . '">
								</i></td>
								</tr>
								<tr>
								<td><span class="rpad">' . $cdnstatus_info_title . '</span></td>
								<td align="right"><span class="bracket">[</span>
								<span id="st" class="dataitem' . ($cdnstatus != 'active' ? '-warn' : NULL ) . '">' . $cdnstatus . '</span>
								<span class="bracket">]</span>
								<i class="fa fa-lg fa-info-circle"
									aria-hidden="true"
									data-trigger="hover"
									data-toggle="popover"
									title="' . $cdnstatus_info_title . '"
									data-content="' . $cdnstatus_info_body . '">
								</i></td>
								</tr>
							</tbody>
						</table>
						<button type="submit" id="sync" class="btn btn-sm btn-primary">Sync</button>
					</div>
				</div>
			</div>
			',
			'wp-godspeed' ) . '</p>';
	}

	public function wpgods_cdn_toggle_cb()
	{
		$data   = $_POST['data'];
		$status = $data['status'];
		if ($status == 1)
		{
			if ( $this->wpgods_cdn_test() )
			{
				update_option( $this->option_name . '_cdn_enabled', $status );
				update_option( $this->option_name . '_notice_distribution_dismissed', 1 );
				echo json_encode(array('result' => 'precheckpassed'));
			}
			else
			{
				update_option( $this->option_name . '_cdn_enabled', 0 );
				$error_code = get_option( $this->option_name . '_cdn_debug_response_code' );
				echo json_encode(array( 'result' => 'precheckfailed', 'error_code' => $error_code ));
			}
		}
		else
		{
			update_option( $this->option_name . '_cdn_enabled', $status );
			echo json_encode(array('result' => 'cdndisabled'));
		}
		wp_die();
	}

	public function wpgods_cdn_test()
	{
		$subdomain    = get_option( $this->option_name . '_subdomain' );
		$upload_dir   = wp_upload_dir();
		$uploads      = $upload_dir['basedir'];
		$url          = $upload_dir['baseurl'];
		$img_dst_fn   = 'wp-godspeed-cdn-test.png';
		$img_src      = plugin_dir_path( __DIR__ );
		$img_src_path = $img_src . 'admin/img/bolt.png';
		$img_dst_path = $uploads . '/' . $img_dst_fn;

		if ( ! copy( $img_src_path, $img_dst_path ) )
		{
			update_option( $this->option_name . '_cdn_debug', $img_src_path . ' to ' . $img_dst_path );
			return FALSE;
		}

		if ( $subdomain )
		{
			$testurl       = $url . '/' . $img_dst_fn;
			$strip         = parse_url($testurl);
			$cdn_test_url  = 'https://' . $subdomain . '.godspeedcdn.com' . $strip['path'];
			$response      = wp_remote_get( $cdn_test_url );
			$response_code = wp_remote_retrieve_response_code( $response );
			//$response_code = 503;
			if ($response_code !== 200)
			{
				unlink( $img_dst_path );
				update_option( $this->option_name . '_cdn_debug_response_code', $response_code );
				return FALSE;
			}
			unlink( $img_dst_path );
			delete_option( $this->option_name . '_cdn_debug_response_code' );
			return TRUE;
		}
		return FALSE;
	}

	public function wpgods_lazyload_toggle_cb()
	{
		$data   = $_POST['data'];
		$status = $data['status'];
		$btn    = $data['btn'];
		if ($status == 1 && $btn == 'img')
		{
			update_option( $this->option_name . '_lazyload_images', 1 );
		}
		if ($status == 0 && $btn == 'img')
		{
			update_option( $this->option_name . '_lazyload_images', 0 );
		}
		if ($status == 1 && $btn == 'if')
		{
			update_option( $this->option_name . '_lazyload_iframes', 1 );
		}
		if ($status == 0 && $btn == 'if')
		{
			update_option( $this->option_name . '_lazyload_iframes', 0 );
			update_option( $this->option_name . '_lazyload_youtube', 0 );
		}
		if ($status == 1 && $btn == 'yt')
		{
			update_option( $this->option_name . '_lazyload_iframes', 1 );
			update_option( $this->option_name . '_lazyload_youtube', 1 );
		}
		if ($status == 0 && $btn == 'yt')
		{
			update_option( $this->option_name . '_lazyload_youtube', 0 );
		}
		die();
	}

	/**
	 * Render the radio input field for position option
	 *
	 * @since  1.0.0
	 */
	public function wpgods_cdn_cb()
	{
		$enabled   = get_option( $this->option_name . '_cdn_enabled'     );
		$cdnstatus = get_option( $this->option_name . '_cdn_status'      );
		$dist_id   = get_option( $this->option_name . '_distribution_id' );
		$status    = get_option( $this->option_name . '_status'          );

		if ( $dist_id && $cdnstatus !== 'disabled' && $status !== 'not registered' )
		{
		?>
			<div class="wpgodspeed cdn">
				<fieldset>
					<div class="btn-group btn-group-toggle" data-toggle="buttons">
						<label id="dea" class="btn btn-secondary btn-deactivated btn-deactivated-cdn <?php echo ( $enabled == 0 ? 'focus active' : NULL ) ?>" > <!-- data-toggle="modal" data-target="#godspeedcdndisabled" -->
							<input type="radio" name="options" id="option1" autocomplete="off"> deactivated
						</label>
						<label id="act" class="btn btn-activated btn-activated-cdn <?php echo ( $enabled == 1 ? 'btn-primary focus active' : 'btn-secondary' ) ?>" > <!-- data-toggle="modal" data-target="#godspeedcdnenabled" -->
							<input type="radio" name="options" id="option2" autocomplete="off" active> activated
						</label>
					</div> <i style="padding-left: 7px;"
								class="fa fa-lg fa-info-circle"
								aria-hidden="true"
								data-trigger="hover"
								data-toggle="popover"
								title="CDN"
								data-content="When activated, all static resources (images/media/etc.) in wp-content/uploads will be served directly via the Godspeed CDN.">
							</i>
				</fieldset>
			</div>

			<div class="modal fade" id="godspeedcdn-deactivated" tabindex="-1" role="dialog" aria-labelledby="godspeedcdn-deactivated-Title" aria-hidden="true">
			  <div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="godspeedcdn-deactivated-LongTitle">Godspeed CDN</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body">Deactivated successfully</div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
				  </div>
				</div>
			  </div>
			</div>

			<div class="modal fade" id="godspeedcdn-activated" tabindex="-1" role="dialog" aria-labelledby="godspeedcdn-activated-Title" aria-hidden="true">
			  <div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="godspeedcdn-activated-LongTitle">Godspeed CDN</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body">Activated successfully</div>
				  <div class="modal-footer">
					  <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
				  </div>
				</div>
			  </div>
			</div>

			<div class="modal fade" id="godspeedcdn-activation-failed" tabindex="-1" role="dialog" aria-labelledby="godspeedcdn-activation-failed-Title" aria-hidden="true">
			  <div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="godspeedcdn-activation-failed-LongTitle">Godspeed CDN</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body">Failed to activate. <p id="godspeedcdn-activation-failed-error"></p></div>
				  <div class="modal-footer">
					  <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
				  </div>
				</div>
			  </div>
			</div>
		<?php
		}
		else
		{
		?>
			<div class="wpgodspeed cdn">
				<fieldset>
					<div class="btn-group btn-group-toggle" data-toggle="buttons">
						<label id="dea" class="btn btn-secondary btn-deactivated btn-disabled-cdn disabled" data-toggle="modal" data-target="#godspeedcdndisabled">
							<input type="radio" name="options" id="option1" autocomplete="off"> deactivated
						</label>
						<label id="act" class="btn btn-secondary btn-activated btn-disabled-cdn disabled" data-toggle="modal" data-target="#godspeedcdnenabled">
							<input type="radio" name="options" id="option2" autocomplete="off"> activated
						</label>
					</div> <i style="padding-left: 7px;"
								class="fa fa-lg fa-info-circle"
								aria-hidden="true"
								data-trigger="hover"
								data-toggle="popover"
								title="CDN"
								data-content="CDN is currently disabled.">
							</i>
				</fieldset>
			</div>
		<?php
		}
	}

	public function wpgods_lazyload_cb()
	{
		$images  = get_option( $this->option_name . '_lazyload_images' );
		$iframes = get_option( $this->option_name . '_lazyload_iframes' );
		$youtube = get_option( $this->option_name . '_lazyload_youtube' );

		?>
		<div class="wpgodspeed lazyload">
			<fieldset>
				<button type="button" class="btn btn-sm <?php echo ( $images == 1 ? 'btn-primary' : 'btn-secondary' ) ?> btn-lazyload-img" data-toggle="button" aria-pressed="true" autocomplete="off">
				  images
				</button>
				<button type="button" class="btn btn-sm <?php echo ( $iframes == 1 ? 'btn-primary' : 'btn-secondary' ) ?> btn-lazyload-if" data-toggle="button" aria-pressed="true" autocomplete="off">
				  iframes
				</button>
				<button type="button" class="btn btn-sm <?php echo ( $youtube == 1 ? 'btn-primary' : 'btn-secondary' ) ?> btn-lazyload-yt" data-toggle="button" aria-pressed="true" autocomplete="off">
				  youtube
				</button> <i style="padding-left: 7px;"
							class="fa fa-lg fa-info-circle"
							aria-hidden="true"
							data-trigger="hover"
							data-toggle="popover"
							title="Lazyloading"
							data-content="The selected resources which reside below the fold will not load until scrolling them into view. This makes the page load much faster as the browser does not have to wait for these resources in order to render the page.">
						</i>

			</fieldset>
		</div>
		<?php
	}

	public function wp_godspeed_cron_interval( $schedules )
	{
		$schedules['wp_godspeed_interval_1min'] = array(
			'interval' => 60, //low for debugging purposes
			'display'  => __( 'WP Godspeed Interval 1 minute' )
		);
		$schedules['wp_godspeed_interval_5min'] = array(
			'interval' => 300, //increased sync frequency when data usage is over 90%
			'display'  => __( 'WP Godspeed Interval 1 minute' )
		);
		return $schedules;
	}

	/**
	 * Display plugin settings link
	 *
	 * @since  1.0.0
	 */
	public function wpgods_admin_plugin_action_links( $links )
	{
		$settings_link = '<a href="options-general.php?page=' . $this->plugin_name . '">' . __( 'Settings' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}


}