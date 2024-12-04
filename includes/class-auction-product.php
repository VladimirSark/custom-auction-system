<?php

class Auction_Product {
    public static function register_product_type() {
        add_action('init', array(__CLASS__, 'register_auction_product_type'));
        add_filter('product_type_selector', array(__CLASS__, 'add_auction_product_type'));
    }

    public static function register_auction_product_type() {
        class WC_Product_Auction extends WC_Product {
            public function __construct($product) {
                $this->product_type = 'auction';
                parent::__construct($product);
            }
        }
    }

    public static function add_auction_product_type($types) {
        $types['auction'] = __('Auction');
        return $types;
    }
}

Auction_Product::register_product_type();