<?php

class RLC_RateQuote_Pallet {

    public $code;

    public $weight;

    public $quantity;

    public function __construct($code, $weight, $quantity) {

        $this->code     = $code;
        $this->weight   = $weight;
        $this->quantity = $quantity;
    }

    public function get_code() {
        return $this->code;
    }

    public function get_weight() {
        return $this->weight;
    }

    public function get_quantity() {
        return $this->quantity;
    }

    public function set_code($newCode) {
        $this->code = $newCode;
    }

    public function set_weight($newWeight) {
        $this->weight = $newWeight;
    }

    public function set_quantity($newQuantity) {
        $this->quantity = $newQuantity;
    }

}