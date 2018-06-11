<?php

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wpgodspeed.io
 * @since      1.0.0
 *
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/includes
 * @author     WP Godspeed <hello@wpgodspeed.io>
 */
class WP_Godspeed {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WP_Godspeed_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The cron instance
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $cron    An instance of cron
	 */
	protected $cron;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if ( defined( 'WP_GODSPEED_VERSION' ) )
		{
			$this->version = WP_GODSPEED_VERSION;
		}
		else
		{
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-godspeed';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Godspeed_Loader. Orchestrates the hooks of the plugin.
	 * - WP_Godspeed_i18n. Defines internationalization functionality.
	 * - WP_Godspeed_Admin. Defines all hooks for the admin area.
	 * - WP_Godspeed_Cron. Load the cron class.
	 * - WP_Godspeed_CDN_Stack_Plugin. Load the CDN class.
	 * - WP_Godspeed_Lazyload_Plugin. Load the lazyload class.
	 * - WP_Godspeed_Public. Defines all hooks for the public side of the site.
	 * - Composer. This is turned off for compatibility purposes.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-godspeed-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-godspeed-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-godspeed-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-godspeed-cron.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-godspeed-cdn-stack.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-godspeed-lazyload.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-godspeed-public.php';

		/**
		 * Composer autoload - disabled for compatibility
		 */
		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';

		$this->loader = new WP_Godspeed_Loader();
		$this->cron   = new WP_Godspeed_Cron();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Godspeed_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WP_Godspeed_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WP_Godspeed_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'create_admin_menus' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_setting' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'cron_schedule' );

		$this->loader->add_action( 'wp_ajax_registration_callback', $plugin_admin, 'registration_callback' );
		$this->loader->add_action( 'wp_ajax_notice_registration_dismissed', $plugin_admin, 'notice_registration_dismissed' );
		$this->loader->add_action( 'wp_ajax_create_distribution_callback', $plugin_admin, 'create_distribution_callback' );

		$this->loader->add_action( 'wp_ajax_wpgods_create_dist', $plugin_admin, 'wpgods_create_dist' );
		$this->loader->add_action( 'wp_ajax_notice_distribution_dismissed', $plugin_admin, 'notice_distribution_dismissed' );
		$this->loader->add_action( 'wp_ajax_wpgods_cdn_toggle_cb', $plugin_admin, 'wpgods_cdn_toggle_cb' );
		$this->loader->add_action( 'wp_ajax_wpgods_lazyload_toggle_cb', $plugin_admin, 'wpgods_lazyload_toggle_cb' );
		$this->loader->add_action( 'wp_ajax_wpgods_dist_update_stats_cb', $plugin_admin, 'wpgods_dist_update_stats_cb' );
		$this->loader->add_action( 'wp_ajax_wpgods_get_dist_state', $plugin_admin, 'wpgods_get_dist_state' );

		$this->loader->add_action( 'admin_notices', $plugin_admin, 'notice_distribution_complete' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'notice_low_data_warning' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'notice_cdn_disabled' );

		$this->loader->add_filter( "plugin_action_links_$this->plugin_name/wp-godspeed.php", $plugin_admin, 'wpgods_admin_plugin_action_links' );
		$this->loader->add_filter( 'cron_schedules', $plugin_admin, 'wp_godspeed_cron_interval' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WP_Godspeed_Public( $this->get_plugin_name(), $this->get_version() );

		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WP_Godspeed_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
