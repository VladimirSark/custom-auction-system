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

?>

<div class="auction-details">
    <h2><?php _e('Auction Details', 'custom-auction-system'); ?></h2>
    <p><?php _e('Registration Fee:', 'custom-auction-system'); ?> <?php echo wc_price($registration_fee); ?></p>
    <p><?php _e('Minimum Participants:', 'custom-auction-system'); ?> <?php echo esc_html($min_participants); ?></p>
    <p><?php _e('Auction Timer:', 'custom-auction-system'); ?> <?php echo esc_html($auction_timer); ?> <?php _e('seconds', 'custom-auction-system'); ?></p>
    <p><?php _e('Bid Cost:', 'custom-auction-system'); ?> <?php echo wc_price($bid_cost); ?></p>
</div>