<?php

if (!defined('ABSPATH')) {
    exit;
}

// Register the custom product types
function register_custom_product_types() {
    include_once plugin_dir_path(__FILE__) . 'class-wc-product-auction.php';
    include_once plugin_dir_path(__FILE__) . 'class-wc-product-participant-fee.php';
}
add_action('woocommerce_loaded', 'register_custom_product_types');

// Add the custom product types to the product type selector
function add_custom_product_types($types) {
    $types['auction'] = __('Auction');
    $types['participant_fee'] = __('Participant Fee');
    return $types;
}
add_filter('product_type_selector', 'add_custom_product_types');

// Add custom fields to the Auction product type
function auction_custom_fields() {
    global $post;

    // Get Participant Fee products
    $participant_fee_products = wc_get_products(array(
        'type' => 'participant_fee',
        'limit' => -1,
    ));

    $participant_fee_options = array('' => __('Select a fee', 'woocommerce'));
    foreach ($participant_fee_products as $product) {
        $participant_fee_options[$product->get_id()] = $product->get_name();
    }

    echo '<div class="options_group show_if_auction">';

    // Participant Fee
    woocommerce_wp_select(
        array(
            'id' => '_participant_fee',
            'label' => __('Participant Fee', 'woocommerce'),
            'options' => $participant_fee_options,
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

// Add custom fields to the Participant Fee product type
function participant_fee_custom_fields() {
    global $post;

    echo '<div class="options_group show_if_participant_fee">';

    // Fee Amount
    woocommerce_wp_text_input(
        array(
            'id' => '_participant_fee_amount',
            'label' => __('Fee Amount', 'woocommerce'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => '0.01',
                'min' => '0',
            ),
        )
    );

    echo '</div>';
}
add_action('woocommerce_product_options_general_product_data', 'participant_fee_custom_fields');

// Save custom fields for Participant Fee
function save_participant_fee_custom_fields($post_id) {
    $participant_fee_amount = isset($_POST['_participant_fee_amount']) ? floatval($_POST['_participant_fee_amount']) : '';

    update_post_meta($post_id, '_participant_fee_amount', $participant_fee_amount);
}
add_action('woocommerce_process_product_meta', 'save_participant_fee_custom_fields');