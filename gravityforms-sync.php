<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              howatsonco.com.au
 * @since             1.0.0
 * @package           hc-gravityforms-sync
 *
 * @wordpress-plugin
 * Plugin Name:       H+C Gravity Forms Sync
 * Plugin URI:        https://github.com/howatsonco/hc-gravityforms-sync
 * Description:       H+C Gravity Forms Sync
 * Version:           1.0.1
 * Author:            Howatson + Co
 * Author URI:        howatsonco.com.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hc-gravityforms-sync
 */

use HC\GravityFormsSync\Sync;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// Define HCGFS_PLUGIN_FILE.
if (!defined('HCGFS_PLUGIN_FILE')) {
	define('HCGFS_PLUGIN_FILE', __FILE__);
}

require dirname( __FILE__ ) . '/vendor/autoload.php';

/**
 * Returns the main instance of HCGFS to prevent the need to use globals.
 *
 * @return GravityFormsSync
 */
function HCGFS()
{
  return Sync::instance();
}

// Global for backwards compatibility.
$GLOBALS['hcgfs'] = HCGFS();