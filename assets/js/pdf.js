$(document).ready(function() {
	// Set the PDF.js worker path
	pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';
	
	let currentPdf = null;
	let currentPage = 1;
	let pdfDoc = null;
	let pageRendering = false;
	let pageNumPending = null;
	let scale = 1.5;
	
	// Open modal when a PDF link is clicked
	$('.pdf-link').on('click', function() {
		const pdfFile = $(this).data('pdf');
		const title = $(this).find('.pdf-title').text();
		
		$('#modalTitle').text(title);
		$('#downloadLink').attr('href', pdfFile);
		
		// Load the PDF
		loadPdf(pdfFile);
		
		// Show the modal
		$('#pdfModal').fadeIn(300).addClass('active');
		$('body').css('overflow', 'hidden');
	});
	
	// Close modal when close button is clicked
	$('.close-btn').on('click', function() {
		closeModal();
	});
	
	// Close modal when clicking outside the content
	$('#pdfModal').on('click', function(e) {
		if ($(e.target).attr('id') === 'pdfModal') {
			closeModal();
		}
	});
	
	// Navigate to previous page
	$('#prevPage').on('click', function() {
		if (currentPage <= 1) return;
		currentPage--;
		renderPage(currentPage);
	});
	
	// Navigate to next page
	$('#nextPage').on('click', function() {
		if (currentPage >= pdfDoc.numPages) return;
		currentPage++;
		renderPage(currentPage);
	});
	
	// Zoom in
	$('#zoomIn').on('click', function() {
		scale += 0.25;
		renderPage(currentPage);
	});
	
	// Zoom out
	$('#zoomOut').on('click', function() {
		if (scale <= 0.5) return;
		scale -= 0.25;
		renderPage(currentPage);
	});
	
	// Close modal function
	function closeModal() {
		$('#pdfModal').fadeOut(300).removeClass('active');
		$('body').css('overflow', 'auto');
		
		// Clean up
		if (currentPdf) {
			currentPdf = null;
		}
		
		$('#pdfViewer').html('<div class="loading">Loading PDF...</div>');
		currentPage = 1;
		scale = 1.5;
	}
	
	// Load PDF function
	function loadPdf(url) {
		currentPdf = './assets/uploads/' + url;
		console.log(currentPdf);
		// Load the PDF document
		const loadingTask = pdfjsLib.getDocument(currentPdf);
		loadingTask.promise.then(function(pdf) {
			pdfDoc = pdf;
			console.log();
			$('#totalPages').text(pdf.numPages);
			
			// Enable/disable buttons
			$('#prevPage').prop('disabled', currentPage <= 1);
			$('#nextPage').prop('disabled', currentPage >= pdf.numPages);
			
			// Render the first page
			renderPage(currentPage);
		}).catch(function(error) {
			console.error('Error loading PDF:', error);
			$('#pdfViewer').html('<div class="loading">Error loading PDF. Please try again.</div>');
		});
	}
	
	// Render page function
	function renderPage(pageNum) {
		pageRendering = true;
		
		// Update page display
		$('#currentPage').text(pageNum);
		
		// Enable/disable buttons
		$('#prevPage').prop('disabled', pageNum <= 1);
		$('#nextPage').prop('disabled', pageNum >= pdfDoc.numPages);
		
		// Get page
		pdfDoc.getPage(pageNum).then(function(page) {
			const viewport = page.getViewport({ scale: scale });
			
			// Prepare canvas
			const canvas = document.createElement('canvas');
			const context = canvas.getContext('2d');
			canvas.height = viewport.height;
			canvas.width = viewport.width;
			
			// Clear the viewer
			$('#pdfViewer').html(canvas);
			
			// Render PDF page
			const renderContext = {
				canvasContext: context,
				viewport: viewport
			};
			
			const renderTask = page.render(renderContext);
			
			renderTask.promise.then(function() {
				pageRendering = false;
				
				if (pageNumPending !== null) {
					renderPage(pageNumPending);
					pageNumPending = null;
				}
			});
		});
	}
});