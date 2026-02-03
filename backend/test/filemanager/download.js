$(document).ready(function() {
	$('#downloadBtn').click(function() {
		var filename = $(this).data('filename');
		downloadFileViaAjax(filename);
	});

	function downloadFileViaAjax(filename) {
		$('#status').html('Downloading...');
		
		$.ajax({
			url: 'ajax_download.php',
			type: 'POST',
			data: { 
				file: filename,
				action: 'download'
			},
			xhrFields: {
				responseType: 'blob' // Important for binary data
			},
			success: function(data, status, xhr) {
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
				
				// Create blob and download
				var blob = new Blob([data]);
				var downloadUrl = URL.createObjectURL(blob);
				
				// Create temporary link
				var a = document.createElement('a');
				a.href = downloadUrl;
				a.download = filename;
				document.body.appendChild(a);
				a.click();
				
				// Clean up
				setTimeout(function() {
					document.body.removeChild(a);
					URL.revokeObjectObjectURL(downloadUrl);
				}, 100);
			},
			error: function(xhr, status, error) {
				$('#status').html('Download failed: ' + error);
			},
			beforeSend: function() {
				$('#status').html('Starting download...');
			}
		});
	}
});