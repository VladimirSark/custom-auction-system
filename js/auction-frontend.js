jQuery(document).ready(function($) {
    $('#register_for_auction_button').on('click', function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'register_for_auction',
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    alert('Registration successful!');
                    location.reload();
                } else {
                    alert(response.data);
                }
            }
        });
    });

    $('#place_bid_button').on('click', function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var bidAmount = $('#bid_amount').val();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'place_bid',
                product_id: productId,
                bid_amount: bidAmount
            },
            success: function(response) {
                if (response.success) {
                    alert('Bid placed successfully!');
                    location.reload();
                } else {
                    alert(response.data);
                }
            }
        });
    });

    $('.start-auction').on('click', function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'start_auction',
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                    location.reload();
                } else {
                    alert(response.data);
                }
            }
        });
    });

    $('.end-auction').on('click', function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'end_auction',
                product_id: productId
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                    location.reload();
                } else {
                    alert(response.data);
                }
            }
        });
    });
});