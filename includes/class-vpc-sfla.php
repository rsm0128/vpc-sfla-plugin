<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://orionorigin.com
 * @since      1.0.0
 *
 * @package    Vpc_Sfla
 * @subpackage Vpc_Sfla/includes
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
 * @package    Vpc_Sfla
 * @subpackage Vpc_Sfla/includes
 * @author     Orion <help@orionorigin.com>
 */
class Vpc_Sfla {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Vpc_Sfla_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'vpc-sfla';
		$this->version = '1.0.0';

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
	 * - Vpc_Sfla_Loader. Orchestrates the hooks of the plugin.
	 * - Vpc_Sfla_i18n. Defines internationalization functionality.
	 * - Vpc_Sfla_Admin. Defines all hooks for the admin area.
	 * - Vpc_Sfla_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vpc-sfla-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vpc-sfla-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vpc-sfla-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-vpc-sfla-public.php';

		$this->loader = new Vpc_Sfla_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Vpc_Sfla_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Vpc_Sfla_i18n();

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

		$plugin_admin = new Vpc_Sfla_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
                $this->loader->add_filter( "vpc_skins_settings",$plugin_admin,"add_save_for_later_settings");
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Vpc_Sfla_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'add_saved_design_endpoint' );
		$this->loader->add_action( 'woocommerce_account_config-saved_endpoint', $plugin_public, 'add_saved_design' );
		$this->loader->add_filter( "vpc_action_buttons",$plugin_public,'get_vpc_sfla_buttons',10,2);
		$this->loader->add_action( 'wp_ajax_save_for_later', $plugin_public, 'save_for_later_ajax');
		$this->loader->add_action( 'wp_ajax_nopriv_save_for_later', $plugin_public, 'save_for_later_ajax');
		$this->loader->add_filter( "vpc_data", $plugin_public,'add_vpc_sfla_var');
		$this->loader->add_filter( 'query_vars', $plugin_public,'vpc_sfla_query_vars', 0 );
		$this->loader->add_filter( 'woocommerce_login_redirect', $plugin_public, 'vpc_sfla_login_redirect',1100,2);
		$this->loader->add_filter( 'woocommerce_registration_redirect', $plugin_public, 'vpc_sfla_registration_redirect',10,1);
		$this->loader->add_action( 'wp_ajax_save_in_cookies', $plugin_public, 'save_in_cookies_ajax');
		$this->loader->add_action( 'wp_ajax_nopriv_save_in_cookies', $plugin_public, 'save_in_cookies_ajax');
		$this->loader->add_action( 'init', $plugin_public, 'vpc_sfla_init_sessions', 1);
		$this->loader->add_action( "vpc_container_end", $plugin_public, 'add_after_component', 10, 2);
		$this->loader->add_filter( "vpc_config_to_load", $plugin_public, "load_vpc_sfla_config_data", 10, 3);
		$this->loader->add_action( 'wp_ajax_delete_config', $plugin_public, 'delete_config_ajax');
		$this->loader->add_action( 'wp_ajax_nopriv_delete_config', $plugin_public, 'delete_config_ajax');
		$this->loader->add_filter( 'woocommerce_account_menu_items', $plugin_public, 'add_saved_design_menu',10,1);
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
	 * @return    Vpc_Sfla_Loader    Orchestrates the hooks of the plugin.
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
