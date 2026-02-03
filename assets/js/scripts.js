// Add smooth scrolling and interactive effects
document.addEventListener('DOMContentLoaded', function() {
	// Mobile menu functionality
	const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
	const navMenu = document.querySelector('.nav-menu');
	
	mobileMenuBtn.addEventListener('click', function() {
		this.classList.toggle('active');
		navMenu.classList.toggle('active');
	});
	
	// Mobile dropdown functionality
	document.querySelectorAll('.nav-link').forEach(link => {
		if (link.nextElementSibling && link.nextElementSibling.classList.contains('dropdown')) {
			link.addEventListener('click', function(e) {
				if (window.innerWidth <= 768) {
					e.preventDefault();
					const dropdown = this.nextElementSibling;
					dropdown.classList.toggle('active');
				}
			});
		}
	});
	
	// Mobile sub-dropdown functionality
	document.querySelectorAll('.dropdown-link').forEach(link => {
		if (link.nextElementSibling && link.nextElementSibling.classList.contains('sub-dropdown')) {
			link.addEventListener('click', function(e) {
				if (window.innerWidth <= 768) {
					e.preventDefault();
					const subDropdown = this.nextElementSibling;
					subDropdown.classList.toggle('active');
				}
			});
		}
	});
	
	// Close menu when clicking outside on mobile
	document.addEventListener('click', function(e) {
		if (window.innerWidth <= 768 && 
			!e.target.closest('.nav-menu') && 
			!e.target.closest('.mobile-menu-btn') &&
			navMenu.classList.contains('active')) {
			mobileMenuBtn.classList.remove('active');
			navMenu.classList.remove('active');
			
			// Also close any open dropdowns
			document.querySelectorAll('.dropdown.active, .sub-dropdown.active').forEach(item => {
				item.classList.remove('active');
			});
		}
	});

	// Smooth scrolling for anchor links
	document.querySelectorAll('a[href^="#"]').forEach(anchor => {
		anchor.addEventListener('click', function (e) {
			e.preventDefault();
			const target = document.querySelector(this.getAttribute('href'));
			if (target) {
				target.scrollIntoView({
					behavior: 'smooth',
					block: 'start'
				});
			}
		});
	});

	// Header scroll effect
	window.addEventListener('scroll', function() {
		const header = document.querySelector('header');
		if (window.scrollY > 100) {
			header.style.background = 'rgba(255, 255, 255, 0.98)';
			header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
		} else {
			header.style.background = 'rgba(255, 255, 255, 0.95)';
			header.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.1)';
		}
	});

	// Animate stats on scroll
	const observerOptions = {
		threshold: 0.5,
		rootMargin: '0px 0px -100px 0px'
	};

	const observer = new IntersectionObserver(function(entries) {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				const statNumbers = entry.target.querySelectorAll('.stat-number');
				statNumbers.forEach(stat => {
					const finalNumber = stat.textContent;
					const numericValue = parseInt(finalNumber.replace(/[^\d]/g, ''));
					let currentNumber = 0;
					const increment = numericValue / 50;
					
					const timer = setInterval(() => {
						currentNumber += increment;
						if (currentNumber >= numericValue) {
							stat.textContent = finalNumber;
							clearInterval(timer);
						} else {
							const displayNumber = Math.floor(currentNumber);
							if (finalNumber.includes('+')) {
								stat.textContent = displayNumber.toLocaleString() + '+';
							} else if (finalNumber.includes('B')) {
								stat.textContent = '$' + (displayNumber / 1000).toFixed(1) + 'B+';
							} else {
								stat.textContent = displayNumber.toLocaleString();
							}
						}
					}, 50);
				});
				observer.unobserve(entry.target);
			}
		});
	}, observerOptions);

	const statsSection = document.querySelector('.stats');
	if (statsSection) {
		observer.observe(statsSection);
	}

	// Add hover effects to feature cards
	const featureCards = document.querySelectorAll('.feature-card');
	featureCards.forEach(card => {
		card.addEventListener('mouseenter', function() {
			this.style.transform = 'translateY(-15px) scale(1.02)';
		});
		
		card.addEventListener('mouseleave', function() {
			this.style.transform = 'translateY(0) scale(1)';
		});
	});
});