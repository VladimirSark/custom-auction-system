<?php

class Auction_Fee_Product {
    public static function register_fee_product_type() {
        add_action('init', array(__CLASS__, 'register_fee_product_types'));
        add_filter('product_type_selector', array(__CLASS__, 'add_fee_product_types'));
        add_action('woocommerce_product_options_general_product_data', array(__CLASS__, 'add_fee_product_pricing_fields'));
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
        woocommerce_wp_text_input(array(
            'id' => '_sale_price',
            'label' => __('Sale price', 'woocommerce') . ' (' . get_woocommerce_currency_symbol() . ')',
            'desc_tip' => 'true',
            'description' => __('Enter the sale price.', 'woocommerce'),
            'type' => 'text',
        ));
        woocommerce_wp_select(array(
            'id' => '_tax_status',
            'label' => __('Tax status', 'woocommerce'),
            'options' => array(
                'taxable' => __('Taxable', 'woocommerce'),
                'shipping' => __('Shipping only', 'woocommerce'),
                'none' => __('None', 'woocommerce'),
            ),
        ));
        woocommerce_wp_select(array(
            'id' => '_tax_class',
            'label' => __('Tax class', 'woocommerce'),
            'options' => wc_get_product_tax_class_options(),
        ));
        echo '</div>';
    }

    public static function save_fee_product_pricing_fields($post_id) {
        $regular_price = isset($_POST['_regular_price']) ? sanitize_text_field($_POST['_regular_price']) : '';
        $sale_price = isset($_POST['_sale_price']) ? sanitize_text_field($_POST['_sale_price']) : '';
        $tax_status = isset($_POST['_tax_status']) ? sanitize_text_field($_POST['_tax_status']) : '';
        $tax_class = isset($_POST['_tax_class']) ? sanitize_text_field($_POST['_tax_class']) : '';

        update_post_meta($post_id, '_regular_price', $regular_price);
        update_post_meta($post_id, '_sale_price', $sale_price);
        update_post_meta($post_id, '_tax_status', $tax_status);
        update_post_meta($post_id, '_tax_class', $tax_class);
    }
}

Auction_Fee_Product::register_fee_product_type();