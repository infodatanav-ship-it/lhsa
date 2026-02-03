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
					<h2>Our Constitution</h2>
					<div class='history-content'>

						<div class="pdf-links">
							
							<div class="pdf-link" data-pdf="DOC-Constitution-LHSA.pdf">
								<div class="pdf-icon">
									<i class="fas fa-file-pdf"></i>
								</div>
								<div class="pdf-title">LHSA Constitution</div>
								<div class="pdf-desc">The Constitution of LHSA.</div>
								<button class="view-btn">View PDF</button>
							</div>

						</div>

						<div class="pdf-link" data-pdf="doc-memo-of-understanding.pdf">
							<div class="pdf-icon">
								<i class="fas fa-file-pdf"></i>
							</div>
							<div class="pdf-title">LHSA Memorandum of <br />Understanding</div>
							<div class="pdf-desc">Memorandum of Understanding.</div>
							<button class="view-btn">View PDF</button>
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