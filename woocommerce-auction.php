<?php
/*
Plugin Name: WooCommerce Auction
Description: Adds a new product type for auctions in WooCommerce.
Version: 1.0
Author: Your Name
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register the custom product type
function register_auction_product_type() {
    class WC_Product_Auction extends WC_Product {
        public function __construct($product) {
            $this->product_type = 'auction';
            parent::__construct($product);
        }
    }
}
add_action('init', 'register_auction_product_type');

// Add the custom product type to the product type selector
function add_auction_product($types) {
    $types['auction'] = __('Auction');
    return $types;
}
add_filter('product_type_selector', 'add_auction_product');