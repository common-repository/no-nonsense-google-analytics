<?php
/**
 * No-Nonsense Google Analytics Settings Page
 *
 * @since 1.0.0
 * @package No-Nonsense Google Analytics
 */

/**
 * No-Nonsense Google Analytics Settings Page.
 *
 * @since 1.0.0
 */
class NNGA_Settings_Page {
	/**
	 * Parent plugin class
	 *
	 * @var   No_Nonsense_Google_Analytics
	 * @since 1.0.0
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  1.0.0
	 * @param  No_Nonsense_Google_Analytics $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'settings_init' ) );
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
	public function _deactivate() {}

	/**
	 * Create the options page and add to the Settings submenu
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function add_settings_page() {
		add_submenu_page(
			'options-general.php',
			'Google Analytics',
			'Google Analytics',
			'manage_options',
			'no-nonsense-google-analytics',
			array( $this, 'options_page' )
		);
	}

	/**
	 * Register settings
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function settings_init() {
		register_setting( 'tracking_codes', 'no_nonsense_google_analytics', array( $this, 'validate_sanitize' ) );

		add_settings_section(
			'tracking_codes_section',
			'',
			array( $this, 'settings_section_callback' ),
			'tracking_codes'
		);

		add_settings_field(
			'ga_code',
			__( 'Tracking Code IDs', 'no-nonsense-google-analytics' ),
			array( $this, 'tracking_codes_render' ),
			'tracking_codes',
			'tracking_codes_section'
		);
	}

	/**
	 * Sanitize data before saving to database
	 *
	 * @since  1.0.0
	 * @param string $option text input from user form submission.
	 * @return sanitized option to be saved
	 */
	public function validate_sanitize( $option ) {
		return sanitize_text_field( $option );
	}

	/**
	 * Render input
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function tracking_codes_render() {
		$options = get_option( 'no_nonsense_google_analytics' );
		echo '<input type="text" name="no_nonsense_google_analytics" value="' . esc_textarea( $options ) . '" />';
		echo '<div class="description">You can enter multiple <code>UA-</code> codes separated by commas.</div>';
	}

	/**
	 * Echoes section information
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function settings_section_callback() {}

	/**
	 * Markup for Options Page
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function options_page() {
		echo '<div class="wrap">';
			echo '<h2>' . __( 'Google Analytics', 'no-nonsense-google-analytics' ) . '</h2>';
			echo "<form action='options.php' method='post'>";
				settings_fields( 'tracking_codes' );
				do_settings_sections( 'tracking_codes' );
				submit_button();
			echo '</form>';
		echo '</div>';
	}
}
