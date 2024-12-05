<?php

class Auction_Websockets {
    public static function initialize() {
        // Add actions for WebSocket connections
        add_action('wp_ajax_nopriv_place_bid', array(__CLASS__, 'place_bid'));
        add_action('wp_ajax_place_bid', array(__CLASS__, 'place_bid'));
        add_action('wp_ajax_end_auction', array(__CLASS__, 'end_auction'));
        add_action('wp_ajax_start_auction', array(__CLASS__, 'start_auction'));
        add_action('wp_ajax_register_for_auction', array(__CLASS__, 'register_for_auction'));

        // Log message to verify AJAX handlers initialization
        error_log('Auction_Websockets initialized.');
    }

    public static function place_bid() {
        error_log('place_bid AJAX handler called.');

        if (!isset($_POST['product_id']) || !isset($_POST['bid_amount']) || !is_user_logged_in()) {
            wp_send_json_error(__('Invalid request', 'custom-auction-system'));
        }

        $product_id = intval($_POST['product_id']);
        $bid_amount = floatval($_POST['bid_amount']);
        $user_id = get_current_user_id();

        // Simulate bid placement
        $current_highest_bid = get_post_meta($product_id, '_auction_highest_bid', true);
        $current_highest_bidder = get_post_meta($product_id, '_auction_highest_bidder', true);

        if ($bid_amount <= $current_highest_bid) {
            wp_send_json_error(__('Bid amount must be higher than the current highest bid', 'custom-auction-system'));
        }

        update_post_meta($product_id, '_auction_highest_bid', $bid_amount);
        update_post_meta($product_id, '_auction_highest_bidder', $user_id);

        // Add bid fee to cart
        $bid_fee_id = get_post_meta($product_id, '_auction_bid_cost', true);
        if ($bid_fee_id) {
            WC()->cart->add_to_cart($bid_fee_id);
        }

        // Update auction end time
        $auction_timer = get_post_meta($product_id, '_auction_timer', true);
        $new_end_time = time() + intval($auction_timer);
        update_post_meta($product_id, '_auction_end_time', $new_end_time);

        // Notify previous highest bidder
        if ($current_highest_bidder && $current_highest_bidder != $user_id) {
            $previous_bidder = get_userdata($current_highest_bidder);
            wp_mail($previous_bidder->user_email, __('You have been outbid', 'custom-auction-system'), __('You have been outbid on the auction.', 'custom-auction-system'));
        }

        // Simulate real-time update
        $response = array(
            'highest_bid' => $bid_amount,
            'highest_bidder' => $user_id
        );
        error_log('Response data: ' . print_r($response, true));
        wp_send_json_success($response);
    }

    public static function end_auction() {
        error_log('end_auction AJAX handler called.');
        error_log('Request data: ' . print_r($_POST, true));

        if (!isset($_POST['product_id']) || !current_user_can('manage_options')) {
            wp_send_json_error(__('Invalid request', 'custom-auction-system'));
        }

        $product_id = intval($_POST['product_id']);
        $highest_bid = get_post_meta($product_id, '_auction_highest_bid', true);
        $highest_bidder = get_post_meta($product_id, '_auction_highest_bidder', true);

        if ($highest_bidder) {
            // Create order for the winner
            $order = wc_create_order();
            $order->add_product(wc_get_product($product_id), 1);
            $order->set_customer_id($highest_bidder);
            $order->calculate_totals();

            // Update auction status
            update_post_meta($product_id, '_auction_status', 'ended');
            update_post_meta($product_id, '_auction_winner', $highest_bidder);
            update_post_meta($product_id, '_auction_winner_order', $order->get_id());

            // Notify the winner
            $winner = get_userdata($highest_bidder);
            wp_mail($winner->user_email, __('You won the auction', 'custom-auction-system'), __('Congratulations! You won the auction.', 'custom-auction-system'));

            $response = __('Auction ended and order created for the winner', 'custom-auction-system');
            error_log('Response data: ' . $response);
            wp_send_json_success($response);
        } else {
            wp_send_json_error(__('No bids placed for this auction', 'custom-auction-system'));
        }
    }

    public static function start_auction() {
        error_log('start_auction AJAX handler called.');
        error_log('Request data: ' . print_r($_POST, true));

        if (!isset($_POST['product_id']) || !current_user_can('manage_options')) {
            wp_send_json_error(__('Invalid request', 'custom-auction-system'));
        }

        $product_id = intval($_POST['product_id']);
        update_post_meta($product_id, '_auction_status', 'live');
        $response = __('Auction started', 'custom-auction-system');
        error_log('Response data: ' . $response);
        wp_send_json_success($response);
    }

    public static function register_for_auction() {
        error_log('register_for_auction AJAX handler called.');

        if (!isset($_POST['product_id']) || !is_user_logged_in()) {
            wp_send_json_error(__('Invalid request', 'custom-auction-system'));
        }

        $product_id = intval($_POST['product_id']);
        $user_id = get_current_user_id();

        // Check if user is already registered
        $registered_participants = get_post_meta($product_id, '_auction_participants', true);
        if (is_array($registered_participants) && in_array($user_id, $registered_participants)) {
            wp_send_json_error(__('You are already registered for this auction', 'custom-auction-system'));
        }

        // Add registration fee to cart
        $registration_fee_id = get_post_meta($product_id, '_auction_registration_fee', true);
        if ($registration_fee_id) {
            WC()->cart->add_to_cart($registration_fee_id);
        }

        // Register the user
        if (!is_array($registered_participants)) {
            $registered_participants = array();
        }
        $registered_participants[] = $user_id;
        update_post_meta($product_id, '_auction_participants', $registered_participants);

        $response = __('Registration successful', 'custom-auction-system');
        error_log('Response data: ' . $response);
        wp_send_json_success($response);
    }
}

Auction_Websockets::initialize();