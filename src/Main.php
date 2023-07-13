<?php
/**
 * Class Main file.
 *
 * @package AvataxWooCommerce
 */

namespace AvataxWooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Main
 *
 * @package AvataxWooCommerce
 */
class Main {
	/**
	 * Version of this plugin.
	 *
	 * @var string.
	 */
	private $version = '1.1.0';

	/**
	 * Plugin name.
	 *
	 * @var string.
	 */
	public $plugin_name = 'Avatax';

	/**
	 * The ID of this plugin settings.
	 *
	 * @var string.
	 */
	public $settings_id = 'avatax';

	/**
	 * Plugin Settings.
	 *
	 * @var Product\Single
	 */
	public $settings = null;

	/**
	 * Product customization.
	 *
	 * @var Product\Single
	 */
	public $product = null;
	
	/**
	 * Instance to call certain functions globally within the plugin
	 *
	 * @var self.
	 */
	protected static $instance = null;

	/**
	 * Construct the plugin.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'load_plugin' ), 1 );
	}

	/**
	 * Main Avatax for WooCommerce.
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Define WC Constants.
	 */
	private function define_constants() {
		// Path related defines.
		$this->define( 'AVATAX_WC_PLUGIN_BASENAME', plugin_basename( AVATAX_WC_PLUGIN_FILE ) );
		$this->define( 'AVATAX_WC_PLUGIN_DIR_PATH', untrailingslashit( plugin_dir_path( AVATAX_WC_PLUGIN_FILE ) ) );
		$this->define( 'AVATAX_WC_PLUGIN_DIR_URL', untrailingslashit( plugins_url( '/', AVATAX_WC_PLUGIN_FILE ) ) );

		// API defines.
		$this->define( 'AVATAX_WC_SANDBOX_API_URL', 'https://excisesbx.avalara.com' );
		$this->define( 'AVATAX_WC_PROD_API_URL', 'https://excise.avalara.com' );

		$this->define( 'AVATAX_WC_VERSION', $this->version );
		$this->define( 'AVATAX_PLUGIN_NAME', $this->plugin_name );
		$this->define( 'AVATAX_SETTINGS_ID', $this->settings_id );
	}

	/**
	 * Determine which plugin to load.
	 */
	public function load_plugin() {
		if ( Utils::WooCommerce_is_active() ) {
			$this->define_constants();
			$this->init_hooks();
		} else {
			add_action( 'admin_notices', array( $this, 'notice_wc_required' ) );
		}
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		$this->get_plugin_settings();
		$this->get_product_settings();
		$this->get_frontend();
	}

	/**
	 * Collection of hooks.
	 */
	public function init_hooks() {
		add_action( 'init', array( $this, 'init' ), 5 );
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Localisation.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'avatax-excise-xi', false, untrailingslashit( dirname( AVATAX_WC_PLUGIN_BASENAME ) ) . '/languages' );
	}

	/**
	 * Get product class.
	 *
	 * @return Product\Single
	 */
	public function get_product_settings() {
		if ( empty( $this->product ) ) {
			$this->product = new Product\Single();
		}

		return $this->product;
	}

	/**
	 * Get settings class.
	 *
	 * @return Product\Single
	 */
	public function get_plugin_settings() {
		if ( empty( $this->settings ) ) {
			$this->settings = new Settings\Settings();
		}

		return $this->settings;
	}

	/**
	 * Get frontend class.
	 */
	public function get_frontend() {
		new Frontend\Cart();
	}

	/**
	 * Get logger object.
	 */
	public function get_logger() {
		return new Logger( $this->get_plugin_settings()->is_logging_enabled() );
	}
	
	/**
	 * Define constant if not already set.
	 *
	 * @param  string      $name Name of constant variable.
	 * @param  string|bool $value Value of constant variable.
	 */
	public function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Admin error notifying user that WC is required.
	 */
	public function notice_wc_required() {
		?>
		<div class="error">
			<p><?php esc_html_e( 'Avatax plugin requires WooCommerce to be installed and activated!', 'avatax-excise-xi' ); ?></p>
		</div>
		<?php
	}
}