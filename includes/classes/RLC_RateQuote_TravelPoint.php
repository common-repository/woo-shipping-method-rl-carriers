<?php

class RLC_RateQuote_TravelPoint {

    public $City;

    public $StateOrProvince;

    public $ZipOrPostalCode;

    public $CountryCode;


    public function __construct($City, $StateOrProvince, $ZipOrPostalCode, $CountryCode) {

        $this->City            = $City;
        $this->StateOrProvince = $StateOrProvince;
        $this->ZipOrPostalCode = $ZipOrPostalCode;
        $this->CountryCode     = $CountryCode;

    }

    public function set_city($newCity) {
        $this->City = $newCity;
    }

    public function set_state($newState) {
        $this->StateOrProvince = $newState;
    }

    public function set_zip($newZip) {
        $this->ZipOrPostalCode = $newZip;
    }
    
    public function set_code($newCode) {
        $this->CountryCode = $newCode;
    }
    
    public function get_city() {
        return $this->city;
    }
    
    public function get_State() {
        return $this->stateOrProvince;
    }
    
    public function get_zip() {
        return $this->zipOrPostalCode;
    }

    public function get_code() {
        return $this->countryCode;
    }
}