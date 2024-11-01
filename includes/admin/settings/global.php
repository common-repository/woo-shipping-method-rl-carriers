<?php


$settings = array(
	'B2B Tools Account' => array(
		'wc_rlc_api_key_prod' => array(
			'title' => __('Production API Key', 'wc_rlc'),
			'type' => 'text',
			'description' => __('Enter the key for your B2B tools <strong>production</strong> account.', 'wc_rlc'),
			'default' => '',
			'placeholder' => 'API key for R+L Carriers B2B Tools'
		),
 		/* 'wc_rlc_sandbox_mode' => array(
			'title' => __('Sandbox Mode', 'wc_rlc'),
			'label' => __('Enable sandbox mode', 'wc_rlc'),
			'type' => 'checkbox',
			'default' => 0,
			'description' => __('Enable sandbox mode to use the sandbox testing API endpoints.', 'wc_rlc') 
		),  */
		'wc_rlc_debug_mode' => array(
			'title' => __('Debug Mode', 'wc_rlc'),
			'label' => __('Enable debug mode', 'wc_rlc'),
			'type' => 'checkbox',
			'default' => 0,
			'description' => __('Enable debug mode to show debugging information on your cart/checkout.', 'wc_rlc')
		),
	),
	'Rates &amp; Packing' => array(
		'wc_rlc_must_ship_freight' => array(
			'title' => __('Enable R+L Per Product Option', 'wc_rlc'),
			'label' => __('Enable individual products to ship as freight at any weight', 'wc_rlc'),
			'type' => 'checkbox',
			'default' => 0,
			'description' => __('Activates a flag on Product edit screens that enables R+L shipping method regardless of weight.', 'wc_rlc')
		),
		'wc_rlc_freight_weight_threshold' => array(
			'title' => __('Forced Freight Threshold', 'wc_rlc'),
			'label' => __('Force R+L Freight based on Cart Weight', 'wc_rlc'),
			'type' => 'text',
			'default' => '',
			'description' => __('Remove all other shipping methods above a certain cart weight. Leave blank or 0 to disable.', 'wc_rlc')
		),
/* 		'wc_rlc_packing_method' => array(
			'title' => __('Packing Method', 'wc_rlc'),
			'type' => 'select',
			'default' => 'per_cart',
			'class' => 'packing_method',
			'options' => array(
				'per_cart' => __('Rates determined by weight & class of shipment package', 'wc_rlc'),
				'palletized' => __('Items will be loaded on shipping pallets', 'wc_rlc'),
			),
		), */
		/* 'wc_rlc_pallet_type' => array(
			'title' => __('Pallet Type', 'wc_rlc'),
			'type' => 'select',
			'class' => 'chosen_select',
			'css' => 'width: 450px;',
			'default' => '',
			'options' => WC_RLC()->get_pallet_types(),
			'description' => __('Choose pallet to use with pallet requests.', 'wc_rlc'),
		), */
		'wc_rlc_pallet_limit' => array(
			'title' => __('Pallet Weight Limit', 'wc_rlc'),
			'type' => 'text',
			'description' => __('Maximum weight of items on a pallet.', 'wc_rlc'),
			'default'   => wc_rlc_get_pallet_limit()
		),
	),
	'Special Shipping Services' => array(
		'wc_rlc_special_shipping_services' => array(
			'title' => __('Special Shipping Services', 'wc_rlc'),
			'type' => 'checkbox',
			'label' => __('Special Shipping Services', 'wc_rlc'),
			'description' => __('Check to display the Special Shipping Services selection at checkout.', 'wc_rlc'),
			'default' => 1,
		),
		'wc_rlc_dest_accessorial_toggle' => array(
			'title' => __('Toggle Destination Accessorials ', 'wc_rlc'),
			'type' => 'title',
			'description' => __('Disable checkout selection of destination accessorials.', 'wc_rlc'),
		),
		'wc_rlc_show_limited_delivery' => array(
			'title' => __('', 'wc_rlc'),
			'type' => 'checkbox',
			'label' => __('Limited Access Delivery', 'wc_rlc'),
			'default' => 1,
			'value' => 1
		),
		'wc_rlc_show_inside_delivery' => array(
			'title' => __('', 'wc_rlc'),
			'type' => 'checkbox',
			'label' => __('Inside Delivery', 'wc_rlc'),
			'default' => 1,
			'value' => 1
		),
		'wc_rlc_show_destination_liftgate' => array(
			'title' => __('', 'wc_rlc'),
			'type' => 'checkbox',
			'label' => __('Liftgate Needed for Delivery', 'wc_rlc'),
			'default' => 1,
			'value' => 1
		),
		'wc_rlc_show_delivery_notification' => array(
			'title' => __('', 'wc_rlc'),
			'type' => 'checkbox',
			'label' => __('Delivery Notification', 'wc_rlc'),
			'default' => 0,
			'value' => 1
		),
		'wc_rlc_override_destination_accessorials' => array(
			'title' => __('Overrides Destination Accessorials', 'wc_rlc'),
			'type' => 'checkbox',
			'label' => __('Override Destination Accessorials', 'wc_rlc'),
			'description' => __('Any destination accessorial options selected below will be mandatory for all R+L shipping quotes.', 'wc_rlc'),
			'default' => 0,
		),
		'wc_rlc_override_limited_delivery' => array(
			'title' => __('', 'wc_rlc'),
			'type' => 'checkbox',
			'label' => __('Limited Access Delivery', 'wc_rlc'),
			'description' => __('Limited Access Delivery', 'wc_rlc'),
			'default' => 0
		),
		'wc_rlc_override_inside_delivery' => array(
			'title' => __('', 'wc_rlc'),
			'type' => 'checkbox',
			'label' => __('Inside Delivery', 'wc_rlc'),
			'description' => __('Inside Delivery', 'wc_rlc'),
			'default' => 0
		),
		'wc_rlc_override_destination_liftgate' => array(
			'title' => __('', 'wc_rlc'),
			'type' => 'checkbox',
			'label' => __('Liftgate Needed for Delivery', 'wc_rlc'),
			'description' => __('Liftgate Needed for Delivery', 'wc_rlc'),
			'default' => 0
		),
		'wc_rlc_override_delivery_notification' => array(
			'title' => __('', 'wc_rlc'),
			'type' => 'checkbox',
			'label' => __('Delivery Notification', 'wc_rlc'),
			'description' => __('Delivery Notification', 'wc_rlc'),
			'default' => 0
		),
	)
);

return $settings;