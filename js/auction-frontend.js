jQuery(document).ready(function($) {
    $('#register_for_auction_button').on('click', function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        console.log('Register for auction clicked. Product ID:', productId);

        $.ajax({
            url: cas_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'register_for_auction',
                product_id: productId
            },
            success: function(response) {
                console.log('Register for auction response:', response);
                if (response.success) {
                    alert('Registration successful!');
                    location.reload();
                } else {
                    alert(response.data);
                }
            },
            error: function(xhr, status, error) {
                console.log('Register for auction error:', error);
            }
        });
    });
});