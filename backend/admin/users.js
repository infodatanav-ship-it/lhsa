// init jQuery
$(document).ready(function() {
	console.log("jQuery is ready!");

	$('#addUserBtn').on('click', function() {
		$('#addModal').css('display', 'flex');
	});

	$('.modal-close').on('click', function() {
		$('#addModal').css('display', 'none');
	});

});