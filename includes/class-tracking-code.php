<?php
/**
 * No-Nonsense Google Analytics Tracking Code
 *
 * @since 1.0.0
 * @package No-Nonsense Google Analytics
 */

/**
 * No-Nonsense Google Analytics Tracking Code.
 *
 * @since 1.0.0
 */
class NNGA_Tracking_Code {
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
		add_action( 'wp_head', array( $this, 'tracking_code' ) );
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function tracking_code() {
		// Exclude all potential authors
		if ( ! current_user_can( 'edit_posts' ) ) {
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

				echo "<!-- Google Analytics -->
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');";
echo "\n" . implode( "\n", $create ) . "\n" . implode( "\n", $send );
echo "</script>
<!-- End Google Analytics -->";
			}
		}
	}
}
