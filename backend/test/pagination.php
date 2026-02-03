<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>User Group Management</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}
		
		body {
			background-color: #f5f7fa;
			color: #333;
			line-height: 1.6;
			padding: 20px;
		}
		
		.container {
			max-width: 1200px;
			margin: 0 auto;
			padding: 20px;
		}
		
		header {
			text-align: center;
			margin-bottom: 30px;
			padding: 20px;
			background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
			color: white;
			border-radius: 10px;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
		}
		
		h1 {
			font-size: 2.5rem;
			margin-bottom: 10px;
		}
		
		.description {
			font-size: 1.1rem;
			opacity: 0.9;
		}
		
		.dashboard {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 25px;
			margin-bottom: 30px;
		}
		
		.card {
			background: white;
			border-radius: 10px;
			padding: 25px;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
		}
		
		.card h2 {
			color: #2c3e50;
			margin-bottom: 20px;
			padding-bottom: 10px;
			border-bottom: 2px solid #eaeaea;
		}
		
		.form-group {
			margin-bottom: 20px;
		}
		
		label {
			display: block;
			margin-bottom: 8px;
			font-weight: 600;
			color: #34495e;
		}
		
		select, input {
			width: 100%;
			padding: 12px 15px;
			border: 1px solid #ddd;
			border-radius: 6px;
			font-size: 16px;
			transition: border-color 0.3s;
		}
		
		select:focus, input:focus {
			border-color: #3498db;
			outline: none;
			box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
		}
		
		button {
			background: #3498db;
			color: white;
			border: none;
			padding: 12px 20px;
			border-radius: 6px;
			cursor: pointer;
			font-size: 16px;
			font-weight: 600;
			transition: background 0.3s;
		}
		
		button:hover {
			background: #2980b9;
		}
		
		.message {
			padding: 12px;
			border-radius: 6px;
			margin-top: 15px;
			display: none;
		}
		
		.success {
			background: #d4edda;
			color: #155724;
			border: 1px solid #c3e6cb;
		}
		
		.error {
			background: #f8d7da;
			color: #721c24;
			border: 1px solid #f5c6cb;
		}
		
		.user-list {
			margin-top: 20px;
		}
		
		.user-item {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 15px;
			border-bottom: 1px solid #eee;
			transition: background 0.2s;
		}
		
		.user-item:hover {
			background: #f9f9f9;
		}
		
		.user-info {
			flex: 1;
		}
		
		.user-name {
			font-weight: 600;
			color: #2c3e50;
		}
		
		.user-email {
			color: #7f8c8d;
			font-size: 0.9rem;
		}
		
		.remove-btn {
			background: #e74c3c;
			padding: 8px 12px;
			font-size: 14px;
		}
		
		.remove-btn:hover {
			background: #c0392b;
		}
		
		.pagination {
			display: flex;
			justify-content: center;
			margin-top: 25px;
			gap: 8px;
		}
		
		.pagination button {
			padding: 8px 15px;
			background: #95a5a6;
			min-width: 40px;
		}
		
		.pagination button.active {
			background: #3498db;
		}
		
		.pagination button:hover:not(.active) {
			background: #7f8c8d;
		}
		
		.stats {
			display: flex;
			justify-content: space-around;
			text-align: center;
			margin-bottom: 30px;
		}
		
		.stat-card {
			background: white;
			padding: 20px;
			border-radius: 10px;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
			flex: 1;
			margin: 0 10px;
		}
		
		.stat-number {
			font-size: 2.5rem;
			font-weight: 700;
			color: #3498db;
			margin: 10px 0;
		}
		
		.stat-title {
			color: #7f8c8d;
			font-weight: 600;
		}
		
		@media (max-width: 900px) {
			.dashboard {
				grid-template-columns: 1fr;
			}
			
			.stats {
				flex-direction: column;
				gap: 15px;
			}
			
			.stat-card {
				margin: 5px 0;
			}
		}
	</style>
