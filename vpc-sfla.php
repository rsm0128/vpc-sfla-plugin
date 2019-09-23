<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.woocommerceproductconfigurator.com
 * @since             1.0.0
 * @package           Vpc_Sfla
 *
 * @wordpress-plugin
 * Plugin Name:       Visual Product Configurator Save For Later Addon
 * Plugin URI:        https://www.woocommerceproductconfigurator.com/demo/save-for-later-configuration/
 * Description:       Allows you to save a configuration and continue later.
 * Version:           2.1
 * Author:            Orion
 * Author URI:        https://www.woocommerceproductconfigurator.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       vpc-sfla
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'VPC_SFLA_URL', plugins_url('/', __FILE__) );
define( 'VPC_SFLA_DIR', dirname(__FILE__) );
define('VPC_SFLA_FILE', 'visual-product-configuratore-save-for-later-addon/vpc-sfla.php' );
define('VPC_SFLA_VERSION', '2.1' );
define('ORION_SFLA_ADDON_NAME', 'Visual Products Configurator Save For Later Add-on' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-vpc-sfla-activator.php
 */
function activate_vpc_sfla() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vpc-sfla-activator.php';
	Vpc_Sfla_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-vpc-sfla-deactivator.php
 */
function deactivate_vpc_sfla() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vpc-sfla-deactivator.php';
	Vpc_Sfla_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vpc_sfla' );
register_deactivation_hook( __FILE__, 'deactivate_vpc_sfla' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vpc-sfla.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_vpc_sfla() {

	$plugin = new Vpc_Sfla();
	$plugin->run();

}
run_vpc_sfla();
