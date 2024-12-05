<?php

add_action('woocommerce_single_product_summary', 'display_auction_product_template', 20);

function display_auction_product_template() {
    global $product;

    if ($product->get_type() === 'auction') {
        error_log('Auction product template is being loaded.');
        wc_get_template('auction-product-template.php', array(), '', CAS_PLUGIN_DIR . 'templates/');
    } else {
        error_log('Product type is not auction.');
    }
}