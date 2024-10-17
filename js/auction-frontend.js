jQuery(document).ready(function($) {
    // Function to simulate starting an auction
    $('#start-auction-button').click(function() {
        alert('Auction started!');
    });

    // Register button event handler
    $('#register-btn').on('click', function() {
        // Show the registration popup
        $('#register-popup').show();

        // You can add the registration fee product to the cart using AJAX
        // Here it's just a demo to show the popup
    });

    // Simulate registration button event handler
    $('#simulate-registration-btn').on('click', function() {
        // Fetch current registered participants and required participants
        var registeredParticipants = parseInt($('#registered-participants').val(), 10);
        var requiredParticipants = parseInt($('#required-participants').val(), 10);

        // Simulate the registration and progress bar update
        if (registeredParticipants < requiredParticipants) {
            registeredParticipants++;

            // Calculate new progress percentage
            var newProgress = (registeredParticipants / requiredParticipants) * 100;

            // Update the progress bar width
            $('.progress-bar').css('width', newProgress + '%');

            // Update the registered participants count on the page
            $('p:nth-of-type(2)').text('Registered Participants: ' + registeredParticipants);

            // Optionally, send an AJAX request to update the backend if needed
        } else {
            alert('All participants are registered! Auction is now in the upcoming phase.');
        }
    });
});
