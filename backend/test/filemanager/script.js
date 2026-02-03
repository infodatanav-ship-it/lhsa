$(document).ready(function() {
	let currentParentId = null;
	
	// Load files on page load
	loadFiles(currentParentId);
	
	// Function to load files
	function loadFiles(parentId) {

		$('.file-list').html('<div class="loading">Loading files...</div>');
		
		$.ajax({
			url: 'files.php',
			type: 'GET',
			data: { parent_id: parentId },
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					displayFiles(response.files);
					displayBreadcrumb(response.breadcrumb);
					currentParentId = parentId;
				} else {
					showError(response.message);
				}
			},
			error: function(xhr, status, error) {
				showError('Error loading files: ' + error);
			}
		});
	}
	
	// Function to display files
	function displayFiles(files) {

		if (files.length === 0) {
			$('.file-list').html('<div class="empty-folder">This folder is empty</div>');
			return;
		}
		
		let html = '<div class="file-header">';
		html += '<div>Name</div>';
		html += '<div>Size</div>';
		html += '<div>Type</div>';
		html += '</div>';
		
		files.forEach(function(file) {
			html += '<div class="file-item" data-id="' + file.id + '" data-type="' + file.type + '">';
			html += '<div class="file-name">';
			html += '<span class="file-icon">' + file.icon + '</span>';
			html += '<span class="' + (file.type === 'directory' ? 'directory' : '') + '">' + file.name + '</span>';
			html += '</div>';
			html += '<div class="file-size">' + (file.type === 'directory' ? '----' : file.formatted_size) + '</div>';
			html += '<div class="file-type">' + file.type + '</div>';
			html += '</div>';
		});
		
		$('.file-list').html(html);
		
		// Add click event to directories
		$('.file-item[data-type="directory"]').click(function() {
			const folderId = $(this).data('id');
			loadFiles(folderId);
		});
		
		// Add click event to files (optional - could open preview/download)
		$('.file-item[data-type="file"]').click(function() {
			const fileName = $(this).find('.file-name span:last').text();
			// console.log(fileName);
			// alert('File clicked: ' + fileName + '\nThis could open a preview or download.');
			downloadFileViaAjax(fileName);
		});
	}

	function downloadFileViaAjax(filename) {
		$('#status').html('Downloading...');

		console.log(filename);
		
		$.ajax({
			url: './download.php',
			type: 'POST',
			data: { 
				file: filename,
				action: 'download'
			},
			xhrFields: {
				responseType: 'blob' // Important for binary data
			},
			success: function(data, status, xhr) {

				console.log(data);
				console.log(status);
				// console.log(xhr);

				$('#status').html('Download completed!');
				
				// Get filename from response headers or use default
				var filename = 'downloaded_file';
				var disposition = xhr.getResponseHeader('Content-Disposition');
				if (disposition && disposition.indexOf('attachment') !== -1) {
					var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
					var matches = filenameRegex.exec(disposition);
					if (matches != null && matches[1]) {
						filename = matches[1].replace(/['"]/g, '');
					}
				}

				// create blob and download
				var blob = new Blob([data]);
				var downloadUrl = URL.createObjectURL(blob);
				
				// Create temporary link
				var a = document.createElement('a');
				a.href = downloadUrl;
				a.download = filename;
				document.body.appendChild(a);
				a.click();

				// clean up
				// setTimeout(function() {
				// 	document.body.removeChild(a);
				// 	URL.revokeObjectObjectURL(downloadUrl);
				// }, 100);
			},
			error: function(xhr, status, error) {
				$('#status').html('Download failed: ' + error);
			},
			beforeSend: function() {
				$('#status').html('Starting download...');
			}
		});
	}
	
	// Function to display breadcrumb
	function displayBreadcrumb(breadcrumb) {
		let html = '';
		
		breadcrumb.forEach(function(item, index) {
			if (index > 0) {
				html += '<span class="breadcrumb-separator">›</span>';
			}
			html += '<span class="breadcrumb-item" data-id="' + item.id + '">' + item.name + '</span>';
		});
		
		$('.breadcrumb').html(html);
		
		// Add click event to breadcrumb items
		$('.breadcrumb-item').click(function() {
			const itemId = $(this).data('id');
			loadFiles(itemId !== 'null' ? itemId : null);
		});
	}
	
	// Function to show error message
	function showError(message) {
		$('.file-list').html('<div class="error">' + message + '</div>');
	}
	
	// Double-click functionality for directories
	$(document).on('dblclick', '.file-item[data-type="directory"]', function() {
		const folderId = $(this).data('id');
		loadFiles(folderId);
	});
	
	// Keyboard navigation
	$(document).keydown(function(e) {
		if (e.key === 'Backspace' && currentParentId !== null) {
			e.preventDefault();
			// Navigate to parent directory
			$.ajax({
				url: 'files.php',
				type: 'GET',
				data: { parent_id: currentParentId },
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						const currentFile = response.files.find(f => f.id == currentParentId);
						if (currentFile && currentFile.parent_id !== null) {
							loadFiles(currentFile.parent_id);
						} else {
							loadFiles(null); // Go to root
						}
					}
				}
			});
		}
	});
});