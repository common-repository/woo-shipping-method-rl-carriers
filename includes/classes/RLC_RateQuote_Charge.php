<?php

class RLC_RateQuote_Charge {

    public $type;

    public $title;

    public $weight;

    public $rate;

    public $amount;

    public function __construct($id = null)
    {
        if ( intval($id) )
        {
            $charge = $this->get($id);
            $this->set_amount($charge['amount']);
            $this->set_rate($charge['rate']);
            $this->set_title($charge['title']);
            $this->set_type($charge['type']);
            $this->set_weight($charge['weight']);
        }
    }

    public function set_type($new_type)
    {
        $this->type = $new_type;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function set_title($new_title)
    {
        $this->title = $new_title;
    }

    public function get_title()
    {
        return $this->title;
    }

    public function set_weight($new_weight)
    {
        $this->weight = $new_weight;
    }

    public function get_weight()
    {
        return $this->weight;
    }

    public function set_rate($new_rate)
    {
        $this->rate = wc_rlc_dollarsToDecimal($new_rate);
    }

    public function get_rate()
    {
        return wc_rlc_asDollars($this->rate);
    }

    public function set_amount($new_amount)
    {
        $this->amount = wc_rlc_dollarsToDecimal($new_amount);
    }

    public function get_amount()
    {
        return wc_rlc_asDollars($this->amount);
    }

    public function get($id)
    {
        global $wpdb;

        $charge = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM " . $wpdb->prefix . "woocommerce_shipping_rlc_quotes_charges
                WHERE `id` = %d LIMIT 1",
            $id
        ));

        if ( ! sizeof($charge) )
            wc_rlc_logMessage( 'Charge# ' . $id . ' not found', 'error');

        return sizeof($charge)?$charge[0]:null;
    }

    /**
    * @param $quote_id
    * @return null
    */
    public function getChargesFromQuote($quote_id)
    {
        global $wpdb;

        $charges = $wpdb->get_results( $wpdb->prepare(
            "SELECT `id` FROM " . $wpdb->prefix . "woocommerce_shipping_rlc_quotes_charges
                WHERE `quote_id` = %d
                ORDER BY `created_at` DESC",
            $quote_id
        ));

        if ( ! sizeof($charges) )
            return null;

        $_charges = array();
        $i = 0;
        foreach ( $charges as $charge )
        {
            $id = $charge->id;
            $charge = $this->get($id);

            $_charges[$i]['type'] = $charge->type;
            $_charges[$i]['title'] = $charge->title;
            $_charges[$i]['weight'] = $charge->weight;
            $_charges[$i]['rate'] = wc_rlc_asDollars($charge->rate);
            $_charges[$i]['amount'] = wc_rlc_asDollars($charge->amount);

            $i++;
        }

        return $_charges;
    }

    public function store($quote_id, $title, $weight, $rate, $amount, $type = 'CLASS')
    {
        global $wpdb;

        $wpdb->insert($wpdb->prefix . 'woocommerce_shipping_rlc_quotes_charges', array(
            'quote_id' => $quote_id,
            'type' => strlen($type)?$type:'CLASS', //@TODO: Test pallet charges
            'title' => $title,
            'weight' => $weight,
            'rate' => wc_rlc_dollarsToDecimal($rate),
            'amount' => wc_rlc_dollarsToDecimal($amount),
        ));

        if ( $wpdb->last_error )
            wc_rlc_logMessage( 'Error inserting into woocommerce_shipping_rlc_quotes_charges: ' . $wpdb->last_error);


        return intval($wpdb->insert_id)?$wpdb->insert_id:null;
    }
}