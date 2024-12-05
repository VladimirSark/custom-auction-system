<?php
/*
Plugin Name: Custom Auction System
Description: A custom auction system integrated with WooCommerce.
Version: 1.0
Author: Your Name
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CAS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CAS_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
include_once CAS_PLUGIN_DIR . 'includes/class-auction-product.php';
include_once CAS_PLUGIN_DIR . 'includes/class-auction-dashboard.php';
include_once CAS_PLUGIN_DIR . 'includes/class-auction-websockets.php';

// Initialize the plugin
function cas_initialize_plugin() {
    // Register custom product type
    Auction_Product::register_product_type();

    // Add admin menu for auction dashboard
    Auction_Dashboard::add_admin_menu();

    // Enqueue scripts and styles
    add_action('wp_enqueue_scripts', 'cas_enqueue_scripts');

    // Initialize WebSockets
    Auction_Websockets::initialize();
}
add_action('plugins_loaded', 'cas_initialize_plugin');

function cas_enqueue_scripts() {
    wp_enqueue_style('cas-auction-css', CAS_PLUGIN_URL . 'css/auction.css');
    wp_enqueue_script('cas-auction-js', CAS_PLUGIN_URL . 'js/auction-frontend.js', array('jquery'), null, true);
    wp_localize_script('cas-auction-js', 'ajaxurl', admin_url('admin-ajax.php'));
}

function cas_enqueue_admin_scripts($hook) {
    if ($hook !== 'toplevel_page_auction-dashboard') {
        return;
    }

    wp_enqueue_script('cas-auction-admin-js', CAS_PLUGIN_URL . 'js/auction-frontend.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'cas_enqueue_admin_scripts');