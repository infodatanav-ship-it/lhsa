/* ----  modal behaviour  ---- */
const modal  = document.getElementById('uploadModal');
const btn    = document.getElementById('uploadBtn');
const span   = document.querySelector('.modal-close');

btn.onclick   = () => modal.style.display = 'flex';
span.onclick  = () => modal.style.display = 'none';
window.onclick = e => { if (e.target == modal) modal.style.display = 'none'; }


// jquery initialize
$(document).ready(function() {

	// populate dropdown
	const inputData = { type: 'folders' };
	populateFolderDropdown(inputData);

});

function populateFolderDropdown(inputData) {
	console.log('populateFolderDropdown');
	$.ajax({
		url: './api_data.php',
		method: 'GET',
		dataType: 'json',
		data: inputData,
		success: function(data) {

			const $select = $('#folder-list');

			$select.empty(); // clear existing options

			$.each(data, function(i, item) {
				$select.append($('<option>', {
					value: item.id,
					text: item.file_path
				}));
			});

		}
	});
}