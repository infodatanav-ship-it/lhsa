<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Luckhoff Alumni Network - Excellence Academy</title>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

	<!-- add style sheet -->
	<link rel="stylesheet" href="./assets/css/styles.css" />
	<link rel="stylesheet" href="./assets/css/adstyles.css" />

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

</head>
<body>

	<?php include './assets/components/header.php';?>

	<main>

		<?php 
			// include './assets/components/hero-section.php';
			// include './assets/components/ads-section.php';
		?>

		<div class="content">
			<div class="container">
				<section class="section">
					<h2>How we Started...</h2>
					<div class='history-content'>

						<h3>Our First Meeting</h3>

						<div class="pdf-links">
							
							<div class="pdf-link" data-pdf="doc-first-meeting-minutes.pdf">
								<div class="pdf-icon">
									<i class="fas fa-file-pdf"></i>
								</div>
								<div class="pdf-title">LHSA Constitution</div>
								<div class="pdf-desc">The Constitution of LHSA.</div>
								<button class="view-btn">View PDF</button>
							</div>

						</div>

						<div>
							<img alt="First-Meeting-Banner.jpg" style="width: 25%" src="./assets/uploads/images/First-Meeting-Banner.jpg" />
							<img alt="how-we-started-03.jpg" style="width: 25%" src="./assets/uploads/images/how-we-started-03.jpg" />
							<img alt="how-we-started-01.jpg" style="width: 35%" src="./assets/uploads/images/how-we-started-01.jpg" />
							<img alt="how-we-started-02.jpg" style="width: 35%" src="./assets/uploads/images/how-we-started-02.jpg" />
							
							<img alt="how-we-started-04.jpg" style="width: 25%" src="./assets/uploads/images/how-we-started-04.jpg" />
							<img alt="how-we-started-05.jpg" style="width: 25%" src="./assets/uploads/images/how-we-started-05.jpg" />
							<img alt="how-we-started-06.jpg" style="width: 20%" src="./assets/uploads/images/how-we-started-06.jpg" />
						</div>

					</div>
				</section>
			</div>
		</div>

		<?php include './assets/components/stats-section.php';?>
	</main>

	<?php include './assets/components/footer.php';?>

	<script type="text/javascript" src="./assets/js/scripts.js"></script>

	
</body>
</html>

<?php include './assets/components/modal.php';?>
<script type="text/javascript" src="./assets/js/pdf.js"></script>