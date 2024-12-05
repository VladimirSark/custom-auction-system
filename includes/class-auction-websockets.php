<?php

class Auction_Websockets {
    public static function initialize() {
        // Add actions for WebSocket connections
        add_action('wp_ajax_nopriv_place_bid', array(__CLASS__, 'place_bid'));
        add_action('wp_ajax_place_bid', array(__CLASS__, 'place_bid'));
        add_action('wp_ajax_end_auction', array(__CLASS__, 'end_auction'));
        add_action('wp_ajax_register_for_auction', array(__CLASS__, 'register_for_auction'));
    }

    public static function place_bid() {
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
        wp_send_json_success(array(
            'highest_bid' => $bid_amount,
            'highest_bidder' => $user_id
        ));
    }

    public static function end_auction() {
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

            wp_send_json_success(__('Auction ended and order created for the winner', 'custom-auction-system'));
        } else {
            wp_send_json_error(__('No bids placed for this auction', 'custom-auction-system'));
        }
    }

    public static function register_for_auction() {
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

        // Charge registration fee
        $registration_fee = get_post_meta($product_id, '_auction_registration_fee', true);
        if ($registration_fee) {
            $order = wc_create_order();
            $order->add_product(wc_get_product($registration_fee), 1);
            $order->set_customer_id($user_id);
            $order->calculate_totals();
        }

        // Register the user
        if (!is_array($registered_participants)) {
            $registered_participants = array();
        }
        $registered_participants[] = $user_id;
        update_post_meta($product_id, '_auction_participants', $registered_participants);

        wp_send_json_success(__('Registration successful', 'custom-auction-system'));
    }
}

Auction_Websockets::initialize();