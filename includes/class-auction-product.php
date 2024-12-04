<?php

class Auction_Product {
    public static function register_product_type() {
        add_action('init', array(__CLASS__, 'register_auction_product_type'));
        add_filter('product_type_selector', array(__CLASS__, 'add_auction_product_type'));
        add_action('woocommerce_product_options_general_product_data', array(__CLASS__, 'add_auction_product_fields'));
        add_action('woocommerce_process_product_meta', array(__CLASS__, 'save_auction_product_fields'));
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

    public static function add_auction_product_fields() {
        global $woocommerce, $post;

        echo '<div class="options_group show_if_auction">';

        // Registration Fee
        woocommerce_wp_select(array(
            'id' => '_auction_registration_fee',
            'label' => __('Registration Fee', 'custom-auction-system'),
            'options' => self::get_fee_options(),
        ));

        // Minimum Number of Participants
        woocommerce_wp_text_input(array(
            'id' => '_auction_min_participants',
            'label' => __('Minimum Number of Participants', 'custom-auction-system'),
            'type' => 'number',
            'custom_attributes' => array(
                'min' => '1',
                'step' => '1',
            ),
        ));

        // Auction Timer
        woocommerce_wp_text_input(array(
            'id' => '_auction_timer',
            'label' => __('Auction Timer (seconds)', 'custom-auction-system'),
            'type' => 'number',
            'custom_attributes' => array(
                'min' => '1',
                'step' => '1',
            ),
        ));

        // Bid Cost
        woocommerce_wp_select(array(
            'id' => '_auction_bid_cost',
            'label' => __('Bid Cost', 'custom-auction-system'),
            'options' => self::get_fee_options(),
        ));

        echo '</div>';
    }

    public static function save_auction_product_fields($post_id) {
        $registration_fee = isset($_POST['_auction_registration_fee']) ? sanitize_text_field($_POST['_auction_registration_fee']) : '';
        $min_participants = isset($_POST['_auction_min_participants']) ? intval($_POST['_auction_min_participants']) : '';
        $auction_timer = isset($_POST['_auction_timer']) ? intval($_POST['_auction_timer']) : '';
        $bid_cost = isset($_POST['_auction_bid_cost']) ? sanitize_text_field($_POST['_auction_bid_cost']) : '';

        update_post_meta($post_id, '_auction_registration_fee', $registration_fee);
        update_post_meta($post_id, '_auction_min_participants', $min_participants);
        update_post_meta($post_id, '_auction_timer', $auction_timer);
        update_post_meta($post_id, '_auction_bid_cost', $bid_cost);
    }

    private static function get_fee_options() {
        $products = wc_get_products(array(
            'status' => 'publish',
            'limit' => -1,
        ));

        $options = array();
        foreach ($products as $product) {
            $options[$product->get_id()] = $product->get_name();
        }

        return $options;
    }
}

Auction_Product::register_product_type();