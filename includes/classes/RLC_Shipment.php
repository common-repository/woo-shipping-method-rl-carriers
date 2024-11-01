<?php

/**
 * Created by PhpStorm.
 * User: Clif.Molina
 * Date: 10/23/2015
 * Time: 1:26 PM
 */
class RLC_Shipment
{

    public $hasItems;

    public $quotes = array();

    public $id;

    public $netTotal;

    public $order_id;

    protected $debug;

    protected $endpoint;

    protected $soap_client_options;

    protected $soap_client;

    protected $shipments_table_name = 'woocommerce_shipping_rlc_shipments';
    protected $quotes_table_name = 'woocommerce_shipping_rlc_quotes';


    public function __construct($id = null)
    {
        if (intval($id)) {
            $shipment = $this->getShipment($id);

            $this->id = $id;
            $this->order_id = $shipment->order_id;
            $this->destination_zipcode = $shipment->destination_zipcode;
            $this->quotes = $this->getQuotes();
            $this->hasItems = $this->hasItems();
            $this->created_at = $shipment->created_at;
        }

    }


	/**
	 * @return array
	 */
	public function getQuotes()
    {
        global $wpdb;

        $_quotes = $wpdb->get_results( $wpdb->prepare(
            "
                   SELECT `id` FROM " . $wpdb->prefix . $this->quotes_table_name . "
                   WHERE shipment_id = %d
                   ",
            $this->id
        ));

        if ( ! sizeof($_quotes) )
            wc_rlc_logMessage( 'Unable to retrieve shipment quotes: ' . $wpdb->last_error, 'error');

	    $quotes = array();

        foreach ( $_quotes as $quote )
        {
            $quotes[] = new RLC_RateQuote($quote->id);
        }

        return $quotes;
    }




    /**
     * @param $order_id
     * @return mixed
     * @internal param $wpdb
     */
    public function getOrderShipments($order_id){
        global $wpdb;

        $shipments = $wpdb->get_results( $wpdb->prepare(
            "
                   SELECT * FROM " . $wpdb->prefix . $this->shipments_table_name . "
                   WHERE order_id = %d
                   ORDER BY `id` DESC
           ",
            $order_id
        ));

        if ( ! sizeof($shipments) ) {

            wc_rlc_logMessage( 'No shipments found for Order#' . $order_id);
            return array();
        }

	    $_shipments = array();

        foreach ( $shipments as $shipment )
        {
            $_shipments[] = new RLC_Shipment($shipment->id);
        }

        return $_shipments;
    }

    /**
     * @param $id
     * @return null
     */
    public function getShipment($id)
    {
        global $wpdb;

        $shipments = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM " . $wpdb->prefix . $this->shipments_table_name . "
                WHERE `id` = %d
                ORDER BY `id` DESC",
            $id
        ));

        if ( ! sizeof($shipments) )
            wc_rlc_logMessage( 'Shipment# ' . $id . ' not found', 'error');

        return sizeof($shipments)?$shipments[0]:null;
    }

    private function hasItems()
    {
        foreach ($this->quotes as $quote)
            if (sizeof($quote->items))
                return true;

        return false;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteShipment($id = 0){
        global $wpdb;

        if ( ! intval($id) )
            $id = $this->id;

        return $wpdb->query('DELETE FROM `' . $wpdb->prefix . 'woocommerce_shipping_rlc_shipments` WHERE `id` = ' . $id);
    }

    public function store($order_id, $destination_zipcode)
    {
        global $wpdb;

        $wpdb->insert($wpdb->prefix . 'woocommerce_shipping_rlc_shipments', array(
            'order_id' => $order_id,
            'destination_zipcode' => $destination_zipcode,
            'created_at' => date('Y-m-d H:i:s')
        ));

        if ( $wpdb->last_error )
            wc_rlc_logMessage( 'Error inserting into woocommerce_shipping_rlc_shipments: ' . $wpdb->last_error);

        return intval($wpdb->insert_id)?$wpdb->insert_id:null;
    }
}
