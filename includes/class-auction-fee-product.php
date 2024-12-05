<?php

class Auction_Fee_Product {
    public static function register_fee_product_type() {
        add_action('init', array(__CLASS__, 'register_fee_product_types'));
        add_filter('product_type_selector', array(__CLASS__, 'add_fee_product_types'));
        add_action('woocommerce_product_options_pricing', array(__CLASS__, 'add_fee_product_pricing_fields'));
        add_action('woocommerce_process_product_meta', array(__CLASS__, 'save_fee_product_pricing_fields'));
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

    public static function add_fee_product_pricing_fields() {
        global $woocommerce, $post;

        echo '<div class="options_group show_if_registration_fee show_if_bid_fee">';
        woocommerce_wp_text_input(array(
            'id' => '_regular_price',
            'label' => __('Regular price', 'woocommerce') . ' (' . get_woocommerce_currency_symbol() . ')',
            'desc_tip' => 'true',
            'description' => __('Enter the regular price.', 'woocommerce'),
            'type' => 'text',
        ));
        echo '</div>';
    }

    public static function save_fee_product_pricing_fields($post_id) {
        $regular_price = isset($_POST['_regular_price']) ? sanitize_text_field($_POST['_regular_price']) : '';
        update_post_meta($post_id, '_regular_price', $regular_price);
    }
}

Auction_Fee_Product::register_fee_product_type();