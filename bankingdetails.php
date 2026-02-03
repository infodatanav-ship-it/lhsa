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
					<h2>Banking Details:</h2>
					<div class='history-content'>

							Bank: NEDBANK<br />
							Account Name: LUCKHOFF HIGH SCHOOL ALUMNI NPC (LHSA NPC)<br />
							Account Nr.: 122 937 8790<br />
							Branch Code: 107 11 000<br />
							Reference: Name & Surname & Year @ Luckhoff<br /><br /><br />


							Please send proof of payment to 1 contact person below:<br /><br />

							1. Vincent Parrot - 082 949 6781<br />
							1. Stanley Amos - 082 464 6283<br /><br /><br />


							<div class="pdf-links">
									
								<div class="pdf-link" data-pdf="DOC-Proof-bank-account.pdf">
									<div class="pdf-icon">
										<i class="fas fa-file-pdf"></i>
									</div>
									<div class="pdf-title">LHSA Proof of Bank Account</div>
									<div class="pdf-desc">LHSA Proof of Bank Account</div>
									<button class="view-btn">View PDF</button>
								</div>

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