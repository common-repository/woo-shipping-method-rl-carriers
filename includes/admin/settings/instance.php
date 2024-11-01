<?php
/**
 * Created by PhpStorm.
 * User: Clif.Molina
 * Date: 10/5/2016
 * Time: 11:14 AM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = array(
	'title' => array(
		'title' => __('Method Title', 'wc_rlc'),
		'type' => 'text',
		'description' => __('This controls the title which the user sees during checkout.', 'wc_rlc'),
		'default' => __('RLC', 'wc_rlc')
	),
	'originCity' => array(
		'title' => __('Origin City', 'wc_rlc'),
		'type' => 'text',
		'description' => __('Enter the city <B>from which</B> orders will be shipped.', 'wc_rlc'),
		'default' => ''
	),
	'originState' => array(
		'title' => __('Origin State/Province', 'wc_rlc'),
		'type' => 'select',
		'class' => 'chosen_select',
		'description' => __('Enter the state <B>from which</B> orders will be shipped.', 'wc_rlc'),
		'options' => array_merge(array('' => 'Choose...'), wc_rlc_get_states() )
	),
	'origin' => array(
		'title' => __('Origin Zip', 'wc_rlc'),
		'type' => 'text',
		'description' => __('Enter the ZIP code <B>from which</B> orders will be shipped.', 'wc_rlc'),
		'default' => '',
		'custom_attributes'=> array(
			'required' => '1',
			'pattern' => '^(\d{5}(-\d{4})?|[A-CEGHJ-NPRSTVXY]\d[A-CEGHJ-NPRSTV-Z] ?\d[A-CEGHJ-NPRSTV-Z]\d)$'
		)
	),
	'originCountry' => array(
		'title' => __('Origin Country', 'wc_rlc'),
		'type' => 'select',
		'class' => 'chosen_select',
		'description' => __('Enter the country <B>from which</B> orders will be shipped.', 'wc_rlc'),
		'options' => array(''     => 'Choose...', 'US' => 'United States', 'CA' => 'Canada'),
		'default' => 'US'
	),
	'accessorials' => array(
		'title' => __('Origin Accessorial Options', 'wc_rlc'),
		'type'  => 'title',
		'description' => __('In order to provide your customers with accurate shipping rates, the options selected below will be applied to R+L Carriers rate quotes for all purchases from this store. <strong>For single origin store setups only.</strong>', 'wc_rlc'),
	),
	'InsidePickup' => array(
		'title' => __('', 'wc_rlc'),
		'type' => 'checkbox',
		'value' => 1,
		'label' => __('Inside Pickup', 'wc_rlc'),
		'description' => __('Select this option if you need the driver to go inside (beyond the unloading area described in <a href="http://www2.rlcarriers.com/freight/shipping-documents/rules-tariff" target="_blank">Item 750 of the Rules Tariff</A>) to pickup your shipments. Additional fees apply for this service.', 'wc_rlc'),
		'default' => '0'
	),
	'LimitedAccessPickup' => array(
		'title' => __('', 'wc_rlc'),
		'type' => 'checkbox',
		'value' => 1,
		'label' => __('Limited Pickup', 'wc_rlc'),
		'description' => __('Select this option if you are shipping from non-commercial, residential and/or private locations. For more see <a href="http://www2.rlcarriers.com/freight/shipping-documents/rules-tariff" target="_blank">Rules Tariff #753</A>. Additional fees apply for this service.', 'wc_rlc'),
		'default' => '0'
	),
	'OriginLiftgate' => array(
		'title' => __('', 'wc_rlc'),
		'type' => 'checkbox',
		'value' => 1,
		'label' => __('Origin Liftgate', 'wc_rlc'),
		'description' => __('Will a lift-gate be required to load items onto truck for delivery? Please select this option if no loading dock is available at the pickup location. A liftgate is a motorized platform on the back of some trucks that can lift item from gound level into the truck.', 'wc_rlc'),
		'default' => 0
	),
	'LimitedAccessPickup' => array(
		'title' => __('', 'wc_rlc'),
		'type' => 'checkbox',
		'value' => 1,
		'label' => __('Limited Access Pickup', 'wc_rlc'),
		'description' => __('Select this option if you are shipping from non-commercial, residential and/or private locations. For more see <a href="http://www2.rlcarriers.com/freight/shipping-documents/rules-tariff" target="_blank">Rules Tariff #753</A>. Additional fees apply for this service.', 'wc_rlc'),
		'default' => 0
	),
	'countries' => array(
		'title' => __('Specific Countries', 'wc_rlc'),
		'type' => 'multiselect',
		'class' => 'chosen_select',
		'css' => 'width: 450px;',
		'description' => __('Hold Ctrl to select multiple', 'wc_rlc'),
		'default' => '',
		'options' => array('US' => 'United States', 'CA' => 'Canada', 'PR' => 'Puerto Rico'),
	),
	'us_state_exclusion' => array(
		'title'   => __('State/Province Exclusion', 'wc_rlc'),
		'type'  =>  'select',
		'default'   => 'all',
		'class' =>  'availability',
		'options'   =>  array(
			'all'   =>  __('Allow All States', 'wc_rlc' ),
			'specific'  =>  __('Exclude Specific States Chosen Below', 'wc_rlc'),
		),
	),
	'excluded_states' => array(
		'title' => __('Exclude these States', 'wc_rlc'),
		'type' => 'multiselect',
		'class' => 'chosen_select',
		'css' => 'width: 450px;',
		'default' => '',
		'options' => wc_rlc_get_states(),
	),
	'force_shipping_class' => array(
		'title' => __('Force NMFC Class', 'wc_rlc'),
		'label' => __('Force a shipping class for all products', 'wc_rlc'),
		'type' => 'select',
		'default' => '',
		'description' => __('Use this shipping class.', 'wc_rlc'),
		'options' => array_merge(array('' => 'Choose...'), wc_rlc_get_shipping_classes()),
	),
	'shipHighestClass' => array(
		'title'     =>  __('Highest Ship Class', 'wc_echo'),
		'label'     =>  __('Highest NMFC class in cart used for entire request.', 'wc_echo'),
		'type'      => 'checkbox',
		'default'   => '',
		'description'   => __('Use the highest item shipping class in a cart for entire order. - IN DEVELOPMENT'),
	),
	'minimum_freight_weight' => array(
		'title' => __('Minimum Freight Weight', 'wc_rlc'),
		'label' => __('Minimum weight for freight shipping', 'wc_rlc'),
		'type' => 'text',
		'default' => '150',
		'description' => __('Enter a minimum weight value for freight rates to appear. Can be overridden by forced freight threshold in general settings or in a product&#39;s shipping settings. Default: 150. Blank or 0 to disable.', 'wc_rlc')
	),
	'rates' => array(
		'title' => __('Rates and Services', 'wc_rlc'),
		'type' => 'title',
		'description' => __('The following settings determine the R+L Carriers shipping services and rates you offer your customers.', 'wc_rlc'),
	),
	'fallback' => array(
		'title' => __('Fallback', 'wc_rlc'),
		'type' => 'text',
		'description' => __('If RLC returns no matching rates, offer this amount for shipping so that the user can still checkout. Leave blank to disable.', 'wc_rlc'),
		'default' => ''
	),
	'minimum_freight_rate' => array(
		'title' => __('Minimum Freight Rate', 'wc_rlc'),
		'type' => 'text',
		'description' => __('Freight rates will always be at least this much after adjustments.', 'wc_rlc'),
		'default' => ''
	),
	'show_service_days' => array(
		'title' => __('Service Days', 'wc_rlc'),
		'type' => 'checkbox',
		'label' => __('Show Service Days in Cart/Checkout', 'wc_rlc'),
		'default' => 0,
		'value' => 1
	),
	'services' => array(
		'type' => 'services'
	),
);

return $settings;