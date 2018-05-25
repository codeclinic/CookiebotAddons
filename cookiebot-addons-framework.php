<?php
/**
 * Plugin Name: ClinicalWP Cookiebot WP-Fixes
 * Author: Code Clinic InternetAgentur
 * Description: Adding support & media optimisations for Cookiebot
 * Version: 1.3.0
 * Author URI: http://codeclinic.de
 * Text Domain: Clinical-CMS-Core
 * Domain Path: /languages/
 * Forked Plugin Name: Cookiebot Addons Framework
 * Author: Phill Healey @ CodeClinic
 * Original Authors: Johan Holst Nielsen & Aytac Kokus
 * Forked Version: 1.2.0
 */

namespace cookiebot_addons_framework;

use cookiebot_addons_framework\controller\Plugin_Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * __DIR__ of this plugin
 */
define( 'CAF_DIR', __DIR__ . DIRECTORY_SEPARATOR );

/**
 * Register autoloader to load files/classes dynamically
 */
include_once CAF_DIR . 'lib/cookiebot-addons-framework-autoloader.php';

/**
 * Load global functions for this plugin
 */
include_once CAF_DIR . 'lib/cookiebot-addons-functions.php';

class Cookiebot_Addons_Framework {

	public function __construct() {
		/**
		 * Load plugin controller to check if addons are active
		 * If active then load the plugin addon configuration class
		 * Else skip it
		 *
		 * @since 1.1.0
		 */
		add_action( 'plugins_loaded', array( new Plugin_Controller(), 'check_addons' ) );
		
		/**
		 * Load CookieBot page optimisations
		 * remove media attachment pages
		 */
		add_filter( 'rewrite_rules_array', array($this, 'clinicalWP_rmvattachment_permalink' ));
		add_filter( 'attachment_link', array($this, 'clinicalWP_rmvattachment_link' ));
	}

	function clinicalWP_rmvattachment_permalink( $rules ) {
		foreach ( $rules as $regex => $query ) {
			if ( strpos( $regex, 'attachment' ) || strpos( $query, 'attachment' ) ) {
				unset( $rules[ $regex ] );
			}
		}
		return $rules;
	}
	function clinicalWP_rmvattachment_link( $link ) {
		return;
	}
}

/**
 * Initiate the cookiebot addons framework plugin
 */
new Cookiebot_Addons_Framework();

