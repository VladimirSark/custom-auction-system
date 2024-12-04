jQuery(document).ready(function($) {
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