</head>
<body>
	<div class="container">
		<header>
			<h1>User Group Management System</h1>
			<p class="description">Easily manage users and groups with this interactive dashboard</p>
		</header>
		
		<div class="stats">
			<div class="stat-card">
				<div class="stat-title">Total Users</div>
				<div class="stat-number">124</div>
				<div class="stat-desc">Registered in system</div>
			</div>
			<div class="stat-card">
				<div class="stat-title">Total Groups</div>
				<div class="stat-number">8</div>
				<div class="stat-desc">Active groups</div>
			</div>
			<div class="stat-card">
				<div class="stat-title">Avg. per Group</div>
				<div class="stat-number">15.5</div>
				<div class="stat-desc">Users per group</div>
			</div>
		</div>
		
		<div class="dashboard">
			<div class="card">
				<h2>Add User to Group</h2>
				<div class="form-group">
					<label for="userSelect">Select User:</label>
					<select id="userSelect">
						<option value="">Loading users...</option>
					</select>
				</div>
				
				<div class="form-group">
					<label for="groupSelect">Select Group:</label>
					<select id="groupSelect">
						<option value="">Loading groups...</option>
					</select>
				</div>
				
				<button id="addUserBtn">Add User to Group</button>
				<div id="addMessage" class="message"></div>
			</div>

			<div class="card">
				<h2>View Group Members</h2>
				<div class="form-group">
					<label for="viewGroupSelect">Select Group:</label>
					<select id="viewGroupSelect">
						<option value="">Loading groups...</option>
					</select>
				</div>
				
				<div id="groupUsers">
					<p class="select-group-prompt">Please select a group to view its members</p>
				</div>
				
				<div class="pagination" id="pagination" style="display: none;">
					<button id="prevPage"><i class="fas fa-chevron-left"></i></button>
					<button id="page3">3</button>
					<button id="page2">2</button>
					<button id="page1" class="active">1</button>
					<button id="nextPage"><i class="fas fa-chevron-right"></i></button>
				</div>
			</div>
		</div>
	</div>

	<script>
		// Current page state
		let currentPage = 1;
		const usersPerPage = 5;
		let totalUsers = 0;
		let currentGroupId = null;

		$(document).ready(function() {
			// Simulate loading users and groups
			simulateLoadData();
			
			// Add user to group
			$('#addUserBtn').click(function() {
				const userId = $('#userSelect').val();
				const groupId = $('#groupSelect').val();
				
				if (!userId || !groupId) {
					showMessage('addMessage', 'Please select both user and group', 'error');
					return;
				}

				// Simulate API call
				simulateAddUserToGroup(userId, groupId);
			});

			// Load group users when group selection changes
			$('#viewGroupSelect').change(function() {
				const groupId = $(this).val();
				if (groupId) {
					currentGroupId = groupId;
					currentPage = 1;
					loadGroupUsers(groupId, currentPage);
				} else {
					$('#groupUsers').html('<p class="select-group-prompt">Please select a group to view its members</p>');
					$('#pagination').hide();
				}
			});

			// Pagination handlers
			$('#prevPage').click(function() {
				if (currentPage > 1) {
					currentPage--;
					loadGroupUsers(currentGroupId, currentPage);
				}
			});

			$('#nextPage').click(function() {
				if (currentPage < Math.ceil(totalUsers / usersPerPage)) {
					currentPage++;
					loadGroupUsers(currentGroupId, currentPage);
				}
			});

			$('[id^="page"]').not('#nextPage, #prevPage').click(function() {
				currentPage = parseInt($(this).text());
				loadGroupUsers(currentGroupId, currentPage);
			});

			// Remove user from group
			$(document).on('click', '.remove-user', function() {
				const userId = $(this).data('user-id');
				const groupId = $('#viewGroupSelect').val();
				
				if (confirm('Are you sure you want to remove this user from the group?')) {
					// Simulate API call
					simulateRemoveUserFromGroup(userId, groupId);
				}
			});
		});

		// Simulate loading users and groups
		function simulateLoadData() {
			// Simulate users data
			const users = [
				{id: 1, username: 'john_doe', email: 'john@example.com'},
				{id: 2, username: 'jane_smith', email: 'jane@example.com'},
				{id: 3, username: 'mike_jones', email: 'mike@example.com'},
				{id: 4, username: 'sarah_williams', email: 'sarah@example.com'},
				{id: 5, username: 'david_brown', email: 'david@example.com'}
			];
			
			// Simulate groups data
			const groups = [
				{id: 1, name: 'Administrators', description: 'System administrators'},
				{id: 2, name: 'Editors', description: 'Content editors'},
				{id: 3, name: 'Authors', description: 'Content authors'},
				{id: 4, name: 'Subscribers', description: 'Regular subscribers'}
			];
			
			// Populate user select
			const userSelect = $('#userSelect');
			userSelect.empty();
			userSelect.append('<option value="">Select a user</option>');
			users.forEach(user => {
				userSelect.append(`<option value="${user.id}">${user.username} (${user.email})</option>`);
			});
			
			// Populate group selects
			const groupSelect = $('#groupSelect');
			const viewGroupSelect = $('#viewGroupSelect');
			
			groupSelect.empty();
			groupSelect.append('<option value="">Select a group</option>');
			
			viewGroupSelect.empty();
			viewGroupSelect.append('<option value="">Select a group</option>');
			
			groups.forEach(group => {
				groupSelect.append(`<option value="${group.id}">${group.name}</option>`);
				viewGroupSelect.append(`<option value="${group.id}">${group.name}</option>`);
			});
		}

		// Load group users with pagination
		function loadGroupUsers(groupId, page) {
			// Simulate API call to get group users
			const allUsers = [
				{id: 1, username: 'john_doe', email: 'john@example.com'},
				{id: 2, username: 'jane_smith', email: 'jane@example.com'},
				{id: 3, username: 'mike_jones', email: 'mike@example.com'},
				{id: 4, username: 'sarah_williams', email: 'sarah@example.com'},
				{id: 5, username: 'david_brown', email: 'david@example.com'},
				{id: 6, username: 'emma_wilson', email: 'emma@example.com'},
				{id: 7, username: 'alex_taylor', email: 'alex@example.com'},
				{id: 8, username: 'olivia_moore', email: 'olivia@example.com'},
				{id: 9, username: 'james_anderson', email: 'james@example.com'},
				{id: 10, username: 'lisa_martin', email: 'lisa@example.com'},
				{id: 11, username: 'robert_clark', email: 'robert@example.com'},
				{id: 12, username: 'sophia_rodriguez', email: 'sophia@example.com'}
			];
			
			totalUsers = allUsers.length;
			const totalPages = Math.ceil(totalUsers / usersPerPage);
			
			// Calculate start and end index for current page
			const startIndex = (page - 1) * usersPerPage;
			const endIndex = Math.min(startIndex + usersPerPage, totalUsers);
			const pageUsers = allUsers.slice(startIndex, endIndex);
			
			// Update pagination UI
			updatePaginationUI(page, totalPages);
			
			// Display users
			const container = $('#groupUsers');
			
			if (pageUsers.length === 0) {
				container.html('<p>No users in this group</p>');
				$('#pagination').hide();
				return;
			}
			
			let html = '<h3>Group Members:</h3><div class="user-list">';
			pageUsers.forEach(user => {
				html += `
					<div class="user-item">
						<div class="user-info">
							<div class="user-name">${user.username}</div>
							<div class="user-email">${user.email}</div>
						</div>
						<button class="remove-user" data-user-id="${user.id}">Remove</button>
					</div>
				`;
			});
			html += '</div>';
			
			container.html(html);
			$('#pagination').show();
		}

		// Update pagination UI
		function updatePaginationUI(currentPage, totalPages) {
			// Clear existing pagination except for prev/next buttons
			$('[id^="page"]').not('#nextPage, #prevPage').remove();
			
			// Add page buttons
			const pagination = $('#pagination');
			const prevButton = $('#prevPage');
			
			// Insert page numbers after prev button
			for (let i = 1; i <= totalPages; i++) {
				const pageButton = $(`<button id="page${i}">${i}</button>`);
				if (i === currentPage) {
					pageButton.addClass('active');
				}
				pageButton.insertAfter(prevButton);
			}
			
			// Enable/disable prev button
			if (currentPage === 1) {
				$('#prevPage').prop('disabled', true).css('opacity', '0.5');
			} else {
				$('#prevPage').prop('disabled', false).css('opacity', '1');
			}
			
			// Enable/disable next button
			if (currentPage === totalPages) {
				$('#nextPage').prop('disabled', true).css('opacity', '0.5');
			} else {
				$('#nextPage').prop('disabled', false).css('opacity', '1');
			}
		}

		// Show message
		function showMessage(elementId, message, type) {
			const element = $('#' + elementId);
			element.removeClass('success error').addClass(type).text(message).show();
			
			// Clear message after 3 seconds
			setTimeout(function() {
				element.hide();
			}, 3000);
		}

		// Simulate adding user to group
		function simulateAddUserToGroup(userId, groupId) {
			// In a real application, this would be an AJAX call to the server
			console.log(`Adding user ${userId} to group ${groupId}`);
			
			// Simulate success
			showMessage('addMessage', 'User added to group successfully!', 'success');
			
			// If we're currently viewing the group we added to, refresh the view
			if (currentGroupId == groupId) {
				loadGroupUsers(currentGroupId, currentPage);
			}
			
			// Clear selections
			$('#userSelect').val('');
			$('#groupSelect').val('');
		}

		// Simulate removing user from group
		function simulateRemoveUserFromGroup(userId, groupId) {
			// In a real application, this would be an AJAX call to the server
			console.log(`Removing user ${userId} from group ${groupId}`);
			
			// Simulate success
			alert('User removed from group successfully!');
			
			// Refresh the current view
			loadGroupUsers(currentGroupId, currentPage);
		}
	</script>
</body>
</html>