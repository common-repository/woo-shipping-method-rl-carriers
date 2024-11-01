<?php

class PalletData {

}


/**
 * Helper function to return an input field based off of passed arguments
 *
 * @version 1.2.1
 *
 * @param $args array of arguments to pass for creating an input field.
 */
function wc_rlc_return_input_field($args){
	// Set defaults for input fields
	$defaults = array(
			'id'=>'DEFAULT_ID',
			'checked'=>0,
			'type'=>'text',
			'value'=>'',
			'default'=>'',
			'placeholder'=>'',
			'description'=>'',
			'min'=>'0',
			'max'=>'',
			'rows'=>10,
			'label' => '',
			'options' => array(),
			'selected' => array(),
            'custom_attributes' => array()
	);

	// Merge user supplied into defaults
	$args = array_merge($defaults,$args);

	$id = $args['id'];
	$class = (!empty($args['class'])) ? $args['class'] : 'regular-text ' . $id ;
	$type = $args['type'];
	$value = ($temp_value = $args['value']) ? $temp_value : $args['default'] ;
	$placeholder = $args['placeholder'];
	$rows = $args['rows'];
	$description = $args['description'];
	$min = $args['min'];
	$max = $args['max'];
	$checked = $args['checked'];
	$options = $args['options'];
	$selected = $args['selected'];
	$custom_attributes = $args['custom_attributes'];

	switch($type){
		case 'text':
			?><input
            type="text"
            class="<?php echo $class; ?>"
            id="<?php echo $id; ?>"
            name="<?php echo $id;  ?>"
            value="<?php echo $value; ?>"
            placeholder="<?php echo $placeholder; ?>"
            <?php foreach($custom_attributes as $attr => $val): echo $attr.'="'.$val.'" '; endforeach;?>
            /><?php
			break;
		case 'email':
			?><input type="email" class="<?php echo $class; ?>" id="<?php echo $id; ?>" name="<?php echo $id;  ?>"  value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>"/><?php
			break;
		case 'textarea':
			?><textarea class="<?php echo $class; ?>" rows="<?php echo $rows; ?>" cols="50" name="<?php echo $id; ?>" id="<?php echo $id; ?>" placeholder="<?php echo $placeholder; ?>"><?php echo $value; ?></textarea><?php
			break;
		case 'submit':
			?><input type="submit" id="<?php echo $id; ?>" class="<?php echo $class; ?>" value="<?php echo $value; ?>"/><?php
			break;
		case 'checkbox':
			?><input id="<?php echo $id; ?>" name="<?php echo $id; ?>" type="checkbox" value="<?php echo (!empty($value)) ? $value : 1 ; ?>" <?php checked($value,$checked); ?>/><?php
			break;
		case 'number':
			?><input type="number" class=" <?php echo $class; ?>" id="<?php echo $id; ?>" name="<?php echo $id;  ?>"  value="<?php echo $value; ?>" placeholder="<?php echo $placeholder; ?>" min="<?php echo $min; ?>" <?php if(!empty($max)):?> max="<?php echo $max; ?>" <?php endif; ?>  /><?php
			break;
		case 'select':
			?><select class="<?php echo $class?>" id="<?php echo $id?>" name="<?php echo $id?>"><?php
				foreach ( $options as $value => $name ) {
				?> <option value="<?php echo $value?>" <?php echo in_array($value, $selected)?'selected="selected"':''?>><?php echo $name?></option> <?php
				}
			?></select><?php
			break;
		case 'hidden':
			?><input type="hidden" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php echo $value; ?>" /><?php
			break;
	}

	if ( strlen($args['label']) )
	{
		?><label for="<?php echo $id?>"><?php echo $args['label']?></label><?php
	}

	if(!empty($description)){
		?><p class="description" style="display: inline"><?php echo $description; ?></p><?php
	}
}

function wc_rlc_get_setting_label_html($title, $for, $classes = 'setting-label') {

	return '<label for="'.$for.'" class="'.$classes.'">'.$title.'</label>';

}

/**
 * @param string $message
 * @param string $type
 */
function wc_rlc_logMessage($message, $type = 'message')
{
	$datetime = new DateTime();

	$directory = wc_rlc_plugin_path() . DIRECTORY_SEPARATOR . 'logs';
		if ( ! is_dir( $directory ) ) mkdir( $directory );

	file_put_contents( $directory . DIRECTORY_SEPARATOR . 'rlc.log', '[' . $datetime->format( 'Y-m-d H:i:s' ) . '] (' . $type . ') ' . $message . "\n", FILE_APPEND );
}

