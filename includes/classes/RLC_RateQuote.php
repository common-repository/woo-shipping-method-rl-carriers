<?php

class RateQuote {

}

class RLC_RateQuote
{

    /**
     * @var string - endpoint URL
     */
    private $endpoints = array(
        'production' => 'https://api.rlc.com/',
        'sandbox'    => 'https://api.rlc.com/'
    );


    public $kt_overDimArray = [];

    private $debug = false;

    public $id;

    public $quote_number;

    public $net_charge;

    public $accessorials;

    public $service_days;

    public $method;

    public $weight;

    public $items;

    public $charges;

    public $is_hazmat;

    public function __construct($id = null)
    {

        if ( $id )
        {
            $quote = $this->getQuote($id);

            $charges_factory = new RLC_RateQuote_Charge();

            $this->id = $quote->id;

            $this->shipment_id = $quote->shipment_id;

            $this->quote_number = $quote->quote_number;

            $this->net_charge = $quote->net_charge;

            $this->accessorials = json_decode($quote->accessorials);

            $this->service_days = $quote->service_days;

            $this->method = $quote->method;

            $this->weight = $quote->weight;

            $this->items = json_decode($quote->items, true);

            $this->charges = $charges_factory->getChargesFromQuote($this->id);

            $this->is_hazmat = $this->is_hazmat();

        }

        $this->debug = wc_rlc_isDebugModeEnabled();

	    $this->endpoints  = apply_filters( 'woocommerce_shipping_rlc_rate_quote_endpoints', $this->endpoints );

        $this->endpoint = wc_rlc_is_sandbox_mode_enabled()? $this->endpoints['sandbox'] : $this->endpoints['production'];


    }

    public function is_hazmat() {

        foreach ( $this->charges as $key => $charge ) {

            if ( $charge['type'] == 'HAZM' )
                return true;

        }

        return false;
    }

    /**
     * @param $shipment_id
     * @param $title
     * @param $quote_number
     * @param $net_charge
     * @param $method
     * @param $service_days
     * @param $weight
     * @param array $accessorials
     * @param array $items
     * @param null $origin_term_id
     * @return null|int
     */
    public function store($shipment_id, $title, $quote_number, $net_charge, $method, $service_days, $weight, $accessorials = array(), $items = array(), $origin_term_id = null )
    {
        global $wpdb;

        try {
            if ( intval($origin_term_id) )
            {
                //Get origin slug
	            $origin_meta_key = get_term($origin_term_id, 'origin')->slug . '_stock';
	            foreach ( $items as $item ) {
                    $id = intval($item['variation_id'])?$item['variation_id']:$item['product_id'];
                    $origin_meta = get_post_meta($id, $origin_meta_key, true);
                    $new_origin_stock = ((int)$origin_meta) - $item['quantity'];
                    if ($new_origin_stock < 0)
                     $new_origin_stock = 0;

                    update_post_meta($id, $origin_meta_key, $new_origin_stock);

	            }
                //update MO stock meta for origin
            }

            $data = array(
                'shipment_id' => $shipment_id,
                'origin_term_id' => $origin_term_id,
                'title' => $title,
                'items' => json_encode($items),
                'quote_number' => $quote_number,
                'net_charge' => wc_rlc_dollarsToDecimal($net_charge),
                'method' => $method,
                'service_days' => $service_days,
                'accessorials' => json_encode($accessorials),
                'weight'  => $weight,
                'created_at' => date('Y-m-d H:i:s')
            );

            $wpdb->insert($wpdb->prefix . 'woocommerce_shipping_rlc_quotes', $data);

            if ( $wpdb->last_error )
                wc_rlc_logMessage( 'Error inserting into woocommerce_shipping_rlc_quotes: ' . $wpdb->last_error );
        } catch (Exception $e) {
			
            wc_rlc_logMessage($e->getMessage(), 'error' );
        }


        return intval($wpdb->insert_id)?$wpdb->insert_id:null;
    }

    public function getSavedQuote($id)
    {
        $quote = false;

        try {

            global $wpdb;

            $result = $wpdb->get_results($wpdb->prepare(
                "
            SELECT * FROM " . $wpdb->prefix . "woocommerce_shipping_rlc_quotes
             WHERE id = %d
            ",
                $id
            ));

            if ($result[0]) $quote = $result[0];
        } catch (Exception $e) {
			
			wc_rlc_logMessage($e->getMessage(), 'error' );
           
        }

        return $quote;
    }

