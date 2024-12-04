<?php

class Auction_Product {
    public static function register_product_type() {
        add_action('init', array(__CLASS__, 'register_auction_product_type'));
        add_filter('product_type_selector', array(__CLASS__, 'add_auction_product_type'));
    }

    public static function register_auction_product_type() {
        if (class_exists('WC_Product')) {
            include_once CAS_PLUGIN_DIR . 'includes/class-wc-product-auction.php';
        }
    }

    public static function add_auction_product_type($types) {
        $types['auction'] = __('Auction', 'custom-auction-system');
        return $types;
    }
}

Auction_Product::register_product_type();