/**
 * @return string
 */
function wc_rlc_plugin_path() {
	return dirname( dirname( __FILE__ ) );
}


/**
 * @return mixed
 */
function wc_rlc_get_states() {
    $countries_obj = new WC_Countries();
    $us_states = $countries_obj->get_states('US');
    $ca_states = $countries_obj->get_states('CA');

	$return = [];

	if ( is_array($us_states) )
	    $return = $us_states;

	if ( is_array($ca_states) )
	    $return = array_merge($return, $ca_states);
	
	return $return;
}

/**
 * @param $package
 * @return bool
 */
function wc_rlc_is_destination_postcode_set($package)
{
	return isset($package['destination']['postcode']) && strlen($package['destination']['postcode']);
}


/**
 * @param WC_Product $item
 * @param bool $debug
 * @return bool
 */
function wc_rlc_item_needs_shipping($item, $debug = false)
{
	if (!$item->needs_shipping()) {
		if ($debug) {
			$message = sprintf(__('Product # is virtual. Skipping.', 'wc_rlc'), $item->get_id(), $item->get_title());
			wc_add_notice($message);
			wc_rlc_logMessage($message, 'message');
		}
		return false;
	}

	return true;
}


/**
 * @param WC_Product $item
 * @param bool $debug
 * @return bool
 */
function wc_rlc_item_has_weight(WC_Product $item, $debug = false)
{
	if (! $item->get_weight()) {
		if ($debug) {
			$message = sprintf(__('Product #%d %s is missing weight. Aborting R+L rate quote request.', 'wc_rlc'), $item->get_id(), $item->get_title());
			wc_add_notice($message);
			wc_rlc_logMessage($message, 'message');
		}
		return false;
	}
	return true;
}

/**
 * Return array of shipping class term names index by term name
 * @return array
 */
function wc_rlc_get_shipping_classes() {
	$classes = array();

	foreach (get_terms(array( 'taxonomy' => 'product_shipping_class', 'hide_empty' => false)) as $class) {
		$classes[$class->name] = $class->name;
	}

	return $classes;
}

/**
 * Return array of shipping class term slugs index by term name
 * @return array
 */
function wc_rlc_get_shipping_class_slugs() {
	$classes = array();

	foreach (get_terms(array( 'taxonomy' => 'product_shipping_class', 'hide_empty' => false)) as $class) {
		$classes[$class->slug] = $class->slug;
	}

	return $classes;
}

/**
 * @return string
 */
function wc_rlc_plugin_uri() {
	return trailingslashit( WP_PLUGIN_URL ) . basename( dirname( dirname( __FILE__ ) ) );
}


function wc_rlc_dollarsToDecimal($str)
{
	return (float) preg_replace('/[^0-9.]*/','',$str);
}

function wc_rlc_asDollars($value) {
	return '$' . number_format($value, 2);
}

/**
 * Regex replace
 * @param $str
 * @return int
 */
function wc_rlc_stringToFloat($str)
{
	return preg_replace("/([^0-9\\.])/i", "", $str);
}

/**
 * @return string
 */
function wc_rlc_get_api_key()
{
	return get_option('wc_rlc_api_key_prod');
}

function wc_rlc_get_forced_freight_weight() {
	return get_option('wc_rlc_freight_weight_threshold');
}

function wc_rlc_get_environment() {
	return get_option('wc_rlc_environment');
}

function wc_rlc_is_sandbox_mode_enabled() {
	return get_option('wc_rlc_sandbox_mode');
}

/**
 * Convert country code into expected format
 * @param $countryCode
 *
 * @return string
 */
function wc_rlc_get_country_code($countryCode)
{

	switch (strtolower($countryCode)) {
		case 'usa':
		case 'us':
			$countryCode = 'USA';
			break;

		case 'ca':
			$countryCode = 'CAN';
			break;

		case 'me':
			$countryCode = 'MEX';
			break;
		
		case 'pr':
			$countryCode = 'PRI';
			break;

		default:
			$countryCode = 'International';
	}

	return $countryCode;
}


/**
 * @return integer
 */
