<!-- ====== NEW FOLDER MODAL ====== -->
<div id="folderModal" class="modal">
	<div class="modal-content">
		<span class="modal-close">&times;</span>
		<h2>New Folder</h2>
		<form id="folderForm">
			<input type="hidden" name="csrf" value="<?= csrf() ?>">
			<input type="hidden" name="parent_id" id="folderParent" value="<?= $folderId ?>">
			<div class="form-group">
				<label>Folder Name</label>
				<input type="text" name="name" required maxlength="60">
			</div>
			<button type="submit" class="btn btn-primary">Create</button>
		</form>
	</div>
</div>

<!-- ====== UPLOAD FILE MODAL ====== -->
<div id="uploadModal" class="modal">
	<div class="modal-content">
		<span class="modal-close">&times;</span>
		<h2>Upload File</h2>
		<form id="uploadForm" enctype="multipart/form-data">
			<input type="hidden" name="csrf" value="<?= csrf() ?>">
			<input type="hidden" name="parent_id" id="uploadParent" value="<?= $folderId ?>">

			<!-- <div class="form-group">
				<label>Choose file (max 5 MB)</label>
				<input type="file" name="file" required>
			</div> -->

			<div class="form-group">
				<!-- list dropdown of folder name with select -->

				<select name="parent_id" id="parent_id">
					<option value="">-------------------</option>
					<?php foreach ($folderList as $folder): ?>
						<option value="<?= $folder['id'] ?>"><?= htmlspecialchars($folder['filename']) ?></option>
					<?php endforeach; ?>
				</select>

			</div>

			<div class="form-group">
				<label for="file-upload">Choose a file: (max 5 MB)</label>
				<input type="file" name="file" id="file-upload" class="custom-file-input">
			</div>

			<button type="submit" class="btn btn-primary">Upload</button>
		</form>
	</div>
</div>