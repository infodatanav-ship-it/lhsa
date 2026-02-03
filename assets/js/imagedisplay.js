// init jquery
$(document).ready(function() {
	// on hover of hero section
	console.log("jQuery is working");

	// $('.hero').css({'background-image': 'url(/assets/img/luckhoff-building-students.jpg)'});

	// Array of image paths
	var images = [
		'/assets/img/luckhoff-building-students.jpg',
		'/assets/img/history-of-luckhoff.jpg',
		'/assets/img/volkskerk_complex.png'
	];

	var currentIndex = 0;

	function changeBackground() {
		// Update background image
		$('.hero').css({
			'background-image': 'url("' + images[currentIndex] + '")'
		});

		let color = 'black';
		let pcolor = 'white';

		// Change text color based on image
		if ( currentIndex === 0) {
			color = 'white';
			pcolor = 'black';
		} else if (currentIndex === 1) {
			color = 'black';
			pcolor = 'white';
		} else {
			color = 'orange';
			pcolor = 'black';
		}

		// console.log(currentIndex + ' - ' + color + ' - ' + pcolor);

		$('.hero h1').css('color', color);
		$('.hero p').css('color', pcolor);

		// Increment index, loop back to 0 if at end
		currentIndex = (currentIndex + 1) % images.length;
	}

	// Set initial background
	changeBackground();

	// Change background every 10 seconds (10000 milliseconds)
	setInterval(changeBackground, 5000);

});

