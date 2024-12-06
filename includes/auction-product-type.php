<?php

if (!defined('ABSPATH')) {
    exit;
}

// Register the custom product types
function register_custom_product_types() {
    include_once plugin_dir_path(__FILE__) . 'class-wc-product-auction.php';
    include_once plugin_dir_path(__FILE__) . 'class-wc-product-participant-fee.php';
    include_once plugin_dir_path(__FILE__) . 'class-wc-product-bid-cost.php';
}
add_action('woocommerce_loaded', 'register_custom_product_types');

// Add the custom product types to the product type selector
function add_custom_product_types($types) {
    $types['auction'] = __('Auction');
    $types['participant_fee'] = __('Participant Fee');
    $types['bid_cost'] = __('Bid Cost');
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

    // Get Bid Cost products
    $bid_cost_products = wc_get_products(array(
        'type' => 'bid_cost',
        'limit' => -1,
    ));

    $bid_cost_options = array('' => __('Select a bid cost', 'woocommerce'));
    foreach ($bid_cost_products as $product) {
        $bid_cost_options[$product->get_id()] = $product->get_name();
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
            'options' => $bid_cost_options,
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

// Display Register button on the auction product page
function display_register_button() {
    global $product;

    if ($product->get_type() == 'auction') {
        $participant_fee_id = get_post_meta($product->get_id(), '_participant_fee', true);
        if ($participant_fee_id) {
            echo '<form class="cart" method="post" enctype="multipart/form-data">';
            echo '<button type="submit" name="add-to-cart" value="' . esc_attr($participant_fee_id) . '" class="single_add_to_cart_button button alt">' . __('Register', 'woocommerce') . '</button>';
            echo '</form>';
        }
    }
}
add_action('woocommerce_single_product_summary', 'display_register_button', 35);

// Count the number of registered participants
function get_registered_participants($product_id) {
    $participant_fee_id = get_post_meta($product_id, '_participant_fee', true);
    if (!$participant_fee_id) {
        return 0;
    }

    $args = array(
        'status' => 'completed',
        'limit' => -1,
        'meta_query' => array(
            array(
                'key' => '_product_id',
                'value' => $participant_fee_id,
                'compare' => '=',
            ),
        ),
    );

    $orders = wc_get_orders($args);
    $count = 0;

    foreach ($orders as $order) {
        foreach ($order->get_items() as $item) {
            if ($item->get_product_id() == $participant_fee_id) {
                $count += $item->get_quantity();
            }
        }
    }

    return $count;
}

// Display progress bar on the auction product page
function display_progress_bar() {
    global $product;

    if ($product->get_type() == 'auction') {
        $min_participants = get_post_meta($product->get_id(), '_min_participants', true);
        $registered_participants = get_registered_participants($product->get_id());

        if ($min_participants > 0) {
            $percentage = ($registered_participants / $min_participants) * 100;
            $percentage = min($percentage, 100);

            echo '<div class="auction-progress-bar">';
            echo '<div class="progress">';
            echo '<div class="progress-bar" role="progressbar" style="width: ' . esc_attr($percentage) . '%;" aria-valuenow="' . esc_attr($percentage) . '" aria-valuemin="0" aria-valuemax="100"></div>';
            echo '</div>';
            echo '<p>' . sprintf(__('Registered Participants: %d / %d', 'woocommerce'), $registered_participants, $min_participants) . '</p>';
            echo '</div>';
        }
    }
}
add_action('woocommerce_single_product_summary', 'display_progress_bar', 25);