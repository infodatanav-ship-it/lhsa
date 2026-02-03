<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Modal Viewer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 1000px;
            width: 100%;
            text-align: center;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2.5rem;
        }
        
        p {
            color: #7f8c8d;
            font-size: 1.1rem;
            margin-bottom: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        
        .pdf-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .pdf-link {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .pdf-link:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .pdf-icon {
            font-size: 48px;
            color: #e74c3c;
            margin-bottom: 15px;
        }
        
        .pdf-title {
            font-size: 1.2rem;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .pdf-desc {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-bottom: 15px;
        }
        
        .view-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .view-btn:hover {
            background: #2980b9;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .modal.active {
            opacity: 1;
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            padding: 20px;
            background: #2c3e50;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 1.4rem;
            font-weight: 600;
        }
        
        .close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.8rem;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .close-btn:hover {
            transform: rotate(90deg);
        }
        
        .pdf-controls {
            padding: 15px 20px;
            background: #ecf0f1;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .control-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background 0.3s;
        }
        
        .control-btn:hover {
            background: #2980b9;
        }
        
        .control-btn:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }
        
        .page-info {
            display: flex;
            align-items: center;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .pdf-container {
            padding: 20px;
            overflow-y: auto;
            display: flex;
            justify-content: center;
            background: #7f8c8d;
        }
        
        .pdf-viewer {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        canvas {
            max-width: 100%;
        }
        
        .loading {
            color: white;
            font-size: 1.2rem;
            padding: 40px;
        }
        
        @media (max-width: 768px) {
            .pdf-links {
                grid-template-columns: 1fr;
            }
            
            .modal-content {
                width: 95%;
            }
            
            .pdf-controls {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PDF Modal Viewer</h1>
        <p>Click on any of the PDF links below to view the document in a modal. You can navigate through pages, zoom in/out, and download the PDF.</p>
        
        <div class="pdf-links">
            <div class="pdf-link" data-pdf="document1.pdf">
                <div class="pdf-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="pdf-title">Sample Document 1</div>
                <div class="pdf-desc">A sample PDF document for demonstration purposes.</div>
                <button class="view-btn">View PDF</button>
            </div>
            
            <div class="pdf-link" data-pdf="DOC-Constitution-LHSA.pdf">
                <div class="pdf-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="pdf-title">LHSA Constitution</div>
                <div class="pdf-desc">The Constitution of LHSA.</div>
                <button class="view-btn">View PDF</button>
            </div>
            
            <div class="pdf-link" data-pdf="document3.pdf">
                <div class="pdf-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="pdf-title">Sample Document 3</div>
                <div class="pdf-desc">A third sample document with additional content.</div>
                <button class="view-btn">View PDF</button>
            </div>
        </div>
    </div>
    
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

    <script>
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
    </script>
</body>
</html>