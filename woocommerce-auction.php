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

<?php
// Add custom fields to the Auction product type
function auction_custom_fields() {
    global $post;

    echo '<div class="options_group show_if_auction">';

    // Participant Fee
    woocommerce_wp_select(
        array(
            'id' => '_participant_fee',
            'label' => __('Participant Fee', 'woocommerce'),
            'options' => array(
                '' => __('Select a fee', 'woocommerce'),
                // Add options dynamically later
            ),
        )
    );

    // Minimum Number of Participants
    woocommerce_wp_text_input(
        array(
            'id' => '_min_participants',
            'label' => __('Minimum Number of Participants', 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => '1',
                'min' => '1',
            ),
        )
    );

    // Auction Timer
    woocommerce_wp_text_input(
        array(
            'id' => '_auction_timer',
            'label' => __('Auction Timer (seconds)', 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => '1',
                'min' => '1',
            ),
        )
    );

    // Bid Cost
    woocommerce_wp_select(
        array(
            'id' => '_bid_cost',
            'label' => __('Bid Cost', 'woocommerce'),
            'options' => array(
                '' => __('Select a bid cost', 'woocommerce'),
                // Add options dynamically later
            ),
        )
    );

    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'auction_custom_fields');

// Save custom fields
function save_auction_custom_fields($post_id) {
    $participant_fee = isset($_POST['_participant_fee']) ? sanitize_text_field($_POST['_participant_fee']) : '';
    $min_participants = isset($_POST['_min_participants']) ? intval($_POST['_min_participants']) : '';
    $auction_timer = isset($_POST['_auction_timer']) ? intval($_POST['_auction_timer']) : '';
    $bid_cost = isset($_POST['_bid_cost']) ? sanitize_text_field($_POST['_bid_cost']) : '';

    update_post_meta($post_id, '_participant_fee', $participant_fee);
    update_post_meta($post_id, '_min_participants', $min_participants);
    update_post_meta($post_id, '_auction_timer', $auction_timer);
    update_post_meta($post_id, '_bid_cost', $bid_cost);
}
add_action('woocommerce_process_product_meta', 'save_auction_custom_fields');