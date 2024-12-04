<?php

class Auction_Dashboard {
    public static function add_admin_menu() {
        add_action('admin_menu', array(__CLASS__, 'register_auction_menu'));
    }

    public static function register_auction_menu() {
        add_menu_page(
            __('Auction Dashboard', 'custom-auction-system'),
            __('Auction Dashboard', 'custom-auction-system'),
            'manage_options',
            'auction-dashboard',
            array(__CLASS__, 'auction_dashboard_page'),
            'dashicons-hammer',
            56
        );
    }

    public static function auction_dashboard_page() {
        echo '<div class="wrap"><h1>' . __('Auction Dashboard', 'custom-auction-system') . '</h1></div>';
    }
}

Auction_Dashboard::add_admin_menu();