function wc_rlc_get_pallet_limit() {
	return get_option('wc_rlc_pallet_limit');
}

/**
 * @return bool
 */
function wc_rlc_get_pallet_type($package, $origin) {
	$apiKey = get_option('wc_rlc_api_key_prod');

	$originCity = $origin["originCity"];
	$originZip = $origin["originZip"];
	$destCity = $package["destination"]["city"];
	$destZip = $package["destination"]["postcode"];


	$zipCodeData = 'OriginCity='.urlencode($originCity).'&OriginZip='.$originZip.'&DestinationCity='.urlencode($destCity).'&DestinationZip='.$destZip;
	$urlDestination = 'https://api.rlc.com/RateQuote/GetPalletTypesByPoints?'.$zipCodeData;
	

	$curl = curl_init();
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
			curl_setopt($curl, CURLOPT_URL, $urlDestination);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'apiKey: '.$apiKey,
				'Accept: */*'
                ]);

            $result     = curl_exec($curl);
            $resultInfo = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	if ($result == false) {
		$errorData = curl_errno($curl);
	}

	if ($resultInfo == 200) {
		$result = json_decode($result);
		$codeData = reset($result->PalletTypes);
		$type = $codeData->Code;
		return $type;
	} else {
		wc_rlc_logMessage('Cannot Retrieve Pallet types.  Please check info '.$originCity.' '.$originZip.' '.$destCity.' '.$destZip);
	}


	//return (($type=get_option('wc_rlc_pallet_type')) == 'choose')?'0001':$type;
}

/** Plugin options getters */
/**
 * @return bool
 */
function wc_rlc_isOverrideDestinationAccessorialsEnabled() {
	return get_option('wc_rlc_override_destination_accessorials');
}

function wc_rlc_isOverrideLimitedAccessDeliveryEnabled() {
	return get_option('wc_rlc_override_limited_delivery');
}

function wc_rlc_isOverrideInsideDeliveryEnabled() {
	return get_option('wc_rlc_override_inside_delivery');
}

function wc_rlc_isOverrideLiftgateDeliveryEnabled() {
	return get_option('wc_rlc_override_destination_liftgate');
}

function wc_rlc_isOverrideDeliveryNotificationEnabled() {
	return get_option('wc_rlc_override_delivery_notification');
}

function wc_rlc_isSpecialShippingServicesEnabled() {
	return get_option('wc_rlc_special_shipping_services');
}

/**
 * Returns true if the Force Freight Shipping per Product is enabled
 * @return bool
 */
function wc_rlc_isMustShipFreightEnabled(){
	return get_option('wc_rlc_must_ship_freight');
}

/**
 * @return bool
 */
function wc_rlc_isSplitPackagingEnabled(){
	return get_option('wc_rlc_split_packaging');
}

function wc_rlc_isDebugModeEnabled() {
	return get_option('wc_rlc_debug_mode');
}

function wc_rlc_is_itemized_rates_enabled() {
	return get_option('wc_rlc_itemized_rates');
}

function wc_rlc_isManageStockEnabled() {
	return 'yes' == get_option( 'woocommerce_manage_stock' );
}

/**
 * @param WC_Product $item
 * @return bool
 */
function wc_rlc_item_has_shipping_class(WC_Product $item){
	if ( ! in_array($item->get_shipping_class(), wc_rlc_get_shipping_class_slugs() )) {
		if (wc_rlc_isDebugModeEnabled()) {
			$message = sprintf(__('Product #%d: %s is missing shipping class. Skipping.', 'wc_rlc'), $item->get_id(), $item->get_title());
			wc_add_notice($message);
			wc_rlc_logMessage($message);
		}
		return false;
	}
	return true;
}

/**
 * Displays accessorial fees on checkout
 *
 */
function wc_rlc_outputItemizedRatesHTML() {
	if ( sizeof( $breakdown = $this->getItemizedCharges() ) ) {
		foreach ( $breakdown as $service => $charge ) {
			?>
			<tr>
				<td style="font-weight: normal">
					<?php echo $service?>
				</td>
				<td style="font-weight: normal">
					<span class="amount"><?php echo $charge?></span>
				</td>
			</tr>
			<?php
		}
	}
}

function wc_rlc_getItemizedCharges()
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

