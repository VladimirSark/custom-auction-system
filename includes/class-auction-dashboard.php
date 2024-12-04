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
        ?>
        <div class="wrap">
            <h1><?php _e('Auction Dashboard', 'custom-auction-system'); ?></h1>
            <h2 class="nav-tab-wrapper">
                <a href="?page=auction-dashboard&tab=upcoming" class="nav-tab"><?php _e('Upcoming', 'custom-auction-system'); ?></a>
                <a href="?page=auction-dashboard&tab=live" class="nav-tab"><?php _e('Live', 'custom-auction-system'); ?></a>
                <a href="?page=auction-dashboard&tab=ended" class="nav-tab"><?php _e('Ended', 'custom-auction-system'); ?></a>
                <a href="?page=auction-dashboard&tab=payment-due" class="nav-tab"><?php _e('Payment Due', 'custom-auction-system'); ?></a>
                <a href="?page=auction-dashboard&tab=mail-setup" class="nav-tab"><?php _e('Mail Setup', 'custom-auction-system'); ?></a>
            </h2>
            <?php
            $tab = isset($_GET['tab']) ? $_GET['tab'] : 'upcoming';
            switch ($tab) {
                case 'upcoming':
                    self::display_upcoming_auctions();
                    break;
                case 'live':
                    self::display_live_auctions();
                    break;
                case 'ended':
                    self::display_ended_auctions();
                    break;
                case 'payment-due':
                    self::display_payment_due_auctions();
                    break;
                case 'mail-setup':
                    self::display_mail_setup();
                    break;
            }
            ?>
        </div>
        <?php
    }

    public static function display_upcoming_auctions() {
        echo '<h2>' . __('Upcoming Auctions', 'custom-auction-system') . '</h2>';

        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_auction_status',
                    'value' => 'upcoming',
                    'compare' => '='
                )
            )
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>' . __('Auction Name', 'custom-auction-system') . '</th><th>' . __('Date Created', 'custom-auction-system') . '</th><th>' . __('Participants', 'custom-auction-system') . '</th><th>' . __('Actions', 'custom-auction-system') . '</th></tr></thead>';
            echo '<tbody>';

            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());
                $participants = get_post_meta($product->get_id(), '_auction_participants', true);
                $min_participants = get_post_meta($product->get_id(), '_auction_min_participants', true);

                echo '<tr>';
                echo '<td>' . get_the_title() . '</td>';
                echo '<td>' . get_the_date() . '</td>';
                echo '<td>' . esc_html($participants) . ' / ' . esc_html($min_participants) . '</td>';
                echo '<td><a href="#" class="button">' . __('Start Auction', 'custom-auction-system') . '</a></td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        } else {
            echo '<p>' . __('No upcoming auctions found.', 'custom-auction-system') . '</p>';
        }

        wp_reset_postdata();
    }

    public static function display_live_auctions() {
        echo '<h2>' . __('Live Auctions', 'custom-auction-system') . '</h2>';

        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_auction_status',
                    'value' => 'live',
                    'compare' => '='
                )
            )
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>' . __('Auction Name', 'custom-auction-system') . '</th><th>' . __('Date Started', 'custom-auction-system') . '</th><th>' . __('Participants', 'custom-auction-system') . '</th><th>' . __('Actions', 'custom-auction-system') . '</th></tr></thead>';
            echo '<tbody>';

            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());
                $participants = get_post_meta($product->get_id(), '_auction_participants', true);

                echo '<tr>';
                echo '<td>' . get_the_title() . '</td>';
                echo '<td>' . get_the_date() . '</td>';
                echo '<td>' . esc_html($participants) . '</td>';
                echo '<td><a href="#" class="button">' . __('End Auction', 'custom-auction-system') . '</a></td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        } else {
            echo '<p>' . __('No live auctions found.', 'custom-auction-system') . '</p>';
        }

        wp_reset_postdata();
    }

    public static function display_ended_auctions() {
        echo '<h2>' . __('Ended Auctions', 'custom-auction-system') . '</h2>';

        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_auction_status',
                    'value' => 'ended',
                    'compare' => '='
                )
            )
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>' . __('Auction Name', 'custom-auction-system') . '</th><th>' . __('Date Ended', 'custom-auction-system') . '</th><th>' . __('Winner', 'custom-auction-system') . '</th><th>' . __('Actions', 'custom-auction-system') . '</th></tr></thead>';
            echo '<tbody>';

            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());
                $winner = get_post_meta($product->get_id(), '_auction_winner', true);

                echo '<tr>';
                echo '<td>' . get_the_title() . '</td>';
                echo '<td>' . get_the_date() . '</td>';
                echo '<td>' . esc_html($winner) . '</td>';
                echo '<td><a href="#" class="button">' . __('Contact Winner', 'custom-auction-system') . '</a></td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        } else {
            echo '<p>' . __('No ended auctions found.', 'custom-auction-system') . '</p>';
        }

        wp_reset_postdata();
    }

    public static function display_payment_due_auctions() {
        echo '<h2>' . __('Payment Due Auctions', 'custom-auction-system') . '</h2>';

        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_auction_status',
                    'value' => 'payment_due',
                    'compare' => '='
                )
            )
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr><th>' . __('Auction Name', 'custom-auction-system') . '</th><th>' . __('Date Ended', 'custom-auction-system') . '</th><th>' . __('Winner', 'custom-auction-system') . '</th><th>' . __('Actions', 'custom-auction-system') . '</th></tr></thead>';
            echo '<tbody>';

            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());
                $winner = get_post_meta($product->get_id(), '_auction_winner', true);

                echo '<tr>';
                echo '<td>' . get_the_title() . '</td>';
                echo '<td>' . get_the_date() . '</td>';
                echo '<td>' . esc_html($winner) . '</td>';
                echo '<td><a href="#" class="button">' . __('Send Payment Reminder', 'custom-auction-system') . '</a></td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
        } else {
            echo '<p>' . __('No payment due auctions found.', 'custom-auction-system') . '</p>';
        }

        wp_reset_postdata();
    }

    public static function display_mail_setup() {
        echo '<h2>' . __('Mail Setup', 'custom-auction-system') . '</h2>';
        // Display mail setup options
    }
}

Auction_Dashboard::add_admin_menu();