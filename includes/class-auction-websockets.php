<?php

class Auction_Websockets {
    public static function initialize() {
        // Add actions for WebSocket connections
        add_action('wp_ajax_nopriv_register_for_auction', array(__CLASS__, 'register_for_auction'));
        add_action('wp_ajax_register_for_auction', array(__CLASS__, 'register_for_auction'));

        // Log message to verify AJAX handlers initialization
        error_log('Auction_Websockets initialized.');
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