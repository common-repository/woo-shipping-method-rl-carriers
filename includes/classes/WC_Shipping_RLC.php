<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Shipping_RLC' ) ) :

    /**
     * WC_Shipping_RLC class.
     *
     * @extends WC_Shipping_Method
     */

    class WC_Shipping_RLC extends WC_Shipping_Method
    {
        /**
         * @var array
         * Default R+L Carriers Freight Service Level names and descriptions
         */

            


        public $services = array(
            "STD" => array(
                'name' => "R+L Carriers Standard Delivery Service",
                'description' => "The value you have come to expect from R+L Carriers at an affordable price."
            ),
            "GSDS" => array(
                'name' => 'R+L Carriers Guaranteed Delivery Service',
                'description' => 'Receive your freight by 5PM on or before service date, based on standard service.',
            ),
            "GSAM" => array(
                'name'  => 'R+L Carriers Guaranteed AM Delivery Service',
                'description' => 'Receive your freight by 12PM on or before service date, based on standard service.'
            ),
            "GSHW" => array(
                'name' => 'R+L Carriers Guaranteed Window Delivery Service',
                'description' => 'Receive your freight within an hourly window on service date, based on standard service.'
            )
        );

        /**
         * @var array
         * Default R+L Carriers Destination Accessorials
         */
        private $accessorials = array (
            'Limited Access Delivery' => 'LimitedAccessDelivery',
            'Inside Delivery' => 'InsideDelivery',
            'Destination Liftgate' => 'DestinationLiftgate',
            'Delivery Notification' => 'DeliveryNotification',
        );

        private $found_rates;

        /**
         * @var int
         * Total maximum weight limit for a shipment package
         */
        private $maxPackageWeight = 20000;

        /**
         * @var WooCommerce_Shipping_RLC The single instance of the class
         * @since 2.1
         */
        protected static $_instance = null;

        /**
         * Main WooCommerce Instance
         *
         * Ensures only one instance of WooCommerce_Shipping_RLC is loaded or can be loaded.
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
         * __construct function.
         *
         * @access public
         *
         * @param int $instance_id
         */
        public function __construct( $instance_id = 0 )
        {
            $this->id = 'rlc';

            $this->instance_id = absint( $instance_id );

            $this->title = 'R+L Carriers';

            $this->method_title = __('R+L Carriers', 'wc_rlc');

            $this->method_description = __('The <strong>R+L Carriers</strong> shipping extension obtains rates dynamically from the R+L Carriers Rate Quote API during cart/checkout.', 'wc_rlc');

            $this->supports = array(
                'shipping-zones',
                'instance-settings',
                'instance-settings-modal',
            );

            $this->init();

            add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));


        }

        /**
         * init function.
         *
         * @access private
         * @return void
         */
        private function init()
        {
            $this->instance_form_fields = include( wc_rlc_plugin_path() . '/includes/admin/settings/instance.php' );
            $this->instance_settings = get_option( $this->get_instance_option_key(), null );
        }

        /**
         * Outputs services table on settings page for customization
         *
         * @access public
         * @return string
         */
        public function generate_services_html()
        {
            ob_start(); ?>
            <tr valign="top" id="service_options">
                <th scope="row" class="titledesc"><?php _e('Services', 'wc_rlc'); ?></th>
                <td class="forminp">
                    <table class="rlc_services widefat">
                        <thead>
                        <th class="sort" style="width:1%">&nbsp;</th>
                        <th><?php _e('Enabled', 'wc_rlc'); ?></th>
                        <th style="width:1%;text-align:center"><?php _e('Code', 'wc_rlc'); ?></th>
                        <th><?php _e('Name', 'wc_rlc'); ?></th>
                        <th><?php _e(__('Adj ($)', 'wc_rlc')); ?></th>
                        <th><?php _e('Adj %', 'wc_rlc'); ?></th>
                        <th><?php _e('Description', 'wl_rlc'); ?></th>
                        </thead>
                        <tbody>
                        <?php foreach ($this->services as $code => $service) : ?>
                            <tr>
                                <td class="sort">
                                    <input type="hidden" class="code" name="rlc_service[<?php echo $code; ?>][code]" value="<?php echo $code?>"/>
                                    <input type="hidden" class="order" name="rlc_service[<?php echo $code; ?>][order]" value="<?php echo $this->get_custom_service_setting($code, 'order')?>"/>
                                </td>
                                <td>
                                    <input type="checkbox" name="rlc_service[<?php echo $code; ?>][enabled]" <?php checked($this->get_custom_service_setting($code, 'enabled', true)); ?> />
                                </td>
                                <td style="text-align:center">
                                    <strong><?php echo $code; ?></strong>
                                </td>
                                <td>
                                    <input type="text" name="rlc_service[<?php echo $code; ?>][name]" value="<?php echo $this->get_custom_service_setting($code, 'name', $service['name'])?>" size="50"/>
                                </td>
                                <td>
                                    <input type="text" name="rlc_service[<?php echo $code; ?>][adjustment]" value="<?php echo $this->get_custom_service_setting($code, 'adjustment')?>" size="4"/>
                                </td>
                                <td>
                                    <input type="text" name="rlc_service[<?php echo $code; ?>][adjustment_percent]" value="<?php echo $this->get_custom_service_setting($code, 'adjustment_percent')?>" size="4"/>
                                </td>
                                <td>
                                    <textarea rows="4" cols="40" name="rlc_service[<?php echo $code; ?>][description]"><?php echo $this->get_custom_service_setting($code, 'description', $service['description'])?></textarea>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <?php

            wp_enqueue_script('rlc-settings');

            return ob_get_clean();

        }

        private function get_custom_service_setting($code, $key, $default = '') {
            $value = get_option($this->get_instance_option_key())['custom_services'][$code][$key];
            return is_bool($value) || (! is_bool($value) && strlen($value))  ? $value : $default;
        }

        /**
         * Validate customized services settings
         *
         * @access public
         * @return mixed
         */
        public function process_admin_options()
        {

            parent::process_admin_options();

            $services = array();


            foreach ($_POST['data'] as $key => $settings) {

                if ( strpos($key, 'rlc_service[') === false )
                    continue;

                $services[$settings['code']] = array(
                    'name' => wc_clean($settings['name']),
                    'order' => wc_clean($settings['order']),
                    'enabled' => isset($settings['enabled']) ? true : false,
                    'adjustment' => wc_clean($settings['adjustment']),
                    'adjustment_percent' => str_replace('%', '', wc_clean($settings['adjustment_percent'])),
                    'description' => isset($settings['description']) ? wc_clean($settings['description']) : '',
                );


            }


            $this->instance_settings['custom_services'] = $services;

            //update_option('custom_services', $services);
            update_option( $this->get_instance_option_key(), apply_filters( 'woocommerce_shipping_' . $this->id . '_instance_settings_values', $this->instance_settings, $this ) );

            return $services;

        }

        //
        // **************************************
        // **** WC Shipping Method Functions ****
        // **************************************
        //

        /**
         * is_available function.
         * Returns true w hen
         *
         * @access public
         * @param mixed $package
         * @return bool
         */
        public function is_available($package)
        {
            $available = true;

            // each non-virtual item in the package must be able to ship freight
            foreach ($package['contents'] as $item) {
                if ($item['data']->virtual == 'yes')
                    continue;

                if (! $this->item_can_ship_freight($item)) {
                    $available = false;
                }
            }

            // Add RLC custom stuff
            if ( $available ) {
                // Add itemized rates breakdown if enabled
                if (wc_rlc_is_itemized_rates_enabled())
                    add_action('woocommerce_review_order_before_shipping', array($this, 'wc_rlc_outputItemizedRatesHTML') );

                // Add service descriptions and accessorials output
                add_action('woocommerce_review_order_after_shipping', array($this, 'jQueryServiceDescriptionTooltips'));
            }

            return $available;
        }



        /**
         * calculate_shipping function.
         *
         * @access public
         * @param mixed $package
         * @return boolean
         */
        public function calculate_shipping($package = array())
        {
            $save_this = array();

            // abort if destination postcode not set OR if pacakge does not ship freight
            if ( ! wc_rlc_is_destination_postcode_set($package))
            {
                wc_rlc_logMessage('Aborting: Destination postcode missing');
                return false;
            }

            if ( ! $this->package_ships_freight($package) )
            {
                wc_rlc_logMessage('Aborting: Package not freight-ready.');
                return false;
            }

            // Initialize arrays
            $quotes = $this->found_rates = $package_requests = array();


            if ( $package_requests = $this->get_package_requests($package) ) {

                try {

                    foreach ($package_requests as $key => $request) {

                        $weight = 0;

                        // @TODO: refactor to loop through items and calculate total weight
                        foreach ($request['items'] as $item)
                        {
                            $weight += $item['weight'];
                        }

                        if ( $weight >= $this->maxPackageWeight )
                        {
                            if ( wc_rlc_isDebugModeEnabled() )
                                wc_rlc_logMessage( 'A shipment exceeds the maximum weight of ' . $this->maxPackageWeight . ' lbs.', 'error');

                            throw new Exception('A shipment exceeds the maximum weight of '. $this->maxPackageWeight .' lbs.');
                        }

                        $result = $this->get_result($request);

                        $this->process_result($result);

                        if ( sizeof( $result ) ) {

                            $save_this[$key]['request'] = $request;
                            $save_this[$key]['result']  = $result;

                            foreach ($result['levels'] as $quote) {

                                $_quotes[$quote->Code] = array(
                                    'quote_id' => $quote->QuoteNumber,
                                    'origin' => $request['originZip'],
                                    'dest_zip' => $request['destinationZip'],
                                    'accessorials' => json_encode($request['accessorials']),
                                    'charges' => json_encode($result['charges']),
                                    'weight' => $weight,
                                    'days' => $quote->ServiceDays,
                                    'method' => $quote->Name,
                                    'items' => json_encode($request['items'])
                                );

                            }
                            $save_this[$key]['quotes'] = $_quotes;
                        }
                    }

                } catch (Exception $ex) {

                    //wc_add_notice('Failed to retrieve R+L Carriers rate quote. ('.$ex->getMessage().') Please <a href="/contact">contact</a> the site administrator.', 'error');
                    wc_rlc_logMessage( 'Failed to retrieve rate quote: ' . $ex->getMessage(), 'error');
                    return false;

                }

                // Ensure rates were found for all packages
                if ($this->found_rates) {
                    foreach ($this->found_rates as $key => $value) {

                        $quotes[$key] = $value;
                        $quotes[$key]['accessorials'] = $request['accessorials'];
                        if ($value['packages'] < sizeof($package_requests))
                            unset($this->found_rates[$key]);
                    }
                }

                if (sizeof($quotes) && sizeof( $save_this ) )
                    WC()->session->set('rlc_' . $_COOKIE['woocommerce_cart_hash'], $save_this);
                else
                    wc_rlc_logMessage('Failed to retrieve save RLC quote. Quote data is missing', 'error');


                // Add all found rates
                foreach ($this->found_rates as $key => $rate) {
                    $rate['meta_data'] = array(
                            'quote'=>$rate['quote'],
                            'service_days'=>$rate['service_days']
                    );
                    $this->add_rate($rate);
                }
            }

            return true;
        }


        /**
         * Build unique components of multiple package requests
         *
         * @access private
         *
         * @param $package
         *
         * @return mixed
         */
        private function get_package_requests($package)
        {
            $requests = array();

            $origins = $this->get_package_origins($package);


            

            foreach ( $origins as $origin ) {
                $pallets = array();

                $request = $this->get_request($package, $origin);

                if (wc_rlc_get_packing_method() == 'palletized') {
                    $pallets['pallets'] = $this->palletized_freight($package, $origin);
                }

                $requests[] = array_merge($request, $pallets);

            }
            return $requests;
        }

        /**
         * palletized_freight - calculate number of pallets and return portion of request array
         *
         * @access private
         * @param $package
         * @return array API formatted array filterable by `woocommerce_rlc_palletized_freight` filter
         */
        private function palletized_freight($package, $origin)
        {
            $pallets_request = array();
            $package_weight = 0;
            $pallet_type = wc_rlc_get_pallet_type($package, $origin);
            $pallet_limit = wc_rlc_get_pallet_limit();

            foreach ($origin['items'] as $item) {
                if ( intval($item['variation_id']) ) {
                    $product = new WC_Product_Variation($item['variation_id']);
                } else {
                    $product = new WC_Product($item['product_id']);
                }

                $package_weight += intval($product->get_weight()) * intval($item['quantity']);
            }

            $pallet_quantity = ceil($package_weight / $pallet_limit );

            $pallet_weight = ceil($package_weight / $pallet_quantity );

            //TODO: uncouple from list to function

            if (isset($pallet_type)) {

                $pallets_request[] = array(
                    'Code' => $pallet_type,
                    'Weight' => $pallet_weight, //each pallet weighs exactly the limit when there's more than one because I can magically chop up products
                    'Quantity' => $pallet_quantity,
                );
    
            }


            return apply_filters( 'woocommerce_rlc_palletized_freight', $pallets_request, $package );
        }


        /**
         * process_result function.
         *
         * @access private
         * @param mixed $result
         * @retvoidvoid
         */
        private function process_result($result = '')
        {
            if (array_key_exists('levels', $result) && $result['levels']) {

                foreach ($result['levels'] as $level) {
                    $rate_code = $level->Code;
                    $rate_cost = (float)str_replace('$', '', woocommerce_clean($level->NetCharge));
                    $rate_id = $level->Code;
                    $rate_name = $level->Name;
                    $service_days = $level->ServiceDays;
                    $quote_number = $level->QuoteNumber;
                    $charges = $result['charges'];
                    $this->prepare_rate($rate_code, $rate_id, $rate_name, $rate_cost, $service_days, $quote_number, $charges);
                }
            } else { // use fallback
                if ( $fallback_rate = floatval($this->get_option('fallback')) ) {
                    $this->prepare_rate( 'STD', 'STD', 'Freight Delivery', $fallback_rate, 0, '', '', '');
                }
            }
        }

        private function set_custom_rate_cost( $rate_code, $rate_cost ) {
            // Cost adjustment %
            if (!empty($adjustment_percent = $this->get_custom_service_setting($rate_code, 'adjustment_percent')) )
                $rate_cost = $rate_cost + ($rate_cost * (floatval($adjustment_percent) / 100));

            //Cost adjustment
            if (!empty($adjustment = $this->get_custom_service_setting($rate_code, 'adjustment')) )
                $rate_cost = $rate_cost + floatval($adjustment);

            //Minimum post-adjustment cost
            if ( ! empty($min_cost = floatval($this->get_option('minimum_freight_rate'))) && $rate_cost < $min_cost )
                $rate_cost = $min_cost;

            return $rate_cost;
        }

        /**
         * Adds jQuery to DOM which applies tooltips to shipping method labels
         */
        public function jQueryServiceDescriptionTooltips() {
            ?>
            <script type="text/javascript">

                jQuery(document).ready(function () {
                    jQuery('label[for="shipping_method_0_std"]').attr('title', '<?php echo $this->get_custom_service_setting('STD', 'description')?>');
                    jQuery('label[for="shipping_method_0_gsds"]').attr('title', '<?php echo $this->get_custom_service_setting('GSDS', 'description')?>');
                    jQuery('label[for="shipping_method_0_gsam"]').attr('title', '<?php echo $this->get_custom_service_setting('GSAM', 'description')?>');
                    jQuery('label[for="shipping_method_0_gshw"]').attr('title', '<?php echo $this->get_custom_service_setting('GSHW', 'description')?>');

                    jQuery('label').tooltip();
                });

                jQuery(document).ajaxComplete(function () {
                    jQuery('label[for="shipping_method_0_std"]').attr('title', '<?php echo $this->get_custom_service_setting('STD', 'description')?>');
                    jQuery('label[for="shipping_method_0_gsds"]').attr('title', '<?php echo $this->get_custom_service_setting('GSDS', 'description')?>');
                    jQuery('label[for="shipping_method_0_gsam"]').attr('title', '<?php echo $this->get_custom_service_setting('GSAM', 'description')?>');
                    jQuery('label[for="shipping_method_0_gshw"]').attr('title', '<?php echo $this->get_custom_service_setting('GSHW', 'description')?>');

                    jQuery('label').tooltip();
                });
            </script>
            <?php
        }

        /**
         * prepare_rate function.
         *
         * @access private
         * @param mixed $rate_code
         * @param mixed $rate_id
         * @param mixed $rate_name
         * @param mixed $rate_cost
         * @return void
         */
        private function prepare_rate($rate_code, $rate_id, $rate_name, $rate_cost, $service_days, $quote_number, $charges)
        {
            // Enabled check
            if ( ! $this->get_custom_service_setting($rate_code, 'enabled') )
                return;

            // Merging @TODO: What is this merging in???
            if (isset($this->found_rates[$rate_id])) {
                $rate_cost = $rate_cost + $this->found_rates[$rate_id]['cost'];
                $packages = 1 + $this->found_rates[$rate_id]['packages'];
            } else {
                $packages = 1;
            }



            $this->found_rates[$rate_id] = array(
                'id' => $rate_id,
                'label' => $this->get_custom_service_setting($rate_code, 'name') . ' ' . $this->show_service_days_if_enabled($service_days),
                'cost' => $this->set_custom_rate_cost($rate_code, $rate_cost),
                'service_days' => $service_days,
                'sort' => $this->get_custom_service_setting($rate_code, 'order'),
                'packages' => $packages,
                'quote' => $quote_number,
                'charges' => $charges,
            );
        }

        /**
         * get_result function.
         *
         * @access private
         * @param mixed $request
         * @return array
         */
        private function get_result($request)
        {

            $client = new RLC_RateQuote();

            $result = $client->getServiceLevels($request);

            if (wc_rlc_isDebugModeEnabled()) {
                wc_add_notice('R+L RESPONSE: <pre>' . print_r($result, true) . '</pre>');
                wc_rlc_logMessage(print_r($result, true), 'message');
            }

            return $result;
        }


        /**
         * Determine request origin information
         *
         * @param $package
         * @return array
         */
        public function get_package_origins($package)
        {
            // origin portion of request that is returned
            $requests = array();

            $request = array();

            $this->add_origin_to_request($request);

            $this->add_origin_accessorials_to_request($request);

            $this->add_package_items_to_request($package, $request);

            $requests[] = $request;

            return $requests;
        }

        /**
         * @param $request
         * @param null $origin_term_id
         */
        public function add_origin_to_request(&$request, $origin_term_id = null)
        {
            $request['originCity'] = $this->get_option('originCity');
            $request['originState'] = $this->get_option('originState');
            $request['originZip'] = $this->get_option('origin');
            $request['originCountry'] = wc_rlc_get_country_code($this->get_option('originCountry'));
        }

        /**
         * @param $request
         * @param null $origin_term_id
         */
        public function add_origin_accessorials_to_request(&$request, $origin_term_id = null)
        {
            if ($this->get_option('InsidePickup') == 'yes') {
                $request['accessorials'][] = 'InsidePickup';
            }
            if ($this->get_option('OriginLiftgate') == 'yes') {
                $request['accessorials'][] = 'OriginLiftgate';
            }
            if ($this->get_option('LimitedAccessPickup') == 'yes') {
                $request['accessorials'][] = 'LimitedAccessPickup';
            }
        }

        /**
         * Build common components for all package requests
         *
         * @access private
         * @param mixed $package - the package being shipped
         * @param $origin - origin information for request
         * @return array - R+L RQR
         */
        private function get_request($package, $origin)
        {
            $request = $origin;

            // Add API key per environment setting
            $request['apikey'] = wc_rlc_get_api_key();

            $request['cod'] = '0';
            $request['customerData'] = '';
            $request['destinationZip'] = str_replace(" ", "", $package['destination']['postcode']);
            $request['destinationCountry'] = wc_rlc_get_country_code($package['destination']['country']);
            $request['type'] = wc_rlc_get_request_quote_type($package);
            $request['destinationCity'] = $package['destination']['city'];
            $request['destinationState'] = $package['destination']['state'];
            $request['value'] = wc_rlc_get_package_value($package);
            $request['accessorials'] = (array_key_exists('accessorials', $request) && is_array($request['accessorials'])) ?
                array_merge_recursive($request['accessorials'], $this->wc_rlc_get_destination_accessorials()) : $this->wc_rlc_get_destination_accessorials();

            return $request;
        }


        private function wc_rlc_get_origin_accessorials() {
            $accessorials = array();

            if ( $this->get_option('InsidePickup') )
                $accessorials[] = 'InsidePickup';

            if ( $this->get_option('OriginLiftgate') )
                $accessorials[] = 'OriginLiftgate';

            if ( $this->get_option('OriginLiftgate') )
                $accessorials[] = 'OriginLiftgate';

            if ( $this->get_option('LimitedAccessPickup') )
                $accessorials[] = 'LimitedAccessPickup';
            
            return $accessorials;
        }

        private function wc_rlc_get_destination_accessorials() {
            $accessorials = array();

            if ( wc_rlc_isOverrideDestinationAccessorialsEnabled() ) // use override dest accessorials if enabled in settings
            {
                if (wc_rlc_isOverrideLimitedAccessDeliveryEnabled()) $accessorials[] = 'LimitedAccessDelivery';
                if (wc_rlc_isOverrideInsideDeliveryEnabled()) $accessorials[] = 'InsideDelivery';
                if (wc_rlc_isOverrideLiftgateDeliveryEnabled()) $accessorials[] = 'DestinationLiftgate';

            } else { // use selection from checkout/cart

                if (is_ajax() && array_key_exists('post_data', $_POST)) { // request came from checkout

                    foreach (explode("&", $_POST['post_data']) as $data) {

                        $accessorial = explode("=",$data)[0];

                        if ( in_array($accessorial, $this->accessorials) ) {

                            $accessorials[] = $accessorial;

                        }

                    }

                }
            }

            return $accessorials;
        }


        /**
         * Determine if cart item item ships freight. Abort of checks fail in this order:
         *  1. passes WC_Product needs_shipping check (catches virtual products)
         *  2. has a weight, determined by checking WC_Product get_weight
         *  3. has a shipping class, determined by checking WC_Product get_shipping_class
         *  4. check against method country availability
         *
         * @param WC_Product $item
         *
         * @return bool
         */
        function item_can_ship_freight( $item ) {

            $item_can_ship_freight = true;

            // set item id
            $item_id = intval($item['variation_id'])?:$item['product_id'];

            if ( ! $item['data']->needs_shipping() ) {

                $message = sprintf(__('Product #%d: %s is virtual. Skipping.', 'wc_rlc'), $item_id, $item['data']->post->post_title);
                if ( function_exists('wc_add_notice') && wc_rlc_isDebugModeEnabled() ) {
                    wc_add_notice($message);
                }

                if ( wc_rlc_isDebugModeEnabled() )
                    wc_rlc_logMessage($message);

                $item_can_ship_freight = false;
            }

            if ( ! $item['data']->get_weight() ) {

                $message = sprintf(__('Product #%d: %s is missing weight. Skipping.', 'wc_rlc'), $item_id, $item['data']->post->post_title);

                if ( function_exists('wc_add_notice') && wc_rlc_isDebugModeEnabled() ) {
                    wc_add_notice($message);
                }

                if ( wc_rlc_isDebugModeEnabled() )
                    wc_rlc_logMessage($message);

                $item_can_ship_freight = false;
            }

            // @TODO: Validate NMFC shipping class
            if ( ! floatval($item['data']->get_shipping_class()) && ! in_array($this->get_option('force_shipping_class'), wc_rlc_get_shipping_classes()) ) {

                $message = sprintf(__('Product #%d: %s is missing shipping class. Skipping.', 'wc_rlc'), $item['data']->id, $item['data']->post->post_title);

                if ( function_exists('wc_add_notice') && wc_rlc_isDebugModeEnabled()) {
                    wc_add_notice($message);
                }
                if ( wc_rlc_isDebugModeEnabled() )
                    wc_rlc_logMessage($message);

                $item_can_ship_freight = false;
            }

            return $item_can_ship_freight;
        }

        /**
         * @return bool
         */
        public function is_destination_state_valid()
        {
            // exclusion not enabled
            if ( 'all' == $this->get_option('us_state_exclusion') )
                return true;

            // can't determine, don't know where we're going yet
            if ( ! is_object( WC()->customer ) )
                return true;

            $valid = true;
            if( in_array( WC()->customer->get_shipping_state(), $this->get_option('excluded_states') ) )
            {
                $valid = false;
                wc_rlc_logMessage('Removing RLC due to state exclusion.');
            }

            return $valid;
        }

        public function package_ships_freight($package)
        {

            if ( ! $this->package_items_have_weights($package) )
                return false;

            if ( ! $this->package_items_have_shipping_classes($package) )
                return false;

            if ( ! $this->is_destination_country_valid() )
                return false;

            if ( ! $this->is_destination_state_valid() )
                return false;

            if ( $this->is_package_forced_freight($package) )
                return true;

            //Force freight threshold is checked within the min/max checking methods
            if ( ! $this->does_package_exceed_minimum_weight($package) )
                return false;

            if ( $this->does_package_exceed_maxmimum_weight($package) )
                return false;

            return true;
        }

        /**
         * Loop through package items and return true if each has a weight, skip if virtual or external
         *
         * @param $package
         * @return bool
         */
        public function package_items_have_weights($package)
        {
            foreach ( $package['contents'] as $item )
            {
                if ( ! wc_rlc_item_needs_shipping($item['data']) ) {
                    continue;
                }

                if ( ! wc_rlc_item_has_weight($item['data']) ) {
                    return false;
                }
            }

            return true;
        }

        /**
         * Loop through package items and return true if each has a shipping class
         *
         * @param $package
         * @return bool
         */
        public function package_items_have_shipping_classes($package)
        {
            foreach ( $package['contents'] as $item ) {

                if ( ! wc_rlc_item_needs_shipping($item['data']) ) {
                    continue;
                }

                if ( ! wc_rlc_item_has_shipping_class($item['data']) ) {
                    return false;
                }
            }

            return true;
        }

        public function is_package_forced_freight($package)
        {
            foreach ( $package['contents'] as $item )
            {
                if ( $this->is_item_forced_freight($item['data']) )
                {
                    return true;
                }
            }
            return false;
        }

        /**
         * returns TRUE if
         * @param WC_Product $item
         * @return Boolean
         */
        public function is_item_forced_freight(WC_Product $item)
        {

            return wc_rlc_isMustShipFreightEnabled() && intval(get_post_meta($item->get_id(), '_force_freight', true));
        }


        /**
         * returns true if package weight exceeds minimum freight weight, or minimum freight weight disabled
         *
         * @param $package
         * @return bool
         */
        public function does_package_exceed_minimum_weight($package)
        {

            if ( ! $min_freight_weight = intval($this->get_option('minimum_freight_weight')) )
                return true;

            if (($threshold = intval(wc_rlc_get_forced_freight_weight())) > 0 && $threshold <= $min_freight_weight )
                $min_freight_weight = $threshold;

            $weight = 0;
            foreach ( $package['contents'] as $item ) {

                if ( ! wc_rlc_item_needs_shipping($item['data']) ) {
                    continue;
                }

                // cart weight
                $weight += intval($item['quantity']) * intval($item['data']->get_weight());
            }



            return $weight >= $min_freight_weight;

        }

        /**
         * returns true if package weight exceeds maximum freight weight
         *
         * @param $package
         * @return bool
         */
        public function does_package_exceed_maxmimum_weight($package)
        {

            if ( ! $max_freight_weight = intval($this->maxPackageWeight) )
                return true;

            $weight = 0;
            foreach ( $package['contents'] as $item ) {

                if ( ! wc_rlc_item_needs_shipping($item['data']) ) {
                    continue;
                }

                // cart weight
                $weight += intval($item['quantity']) * intval($item['data']->get_weight());
            }

            return $weight >= $max_freight_weight;

        }

        /**
         * @return bool
         */
        public function is_destination_country_valid()
        {
            return is_object( WC()->customer ) && ( ! $this->get_option('countries') || in_array( WC()->customer->get_shipping_country(), $this->get_option('countries') ) );
        }




        public function add_package_items_to_request($package, &$request)
        {
            $contents = $package['contents'];

            foreach ( $contents as $key => $item )
            {
                $id = intval($item['variation_id'])?$item['variation_id']:$item['product_id'];
                $_pf = new WC_Product_Factory();
                $product = $_pf->get_product($id);

                $request['items'][$key] = array(
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'quantity' => $item['quantity'],
                    'origin' => array_key_exists('origin', $package)?$package['origin']:null,
                    'class' => wc_rlc_get_item_shipping_class($id, $package, $this),
                    'weight' => $product->get_weight(),
                    'is_hazmat' => wc_rlc_is_product_hazmat($id),
                    'length' => $product->get_length(),
                    'width' => $product->get_width(),
                    'height'=> $product->get_height()
                );
            }
        }


        private function show_service_days_if_enabled($service_days) {
            return wc_rlc_is_service_days_enabled($this)? ' - ' . $service_days.' day(s)':'';
        }

    }


endif;


/**
 * Returns the main instance of WooCommerce_Shipping_RLC to prevent the need to use globals.
 *
 * @return WooCommerce_Shipping_RLC
 */
function WC_Shipping_RLC() {
    return WC_Shipping_RLC::instance();
}

// Global for backwards compatibility.
$GLOBALS['WC_Shipping_RLC'] = WC_Shipping_RLC();