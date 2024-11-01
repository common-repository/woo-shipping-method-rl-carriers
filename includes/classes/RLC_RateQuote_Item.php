<?php

class RLC_RateQuote_Item{

    public $width;

    public $height;

    public $length;

    public $itemClass;

    public $weight;

    public function __construct($weight, $width=null, $height=null, $length=null, $itemClass=null, $quantity) {
        
        $this->width     = $width;
        $this->height    = $height;
        $this->length    = $length;
        $this->itemClass = $itemClass;
        $this->weight    = $weight;
        $this->quantity  = $quantity;
    }

    public function get_width() {
        return $this->width;
    }

    public function get_height() {
        return $this->height;
    }

    public function get_length() {
        return $this->length;
    }

    public function get_class() {
        return $this->itemClass;
    }

    public function get_weight() {
        return $this->weight;
    }

    public function get_quantity() {
        return $this->quantity;
    }

    public function set_width($newWidth) {
        $this->width = $newWidth;
    }
    
    public function set_height($newHeight) {
        $this->height = $newHeight;
    }
    
    public function set_length($newLength) {
        $this->length = $newLength;
    }
    
    public function set_class($newClass) {
        $this->itemClass = $newClass;
    }

    public function set_weight($newWeight) {
        $this->weight = $newWeight;
    }

    public function set_quantity($newQuantity) {
        $this->quantity = $newQuantity;
    }



}