<div class="modal" id="pdfModal">
	<div class="modal-content">
		<div class="modal-header">
			<div class="modal-title" id="modalTitle">PDF Document</div>
			<button class="close-btn">&times;</button>
		</div>
		
		<div class="pdf-controls">
			<button class="control-btn" id="prevPage" disabled>
				<i class="fas fa-chevron-left"></i> Previous
			</button>
			
			<div class="page-info">
				Page <span id="currentPage">1</span> of <span id="totalPages">1</span>
			</div>
			
			<button class="control-btn" id="nextPage">
				Next <i class="fas fa-chevron-right"></i>
			</button>
			
			<button class="control-btn" id="zoomIn">
				<i class="fas fa-search-plus"></i> Zoom In
			</button>
			
			<button class="control-btn" id="zoomOut">
				<i class="fas fa-search-minus"></i> Zoom Out
			</button>
			
			<a class="control-btn" id="downloadLink" href="#" target="_blank">
				<i class="fas fa-download"></i> Download
			</a>
		</div>
		
		<div class="pdf-container">
			<div class="pdf-viewer" id="pdfViewer">
				<div class="loading">Loading PDF...</div>
			</div>
		</div>
	</div>
</div>