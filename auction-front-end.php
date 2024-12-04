<?php

add_action('woocommerce_single_product_summary', 'display_auction_product_template', 20);

function display_auction_product_template() {
    global $product;

    if ($product->get_type() === 'auction') {
        wc_get_template('auction-product-template.php', array(), '', CAS_PLUGIN_DIR . 'templates/');
    }
}