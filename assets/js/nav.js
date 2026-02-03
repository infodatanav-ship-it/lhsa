// Optional: close mobile menu when an item is clicked
document.querySelectorAll('.nav-list a').forEach(a =>
	a.addEventListener('click', () => {
		if (window.innerWidth <= 768) {
		document.getElementById('nav-toggle').checked = false;
		}
	})
);