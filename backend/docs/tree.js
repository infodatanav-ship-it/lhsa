/* tree.js – jQuery */
/* open/close folder */
$('.tree').on('click', '.toggle', function () {
	const $node = $(this).closest('.node'), id = $node.data('id');
	const $kids = $node.nextUntil('.node[data-folder="1"]', '.child');
	if ($node.hasClass('open')) {          // collapse
		$kids.remove();
		$node.removeClass('open');
	} else {                               // expand – load children
		$.getJSON('tree_data.php', {parent:id}, res => {
			if (res.ok) {
				let html = '';
				res.items.forEach(it => {
					const icon = it.is_folder ? 'fa-folder text-warning' :
						{pdf:'fa-file-pdf text-danger',jpg:'fa-file-image text-info',zip:'fa-file-zipper text-muted'}[it.ext] || 'fa-file text-muted';
					html += `
					<div class="node child" style="margin-left:24px" data-id="${it.id}" data-folder="${it.is_folder}">
						<span class="toggle ${it.is_folder?'fa-fw fa-solid fa-chevron-right':''}"></span>
						<span class="icon"><i class="fa-fw fa-regular ${icon}"></i></span>
						<span class="name" title="${it.filename}">${it.filename.length>30?it.filename.substr(0,29)+'…':it.filename}</span>
						${!it.is_folder?`
						<span class="size">${it.hsize}</span>
						<span class="date">${it.date}</span>
						<span class="actions">
							<a href="download.php?id=${it.id}" title="Download"><i class="fa-solid fa-download"></i></a>
							<a href="#" class="delFile" data-id="${it.id}" title="Delete"><i class="fa-solid fa-trash"></i></a>
						</span>`:''}
					</div>`;
				});
				$node.after(html).addClass('open');
			}
		});
	}
});

/* modals */
$('#addFolderBtn, #uploadBtn').on('click', function () {
	const modal = $(this).is('#addFolderBtn') ? $('#folderModal') : $('#uploadModal');
	modal.css('display','flex');
});

/* save folder */
$('#folderForm').on('submit', function (e) {

	e.preventDefault();

	$.ajax({
		url: 'tree_save.php',
		type: 'POST',
		data: new FormData(this),
		contentType: false,
		processData: false,
		success: res => { if (res.ok) location.reload(); else alert(res.msg||'Error'); },
		dataType: 'json'
	});

});

/* save upload */
$('#uploadForm').on('submit', function (e) {
	console.log('upload submit');
	e.preventDefault();
	$.ajax({
		url: 'tree_save.php',
		type: 'POST',
		data: new FormData(this),
		contentType: false,
		processData: false,
		success: res => { if (res.ok) location.reload(); else alert(res.msg||'Error'); },
		dataType: 'json'
	});
});

/* delete file */
$('.tree').on('click', '.delFile', function (e) {
	e.preventDefault();
	if (!confirm('Delete this file?')) return;
	const id = $(this).data('id');
	$.post('tree_save.php', {delete_id:id, csrf:$('[name=csrf]').val()}, res => {
		if (res.ok) location.reload(); else alert(res.msg||'Error');
	},'json');
});

/* close modals */
$('.modal-close, window').on('click', function (e) {
	if (e.target === this) $('.modal').hide();
});