    public function getServiceLevels($request)
    {
        $dataRate = $this->doRateQuoteCall($request);
        if (!is_null($dataRate)) {
            return array(
                'levels'  => $dataRate->RateQuote->ServiceLevels,
                'charges' => $dataRate->RateQuote->Charges
            );
        } else { return array(); }
    }

    public function doRateQuoteCall($request)
    {
        //create $data variable will be used to return the result
        $data = null;
        $OverDimensions = [];
        $ocean = false;
        $CODAmount = 0;
        $Origin = new RLC_RateQuote_TravelPoint($request['originCity'], $request['originState'], $request['originZip'], $request['originCountry']);
        $Destination   = new RLC_RateQuote_TravelPoint($request['destinationCity'], $request['destinationState'], $request['destinationZip'], $request['destinationCountry']);
        $Pallets = [];

        if ($Destination->StateOrProvince === 'HI' or $Destination->StateOrProvince === 'AK' or $Destination->CountryCode === 'PRI')  { 
            $ocean = true;
        }

        if (array_key_exists('pallets', $request) && count($request['pallets'])) {  
            foreach ($request['pallets'] as $pallet) {
                if ($pallet['Code'] !== 'NONE') {
                    $Pallets  = [['Code'=>$pallet['Code'], 'Weight' =>$pallet['Weight'], 'Quantity'=>$pallet['Quantity']]];
                }
            }
        }

        $Items = [];

        if (array_key_exists('items', $request) && count($request['items'])) {
            $weight_unit    = get_option('woocommerce_weight_unit');
            $dimension_unit = get_option('woocommerce_dimension_unit');
            $itemCount      = 0;

            if ($ocean == true){

                foreach ($request['items'] as $key => $item) {
                    if ( $item['is_hazmat'] ) $this->is_hazmat = true;
    
                    $itemCount++;
                    
                    // convert weight to pounds
                    $itemWeightPounds = $item['weight'];
                    switch ($weight_unit) {
                        case 'oz':
                            $itemWeightPounds = floatval($item['weight']) * 0.0625;
                        break;
    
                        case 'kg':
                            $itemWeightPounds = floatval($item['weight']) * 2.20462;
                        break;
    
                        case 'g':
                            $itemWeightPounds = floatval($item['weight']) * 0.00220462;
                        break;
    
                        default:
                            $itemWeightPounds = $item['weight'];
                        break;
                    }
                    
                    // convert dimensions to inches
    
                    switch($dimension_unit){
                        case 'm':
                            $itemWidthInches = floatval($item['width']) * 39.3701;
                            $itemLengthInches = floatval($item['length']) * 39.3701;
                            $itemHeightInches = floatval($item['height']) * 39.3701;
                            break;
                        case 'cm':
                            $itemWidthInches = floatval($item['width']) * 0.393701;
                            $itemLengthInches = floatval($item['length']) * 0.393701;
                            $itemHeightInches = floatval($item['height']) * 0.393701;
                            break;
                        case 'mm':
                            $itemWidthInches = floatval($item['width']) * 0.0393701;
                            $itemLengthInches = floatval($item['length']) * 0.0393701;
                            $itemHeightInches = floatval($item['height']) * 0.0393701;
                            break;
                        case 'yd':
                            $itemWidthInches = floatval($item['width']) * 36;
                            $itemLengthInches = floatval($item['length']) * 36;
                            $itemHeightInches = floatval($item['height']) * 36;
                            break;
                        default:
                            $itemWidthInches = $item['width'];
                            $itemLengthInches = $item['length'];
                            $itemHeightInches = $item['height'];
                    }
    
                    if ($itemWidthInches > 96 || $itemLengthInches > 96 ) {
                        $maxInches = max($itemWidthInches,$itemLengthInches);
                        if (in_array($maxInches,$this->kt_overDimArray))
                            $this->kt_overDimArray[$maxInches]    += $item['quantity']++;
                            else
                                $this->kt_overDimArray[$maxInches] = $item['quantity'];
                    }

                    for ($k = 0; $k < $item['quantity']; $k++) {
                        $Items[] = array_filter(['Width'=>$itemWidthInches, 'Height'=>$itemHeightInches, 'Length'=>$itemLengthInches, 
                            'Class'=>$item['class'], 'Weight'=>(floatval($item['weight'])), 'Quantity'=>1]);
                    }
                                        
                                
                }
                //end foreach

            } else {
                foreach ($request['items'] as $key => $item) {
                    if ( $item['is_hazmat'] ) $this->is_hazmat = true;
    
                    $itemCount++;
                    
                    // convert weight to pounds
                    $itemWeightPounds = $item['weight'];
                    switch ($weight_unit) {
                        case 'oz':
                            $itemWeightPounds = floatval($item['weight']) * 0.0625;
                        break;
    
                        case 'kg':
                            $itemWeightPounds = floatval($item['weight']) * 2.20462;
                        break;
    
                        case 'g':
                            $itemWeightPounds = floatval($item['weight']) * 0.00220462;
                        break;
    
                        default:
                            $itemWeightPounds = $item['weight'];
                        break;
                    }
                    
                    // convert dimensions to inches
    
                    switch($dimension_unit){
                        case 'm':
                            $itemWidthInches = floatval($item['width']) * 39.3701;
                            $itemLengthInches = floatval($item['length']) * 39.3701;
                            $itemHeightInches = floatval($item['height']) * 39.3701;
                            break;
                        case 'cm':
                            $itemWidthInches = floatval($item['width']) * 0.393701;
                            $itemLengthInches = floatval($item['length']) * 0.393701;
                            $itemHeightInches = floatval($item['height']) * 0.393701;
                            break;
                        case 'mm':
                            $itemWidthInches = floatval($item['width']) * 0.0393701;
                            $itemLengthInches = floatval($item['length']) * 0.0393701;
                            $itemHeightInches = floatval($item['height']) * 0.0393701;
                            break;
                        case 'yd':
                            $itemWidthInches = floatval($item['width']) * 36;
                            $itemLengthInches = floatval($item['length']) * 36;
                            $itemHeightInches = floatval($item['height']) * 36;
                            break;
                        default:
                            $itemWidthInches = $item['width'];
                            $itemLengthInches = $item['length'];
                            $itemHeightInches = $item['height'];
                    }
    
                    if ($itemWidthInches > 96 || $itemLengthInches > 96 ) {
                        $maxInches = max($itemWidthInches,$itemLengthInches);
                        if (in_array($maxInches,$this->kt_overDimArray))
                            $this->kt_overDimArray[$maxInches]    += $item['quantity']++;
                            else
                                $this->kt_overDimArray[$maxInches] = $item['quantity'];
                    }
                    
                    $Items[] = array_filter(['Width'=>$itemWidthInches, 'Height'=>$itemHeightInches, 'Length'=>$itemLengthInches, 'Class'=>$item['class'], 'Weight'=>(floatval($item['weight'])*$item['quantity']), 'Quantity'=>$item['quantity']]);
                                
                }
                //end foreach

            }

        }

        $AdditionalServices = [];

        if ($this->is_hazmat === true) {
            $AdditionalServices[] = 'Hazmat';
        }

        if (!empty($this->kt_overDimArray) and $ocean == false) {
            $AdditionalServices[] = 'OverDimension';
        }

        if ($this->is_hazmat === true || !empty($request['accessorials'])) {

            if (empty($request['accessorials']) === false) {

                foreach ($request['accessorials'] as $accessorial) {
                    $AdditionalServices[] = $accessorial;
                }
            }
        }//end if
        if (empty($this->kt_overDimArray) === false  and $ocean == false) {           

            foreach ($this->kt_overDimArray as $key => $numItems) {
                 $OverDimensions[] = ['Inches'=>$key, 'Pieces'=>$numItems];
            }
        }

        $declaredValue = $request['value'];

        $RateQuoteRequest = new RLC_RateQuote_Request(
            $CODAmount,
            $Origin, 
            $Destination, 
            $Pallets,
            $Items,
            $AdditionalServices,
            $OverDimensions,
            $declaredValue
        );

        $rq = new Ratequote();

        $rq->RateQuote = $RateQuoteRequest;

        $RateQuote = json_encode($rq, JSON_PRETTY_PRINT);
        
        if (wc_rlc_isDebugModeEnabled()) {
            wc_add_notice('R+L REQUEST: <pre>'.$RateQuote.'</pre>');
            wc_rlc_logMessage('R+L REQUEST: '.$RateQuote);
        }

        try {
            $curl = curl_init();
            $urlSet = $this->endpoint.'RateQuote';
            $ApiKeyset = 'Apikey:  '.$request['apikey'];
            $headers = [$ApiKeyset, 'Accept: application/json','Content-Type: application/json'];
            curl_setopt($curl, CURLOPT_URL, $urlSet);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $RateQuote);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $result     = curl_exec($curl);
            $resultInfo = curl_getinfo($curl, CURLINFO_HTTP_CODE);



            //service_id is a counter that also identifies which service level an accrued total is for, since services are processed in the same order on each iteration of this loop
            $service_id = 0;

            //newnetTotal is an array containing accrued totals for each servicelevel.
            //$service_id is the index of this array representing the servicelevel currently being calculated in the foreach loop
            $newnetTotal = array(0, 0, 0, 0);

            if ($resultInfo == 200) {

                //$service_levels = $result->ServiceLevels->ServiceLevel;
                $resultData = json_decode($result);
                $service_levels = $resultData->RateQuote->ServiceLevels;
                
                //this loop runs for each unique product and accrues the total netcharge for each service level in the $newnetTotal array
                foreach ($service_levels as $service) {
                    if (is_object($service)) {
                        $net = str_replace('$', '', $service->NetCharge); //remove dollar sign from net charge string that is returned by RateQuote API and store in new $net string
                        $net = str_replace(',', '', $net); //remove comma(s)

                        //multiply net cost per pallet by quantity and save in $newnet float
                        $newnet = (floatval($net));
                        $newnetTotal[$service_id] += $newnet;

                        //convert newnetTotal to to string with dollar sign
                        $totalNetCharge = '$'.strval($newnetTotal[$service_id]);

                        //Replace unit rate returned from RateQuote API with totalNetCharge
                        $service->NetCharge = $totalNetCharge;
                        $service->ServiceLevel = $service_id;
                        $service_id += 1;
                    }
                }

            $data = $resultData;

            } else if ($this->debug === '1') {
                $resultError = json_decode($result);
                $error       = $resultError->Errors;
                foreach ($error as $errorMsg) {
                    $msgContent = $errorMsg->ErrorMessage;
                    add_action('admin_notices', 'wc_rlc_error_rq_notice');                    
                    wc_rlc_logMessage('API Error:'.$msgContent);
                    wc_add_notice('API Error: '.$msgContent);
                }
            }
        } catch (Exception $e) {
            wc_add_notice('Failed to retrieve R+L Carriers rate quote. Please contact the site administrator.');
            wc_rlc_logMessage($e->getMessage(), 'error');
        }

