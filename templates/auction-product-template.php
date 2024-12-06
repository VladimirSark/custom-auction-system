<?php
defined('ABSPATH') || exit;

global $product;

if ($product->get_type() !== 'auction') {
    error_log('Product type is not auction in template.');
    return;
}

$registration_fee = get_post_meta($product->get_id(), '_auction_registration_fee', true);
$min_participants = get_post_meta($product->get_id(), '_auction_min_participants', true);
$auction_timer = get_post_meta($product->get_id(), '_auction_timer', true);
$bid_cost = get_post_meta($product->get_id(), '_auction_bid_cost', true);
$current_highest_bid = get_post_meta($product->get_id(), '_auction_highest_bid', true);
$current_highest_bidder = get_post_meta($product->get_id(), '_auction_highest_bidder', true);
$auction_status = get_post_meta($product->get_id(), '_auction_status', true);
$auction_winner = get_post_meta($product->get_id(), '_auction_winner', true);
$registered_participants = get_post_meta($product->get_id(), '_auction_participants', true);
$auction_end_time = get_post_meta($product->get_id(), '_auction_end_time', true);

if (!is_array($registered_participants)) {
    $registered_participants = array();
}

$registered_count = count($registered_participants);
$progress_percent = ($registered_count / $min_participants) * 100;

error_log('Auction product details: ' . print_r(array(
    'registration_fee' => $registration_fee,
    'min_participants' => $min_participants,
    'auction_timer' => $auction_timer,
    'bid_cost' => $bid_cost,
    'current_highest_bid' => $current_highest_bid,
    'current_highest_bidder' => $current_highest_bidder,
    'auction_status' => $auction_status,
    'auction_winner' => $auction_winner,
    'registered_participants' => $registered_participants,
    'auction_end_time' => $auction_end_time,
    'registered_count' => $registered_count,
    'progress_percent' => $progress_percent,
), true));
?>

<div class="auction-details">
    <h2><?php _e('Auction Details', 'custom-auction-system'); ?></h2>
    <p><?php _e('Registration Fee:', 'custom-auction-system'); ?> <?php echo wc_price($registration_fee); ?></p>
    <p><?php _e('Minimum Participants:', 'custom-auction-system'); ?> <?php echo esc_html($min_participants); ?></p>
    <p><?php _e('Auction Timer:', 'custom-auction-system'); ?> <?php echo esc_html($auction_timer); ?> <?php _e('seconds', 'custom-auction-system'); ?></p>
    <p><?php _e('Bid Cost:', 'custom-auction-system'); ?> <?php echo wc_price($bid_cost); ?></p>
    <p><?php _e('Current Highest Bid:', 'custom-auction-system'); ?> <?php echo wc_price($current_highest_bid); ?></p>
    <p><?php _e('Current Highest Bidder:', 'custom-auction-system'); ?> <?php echo esc_html($current_highest_bidder); ?></p>
    <p><?php _e('Registered Participants:', 'custom-auction-system'); ?> <?php echo esc_html($registered_count); ?> / <?php echo esc_html($min_participants); ?></p>
    <div class="progress-bar">
        <div class="progress" style="width: <?php echo esc_attr($progress_percent); ?>%;"></div>
    </div>
</div>

<?php if ($auction_status === 'ended') : ?>
    <div class="auction-winner">
        <h3><?php _e('Auction Winner', 'custom-auction-system'); ?></h3>
        <p><?php _e('Winner:', 'custom-auction-system'); ?> <?php echo esc_html($auction_winner); ?></p>
    </div>
<?php elseif (is_user_logged_in()) : ?>
    <?php if ($auction_status === 'upcoming') : ?>
        <div class="register-for-auction">
            <h3><?php _e('Register for Auction', 'custom-auction-system'); ?></h3>
            <button id="register_for_auction_button" data-product-id="<?php echo esc_attr($product->get_id()); ?>"><?php _e('Register', 'custom-auction-system'); ?></button>
        </div>
    <?php elseif ($auction_status === 'live') : ?>
        <div class="auction-timer">
            <h3><?php _e('Auction Timer', 'custom-auction-system'); ?></h3>
            <p id="auction_timer"><?php echo esc_html($auction_timer); ?></p>
        </div>
        <div class="place-bid">
            <h3><?php _e('Place Your Bid', 'custom-auction-system'); ?></h3>
            <input type="number" id="bid_amount" name="bid_amount" min="<?php echo esc_attr($current_highest_bid + 1); ?>" step="0.01">
            <button id="place_bid_button" data-product-id="<?php echo esc_attr($product->get_id()); ?>"><?php _e('Place Bid', 'custom-auction-system'); ?></button>
        </div>
    <?php endif; ?>
<?php else : ?>
    <p><?php _e('You must be logged in to participate in the auction.', 'custom-auction-system'); ?></p>
<?php endif; ?>