function wc_rlc_get_shipping_zones_for_select() {

	$options = array();

	global $wpdb;

	$rest_of_world = WC_Shipping_Zones::get_zone_by('zone_id')->get_data();

	$instance_id = $wpdb->get_var( $wpdb->prepare( "SELECT instance_id FROM {$wpdb->prefix}woocommerce_shipping_zone_methods as methods WHERE methods.zone_id = %d AND methods.method_id = %s LIMIT 1;", $rest_of_world['zone_id'], 'rlc' ) );

	if ( intval($instance_id) )
		$options[$instance_id] = $rest_of_world['zone_name'];

	foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
		foreach ( $zone['shipping_methods'] as $method ) {
			if ( $method->id == 'rlc' )	{
				$options[$method->instance_id] = $zone['zone_name'];
			}
		}

	}

	return $options;
}

function wc_rlc_show_delivery_notification() {
	return get_option('wc_rlc_show_delivery_notification');
}

function wc_rlc_show_limited_delivery() {
	return get_option('wc_rlc_show_limited_delivery');
}

function wc_rlc_show_inside_delivery() {
	return get_option('wc_rlc_show_inside_delivery');
}

function wc_rlc_show_destination_liftgate() {
	return get_option('wc_rlc_show_destination_liftgate');
}

function wc_rlc_is_product_hazmat($id) {
	return intval(get_post_meta($id, '_is_hazmat', true))?true:false;
}

function wc_rlc_get_package_weight($package) {
	$weight = 0;

	foreach ( $package['contents'] as $item )
	{
		$weight += floatval($item['data']->weight) * $item['quantity'];
	}

	return $weight;
}

function wc_rlc_product_get_shipping_class($id) {
	$_pf = new WC_Product_Factory();
	$product = $_pf->get_product($id);
	return str_replace('-', '.', $product->get_shipping_class());
}

function wc_rlc_get_item_shipping_class( $id, $package, $instance ) {
	if ( strlen($forced_class = $instance->get_option('force_shipping_class')) )
		return $forced_class;

	if ( $instance->get_option('shipHighestClass') == 'yes')
		return wc_rlc_package_get_highest_shipping_class($package);

	return wc_rlc_product_get_shipping_class($id);
}

function wc_rlc_package_get_highest_shipping_class($package) {
	$highest_class = $higher_class = 0;
	if ( array_key_exists('contents', $package) )
    {
        $package = $package['contents'];
    }
	foreach ($package as $item)
	{
		$product_id = intval($item['variation_id'])?$item['variation_id']:$item['product_id'];
		if ( floatval( $higher_class = wc_rlc_product_get_shipping_class($product_id) ) > floatval($highest_class) ) {
			$highest_class = $higher_class;
		}
	}

	return $highest_class;
}

function wc_rlc_order_get_highest_shipping_class($order) {
	$highest_class = $higher_class = 0;
	foreach ( $order->get_items() as $key => $item )
	{
		$product_id = intval($item['variation_id'])?$item['variation_id']:$item['product_id'];
		if ( floatval( $higher_class = wc_rlc_product_get_shipping_class($product_id) ) > floatval($highest_class) ) {
			$highest_class = $higher_class;
		}
	}

	return $highest_class;
}

function wc_rlc_get_packing_method() {
	return get_option('wc_rlc_packing_method');
}

function wc_rlc_get_request_quote_type($package) {
	return in_array($package['destination']['country'], array("US", "CA")) ? in_array($package['destination']['state'], array('HI', 'AK'))?'AlaskaHawaii':'Domestic':'International';
}

function wc_rlc_get_package_value( $package ) {
	$value = 0;
	foreach ( $package['contents'] as $item )
	{
		$value += $item['line_total'] * $item['quantity'];
	}

	return $value;
}

function wc_rlc_is_service_days_enabled($instance) {
    return $instance->get_option('show_service_days') == 'yes';
}

function wc_rlc_error_connect_notice() {
	
	if (!defined( 'DOING_AJAX' ) || (defined( 'DOING_AJAX' ) && !DOING_AJAX )){
		$class = 'notice notice-error';
		$message = __( 'There was an error connecting to RLC API', 'sample-text-domain' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
}

function wc_rlc_error_rq_notice() {
	
	if (!defined( 'DOING_AJAX' ) || (defined( 'DOING_AJAX' ) && !DOING_AJAX )){
		$class = 'notice notice-error';
		$message = __( 'Unable to get Rate Quote', 'sample-text-domain' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
}
