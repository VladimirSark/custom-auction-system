<?php

if (class_exists('WC_Product')) {
    class WC_Product_Bid_Fee extends WC_Product {
        public function __construct($product) {
            $this->product_type = 'bid_fee';
            parent::__construct($product);
        }

        public function get_type() {
            return 'bid_fee';
        }
    }
}