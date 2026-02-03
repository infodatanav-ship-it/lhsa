$(document).ready(function() {
	// Load users and groups on page load
	loadUsers();
	loadGroups();
	loadGroupsForView();

	// Add user to group
	$('#addUserBtn').click(function() {

		const userId = $('#userSelect').val();
		const groupId = $('#groupSelect').val();

		console.log(userId, groupId);

		if (!userId || !groupId) {
			showMessage('addMessage', 'Please select both user and group', 'error');
			return;
		}

		$.ajax({
			url: 'api_post.php',
			type: 'POST',
			data: {
				action: 'addUserToGroup',
				user_id: userId,
				group_id: groupId
			},
			success: function(response) {
				if (response.success) {
					showMessage('addMessage', response.message, 'success');
					// Refresh the group users view if the same group is selected
					const viewGroupId = $('#viewGroupSelect').val();
					if (viewGroupId == groupId) {
						loadGroupUsers(groupId);
					}
				} else {
					showMessage('addMessage', response.message, 'error');
				}
			},
			error: function() {
				showMessage('addMessage', 'An error occurred while processing the request', 'error');
			},
			dataType: 'json'
		});

		// $.post('api.php', {
		// 	action: 'addUserToGroup',
		// 	user_id: userId,
		// 	group_id: groupId
		// }, function(response) {
		// 	if (response.success) {
		// 		showMessage('addMessage', response.message, 'success');
		// 		// Refresh the group users view if the same group is selected
		// 		const viewGroupId = $('#viewGroupSelect').val();
		// 		if (viewGroupId == groupId) {
		// 			loadGroupUsers(groupId);
		// 		}
		// 	} else {
		// 		showMessage('addMessage', response.message, 'error');
		// 	}
		// }, 'json');
	});

	// Load group users when group selection changes
	$('#viewGroupSelect').change(function() {
		const groupId = $(this).val();
		if (groupId) {
			loadGroupUsers(groupId);
		} else {
			$('#groupUsers').html('<p>Select a group to view its members</p>');
		}
	});

	// Remove user from group
	$(document).on('click', '.remove-user', function() {
		const userId = $(this).data('user-id');
		const groupId = $('#viewGroupSelect').val();
		
		if (confirm('Are you sure you want to remove this user from the group?')) {
			// $.post('api_post.php', {
			// 	action: 'removeUserFromGroup',
			// 	user_id: userId,
			// 	group_id: groupId
			// }, function(response) {
			// 	if (response.success) {
			// 		loadGroupUsers(groupId);
			// 	} else {
			// 		alert('Error: ' + response.message);
			// 	}
			// }, 'json');

			$.ajax({
				url: 'api_post.php',
				type: 'POST',
				data: {
					action: 'removeUserFromGroup',
					user_id: userId,
					group_id: groupId
				},
				success: function(response) {
					if (response.success) {
						loadGroupUsers(groupId);
					} else {
						alert('Error: ' + response.message);
					}
				},
				error: function() {
					alert('An error occurred while processing the request');
				},
				dataType: 'json'
			});
		}
	});
});

function loadUsers() {
	$.get('api_get.php?action=getUsers', function(users) {
		console.log(users);
		const select = $('#userSelect');
		select.empty();
		select.append('<option value="">Select a user</option>');
		
		users.forEach(function(user) {
			select.append(`<option value="${user.id}">${user.username} (${user.email})</option>`);
		});
	}, 'json');
}

function loadGroups() {
	$.get('api_get.php?action=getGroups', function(groups) {
		const select = $('#groupSelect');
		select.empty();
		select.append('<option value="">Select a group</option>');
		
		groups.forEach(function(group) {
			select.append(`<option value="${group.id}">${group.name}</option>`);
		});
	}, 'json');
}

function loadGroupsForView() {
	$.get('api_get.php?action=getGroups', function(groups) {
		const select = $('#viewGroupSelect');
		select.empty();
		select.append('<option value="">Select a group</option>');
		
		groups.forEach(function(group) {
			select.append(`<option value="${group.id}">${group.name}</option>`);
		});
	}, 'json');
}

function loadGroupUsers(groupId) {
	$.get(`api_get.php?action=getGroupUsers&group_id=${groupId}`, function(users) {
		const container = $('#groupUsers');
		
		if (users.length === 0) {
			container.html('<p>No users in this group</p>');
			return;
		}

		let html = '<h3>Group Members:</h3><div class="user-list">';
		users.forEach(function(user) {
			html += `
				<div class="user-item">
					<span>${user.username} (${user.email})</span>
					<button class="remove-user" data-user-id="${user.id}">Remove</button>
				</div>
			`;
		});
		html += '</div>';
		
		container.html(html);
	}, 'json');
}

function showMessage(elementId, message, type) {
	const element = $('#' + elementId);
	element.removeClass('success error').addClass(type).text(message);
	
	// Clear message after 3 seconds
	setTimeout(function() {
		element.text('').removeClass('success error');
	}, 3000);
}