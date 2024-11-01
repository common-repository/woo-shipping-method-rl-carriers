<?php

class RLC_RateQuote_Request {

    public $CODAmount;

    public RLC_RateQuote_TravelPoint $Origin;

    public RLC_RateQuote_TravelPoint $Destination;

    public $Items = [];

    public $OverDimensions = [];

    public $Pallets = [];

    public $AdditionalServices = [];

    public $DeclaredValue;

    public function __construct($CODAmount, $Origin, $Destination, $Pallets, $Items, $AdditionalServices, $OverDimensions = null, $DeclaredValue) {
        $this->CODAmount          = $CODAmount;
        $this->Origin             = $Origin;
        $this->Destination        = $Destination;
        $this->Items              = $Items;
        $this->Pallets            = $Pallets;
        $this->AdditionalServices = $AdditionalServices;
        $this->OverDimensions     = $OverDimensions;
        $this->DeclaredValue      = $DeclaredValue;
    }

    public function get_codAmount(){
        return $this->codAmount;
    }

    public function get_origin() {
        return $this->$origin;
    }

    public function get_destination() {
        return $this->destination;
    }

    public function get_items() {
        return $this->$items;
    }

    public function get_pallet() {
        return $this->pallet;
    }

    public function get_declaredValue() {
        return $this->declaredValue;
    }

    public function get_Services() {
        return $this->AdditionalServices;
    }

    public function get_inches() {
        return $this->inches;
    }

    public function get_numItems() {
        return $this->numItems;
    }

    public function set_inches($newInches) {
        $this->inches = $newInches;
    }

    public function set_Servicees($newServices) {
        $this->inches = $newServices;
    }

    public function set_numItems($newNumItems) {
        $this->numItems = $newNumItems;
    }

    public function set_Origin($newOrigin) {
        $this->origin = $newOrigin;
    }

    public function set_Destination($newDestination) {
        $this->destination = $newDestination;
    }

    public function set_newItems($newItems) {
        $this->items = $newItems;
    }

    public function set_Pallet($newPallet) {
        $this->pallet = $newPallet;
    }

    public function set_declaredValue($newValue) {
        $this->declaredValue = $newValue;
    }

    public function set_codAmount($newCod) {
        $this->codAmount = $newCod;
    }

}