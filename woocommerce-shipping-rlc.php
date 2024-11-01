<?php
/**
 * Plugin Name: WooCommerce Shipping Method: R+L Carriers
 * Description: The R+L Carriers Shipping Plugin for WooCommerce retrieves your dynamic shipping rates from R+L Carriers.
 * Version: 1.8.3
 * Author: R+L Carriers
 * Author URI: http://www2.rlcarriers.com/contact
 * Tested up to: 6.5.4
 * WC requires at least: 7.8
 * WC tested up to: 8.9.2
 * Copyright: 2023 R+L Carriers
**/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WooCommerce_Shipping_RLC' ) ) :

	/**
	 * Main WooCommerce_Shipping_RLC Class
	 *
	 * @class WooCommerce_Shipping_RLC
	 * @version	3.0
	 */
	final class WooCommerce_Shipping_RLC {

		/**
		 * @var string
		 */
		public $version = '3.0';

		public $icon_svg = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNDAiIGhlaWdodD0iMjQwIiB2aWV3Qm94PSIwIDAgMjQwIDI0MCI+PGRlZnM+PHN0eWxlPi5he2ZpbGw6IzljYTFhNjt9PC9zdHlsZT48L2RlZnM+PHRpdGxlPkFydGJvYXJkIDE8L3RpdGxlPjxwYXRoIGNsYXNzPSJhIiBkPSJNMjE0Ljg0LDEyNi41NmMtNi45MSwwLTEzLjgyLDAtMjAuNzMsMHExMC4zNS0zMy4zMSwyMC42MS02Ni42NWMtMy44My0uMDYtNy42OC4xMS0xMS41MS0uMTMtMTAuMTUsMC0yMC4zLDAtMzAuNDUtLjA2TDE2My45Myw4OC4yYy0xLjUsNC42Ny0yLjgzLDkuMzktNC40MywxNC00LjktLjA1LTkuODEsMC0xNC43MSwwLDEuODQtNi42MiwzLjg0LTEzLjIsNS43NC0xOS44LTIuMjguMDYtNC41NiwwLTYuODMsMC0yLDYuNTktNCwxMy4xOC02LDE5Ljc3LTUuMjYsMC0xMC41Mi4wOC0xNS43OCwwLC44NS04Ljg5LTEtMTcuODgtNC4yNC0yNi4xM2EyMy4zNSwyMy4zNSwwLDAsMC0zLjIyLTZjLTMuMjktNC42Ni04LTguNTgtMTMuNTctMTAtMi0uNi00LjA4LS4yNS02LjExLS40N0gzMS4yMVExNS42OCwxMTUsMCwxNzAuM3YuMTdxMTguMTgsMCwzNi4zNiwwYzIuNDEtOC4wNiw0Ljc0LTE2LjE1LDcuMTItMjQuMjIsMi43LTkuMjksNS40OC0xOC41Niw4LjEyLTI3Ljg2LDEsMi41OCwxLjg4LDUuMjIsMi44NCw3LjgzTDY2LjUxLDE2MGMxLjI4LDMuNTMsMi4zOCw3LjEyLDMuNjUsMTAuNjUsMTAtLjE3LDIwLC4wOCwzMC0uMTUsNy40NywwLDE0Ljk0LjA2LDIyLjQxLDBxNy4yMS0yMi4xOSwxNC40OS00NC4zNWMxLjY3LTUsMy4yMy0xMC4xLDUtMTUuMS45Mi4wOCwxLjg0LjE3LDIuNzcuMTYsNCwwLDgsLjA4LDExLjkzLjEtMi40MSw4LjMyLTUsMTYuNTgtNy41MywyNC44OXEtNS4yMiwxNy4xNS0xMC40NSwzNC4zMWMxMi4wNSwwLDI0LjExLDAsMzYuMTYuMDVxMjIuMjIsMCw0NC40NCwwYzQuMzctMTQuNjcsOC44MS0yOS4zMywxMy4xNy00NEMyMjYuNjQsMTI2LjUyLDIyMC43NCwxMjYuNDksMjE0Ljg0LDEyNi41NlpNNzguOTEsMTEwLjgxYTExLjExLDExLjExLDAsMCwxLTcuNTUsMi41M2MtNi4xNywwLTEyLjM0LDAtMTguNTEsMCwxLjYyLTUuNjUsMy4zOC0xMS4yNiw1LTE2LjkxLDUuOS0uMTUsMTEuODEsMCwxNy43Mi0uMDdhNS45Miw1LjkyLDAsMCwxLDUuMjIsMi45NEE5LjI4LDkuMjgsMCwwLDEsNzguOTEsMTEwLjgxWk0xMjgsMTMzLjY5Yy00LjM1LDAtOC42OSwwLTEzLDAtNC44OS0uMjctOS44LS4xNC0xNC42OC0uNDNhMzYuMjEsMzYuMjEsMCwwLDAsMTQuNTUtMTEuNDMsMzcuMzYsMzcuMzYsMCwwLDAsNS40OC0xMC41NmMyLjQ0LS4xMyw0LjksMCw3LjM1LS4wNnM0Ljg0LS4yMSw3LjI2LS4xMVExMzEuNDcsMTIyLjM5LDEyOCwxMzMuNjlaIi8+PHBhdGggY2xhc3M9ImEiIGQ9Ik0yMzYuODEsMTY1LjI2YTguMzksOC4zOSwwLDAsMC0xMC4zOC4yNmMtMy41NCwzLTMuNzIsOS0uNiwxMi4zMWE4LjM2LDguMzYsMCwwLDAsNC45MiwyLjU2aDEuODdhOC40Nyw4LjQ3LDAsMCwwLDUtMi4zNkMyNDEuMDgsMTc0LjY0LDI0MC43NCwxNjguMTYsMjM2LjgxLDE2NS4yNlpNMjM2LjMxLDE3N2E2LjMzLDYuMzMsMCwwLDEtNi40NiwxLjY0LDUuOTMsNS45MywwLDAsMS0zLjQzLTMsOC4xOCw4LjE4LDAsMCwxLS4xMi03LjE1LDYuMSw2LjEsMCwwLDEsNC0zLjMyLDYuNDksNi40OSwwLDAsMSw3LjQ3LDMuOTJBNy4zNCw3LjM0LDAsMCwxLDIzNi4zMSwxNzdabS0xLjI0LTUuMjJjMS0xLjI5LjU3LTMuNjgtMS4xOS00LjA1YTI2LjgsMjYuOCwwLDAsMC01LjEzLS4xOXEwLDQuNTUsMCw5LjExbDEuNDksMGMwLTEuMjgtLjA3LTIuNTcsMC0zLjg0LjM3LS4yMy44OSwwLDEuMzIsMGEyNS42NSwyNS42NSwwLDAsMCwyLjQyLDMuNzRjLjUxLjI4LDEuMjEuMDYsMS43OS4xMi0uODQtMS4yOS0xLjczLTIuNTUtMi40Ni0zLjlBMy4zMiwzLjMyLDAsMCwwLDIzNS4wNywxNzEuNzlabS00LjgzLS4yOWMwLS45NCwwLTEuODgsMC0yLjgxLDEuMjMuMDksMi43OS0uMjMsMy43MS44LjE4LjYuMTksMS40NC0uNDcsMS43NUMyMzIuNDgsMTcxLjcsMjMxLjMyLDE3MS40MywyMzAuMjQsMTcxLjVaIi8+PC9zdmc+';

		/**
		 * @var int
		 * Default pallet weight limit
		 */
		public $pallet_weight_limit = 2750;

		public $nmfcClasses = array(
			'50.0',
			'55.0',
			'60.0',
			'65.0',
			'70.0',
			'77.5',
			'85.0',
			'92.5',
			'100.0',
			'110.0',
			'125.0',
			'150.0',
			'175.0',
			'200.0',
			'250.0',
			'300.0',
			'400.0',
			'500.0'
		);

		/**
		 * @var WooCommerce_Shipping_RLC The single instance of the class
		 * @since 2.1
		 */
		protected static $_instance = null;

		/**
		 * Main WooCommerce Instance
		 *
		 * Ensures only one instance of WooCommerce_Shipping_RLC is loaded', 'can be loaded.
		 *
		 * @since 2.1
		 * @static
		 * @see WC_RLC()
		 * @return WooCommerce_Shipping_RLC - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 2.1
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wc_rlc' ), '2.1' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 2.1
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wc_rlc' ), '2.1' );
		}

		/**
		 * WooCommerce_Shipping_RLC Constructor.
		 * @access public
		 */
		public function __construct() {

			include_once( 'includes/helpers.php' );
			include_once( 'includes/classes/RLC_RateQuote.php' );
			include_once( 'includes/classes/RLC_RateQuote_Charge.php' );
			include_once( 'includes/classes/RLC_Shipment.php' );
			include_once( 'includes/classes/RLC_RateQuote_TravelPoint.php');
			include_once( 'includes/classes/RLC_RateQuote_Request.php');

			/* Register Activation Hooks */
			register_activation_hook( __FILE__, array($this, 'activation_check'));
			register_activation_hook( __FILE__, array($this,'create_shipping_class_taxonomy_terms'));
			register_activation_hook( __FILE__, array($this,'create_plugin_database_tables'));

			/* Plugin Menus and Settings */
			// Menus
			add_action('admin_menu', array($this, 'rlc_menu'));
			// Settings
			add_action('admin_init', array($this, 'wc_rlc_settings_init'));
			// Plugin Page Links
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'plugin_links_html') );

			/* Includes */
			add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts') );
			add_action( 'wp_enqueue_scripts', array($this, 'frontend_scripts') );

			/* Shipping Method */
			add_action( 'woocommerce_shipping_init', array($this, 'init') );
			add_filter( 'woocommerce_shipping_methods', array($this, 'add_shipping_method') );
			add_filter( 'woocommerce_package_rates', array($this, 'force_package_freight'), 10, 2 );
			// Save Order Shipment Metadata

			add_action( 'woocommerce_checkout_update_order_meta', array($this, 'checkout_store_order_shipments') );
			// Order Details
			add_action('woocommerce_get_order_item_totals', array($this, 'order_details'), 10, 2);


			// Add accessorial selection to checkout form if not overridden or disabled
			if ( wc_rlc_isSpecialShippingServicesEnabled() && ! wc_rlc_isOverrideDestinationAccessorialsEnabled() ) {
				add_filter( 'woocommerce_checkout_fields', array($this, 'filter_checkout_fields') );
				add_action( 'woocommerce_checkout_after_customer_details', array($this, 'extra_checkout_fields') );
			}

			/* Product Fields */
			// Hazmat
			add_action( 'woocommerce_product_options_shipping', array($this, 'add_simple_product_hazmat_fields'));
			add_action( 'woocommerce_process_product_meta', array($this, 'save_product_hazmat_fields') );
			add_action( 'woocommerce_product_after_variable_attributes', array($this, 'add_variation_product_hazmat_fields'), 10, 3);
			add_action( 'woocommerce_save_product_variation', array($this, 'process_variation_product_hazmat_fields') , 10, 1 );

			// forced freight
			if ( wc_rlc_isMustShipFreightEnabled() ) {
				add_action( 'woocommerce_process_product_meta', array($this, 'saveProductForcedFreight') );
				add_action( 'woocommerce_product_options_shipping', array($this, 'addForceFreightProductGeneralField') );
				add_action( 'woocommerce_product_after_variable_attributes', array($this, 'addForceFreightProductVariationField'), 10, 3 );
				add_action( 'woocommerce_product_after_variable_attributes_js', array($this, 'addForcedFreightVariableFieldsJS'), 10, 1 );
				add_action( 'woocommerce_save_product_variation', array($this, 'processForcedFreightVariableFields') , 10, 1 );
			}

			if ( ! wc_rlc_isOverrideDestinationAccessorialsEnabled() ) {
				add_action('wp_ajax_reset_shipping', array($this, 'ajax_reset_shipping'));
                add_action('wp_ajax_nopriv_reset_shipping', array($this, 'ajax_reset_shipping'));
				add_action('wp_ajax_itemized_rates', array($this, 'ajax_itemized_rates'));
                add_action('wp_ajax_nopriv_itemized_rates', array($this, 'ajax_itemized_rates'));
}

			add_action('woocommerce_admin_order_items_after_shipping', array($this, 'wc_rlc_order_rlc_quote_number'));

            add_filter( 'woocommerce_hidden_order_itemmeta', array($this, 'hide_order_itemmeta') );

            add_action( 'before_woocommerce_init', function() {
                if( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                    \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true);
                }
			} );
			

        }

		public function wc_rlc_order_rlc_quote_number( $order_id ) {
            $shipment = new RLC_Shipment();

            $shipments = $shipment->getOrderShipments($order_id);
//            echo "<t
//><td colspan=3>".print_r($shipments, 1)."</td></tr>";
            if ( !sizeof( $shipments) ){
                // failed to get shipment get it from post meta instead
                $order = wc_get_order( $order_id );
                foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
                    $quoteNumber = $shipping_item_obj->get_meta('quote');
                    $serviceDays = $shipping_item_obj->get_meta('service_days');
                    if($quoteNumber){
                        echo "<tr><td></td><td>R+L Carriers Quote # </td><td colspan='3' style='text-align: right;' class='alt'>".$quoteNumber."</td><td></td></tr>";
                    }
                }
            } else{
                foreach ( $shipments as $shipment ) {

                    foreach ( $shipment->getQuotes() as $quote ) {
                        echo "<tr><td></td><td>R+L Carriers Quote # </td><td colspan='3' style='text-align: right;'>".$quote->quote_number."</td><td></td></tr>";
                    }

                }
            }







		}

		/**
		 *
		 * @param $fields
		 *
		 * @return mixed
		 */
		public function filter_checkout_fields( $fields ) {

			$hide = true;

			if ( wc_rlc_show_limited_delivery() ) {
				$hide = false;
				$new_fields['LimitedAccessDelivery']         = array(
					'type'              => 'checkbox',
					'custom_attributes' => array(
						'title' => 'Limited Access Delivery - Select this option if you are shipping to non-commercial, residential and/or private locations.',
					),
					'required'          => false,
					'label'             => __( 'Limited Access Delivery' ),
					'value'             => '1'
				);
			}

			//Special Shipping Services Fields
			if (wc_rlc_show_inside_delivery()) {
				$hide = false;
				$new_fields['InsideDelivery'] = array(
					'type' => 'checkbox',
					'custom_attributes' => array(
						'title' => 'Inside Delivery - Select this option if you need the driver to go inside to deliver your shipment.',
					),
					'required' => false,
					'label' => __('Inside Delivery'),
					'value' => '1'
				);
			}

			if (wc_rlc_show_destination_liftgate()) {
				$hide = false;

				$new_fields['DestinationLiftgate'] = array(
					'type' => 'checkbox',
					'custom_attributes' => array(
						'title' => 'Destination Liftgate - Select this service when a loading dock is not available. A liftgate is a platform at the back of certain trucks that can lower the shipment from the truck to the ground.'
					),
					'required' => false,
					'label' => __('Destination Liftgate'),
					'value' => '1'
				);
			}

			if (wc_rlc_show_delivery_notification() ) {
				$hide = false;

				$new_fields['DeliveryNotification']        = array(
					'type'              => 'checkbox',
					'custom_attributes' => array(
						'title' => 'Delivery Notification - Select this option if you need R+L Carriers to contact the shipper or consignee to schedule a delivery appointment. Additional fees apply for this service.'
					),
					'required'          => false,
					'label'             => __( 'Delivery Notification' ),
					'value'             => '1'
				);
			}

			$new_fields['saveDestinationAccessorials'] = array(
				'type'     => 'button',
				'required' => false,
				'label'    => __( 'Update Shipping' ),
				'value'    => ''
			);

			if ( ! $hide )
				$fields['dest_accessorials'] = $new_fields;

			return $fields;
		}


		public function extra_checkout_fields() {
			if ( array_key_exists('dest_accessorials', WC()->checkout()->checkout_fields)) :
				?>
				<div class="rlc-special-shipping">
					<h3>Special Shipping Services</h3>
                    <p>R+L Carriers offers the following optional special shipping services</p>
					<ul id="rqr-destination-accessorials">
						<?php foreach ( WC()->checkout()->checkout_fields['dest_accessorials'] as $key => $field ): ?>
							<?php woocommerce_form_field( $key, $field, WC()->checkout()->get_value( $key ) ); ?>
						<?php endforeach ?>
						<li>
							<button class="button" id="save-dest-accessorials">Update Shipping</button>
						</li>
					</ul>
				</div>
				<?php
			endif;
		}

		public function force_package_freight( $available_methods, $package) {

			if ( ! $threshold = intval(wc_rlc_get_forced_freight_weight()) )
				return $available_methods;

			if ( wc_rlc_get_package_weight($package) >= $threshold )
			{
				foreach ( $available_methods as $key => $method ) {

					if ( $method->method_id != 'rlc' ) {

						unset( $available_methods[ $key ] );

					}

				}
			}

			return $available_methods;
		}

		//
		// ********************************************
		// **** Plugin Scripts and Includes        ****
		// ********************************************
		//

		/**
		 * Register and Enqueue dashboard scripts
		 */
		public function admin_scripts( $hook_suffix ) {

			$js_ext = '.min.js';
			$css_ext = '.min.css';

			if ( get_option('wc_rlc_debug_mode') )
			{
				$js_ext = '.js';
				$css_ext = '.css';
			}

			wp_register_script( 'rlc-settings', wc_rlc_plugin_uri().'/assets/js/rlc-settings'.$js_ext, array(), '2.0', true );


		}

		/**
		 * Register and Enqueue front-end scripts
		 */
		public function frontend_scripts() {
			$js_ext = '.js';
			$css_ext = '.css';

			if ( get_option('wc_rlc_debug_mode') )
			{
				$js_ext = '.min'.$js_ext;
				$css_ext = '.min'.$css_ext;
			}

			wp_register_script( 'rlc-special-shipping',  wc_rlc_plugin_uri().'/assets/js/checkout-special-shipping-services'.$js_ext, array('jquery'), '1.0', true );
			wp_register_style( 'rlc-special-shipping',  wc_rlc_plugin_uri().'/assets/css/special-shipping'.$css_ext );

			wp_register_style( 'jquery-ui', wc_rlc_plugin_uri().'/assets/css/vendor/jquery-ui'.$css_ext );
			wp_register_style( 'wc_rlc_tooltip', plugin_dir_url( __FILE__ ) . 'assets/css/tooltip'.$css_ext );

			if ( is_checkout() )
			{
				wp_enqueue_style( 'rlc-special-shipping' );
				wp_enqueue_style( 'jquery-ui' );
				wp_enqueue_script( 'jquery-ui-tooltip' );

				$localization_params = array(
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => wp_create_nonce( 'rlc_reset_shipping_nonce' ),
					'ajax_url2'   => admin_url( 'admin-ajax.php' ),
					'ajax_nonce2' => wp_create_nonce( 'rlc_itemized_rates_nonce' )
				);

				wp_enqueue_script( 'reset-shipping', plugins_url( '/assets/js/checkout-special-shipping-services'.$js_ext, __FILE__ ), 'reset-shipping', '1.0', true );
				wp_localize_script( 'reset-shipping', 'ajax_reset_shipping', $localization_params );
				wp_localize_script( 'reset-shipping', 'ajax_itemized_rates', $localization_params );
			}

		}


		//
		// ********************************************
		// **** Plugin Installation and Activation ****
		// ********************************************
		//

		/**
		 * Plugin activation check
		 */
		public function activation_check() {

			if ( ! $this->is_woocommerce_active() ) {

				deactivate_plugins( basename( __FILE__ ) );

				wp_die( "Sorry, but you must activate WooCommerce before activating the R+L Carriers Shipping Method" );

			}

/* 			if ( ! class_exists( 'SoapClient' ) ) {

				deactivate_plugins( basename( __FILE__ ) );

				wp_die( 'Sorry, but you cannot run this plugin, it requires the <a href="http://php.net/manual/en/class.soapclient.php">SOAP</a> support on your server/hosting to function.' );

			} */

		}

		public function is_woocommerce_active() {
			return in_array( 'woocommerce/woocommerce.php', (array) get_option( 'active_plugins', array() ) ) || array_key_exists( 'woocommerce/woocommerce.php', (array) get_option( 'active_plugins', array() ) );
		}


		/**
		 * Initialize
		 *
		 * @access public
		 * @return void
		 */
		function init() {
			include_once(wc_rlc_plugin_path() . '/includes/classes/WC_Shipping_RLC.php' );
		}


		/**
		 * Add shipping method
		 *
		 * @access public
		 *
		 * @param mixed $methods
		 *
		 * @return mixed $methods
		 */
		function add_shipping_method( $methods ) {
			$methods['rlc'] = 'WC_Shipping_RLC';

			return $methods;
		}

		//
		// ********************************************
		// **** Plugin Settings and Configuration  ****
		// ********************************************
		//

		/**
		 * Plugin page links
		 *
		 * @param string $links
		 * @return mixed
		 */
		function plugin_links_html( $links ) {

			return array_merge( array('<a href="' . admin_url( 'admin.php?page=wc-rlc-settings' ) . '">' . __( 'Global Settings', 'wc_rlc' ) . '</a>'), $links );
		}


		/**
		 *
		 * Adds NMFC terms into woocommerce product_shipping_class taxonomy
		 *
		 * @params none
		 * @return void
		 **/
		function create_shipping_class_taxonomy_terms() {
			foreach ( $this->nmfcClasses as $class ) {
				wp_insert_term( $class, 'product_shipping_class' );
			}
		}


		/**
		 * createTables
		 *
		 * Registered activation hook creates DBs to store quote, product, and origin data
		 *
		 * @params none
		 * @return void
		 */
		function create_plugin_database_tables() {

			global $wpdb;

			$table_name = $wpdb->prefix . 'woocommerce_shipping_rlc_shipments';

			$sql[] = "CREATE TABLE IF NOT EXISTS $table_name (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` bigint(20) UNSIGNED NOT NULL,
                `destination_zipcode` varchar(255) NOT NULL,
                    `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

			$table_name = $wpdb->prefix . 'woocommerce_shipping_rlc_quotes';

			$sql[] = "CREATE TABLE IF NOT EXISTS $table_name (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `shipment_id` bigint(20) UNSIGNED NOT NULL,
                `origin_term_id` bigint(20) UNSIGNED DEFAULT NULL,
                `title` varchar(255) DEFAULT NULL,
                `items` text,
                `quote_number` bigint(20) NOT NULL,
                `net_charge` decimal(20,2) DEFAULT NULL,
                `method` varchar(255) NOT NULL,
                `service_days` int(10) DEFAULT NULL,
                `weight` decimal(20,2) NOT NULL DEFAULT '0.00',
                `accessorials` text DEFAULT NULL,
                `bol` int(11) DEFAULT NULL,
                `bol_pdf` mediumblob,
                `webpro` varchar(255) DEFAULT NULL,
                `pur` int(11) DEFAULT NULL,
                `pur_date` datetime DEFAULT NULL,
                `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";


			$table_name = $wpdb->prefix . 'woocommerce_shipping_rlc_quotes_charges';

			$sql[] = "CREATE TABLE IF NOT EXISTS $table_name (
                `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `quote_id` bigint(20) UNSIGNED NOT NULL,
                `type` varchar(255) DEFAULT NULL,
                `title` varchar(255) NOT NULL,
                `weight` decimal(20,2) UNSIGNED NOT NULL DEFAULT '0.00',
                `rate` decimal(20,2) NOT NULL DEFAULT '0.00',
                `amount` decimal(20,2) NOT NULL DEFAULT '0.00',
                `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			foreach ( $sql as $statement ) {

				dbDelta( $statement );

				if ( $wpdb->last_error ) {
					wc_rlc_logMessage( 'DB Installation Error: ' . $wpdb->last_error, 'error');
				}


			}

		}

		public function rlc_menu()
		{

			$perms = 'manage_woocommerce';

			// Main Menu
			add_menu_page(
				__('R+L Carriers Global Settings', 'wc_rlc'),
				__('R+L Carriers', 'wc_rlc'),
				$perms,
				'wc-rlc-settings',
				array($this, 'wc_rlc_get_menu'),
				$this->icon_svg
			);

			// Sub-menu: Plugin Settings
			add_plugins_page(
				__('R+L Carriers Global Settings', 'wc_rlc'),
				__('Settings', 'wc_rlc'),
				$perms,
				'wc-rlc-settings',
				array($this, 'wc_rlc_get_menu')
			);


		}

		/**
		 * Menu callback function
		 */
		function wc_rlc_get_menu() {
			$current_page = isset($_REQUEST['page']) ? esc_html($_REQUEST['page']) : 'wc_rlc';
			switch ($current_page) {
				// Global Settings
				case 'wc-rlc-settings':
					include_once('views/settings_global.php');
					break;
				// Shipments
				case 'wc-rlc-shipments':
					include_once('views/shipments.php');
					break;
				// Pick-ups
				case 'wc-rlc-pickups':
					include_once('views/pickups.php');
					break;
			}
		}

		/**
		 * Adds Global settings sections and fields
		 */
		public function wc_rlc_settings_init() {
		global $pagenow;

		if ((isset($_GET['page']) && $_GET['page'] === 'wc-rlc-settings' && $pagenow == "admin.php") || $pagenow == "options.php") {
			$settings = include_once('includes/admin/settings/global.php');

			foreach ( $settings as $setting_section => $setting ) {

				$section_name = 'wc_rlc_settings_section_'.strtolower(str_replace(' ', '_', $setting_section));

				add_settings_section(
					$section_name,
					__($setting_section, 'wc_rlc'),
					'',
					'wc_rlc_settings'
				);

				foreach ( $setting as $setting_name => $value )
				{
					add_settings_field(
						$setting_name,
						__($value['title'],'wc_rlc'),
						array($this, $setting_name),
						'wc_rlc_settings',
						$section_name,
						$setting[$setting_name] + array('id' => $setting_name) + array('section' => $section_name)
					);
					register_setting('wc_rlc_settings', $setting_name);
				}

			}

}
		}

		public function wc_rlc_environment( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'selected'=>array(get_option($args['id'])) ,
				'type'=>$args['type'],
				'value'=>'',
				'default'=>$args['default'],
				'description'=>$args['description'],
				'options'=>$args['options']
			));
		}

		public function wc_rlc_freight_weight_threshold( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'type'=>$args['type'],
				'value'=>get_option($args['id']),
				'default'=>$args['default'],
				'description'=>$args['description'],
			));
		}

		public function wc_rlc_api_key_prod( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'type'=>$args['type'],
				'value'=>get_option($args['id']),
				'default'=>$args['default'],
				'description'=>$args['description'],
			));
		}

		public function wc_rlc_sandbox_mode( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=>get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'description'=>$args['description'],
			));
		}

		public function wc_rlc_debug_mode( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=>get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'description'=>$args['description'],
			));
		}

		public function wc_rlc_must_ship_freight( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=>get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'description'=>$args['description'],
			));
		}

		public function wc_rlc_itemized( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=>get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'description'=>$args['description'],
			));
		}

		public function wc_rlc_packing_method( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'type'=>$args['type'],
				'selected'=>array(get_option($args['id'], $args['default'])),
				'default'=>$args['default'],
				'options'=>$args['options'],
			));
		}

		public function wc_rlc_default_package_type( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'selected'=>array(get_option($args['id'], $args['default'])),
				'type'=>$args['type'],
				'description'=>$args['description'],
				'options'=>$args['options'],
			));
		}

		public function wc_rlc_pallet_type( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'selected'=>array(get_option($args['id'], $args['default'])),
				'type'=>$args['type'],
				'default'=>$args['default'],
				'description'=>$args['description'],
				'options'=>$args['options'],
			));
		}

		public function wc_rlc_hazmat_contract_holder( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'type'=>$args['type'],
				'value'=>get_option($args['id']),
				'default'=>$args['default'],
				'description'=>$args['description'],
			));
		}

		public function wc_rlc_hazmat_contract_number( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'type'=>$args['type'],
				'value'=>get_option($args['id']),
				'default'=>$args['default'],
				'description'=>$args['description'],
			));
		}

		public function wc_rlc_hazmat_emergency_number( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'type'=>$args['type'],
				'value'=>get_option($args['id']),
				'default'=>$args['default'],
				'description'=>$args['description'],
			));
		}

        public function wc_rlc_pallet_limit( $args  ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'type'=>$args['type'],
				'value'=> get_option($args['id'], $this->pallet_weight_limit),
				'default'=>$args['default'],
				'description'=>$args['description'],
			));
		}

		public function wc_rlc_special_shipping_services( $args ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=>get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'description'=>$args['description'],
			));
		}

		public function wc_rlc_dest_accessorial_toggle( $args ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'type'=>$args['type'],
				'description'=>$args['description'],
			));
		}

		public function wc_rlc_show_limited_delivery( $args ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=> get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'label'=>$args['label'],
			));
		}

		public function wc_rlc_show_inside_delivery( $args ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=> get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'label'=>$args['label'],
			));
		}

		public function wc_rlc_show_destination_liftgate( $args ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=>get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'label'=>$args['label'],
			));
		}
		public function wc_rlc_show_delivery_notification( $args ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=>get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'label'=>$args['label'],
			));
		}

		public function wc_rlc_override_destination_accessorials( $args ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=>get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'label'=>$args['label'],
			));
		}

		public function wc_rlc_override_limited_delivery( $args ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=>get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'label'=>$args['label'],
			));
		}

		public function wc_rlc_override_inside_delivery( $args ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=>get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'label'=>$args['label'],
			));
		}

		public function wc_rlc_override_destination_liftgate( $args ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=>get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'label'=>$args['label'],
			));
		}

		public function wc_rlc_override_delivery_notification( $args ) {
			wc_rlc_return_input_field(array(
				'id'=>$args['id'],
				'checked'=>get_option($args['id'], $args['default']),
				'type'=>$args['type'],
				'value'=>1,
				'default'=>$args['default'],
				'label'=>$args['label'],
			));
		}


		//
		// ********************************************
		// **** Uninstallation and Deactivation    ****
		// ********************************************
		//


		/**
		 * Get list of available pallet types for API key
		 * @TODO: Store pallet types locally so API request isn't made on every settings page load, add button to force-update from API
		 * @access private
		 * @return mixed array of pallet codes => types
		 */
		public function get_pallet_types()
		{
			$request = array();

			$request['apikey'] = wc_rlc_get_api_key();

			if ( ! strlen($request['apikey']) )
				return array( 'na' => 'Save API key first.' );

			if ( wc_rlc_is_sandbox_mode_enabled() )
				return array('choose' => 'Pallet Rates not available in Sandbox Mode.');

			$rateQuote = new RLC_RateQuote();

			$palletTypes = $rateQuote->getPalletTypes( $request );

			$pallets = array();

			if ( sizeof( $palletTypes ) ) {
				$pallets['choose'] = 'Choose';
				foreach ( $palletTypes as $type ) {
					$pallets[ $type->Code ] = $type->Code . ': ' . $type->Description;
				}
			} else {
				$pallets[] = 'Error loading pallet types.';
			}

			return $pallets;
		}

		// ****************** //
		// ** Shipments    ** //
		// ****************** //

		/**
		 * Output order RLC shipment details on thankyou page
		 */
		public function order_details( $rows, $order ) {

			$shipment_factory = new RLC_Shipment();

			$shipments = $shipment_factory->getOrderShipments($order->id);

			$order_id = $order->id;
			$destination_zipcode = get_post_meta( $order_id, '_shipping_postcode', true );
			$destination_country = get_post_meta( $order_id, '_shipping_country', true );
			$destination_state = get_post_meta( $order_id, '_shipping_state', true );

			$fake_package = array();
			$fake_package['destination']['country'] = $destination_country;
			$fake_package['destination']['state'] = $destination_state;
			$fake_package['destination']['postcode'] = $destination_zipcode;
			/** @var TYPE_NAME $fake_package */
			$zone             = WC_Shipping_Zones::get_zone_matching_package($fake_package);

			if ( ! class_exists('WC_Shipping_RLC') )
				$this->init();

			$rates_factory = new WC_Shipping_RLC($zone->get_zone_id());



			if ( sizeof($shipments) )
			{
				foreach ( $shipments as $shipment )
				{
					foreach ($shipment->quotes as $quote) {

						$service_days = '';

						if ( wc_rlc_is_service_days_enabled($rates_factory) && intval($quote->service_days) ) {

							$service_days = '<br/>Service Days: <small>' . $quote->service_days . '</small>';

						}

						$new_rows['wc_rlc_shipping']['label'] = 'R+L Carriers:';

						$new_rows['wc_rlc_shipping']['value'] = 'R+L Quote #: <small>'.$quote->quote_number . '</small>'.$service_days.'<br/>';

						$rows = array_slice($rows, 0, 1) + $new_rows + array_slice($rows, 1);
					}
				}
			} else {
            // Quote didn't get saved so get it from the meta data
                foreach( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ){
                    $quoteNumber = $shipping_item_obj->get_meta('quote');
                    $serviceDays = $shipping_item_obj->get_meta('service_days');
                    if($quoteNumber){
                        $service_days = '';

                        if ( wc_rlc_is_service_days_enabled($rates_factory) && intval($serviceDays) ) {

                            $service_days = '<br/>Service Days: <small>' . $serviceDays . '</small>';

                        }
                        $new_rows['wc_rlc_shipping']['label'] = 'R+L Carriers:';

                        $new_rows['wc_rlc_shipping']['value'] = 'R+L Quote #: <small class="alt">'.$quoteNumber . '</small>'.$service_days.'<br/>';

                        $rows = array_slice($rows, 0, 1) + $new_rows + array_slice($rows, 1);
                    }
                }
            }

			return $rows;
		}

		/**
		 * @param $order_id
		 *
		 * @return bool
		 */
		public function checkout_store_order_shipments( $order_id ) {

			$rate_quote_factory = new RLC_RateQuote();
			$shipment_factory = new RLC_Shipment();

			$order = new WC_Order( $order_id );

            $rlc_info = WC()->session->get('rlc_' . $_COOKIE['woocommerce_cart_hash']);

			if ( ! is_array($rlc_info) )
			{
				wc_rlc_logMessage('RLC Quote Transient missing or not set. Unable to store RLC Quote Data.', 'error');
				return false;
			}

			$method_ids = array();
            $chosenShippingMethod = (isset($_POST['shipping_method']))? $_POST['shipping_method'] : [];
			foreach ( $order->get_shipping_methods() as $method ) {
				$method_ids[] = $method['method_id'];
			}

			$destination_zipcode = get_post_meta( $order_id, '_shipping_postcode', true );

			$shipment_id = $shipment_factory->store($order_id, $destination_zipcode);

			foreach ( $rlc_info as $shipment ) {

				$accessorials = $shipment['request']['accessorials'];

				foreach ( $shipment['request']['items'] as $item ) {
					if ( intval($item['is_hazmat']) && ! in_array('Hazmat', $accessorials) ) $accessorials[] = 'Hazmat';
				}

				$infos = $shipment['result']['levels'];

				foreach ( $infos as $key => $info ) {

                    if (in_array($info->Code, $chosenShippingMethod)) {
						$methods[] = $info;

						$quote_id = $rate_quote_factory->store(
							$shipment_id,
							$info->Title,
							$info->QuoteNumber,
							$info->NetCharge,
                            $method_ids[0].'-'.$chosenShippingMethod[0],
							$info->ServiceDays,
							$shipment['quotes'][$info->Code]['weight'],
							$accessorials,
							$shipment['request']['items'],
							$shipment['request']['originId']
						);

						if ( ! intval($quote_id) )
						{
							wc_rlc_logMessage('Error saving RLC Quote to database', 'error');
							return false;
						}

						foreach ($shipment['result']['charges'] as $charge) {

							$charge_factory = new RLC_RateQuote_Charge();

							$charge_factory->store($quote_id, $charge->Title, $charge->Weight, $charge->Rate, $charge->Amount, $charge->Type);
						}
					}
				}
			}
			return true;
		}





		//*************************//
		//**** Cart & Checkout ****//
		//*************************//

		/**
		 *
		 */
		public function ajax_reset_shipping()
		{
			$package_count = sizeof(WC()->session->get('cart'));

			for( $i=0; $i<$package_count; $i++ ) {
				WC()->session->__unset('shipping_for_package_'.$i);
			}

			echo json_encode(array('success'=>true, 'message' => 'Shipping_for_packages cleared from WC_Session.'));
			die();
		}

		/**
		 *
		 */
		public function ajax_itemized_rates()
		{
			echo json_encode($this->get_itemized_rate_charges());
			die();
		}

		/**
		 * @return array
		 */
		private function get_itemized_rate_charges()
		{
			$breakdown = array();

			$levels    = get_transient( 'rlc_' . $_COOKIE['woocommerce_cart_hash'] );

			if ( ! is_array( $levels ) ) {
				$levels = json_decode( str_replace( '\"', '"', $_COOKIE['rlc_shipping'] ), true );
			}

			if( is_array($levels) ) {
				foreach ($levels as $_levels)
				{
					foreach ( $_levels['result']['charges'] as $charge )
					{
						if ($charge->Type == 'RC' || $charge->Type == 'LIFT' || $charge->Type == 'ID') {
							$breakdown[$charge->Title] += floatval(explode('$',$charge->Amount)[1]);
						}
					}
				}

				foreach ( $breakdown as $key => $charge ) {
					$breakdown[$key] = wc_price($charge);
				}

			}

			return $breakdown;
		}

		// ******************************* //
		// *** Hazmat Product Fields   *** //
		// ******************************* //

		/**
		 * Outputs hazmat checkbox and hazmat description text input on simple products
		 * Outputs hazmat-related javascript for toggling hazmat description when is_hazmat is checked
		 */
		public function add_simple_product_hazmat_fields() {
			woocommerce_wp_checkbox(array(
				'id' => '_is_hazmat',
				'wrapper_class' => '',
				'label' => __('Hazmat', 'wc_rlc'),
				'description' => __('Check if item is classified as Hazardous Material'),
				'cbvalue'     => true
			));

		}

		/**
		 * Saves hazmat setting if option checked, else deletes as precaution
		 * @param $post_id
		 */
		public function save_product_hazmat_fields( $post_id ) {
			if ( ! empty( $_POST['_is_hazmat'] ) ) {
				update_post_meta( $post_id, '_is_hazmat', true );
			} else {
				delete_post_meta( $post_id, '_is_hazmat' );
			}

		}

		/**
		 * Adds hazmat fields on variable products
		 * @param $loop
		 * @param $variation_data
		 * @param $variation
		 */
		public function add_variation_product_hazmat_fields( $loop, $variation_data, $variation ) {
			$is_hazmat = get_post_meta($variation->ID, '_is_hazmat', true);
			?>
            <tr>
                <td>
                    <div>
                        <label for="is_hazmat_variation[<?php echo $loop ?>]"><?php _e( 'Hazmat', 'woocommerce' ) ?></label>
                        <input id="is_hazmat_variation[<?php echo $loop ?>]" class="variation_is_hazmat" type="checkbox" name="is_hazmat_variation[<?php echo $loop ?>]"
                               value="1" <?php echo checked('1', $is_hazmat)?>
                        " />
                    </div>
                </td>
            </tr>


			<?php

		}


		/**
		 * Stores hazmat checkbox and hazmat description fields for variations
		 * @param $variation_id
		 */
		public function process_variation_product_hazmat_fields( $variation_id ) {
			$current_variation_key = array_search($variation_id, $_POST['variable_post_id']);

			if ( intval( $_POST['is_hazmat_variation'][ $current_variation_key ] ) ) {
				update_post_meta( $variation_id, '_is_hazmat', wc_clean($_POST['is_hazmat_variation'][ $current_variation_key ]) );
			} else {
				delete_post_meta( $variation_id, '_is_hazmat' );
			}

		}

		/**
		 * Outputs forced freight checkbox for simple products general field
		 */
		public function addForceFreightProductGeneralField() {

			echo '<div class="options_group">';

			woocommerce_wp_checkbox(
				array(
					'id'          => '_force_freight',
					'label'       => __( 'R+L at any weight', 'woocommerce' ),
					'description' => __( 'Indicates that this item may be shipped with R+L Freight regardless of weight.', 'woocommerce' ),
					'cbvalue'     => '1'
				)
			);

			echo '</div>';
		}

		/**
		 * Saves forced freight setting if option checked, else deletes as precaution
		 * @param $post_id
		 */
		public function saveProductForcedFreight( $post_id ) {
			if ( ! empty( $_POST['_force_freight'] ) ) {
				update_post_meta( $post_id, '_force_freight', '1' );
			} else {
				delete_post_meta( $post_id, '_force_freight' );
			}
		}

		public function addForceFreightProductVariationField( $loop, $variation_data, $variation ) {
			?>
			<tr>
				<td>
					<div>
						<label for="force_freight_variation[<?php echo $loop ?>]"><?php _e( 'Force R+L Freight', 'woocommerce' ) ?></label>
						<input id="force_freight_variation[<?php echo $loop ?>]" type="checkbox" name="force_freight_variation[<?php echo $loop ?>]"
						       value="1" <?php echo checked(1, get_post_meta($variation->ID, '_force_freight', true))?>
						" />
					</div>
				</td>
			</tr>
			<?php
		}


		public function addForcedFreightVariableFieldsJS() {
			?>
			<tr>\
				<td>\
					<div>\
						<label for="force_freight_variation['+loop+']"><?php _e( 'Force R+L Freight', 'woocommerce' ); ?></label>\
						<input id="force_freight_variation['+loop+']" type="checkbox" name="force_freight_variation['+loop+']"/>\
					</div>
					\
				</td>
				\
			</tr>\
			<?php
		}

		public function processForcedFreightVariableFields( $variation_id ) {
			$current_variation_key = array_search($variation_id, $_POST['variable_post_id']);

			if ( intval( $_POST['force_freight_variation'][ $current_variation_key ] ) ) {
				update_post_meta( $variation_id, '_force_freight', 1 );
			} else {
				delete_post_meta( $variation_id, '_force_freight' );
			}
		}

       public function hide_order_itemmeta($args){
            $args[] = 'quote';
            $args[] = 'service_days';
            return $args;

        }
	}


endif;


/**
 * Returns the main instance of WooCommerce_Shipping_RLC to prevent the need to use globals.
 *
 * @return WooCommerce_Shipping_RLC
 */
function WC_RLC() {
	return WooCommerce_Shipping_RLC::instance();
}

// Global for backwards compatibility.
$GLOBALS['woocommerce_shipping_rlc'] = WC_RLC();