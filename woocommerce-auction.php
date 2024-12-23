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

// Check if WooCommerce is active
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('plugins_loaded', 'woocommerce_auction_init', 20);
} else {
    add_action('admin_notices', 'woocommerce_inactive_notice');
}

function woocommerce_auction_init() {
    // Include the necessary files
    include_once plugin_dir_path(__FILE__) . 'includes/class-wc-product-auction.php';
    include_once plugin_dir_path(__FILE__) . 'includes/class-wc-product-participant-fee.php';
    include_once plugin_dir_path(__FILE__) . 'includes/class-wc-product-bid-cost.php';
    include_once plugin_dir_path(__FILE__) . 'includes/auction-product-type.php';
}

function woocommerce_inactive_notice() {
    echo '<div class="error"><p><strong>WooCommerce Auction</strong> requires WooCommerce to be installed and active.</p></div>';
}

function enqueue_auction_scripts() {
    if (is_product()) {
        wp_enqueue_style('auction-style', plugin_dir_url(__FILE__) . 'assets/css/auction-style.css');
        wp_enqueue_script('auction-script', plugin_dir_url(__FILE__) . 'assets/js/auction-script.js', array('jquery'), null, true);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_auction_scripts');