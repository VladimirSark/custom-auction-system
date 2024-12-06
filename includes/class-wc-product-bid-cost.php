<?php

if (!defined('ABSPATH')) {
    exit;
}

class WC_Product_Bid_Cost extends WC_Product {
    public function __construct($product) {
        $this->product_type = 'bid_cost';
        parent::__construct($product);
    }
}