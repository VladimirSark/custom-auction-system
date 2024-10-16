<?php
/**
 * Auction Front End - Participant Registration & Status
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Hook to display auction registration and status dropdown on the single product page
add_action('woocommerce_single_product_summary', 'display_auction_registration_info', 25);

function display_auction_registration_info() {
    global $product;

    // Ensure we are on an auction product
    if ($product->get_type() !== 'auction') {
        return;
    }

    // Fetch auction product meta
    $min_participants = get_post_meta($product->get_id(), '_min_participants', true);
    $registration_fee_id = get_post_meta($product->get_id(), '_registration_fee', true);
    $registration_fee_product = wc_get_product($registration_fee_id);
    $registration_fee_cost = $registration_fee_product ? $registration_fee_product->get_price() : 'N/A';

    ?>
    <div class="auction-registration">
        <h2><?php _e('Auction Registration', 'woocommerce'); ?></h2>

        <!-- Show required participants -->
        <p><strong><?php _e('Minimum Participants Required:', 'woocommerce'); ?></strong> <?php echo esc_html($min_participants); ?></p>

        <!-- Progress bar (for visualizing participant registration progress) -->
        <div class="registration-progress">
            <strong><?php _e('Registration Progress:', 'woocommerce'); ?></strong>
            <div id="progress-bar" style="width: 100%; background-color: #f3f3f3; border: 1px solid #ccc;">
                <div id="progress-fill" style="width: 0%; height: 20px; background-color: #4caf50;"></div>
            </div>
            <p><span id="registered-count">0</span> / <?php echo esc_html($min_participants); ?> participants registered</p>
        </div>

        <!-- Registration fee -->
        <p><strong><?php _e('Registration Fee:', 'woocommerce'); ?></strong> <?php echo esc_html($registration_fee_cost . ' ' . get_woocommerce_currency_symbol()); ?></p>

        <!-- Register button with popup -->
        <button id="register-button" class="button"><?php _e('Register for Auction', 'woocommerce'); ?></button>
        <div id="register-popup" style="display:none;">
            <p><?php _e('After pressing register, a product called "registration fee" is added to the cart. After successful payment, the participant will see upcoming auction status.', 'woocommerce'); ?></p>
        </div>

        <!-- Simulate participant registration button -->
        <button id="simulate-button" class="button"><?php _e('Simulate Registration', 'woocommerce'); ?></button>

        <!-- Restart simulation button -->
        <button id="restart-button" class="button"><?php _e('Restart Simulation', 'woocommerce'); ?></button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var progressBarFill = document.getElementById('progress-fill');
        var registeredCount = document.getElementById('registered-count');
        var minParticipants = <?php echo esc_js($min_participants); ?>;
        var currentParticipants = 0;

        // Register button action (shows popup)
        document.getElementById('register-button').addEventListener('click', function() {
            alert('After pressing register, a product called "registration fee" is added to the cart. After successful payment, the participant will see upcoming auction status.');
        });

        // Simulate registration button action (fills progress bar)
        document.getElementById('simulate-button').addEventListener('click', function() {
            if (currentParticipants < minParticipants) {
                currentParticipants++;
                var progressPercentage = (currentParticipants / minParticipants) * 100;
                progressBarFill.style.width = progressPercentage + '%';
                registeredCount.innerText = currentParticipants;

                if (currentParticipants >= minParticipants) {
                    alert('Auction is fully registered!');
                }
            }
        });

        // Restart simulation button action (resets progress bar)
        document.getElementById('restart-button').addEventListener('click', function() {
            currentParticipants = 0;
            progressBarFill.style.width = '0%';
            registeredCount.innerText = currentParticipants;
        });
    });
    </script>

    <style>
        .auction-registration {
            margin-top: 20px;
        }
        .auction-registration .button {
            margin-top: 10px;
        }
    </style>
    <?php
}

// Hook to display auction status dropdown on the single product page
add_action('woocommerce_single_product_summary', 'display_auction_status_dropdown', 30);

function display_auction_status_dropdown() {
    global $product;

    // Ensure we are on an auction product
    if ($product->get_type() !== 'auction') {
        return;
    }

    // Fetch current auction status from product meta
    $auction_status = get_post_meta($product->get_id(), '_auction_status', true);

    // Define available statuses
    $statuses = array(
        'registering' => __('Registering', 'woocommerce'),
        'upcoming' => __('Upcoming', 'woocommerce'),
        'live' => __('Live', 'woocommerce')
    );

    ?>
    <div class="auction-status">
        <h2><?php _e('Auction Status', 'woocommerce'); ?></h2>
        <label for="auction-status-dropdown"><?php _e('Select Auction Status:', 'woocommerce'); ?></label>
        <select id="auction-status-dropdown" name="auction_status">
            <?php foreach ($statuses as $key => $label): ?>
                <option value="<?php echo esc_attr($key); ?>" <?php selected($auction_status, $key); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Handle dropdown change event
        $('#auction-status-dropdown').change(function() {
            var selectedStatus = $(this).val();
            var productId = <?php echo $product->get_id(); ?>;
            
            // Send AJAX request to save the selected status
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    action: 'save_auction_status',
                    auction_status: selectedStatus,
                    product_id: productId
                },
                success: function(response) {
                    alert('Auction status updated to ' + selectedStatus);
                }
            });
        });
    });
    </script>
    <?php
}

// Hook to display the current auction status
add_action('woocommerce_single_product_summary', 'display_current_auction_status', 35);

function display_current_auction_status() {
    global $product;

    // Ensure we are on an auction product
    if ($product->get_type() !== 'auction') {
        return;
    }

    // Fetch the current auction status from product meta
    $auction_status = get_post_meta($product->get_id(), '_auction_status', true);

    // Define status labels
    $status_labels = array(
        'registering' => __('Registering', 'woocommerce'),
        'upcoming' => __('Upcoming', 'woocommerce'),
        'live' => __('Live', 'woocommerce')
    );

    // Display the current status
    if ($auction_status && isset($status_labels[$auction_status])) {
        echo '<p><strong>' . __('Current Auction Status:', 'woocommerce') . '</strong> ' . esc_html($status_labels[$auction_status]) . '</p>';
    }
}

// AJAX handler to save auction status
add_action('wp_ajax_save_auction_status', 'save_auction_status');
add_action('wp_ajax_nopriv_save_auction_status', 'save_auction_status');

function save_auction_status() {
    // Verify request
    if (isset($_POST['auction_status']) && isset($_POST['product_id'])) {
        $auction_status = sanitize_text_field($_POST['auction_status']);
        $product_id = intval($_POST['product_id']);

        // Update auction status in product meta
        update_post_meta($product_id, '_auction_status', $auction_status);

        // Send success response
        wp_send_json_success(array('message' => 'Auction status updated successfully.'));
    } else {
        wp_send_json_error(array('message' => 'Invalid request.'));
    }

    wp_die(); // Terminate immediately and return a proper response
}
