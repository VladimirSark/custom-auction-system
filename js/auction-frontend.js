jQuery(document).ready(function($) {
    function startCountdown(duration, display) {
        var timer = duration, minutes, seconds;
        setInterval(function() {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.text(minutes + ":" + seconds);

            if (--timer < 0) {
                timer = duration;
            }
        }, 1000);
    }

    if ($('#auction_timer').length) {
        var auctionTimer = parseInt($('#auction_timer').text(), 10);
        startCountdown(auctionTimer, $('#auction_timer'));
    }

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