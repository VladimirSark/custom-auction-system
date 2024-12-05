<?php

class Auction_Fee_Product {
    public static function register_fee_product_type() {
        add_action('init', array(__CLASS__, 'register_fee_product_types'));
        add_filter('product_type_selector', array(__CLASS__, 'add_fee_product_types'));
    }

    public static function register_fee_product_types() {
        if (class_exists('WC_Product')) {
            include_once CAS_PLUGIN_DIR . 'includes/class-wc-product-registration-fee.php';
            include_once CAS_PLUGIN_DIR . 'includes/class-wc-product-bid-fee.php';
        }
    }

    public static function add_fee_product_types($types) {
        $types['registration_fee'] = __('Registration Fee', 'custom-auction-system');
        $types['bid_fee'] = __('Bid Fee', 'custom-auction-system');
        return $types;
    }
}

Auction_Fee_Product::register_fee_product_type();