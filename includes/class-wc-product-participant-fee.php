<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_Product_Participant_Fee extends WC_Product {
    public function __construct($product) {
        $this->product_type = 'participant_fee';
        parent::__construct($product);
    }
}