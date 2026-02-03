<!DOCTYPE html>
<html>
<head>
	<title>Force File Download</title>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
	<h2>Download Files</h2>
	
	<!-- Simple link method -->
	<p><a href="download.php?file=document.pdf" class="download-link">Download PDF</a></p>
	
	<!-- Button method with jQuery -->
	<button class="download-btn" data-file="document.pdf">Download PDF</button>

	<script>
	$(document).ready(function() {
		// jQuery method
		$('.download-btn').click(function() {
			var filename = $(this).data('file');
			window.location.href = 'download.php?file=' + encodeURIComponent(filename);
		});
		
		// Alternative method using anchor click
		$('.download-link').click(function(e) {
			e.preventDefault();
			window.location.href = $(this).attr('href');
		});
	});
	</script>
</body>
</html>