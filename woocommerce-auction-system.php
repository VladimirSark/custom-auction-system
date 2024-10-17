<?php
/**
 * Plugin Name: WooCommerce Auction System
 * Description: A custom WooCommerce plugin for managing auctions.
 * Version: 1.0
 * Author: Vladimir
 * Text Domain: woocommerce-auction-system
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Register the auction product type
add_action('init', 'register_auction_product_type');
function register_auction_product_type() {
    class WC_Product_Auction extends WC_Product {
        public function __construct($product = 0) {
            $this->product_type = 'auction';
            parent::__construct($product);
        }
    }
}

// Add Auction product type to WooCommerce product types
add_filter('product_type_selector', 'add_auction_product_type');
function add_auction_product_type($types) {
    $types['auction'] = __('Auction', 'woocommerce');
    return $types;
}

// Add Auction tab to Product Data
add_filter('woocommerce_product_data_tabs', 'add_auction_product_tab');
function add_auction_product_tab($tabs) {
    $tabs['auction'] = array(
        'label' => __('Auction', 'woocommerce'),
        'target' => 'auction_product_data',
        'class' => array('show_if_auction'),
    );
    return $tabs;
}

// Add fields to the Auction tab
add_action('woocommerce_product_data_panels', 'add_auction_product_fields');
function add_auction_product_fields() {
    global $post;

    echo '<div id="auction_product_data" class="panel woocommerce_options_panel hidden">';
    
    // Registration Fee Selection
    woocommerce_wp_select(array(
        'id' => '_registration_fee',
        'label' => __('Registration Fee', 'woocommerce'),
        'options' => get_registration_fee_options(),
        'description' => __('Select the registration fee for this auction.', 'woocommerce'),
    ));
    
    woocommerce_wp_text_input(array(
        'id' => '_min_participants',
        'label' => __('Minimum Participants', 'woocommerce'),
        'desc_tip' => 'true',
        'description' => __('Enter the minimum number of participants required to start the auction.', 'woocommerce'),
        'value' => get_post_meta($post->ID, '_min_participants', true),
    ));
    woocommerce_wp_text_input(array(
        'id' => '_auction_timer',
        'label' => __('Auction Timer (seconds)', 'woocommerce'),
        'desc_tip' => 'true',
        'description' => __('Enter the auction timer in seconds.', 'woocommerce'),
        'value' => get_post_meta($post->ID, '_auction_timer', true),
    ));
    woocommerce_wp_text_input(array(
        'id' => '_bid_cost',
        'label' => __('Bid Cost', 'woocommerce'),
        'desc_tip' => 'true',
        'description' => __('Enter the cost associated with each bid placed.', 'woocommerce'),
        'value' => get_post_meta($post->ID, '_bid_cost', true),
    ));
    echo '</div>';
}

// Function to get registration fee options
function get_registration_fee_options() {
    // Specify the IDs of the registration fees
    $registration_fee_ids = array(2697, 2715);
    
    $options = array('' => __('Select a Registration Fee', 'woocommerce'));
    
    foreach ($registration_fee_ids as $id) {
        $product = get_post($id);
        if ($product) {
            $price = get_post_meta($id, '_price', true);
            if (!empty($price)) {
                $options[$id] = $product->post_title . ' (' . $price . ' ' . get_woocommerce_currency_symbol() . ')';
            }
        }
    }
    
    return $options;
}

// Save Auction Fields
add_action('woocommerce_process_product_meta', 'save_auction_product_fields');
function save_auction_product_fields($post_id) {
    update_post_meta($post_id, '_product_type', 'auction');

    if (isset($_POST['_registration_fee'])) {
        update_post_meta($post_id, '_registration_fee', sanitize_text_field($_POST['_registration_fee']));
    }
    if (isset($_POST['_min_participants'])) {
        update_post_meta($post_id, '_min_participants', sanitize_text_field($_POST['_min_participants']));
    }
    if (isset($_POST['_auction_timer'])) {
        update_post_meta($post_id, '_auction_timer', sanitize_text_field($_POST['_auction_timer']));
    }
    if (isset($_POST['_bid_cost'])) {
        update_post_meta($post_id, '_bid_cost', sanitize_text_field($_POST['_bid_cost']));
    }
}

// Create Admin Menu for Auction Management
add_action('admin_menu', 'auction_management_menu');
function auction_management_menu() {
    add_menu_page(
        __('Auction Management', 'woocommerce'),
        __('Auction Management', 'woocommerce'),
        'manage_options',
        'auction-management',
        'auction_management_page',
        'dashicons-hammer',
        55
    );
}

// Auction Management Page
function auction_management_page() {
    ?>
    <div class="wrap">
        <h1>Auction Management</h1>
        <h2><a href="<?php echo admin_url('post-new.php?post_type=product'); ?>" class="button button-primary">Add New Auction Product</a></h2>
        <h2>Manage Existing Auctions</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Auction Name', 'woocommerce'); ?></th>
                    <th><?php _e('Minimum Participants', 'woocommerce'); ?></th>
                    <th><?php _e('Auction Timer', 'woocommerce'); ?></th>
                    <th><?php _e('Bid Cost', 'woocommerce'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $args = array(
                    'post_type' => 'product',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => '_product_type',
                            'value' => 'auction',
                        ),
                        array(
                            'key' => '_min_participants',
                            'compare' => 'EXISTS',
                        ),
                    ),
                );

                $auctions = new WP_Query($args);
                if ($auctions->have_posts()) {
                    while ($auctions->have_posts()) {
                        $auctions->the_post();
                        $min_participants = get_post_meta(get_the_ID(), '_min_participants', true);
                        $auction_timer = get_post_meta(get_the_ID(), '_auction_timer', true);
                        $bid_cost = get_post_meta(get_the_ID(), '_bid_cost', true);
                        ?>
                        <tr>
                            <td><?php the_title(); ?></td>
                            <td><?php echo esc_html($min_participants); ?></td>
                            <td><?php echo esc_html($auction_timer); ?></td>
                            <td><?php echo esc_html($bid_cost); ?></td>
                        </tr>
                        <?php
                    }
                    wp_reset_postdata();
                } else {
                    echo '<tr><td colspan="4">' . __('No auctions found.', 'woocommerce') . '</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Include the front-end functionality
include_once plugin_dir_path(__FILE__) . 'auction-front-end.php';


// Display product meta values after saving
add_action('admin_notices', 'check_auction_meta_values');
function check_auction_meta_values() {
    if (isset($_GET['post']) && get_post_type($_GET['post']) === 'product') {
        $product_id = intval($_GET['post']);
        $product_type = get_post_meta($product_id, '_product_type', true);
        $min_participants = get_post_meta($product_id, '_min_participants', true);
        $auction_timer = get_post_meta($product_id, '_auction_timer', true);
        $bid_cost = get_post_meta($product_id, '_bid_cost', true);
        $registration_fee = get_post_meta($product_id, '_registration_fee', true);

        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>Product ID: ' . $product_id . '</p>';
        echo '<p>Product Type: ' . esc_html($product_type) . '</p>';
        echo '<p>Minimum Participants: ' . esc_html($min_participants) . '</p>';
        echo '<p>Auction Timer: ' . esc_html($auction_timer) . '</p>';
        echo '<p>Bid Cost: ' . esc_html($bid_cost) . '</p>';
        echo '<p>Registration Fee: ' . esc_html(get_the_title($registration_fee)) . '</p>';
        echo '</div>';
    }
}

// Auction registration and upcoming auction status
add_action('woocommerce_product_options_general_product_data', 'add_auction_registration_fields');
function add_auction_registration_fields() {
    global $post;

    // Checkbox for 'Upcoming Auction' (for participants to see it as upcoming after registering)
    woocommerce_wp_checkbox(
        array(
            'id' => '_upcoming_auction',
            'label' => __('Upcoming Auction', 'woocommerce'),
            'description' => __('Mark auction as upcoming after all participants are registered.', 'woocommerce'),
        )
    );

    // Field to track the number of registered participants
    woocommerce_wp_text_input(
        array(
            'id' => '_registered_participants',
            'label' => __('Registered Participants', 'woocommerce'),
            'description' => __('The number of participants who have registered for the auction.', 'woocommerce'),
        )
    );
}