        return $data;
    }    

    public function getRateQuote($request)
    {
        $data = $this->doRateQuoteCall($request);
        return $data;
    }

    public function getQuote($id){
        global $wpdb;

        $quote = $wpdb->get_results( $wpdb->prepare(
            "
                   SELECT * FROM " . $wpdb->prefix . "woocommerce_shipping_rlc_quotes
                   WHERE `id` = %d
                   ORDER BY `id` DESC
                   ",
            $id
        ));

        if (! sizeof($quote) )
            return null;

        $quote = $quote[0];

        return $quote;
    }



    public function getNetCharge($charges = null)
    {
        if (is_null($charges))
            $charges = $this->charges;

        return strlen($charges[sizeof($charges)-1]['Amount'])?$charges[sizeof($charges)-1]['Amount']:'&mdash;';
    }

    public function deleteQuote($id = 0){
        global $wpdb;

        if ( !intval($id) )
            $id = $this->id;

        return $wpdb->query('DELETE FROM `' . $wpdb->prefix . 'woocommerce_shipping_rlc_quotes` WHERE `id` = ' . $id);
    }

    public function getShipmentFromQuote($quote_id = null)
    {
        global $wpdb;

        if (is_null($quote_id) )
            $quote_id = $this->id;

        $wpdb->query('SELECT `id` FROM `' . $wpdb->prefix . 'woocommerce_shipping_rlc_shipments` WHERE `order_id` = ' . $this->getOrderFromQuote($quote_id) . ' AND ' . '`quotes` LIKE "%'.$quote_id.'%"');

        return sizeof($wpdb->last_result)?$wpdb->last_result[0]->id:0;
    }

    public function getOrderFromQuote($quote_id)
    {
        global $wpdb;

        $wpdb->query('SELECT `order_id` FROM `' . $wpdb->prefix . 'woocommerce_shipping_rlc_quotes` WHERE `id` = ' . $quote_id);

        return sizeof($wpdb->last_result)?$wpdb->last_result[0]->order_id:0;
    }

}