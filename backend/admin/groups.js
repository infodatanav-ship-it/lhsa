$(document).ready(function() {

	$('#manageMembersBtn').on('click', function() {
		// console.log("Members Modal button clicked");
		$('#membersModal').css('display', 'flex');
	});
	$('.modal-close').on('click', function() {
		$('#membersModal').css('display', 'none');
	});

	/* admin/groups.js – jQuery */
	const $modal = $('#groupModal'), $form = $('#groupForm'), $table = $('.tbl tbody');

	/* open add modal */
	$('#addGroupBtn').on('click', function () {
		console.log("Add Group button clicked");
		$('#modalTitle').text('Add Group');
		$('#group_id').val('');
		$('#gname').val('');
		$modal.css('display','flex');
	});

	// dit is reg met my 

	/* open edit modal */
	$table.on('click', '.edit', function (e) {
		e.preventDefault();
		console.log('sdsd');
		const row = $(this).closest('tr'), id = row.data('id'), name = row.find('.gname').text();
		$('#modalTitle').text('Edit Group');
		$('#group_id').val(id);
		$('#gname').val(name);
		$modal.css('display','flex');
	});

	/* delete */
	$table.on('click', '.delete', function (e) {
		e.preventDefault();
		if (!confirm('Delete this group?')) return;
		const id = $(this).closest('tr').data('id');
		$.post('', {delete_id:id, csrf:$('[name=csrf]').val()}, res => {
			if (res.ok) location.reload();
			else alert(res.msg||'Error');
		}, 'json');
	});

	/* save (add or edit) */
	$form.on('submit', function (e) {
		e.preventDefault();
		$.post('group_save.php', $(this).serialize(), res => {
			if (res.ok) location.reload();
			else alert(res.msg||'Error');
		}, 'json');
	});

	/* close modal */
	$('.modal-close, window').on('click', function (e) {
		if (e.target === this) $modal.hide();
	});


	/* ======  GROUP MEMBERS  ====== */
	const $memModal = $('#membersModal'), $memForm = $('#membersForm');
	const $avail    = $('#availList'), $member = $('#memberList');

	/* open members modal */
	$('.tbl').on('click', '.users', function (e) {
		e.preventDefault();
		console.log("Members Modal button clicked");
		const gid  = $(this).data('id');
		const gname= $(this).data('name');
		$('#group_id').val(gid);
		$('#membersTitle').text('Members of «'+gname+'»');

		// fetch users & members
		$.getJSON('members_data.php', {group_id:gid}, res => {
			console.log('bevan');
			if (!res.ok) return alert(res.msg);
			$avail.empty();
			res.avail.forEach(u=> $avail.append(`<option value="${u.id}">${u.username}</option>`));
			$member.empty();
			res.members.forEach(u=> $member.append(`<option value="${u.id}">${u.username}</option>`));
			$memModal.css('display','flex');
		});
	});

	/* move buttons */
	$('#addBtn').on('click', () => $avail.find(':selected').appendTo($member));
	$('#remBtn').on('click', () => $member.find(':selected').appendTo($avail));

	/* save members */
	$memForm.on('submit', function (e) {
		e.preventDefault();
		const users = [...$member.find('option')].map(o=>o.value);
		$.post('members_save.php', {
			csrf:$('[name=csrf]').val(),
			group_id:$('#group_id').val(),
			users:users
		}, res => {
			if (res.ok) location.reload();
			else alert(res.msg||'Error');
		},'json');
	});

	/* close modal */
	$('.modal-close, window').on('click', function (e) {
		if (e.target === this) $memModal.hide();
	});
});