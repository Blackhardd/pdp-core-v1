<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.instagram.com/lovu_volnu/
 * @since             1.0.0
 * @package           Pdp_core
 *
 * @wordpress-plugin
 * Plugin Name:       PIED-DE-POULE Core
 * Plugin URI:        https://www.instagram.com/lovu_volnu/
 * Description:       Core functionality plugin.
 * Version:           1.0.1
 * Author:            Alexander Piskun
 * Author URI:        https://www.instagram.com/lovu_volnu/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pdp_core
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ){
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PDP_CORE_VERSION', '1.0.1a' );

define( 'PDP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pdp_core-activator.php
 */
function activate_pdp_core() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pdp_core-activator.php';
	Pdp_core_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pdp_core-deactivator.php
 */
function deactivate_pdp_core() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pdp_core-deactivator.php';
	Pdp_core_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pdp_core' );
register_deactivation_hook( __FILE__, 'deactivate_pdp_core' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pdp_core.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pdp_core() {

	$plugin = new PDP_Core();
	$plugin->run();

}
run_pdp_core();

/**
 * Require plugin functions file.
 */
require_once plugin_dir_path( __FILE__ ) . 'pdp_core-functions.php';


/**
 * Session start
 */
add_action( 'init', 'pdp_session_start' );

function pdp_session_start(){
    if( !session_id() ){
        session_start();
    }
}