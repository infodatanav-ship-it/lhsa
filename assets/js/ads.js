// Add subtle animation to make placeholders more engaging
document.addEventListener('DOMContentLoaded', function() {
	const adContents = document.querySelectorAll('.ad-content');
	
	adContents.forEach(content => {
		content.addEventListener('mouseenter', function() {
			this.classList.add('pulse');
		});
		
		content.addEventListener('mouseleave', function() {
			this.classList.remove('pulse');
		});
	});
	
	// Simulate ad rotation for demonstration
	setInterval(function() {
		const adTitles = document.querySelectorAll('.ad-title');
		const titleTexts = [
			"Your Ad Could Be Here",
			"Premium Advertising Space",
			"Connect With Our Audience",
			"Promote Your Business"
		];
		
		adTitles.forEach(title => {
			const randomIndex = Math.floor(Math.random() * titleTexts.length);
			title.textContent = titleTexts[randomIndex];
		});
	}, 3000);
});