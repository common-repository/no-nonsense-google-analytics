<?php
/**
 * Plugin Name: No-Nonsense Google Analytics
 * Plugin URI:  https://labs.inn.org
 * Description: Simple Google Analytics plugin for embedding multiple Universal Analytics codes on your site. No dashboard, no reports.
 * Version:     1.3
 * Author:      innlabs
 * Author URI:  https://labs.inn.org
 * Donate link: https://labs.inn.org
 * License:     GPLv2
 * Text Domain: no-nonsense-google-analytics
 * Domain Path: /languages
 *
 * @link https://labs.inn.org
 *
 * @package No-Nonsense Google Analytics
 * @version 1.0.0
 */

/**
 * Copyright (c) 2017 innlabs (email : labs@inn.org)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Include additional php files here.
require 'includes/class-settings-page.php';
require 'includes/class-tracking-code.php';
require 'includes/class-endpoint.php';

/**
 * Main initiation class
 *
 * @since  1.0.0
 */
final class No_Nonsense_Google_Analytics {

	/**
	 * Current version
	 *
	 * @var  string
	 * @since  1.0.0
	 */
	const VERSION = '1.0.0';

	/**
	 * URL of plugin directory
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $path = '';

	/**
	 * Plugin basename
	 *
	 * @var string
	 * @since  1.0.0
	 */
	protected $basename = '';

	/**
	 * Detailed activation error messages
	 *
	 * @var array
	 * @since  1.0.0
	 */
	protected $activation_errors = array();

	/**
	 * Singleton instance of plugin
	 *
	 * @var No_Nonsense_Google_Analytics
	 * @since  1.0.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of NNGA_Settings_Page
	 *
	 * @since1.0.0
	 * @var NNGA_Settings_Page
	 */
	protected $settings_page;

	/**
	 * Instance of NNGA_Tracking_Code
	 *
	 * @since1.0.0
	 * @var NNGA_Tracking_Code
	 */
	protected $tracking_code;

	/**
	 * Instance of NNGA_Endpoint
	 *
	 * @since1.0.0
	 * @var NNGA_Endpoint
	 */
	protected $endpoint;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since  1.0.0
	 * @return No_Nonsense_Google_Analytics A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 *
	 * @since  1.0.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function plugin_classes() {
		// Attach other plugin classes to the base plugin class.
		$this->settings_page = new NNGA_Settings_Page( $this );
		$this->tracking_code = new NNGA_Tracking_Code( $this );
		$this->endpoint = new NNGA_Endpoint( $this );
	} // END OF PLUGIN CLASSES FUNCTION

	/**
	 * Add hooks and filters
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Activate the plugin
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function _activate() {
		// Make sure any rewrite functionality has been loaded.
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function _deactivate() {
		// Leave no trace
		delete_option( 'no_nonsense_google_analytics' );
	}

	/**
	 * Init hooks
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function init() {

		// Load translated strings for plugin.
		load_plugin_textdomain( 'no-nonsense-google-analytics', false, dirname( $this->basename ) . '/languages/' );

		// Initialize plugin classes.
		$this->plugin_classes();
	}

	/**
	 * Deactivates this plugin, hook this function on admin_init.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function deactivate_me() {
		// We do a check for deactivate_plugins before calling it, to protect
		// any developers from accidentally calling it too early and breaking things.
		if ( function_exists( 'deactivate_plugins' ) ) {
			deactivate_plugins( $this->basename );
		}
	}
	/**
	 * Magic getter for our object.
	 *
	 * @since  0.0.0
	 *
	 * @param  string $field Field to get.
	 * @throws Exception     Throws an exception if the field is invalid.
	 * @return mixed         Value of the field.
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::VERSION;
			case 'basename':
			case 'url':
			case 'path':
			case 'settings_page':
			case 'tracking_code':
			case 'endpoint':
				return $this->$field;
			default:
				throw new Exception( 'Invalid ' . __CLASS__ . ' property: ' . $field );
		}
	}
}

/**
 * Grab the No_Nonsense_Google_Analytics object and return it.
 * Wrapper for No_Nonsense_Google_Analytics::get_instance()
 *
 * @since  1.0.0
 * @return No_Nonsense_Google_Analytics  Singleton instance of plugin class.
 */
function no_nonsense_google_analytics() {
	return No_Nonsense_Google_Analytics::get_instance();
}

// Kick it off.
add_action( 'plugins_loaded', array( no_nonsense_google_analytics(), 'hooks' ) );

register_activation_hook( __FILE__, array( no_nonsense_google_analytics(), '_activate' ) );
register_deactivation_hook( __FILE__, array( no_nonsense_google_analytics(), '_deactivate' ) );
