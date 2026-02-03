// jquery
// document ready
$(document).ready(function() {
	// handle form submission

	// $('#submitBtn').css("disabled",true);

	// check inputs on keyup
	// $('#username, #password').on('keyup', checkInputs);

	// check inputs on page load
	// checkInputs();
	// handle form submission

	$('#loginForm').on('submit', function(event) {

		console.log('Form submitted'); // debug log
		event.preventDefault(); // prevent default form submission
		const formData = $(this).serialize(); // serialize form data
		console.log('Form Data:', formData); // debug log
		// For demonstration, we'll just log the form data instead of making an actual AJAX call
		// In a real application, you would uncomment the AJAX call below and handle the response accordingly

		console.log($('#username').val());
		console.log($('#password').val());

		// clear previous messages
		// $('#error').hide();
		$('.displayTxt').hide();

		if ( $('#username').val() === '' || $('#password').val() === '' ) {
			$('.displayTxt').text('Please fill in all fields.').show();

			// alert('Please fill in all fields.');
			return;
		} else {

			$.ajax({
				type: 'POST',
				url: '../auth/process_login.php',
				data: formData,
				// expect JSON response
				dataType: 'json', // expect text response
				// content type JSON
				// contentType: 'application/json',
				beforeSend: function() {

					// show loading indicator
					$('.displayTxt').text('Processing...').show();	

				},
				complete: function() {

					// hide loading indicator
					$('.displayTxt').hide();

				},
				success: function(data) {

					console.log(data);

					let continueScript = true;

					if ( continueScript === false ) {
						return;
					} else {
						// console.log('AJAX Response:', JSON.parse(data)); // debug log

						let response = data;
						// handle response
						// if success, redirect to dashboard
						// if error, display error message

						console.log(response.status); // debug log

						if (response.status) {

							console.log('Login successful'); // debug log

							// redirect to the specified URL on success
							// window.location.href = response.redirect_url;

							console.log('Redirecting to:', response.redirect); // debug log
							$('.displayTxt').text('Login successful! Redirecting...').show();

							// setTimeout(function() {
							window.location.href = response.redirect;
							// }, 1500);

						} else {
							// display error message
							$('#error').text(response.error).show();
						}
					}

				},
				error: function(xhr, status, error) {
					// handle error
					console.error('AJAX Error: ' + status + error);
					$('#errorMessage').text('An error occurred. Please try again.').show();
				}
			});
		}
	});
});


// check if text inputs are empty
function checkInputs() {
	const username = $('#username').val();
	const password = $('#password').val();
	if (username === '' || password === '') {
		$('#submitBtn').prop('disabled', true);
	} else {
		$('#submitBtn').prop('disabled', false);
	}
}