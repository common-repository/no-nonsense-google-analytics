<?php
/**
 * No-Nonsense Google Analytics Endpoint.
 *
 * @since   1.2.0
 * @package No_Nonsense_Google_Analytics
 */

/**
 * Endpoint class.
 *
 * @since   1.2.0
 * @package No_Nonsense_Google_Analytics
 */
if ( class_exists( 'WP_REST_Controller' ) ) {
	class NNGA_Endpoint extends WP_REST_Controller {
		/**
		 * Parent plugin class.
		 *
		 * @var   No_Nonsense_Google_Analytics
		 * @since 1.2.0
		 */
		protected $plugin = null;

		/**
		 * Constructor.
		 *
		 * @since  1.2.0
		 *
		 * @param  No_Nonsense_Google_Analytics $plugin Main plugin object.
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
			$this->hooks();
		}

		/**
		 * Add our hooks.
		 *
		 * @since  1.2.0
		 */
		public function hooks() {
			add_action( 'rest_api_init', array( $this, 'register_routes' ) );
		}

		/**
	     * Register the routes for the objects of the controller.
	     *
	     * @since  1.2.0
	     */
		public function register_routes() {

			// Set up defaults.
			$version = '1';
			$namespace = 'no-nonsense-google-analytics/v' . $version;
			$base = 'tracking-code';

			register_rest_route( $namespace, '/' . $base, array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_tracking_code' ),
					'permission_callback' => array( $this, 'get_tracking_code_permissions_check' ),
					'args'                => array(
						'context' => array(
							'default' => 'view',
						),
					),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_tracking_code' ),
					'permission_callback' => array( $this, 'update_tracking_code_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( false ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_tracking_code' ),
					'permission_callback' => array( $this, 'delete_tracking_code_permissions_check' ),
					'args'                => array(
						'force' => array(
							'default' => false,
						),
					),
				),
			));

			register_rest_route( $namespace, '/' . 'snippet', array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_tracking_snippet' ),
					'args'                => array(
						'context' => array(
							'default' => 'view',
						),
					),
				),
			));
		}

		/**
		 * Get tracking code.
		 *
		 * @since  1.2.0
		 *
		 * @param  WP_REST_Request $request Full details about the request.
		 */
		public function get_tracking_code( $request ) {
			return get_option( 'no_nonsense_google_analytics' );
		}

		/**
		 * Get tracking snippet.
		 *
		 * @since  1.2.0
		 *
		 * @param  WP_REST_Request $request Full details about the request.
		 */
		public function get_tracking_snippet( $request ) {
			$raw_codes = get_option( 'no_nonsense_google_analytics' );
			if ( $raw_codes ) {
				$codes = explode( ',', $raw_codes );
				$count = 0;
				foreach ( $codes as $key => $code ) {

					// Name all codes after the first one.
					if ( 0 === $count ) {
						$create[ $key ] = "ga('create', '" . trim( $code ) . "', 'auto');";
						$send[ $key ] = "ga('send', 'pageview');";
					} else {
							$create[ $key ] = "ga('create', '" . trim( $code ) . "', 'auto', 'code_" . $key . "');";
							$send[ $key ] = "ga('code_" . $key . ".send', 'pageview');";
					}
					$count++;
				}

				$snippet = "<!-- Google Analytics -->
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');";
				$snippet .= "\n" . implode( "\n", $create ) . "\n" . implode( "\n", $send );
				$snippet .= "</script>
<!-- End Google Analytics -->";

				return $snippet;
			}
		}

		/**
		 * Permission check for getting tracking code.
		 *
		 * @since  1.2.0
		 *
		 * @param  WP_REST_Request $request Full details about the request.
		 */
		public function get_tracking_code_permissions_check( $request ) {
			return true;
		}

		/**
		 * Update tracking codes.
		 *
		 * @since  1.2.0
		 *
		 * @param  WP_REST_Request $request Full details about the request.
		 */
		public function update_tracking_code( $request ) {
			$code = urldecode( $request['code'] );
			return update_option( 'no_nonsense_google_analytics', sanitize_text_field( $code ) );
		}

		/**
		 * Permission check for updating items.
		 *
		 * @since  1.2.0
		 *
		 * @param  WP_REST_Request $request Full details about the request.
		 */
		public function update_tracking_code_permissions_check( $request ) {
			if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Delete tracking code.
		 *
		 * @since  1.2.0
		 *
		 * @param  WP_REST_Request $request Full details about the request.
		 */
		public function delete_tracking_code( $request ) {
			if ( true == $request['force'] ) {
				delete_option( 'no_nonsense_google_analytics' );
				return true;
			} else {
				return 'You must set force to true in order to delete this item.';
			}
		}

		/**
		 * Permission check for deleting items.
		 *
		 * @since  1.2.0
		 *
		 * @param  WP_REST_Request $request Full details about the request.
		 */
		public function delete_tracking_code_permissions_check( $request ) {
			if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
				return true;
			} else {
				return false;
			}
		}
	}
}
