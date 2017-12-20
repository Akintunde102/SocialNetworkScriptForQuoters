// magic.js
<?php echo 'http://'.$site_address.'/addquote.php';?>
$(document).ready(function() {

	// process the form
	$('form').submit(function(event) {

		$('.quote-group').removeClass('has-error'); // remove the error class
		$('.error').remove(); // remove the error text

		// get the form data
		// there are many ways to get this data using jQuery (you can use the class or id also)
		var formData = {
			'quote' 				: $('input[name=quote]').val(),
			'tags' 			: $('input[name=tags]').val(),
			'desc' 	: $('input[name=desc]').val()
		};

		// process the form
		$.ajax({
			type 		: 'POST', // define the type of HTTP verb we want to use (POST for our form)
			url 		: "<?php echo 'http://'.$site_address.'/addquote.php';?>", // the url where we want to POST
			data 		: formData, // our data object
			dataType 	: 'json', // what type of data do we expect back from the server
			encode 		: true
		})
			// using the done promise callback
			.done(function(data) {

				// log data to the console so we can see
				console.log(data); 

				// here we will handle errors and validation messages
				if ( ! data.success) {
					// handle errors for name ---------------
					if (data.errors.quote) {
						$('#quote-group').addClass('has-error'); // add the error class to show red input
						$('#result').append('<p class="error"><i class="fa fa-asterisk">' + data.errors.quote + '</i></p>'); // add the actual error message under our input
					}
				} else {
					// ALL GOOD! just show the success message!
					$('result').append('<div class="alert alert-success">' + data.message + '</div>');
				}
			})

			// using the fail promise callback
			.fail(function(data) {

				// show any errors
				// best to remove for production
				console.log(data);
			});

		// stop the form from submitting the normal way and refreshing the page
		event.preventDefault();
	});

});
