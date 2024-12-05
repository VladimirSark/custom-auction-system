<?php

if (class_exists('WC_Product')) {
    class WC_Product_Registration_Fee extends WC_Product {
        public function __construct($product) {
            $this->product_type = 'registration_fee';
            parent::__construct($product);
        }

        public function get_type() {
            return 'registration_fee';
        }
    }
}