<?php
defined('ABSPATH') || exit;

global $product;

if ($product->get_type() !== 'auction') {
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

?>

<div class="auction-details">
    <h2><?php _e('Auction Details', 'custom-auction-system'); ?></h2>
    <p><?php _e('Registration Fee:', 'custom-auction-system'); ?> <?php echo wc_price($registration_fee); ?></p>
    <p><?php _e('Minimum Participants:', 'custom-auction-system'); ?> <?php echo esc_html($min_participants); ?></p>
    <p><?php _e('Auction Timer:', 'custom-auction-system'); ?> <?php echo esc_html($auction_timer); ?> <?php _e('seconds', 'custom-auction-system'); ?></p>
    <p><?php _e('Bid Cost:', 'custom-auction-system'); ?> <?php echo wc_price($bid_cost); ?></p>
    <p><?php _e('Current Highest Bid:', 'custom-auction-system'); ?> <?php echo wc_price($current_highest_bid); ?></p>
    <p><?php _e('Current Highest Bidder:', 'custom-auction-system'); ?> <?php echo esc_html($current_highest_bidder); ?></p>
</div>

<?php if ($auction_status === 'ended') : ?>
    <div class="auction-winner">
        <h3><?php _e('Auction Winner', 'custom-auction-system'); ?></h3>
        <p><?php _e('Winner:', 'custom-auction-system'); ?> <?php echo esc_html($auction_winner); ?></p>
    </div>
<?php elseif (is_user_logged_in()) : ?>
    <div class="place-bid">
        <h3><?php _e('Place Your Bid', 'custom-auction-system'); ?></h3>
        <input type="number" id="bid_amount" name="bid_amount" min="<?php echo esc_attr($current_highest_bid + 1); ?>" step="0.01">
        <button id="place_bid_button" data-product-id="<?php echo esc_attr($product->get_id()); ?>"><?php _e('Place Bid', 'custom-auction-system'); ?></button>
    </div>
<?php else : ?>
    <p><?php _e('You must be logged in to place a bid.', 'custom-auction-system'); ?></p>
<?php endif; ?>