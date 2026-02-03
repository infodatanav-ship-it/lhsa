<!-- ======  UPLOAD MODAL  ====== -->
<div id="uploadModal" class="modal">
	<div class="modal-content">
		<span class="modal-close">&times;</span>
		<h2>Upload Document</h2>

		<form id="uploadForm" method="post" action="upload_save.php" enctype="multipart/form-data" class="user-form">
			<input type="hidden" name="csrf" value="<?= csrf() ?>">

			<div class="form-group">
				<label>Choose file (max 5 MBs)</label>
				<input type="file" name="file" required accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.zip">
			</div>

			<div class="form-group">
				<label>Choose Folder Location</label>
				<select name="folder-list" id="folder-list">
					<option value="">-------------------</option>
				</select>
			</div>

			<div class="form-group">
				<label>Group Permissions</label>
				<select name="groups" id="groups">
					<option value="">-------------------</option>
				</select>

			</div>

			<button type="submit" class="btn btn-primary">Upload</button>
		</form>
	</div>
</div>