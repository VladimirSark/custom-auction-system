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

// Include the necessary files
include_once plugin_dir_path(__FILE__) . 'includes/class-wc-product-auction.php';
include_once plugin_dir_path(__FILE__) . 'includes/class-wc-product-participant-fee.php';
include_once plugin_dir_path(__FILE__) . 'includes/auction-product-type.php';