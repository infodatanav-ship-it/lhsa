<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/permissions.php';

require_once './browser_permissions.php';

$baseDir = __DIR__ . '/uploads/';
$baseReal = realpath($baseDir);
if ($baseReal === false) {
    http_response_code(500);
    echo "Base browser directory is missing.";
    exit;
}

$requested = isset($_GET['path']) ? (string)$_GET['path'] : '';
$requested = trim($requested, "\0/");

// ensure CSRF token
if (empty($_SESSION['csrf_token'])) {
  try {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  } catch (Exception $e) {
    $_SESSION['csrf_token'] = substr(bin2hex(openssl_random_pseudo_bytes(32)),0,64);
  }
}
$csrf = $_SESSION['csrf_token'];

// delegate permission checks to backend/browser_permissions.php

$candidate = $baseReal . ($requested !== '' ? '/' . $requested : '');
$targetReal = realpath($candidate);
if ($targetReal === false || strpos($targetReal, $baseReal) !== 0) {
    $targetReal = $baseReal;
    $requested = '';
}

function listDirContents($path) {
    $out = ['dirs'=>[], 'files'=>[]];
    if (!is_dir($path)) return $out;
    $items = array_values(array_diff(scandir($path), ['.','..']));
    foreach ($items as $it) {
        if (is_dir($path . '/' . $it)) $out['dirs'][] = $it; else $out['files'][] = $it;
    }
    sort($out['dirs']);
    sort($out['files']);
    return $out;
}

function encodePathForUrl($p) {
    if ($p === '') return '';
    return implode('/', array_map('rawurlencode', explode('/', $p)));
}

function getFileIconSvg($ext) {
  $ext = strtolower($ext);
  if (in_array($ext, ['png','jpg','jpeg','gif','svg'])) {
    return '<svg class="icon" viewBox="0 0 24 24" fill="#10b981" xmlns="http://www.w3.org/2000/svg"><path d="M21 19V5a2 2 0 0 0-2-2H7l-2 2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2z"/><path d="M8.5 13.5l2 2 3-3 4 5H6l2.5-4.5z"/></svg>';
  }
  if ($ext === 'pdf') {
    return '<svg class="icon" viewBox="0 0 24 24" fill="#ef4444" xmlns="http://www.w3.org/2000/svg"><path d="M6 2h7l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/><text x="7" y="16" font-size="6" fill="#fff">PDF</text></svg>';
  }
  if (in_array($ext, ['txt','md','csv','log'])) {
    return '<svg class="icon" viewBox="0 0 24 24" fill="#6b7280" xmlns="http://www.w3.org/2000/svg"><path d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/></svg>';
  }
  if (in_array($ext, ['html','htm','xml','php','js','css','json'])) {
    return '<svg class="icon" viewBox="0 0 24 24" fill="#2563eb" xmlns="http://www.w3.org/2000/svg"><path d="M8.293 6.293L3.586 11l4.707 4.707 1.414-1.414L6.414 11l3.293-3.293-1.414-1.414zM15.707 6.293l-1.414 1.414L17.586 11l-3.293 3.293 1.414 1.414L20.414 11l-4.707-4.707z"/></svg>';
  }
  if (in_array($ext, ['doc','docx','xls','xlsx','ppt','pptx'])) {
    return '<svg class="icon" viewBox="0 0 24 24" fill="#7c3aed" xmlns="http://www.w3.org/2000/svg"><path d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/></svg>';
  }
  return '<svg class="icon" viewBox="0 0 24 24" fill="#9aa4b2" xmlns="http://www.w3.org/2000/svg"><path d="M6 2h7l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/></svg>';
}

// Render a nested folder tree for the sidebar. Returns HTML string.
function renderTree(string $baseReal, string $relative = ''): string {
  $path = $baseReal . ($relative === '' ? '' : '/' . $relative);
  if (!is_dir($path)) return '';
  $items = array_values(array_diff(scandir($path), ['.','..']));
  $dirs = [];
  foreach ($items as $it) {
    if (is_dir($path . '/' . $it)) $dirs[] = $it;
  }
  sort($dirs);
  if (count($dirs) === 0) return '';
  $html = '<ul class="browser-tree">';
  foreach ($dirs as $d) {
    $relPath = ($relative === '' ? $d : $relative . '/' . $d);
    $html .= '<li class="browser-tree-item"><a href="?path=' . rawurlencode($relPath) . '">' . htmlspecialchars($d) . '</a>';
    $html .= renderTree($baseReal, $relPath);
    $html .= '</li>';
  }
  $html .= '</ul>';
  return $html;
}

$contents = listDirContents($targetReal);

// build breadcrumb
$breadcrumbs = [];
$breadcrumbs[] = ['name' => 'browser', 'path' => ''];
if ($requested !== '') {
    $parts = explode('/', $requested);
    $acc = '';
    foreach ($parts as $part) {
        $acc = $acc === '' ? $part : $acc . '/' . $part;
        $breadcrumbs[] = ['name' => $part, 'path' => $acc];
    }
}

// handle delete/rename POSTs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['op'])) {
  $op = $_POST['op'];
  if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $_SESSION['browser_error'] = 'Invalid CSRF token.';
    header('Location: ?path=' . rawurlencode($requested)); exit;
  }
  if (!userCanModify()) { $_SESSION['browser_error'] = 'Permission denied.'; header('Location: ?path=' . rawurlencode($requested)); exit; }
  $msg = null;
  if ($op === 'delete') {
    $target = isset($_POST['target']) ? trim($_POST['target'], "\0/") : '';
    $full = realpath($baseReal . '/' . $target);
    if ($full === false || strpos($full, $baseReal) !== 0) {
      $msg = 'Invalid target.';
    } elseif (is_dir($full)) {
      $files = array_diff(scandir($full), ['.','..']);
      if (count($files) > 0) { $msg = 'Directory not empty.'; }
      else { if (@rmdir($full)) $_SESSION['browser_success'] = 'Deleted directory: ' . $target; else $msg = 'Failed to delete directory.'; }
    } elseif (is_file($full)) { if (@unlink($full)) $_SESSION['browser_success'] = 'Deleted file: ' . $target; else $msg = 'Failed to delete file.'; }
    else { $msg = 'Target does not exist.'; }
  } elseif ($op === 'rename') {
    $target = isset($_POST['target']) ? trim($_POST['target'], "\0/") : '';
    $newname = isset($_POST['newname']) ? trim($_POST['newname']) : '';
    $newname = preg_replace('/[^A-Za-z0-9._ -]/', '_', basename($newname));
    if ($newname === '') $msg = 'Invalid new name.';
    else {
      $full = realpath($baseReal . '/' . $target);
      if ($full === false || strpos($full, $baseReal) !== 0) { $msg = 'Invalid target.'; }
      else { $dir = dirname($full); $dest = $dir . '/' . $newname; if (file_exists($dest)) $msg = 'Destination already exists.'; else { if (@rename($full, $dest)) $_SESSION['browser_success'] = 'Renamed to: ' . $newname; else $msg = 'Rename failed.'; } }
    }
  }
  if ($msg !== null) $_SESSION['browser_error'] = $msg;
  header('Location: ?path=' . rawurlencode($requested)); exit;
}

// handle uploads
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
  $up = $_FILES['upload'];
  if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) { $_SESSION['browser_error'] = 'Invalid CSRF token.'; header('Location: ?path=' . rawurlencode($requested)); exit; }
  $err = null; $maxBytes = 10 * 1024 * 1024;
  if (!isset($up) || !isset($up['tmp_name']) || $up['tmp_name'] === '') { $err = 'No file uploaded.'; }
  elseif ($up['error'] !== UPLOAD_ERR_OK) { $err = 'Upload failed (code ' . intval($up['error']) . ').'; }
  elseif (!is_uploaded_file($up['tmp_name'])) { $err = 'Upload is not a valid uploaded file.'; }
  elseif ($up['size'] > $maxBytes) { $err = 'File too large (max 10 MB).'; }
  else {
    $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($up['name']));
    $destRel = ($requested === '' ? $safeName : $requested . '/' . $safeName);
    $destPath = $baseReal . '/' . $destRel;
    $destDir = dirname($destPath);
    if (!is_dir($destDir)) { if (!mkdir($destDir, 0755, true)) { $err = 'Failed to create destination directory.'; } }
    if ($err === null && !is_writable($destDir)) { $err = 'Destination directory is not writable.'; }
    if ($err === null) { if (!@move_uploaded_file($up['tmp_name'], $destPath)) { $err = 'Unable to move uploaded file to destination.'; } else { $_SESSION['browser_success'] = 'Uploaded: ' . $safeName; } }
  }
  if ($err !== null) $_SESSION['browser_error'] = $err;
  header('Location: ?path=' . rawurlencode($requested)); exit;
}

$contents = listDirContents($targetReal);

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Dashboard</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../auth/auth.css">
	<link rel="stylesheet" href="../docs/docs.css">
	<!-- re-use table styles -->
  <link rel="stylesheet" href="../admin/dashboard.css">

	<link rel="stylesheet" href="./../includes/nav.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body class="browser-page">


<div style="display: flex; flex-direction: column; align-items: center;">

  <?php include __DIR__ . '/../includes/nav.php'; ?>

  <h1>Browsers</h1>

  <div class="browser-root">
    <aside class="browser-aside"><div class="panel"><div class="panel-title">Folders</div><?php echo renderTree($baseReal); ?></div></aside>
    <div class="browser-main">

  <?php if (!empty($_SESSION['browser_error'])): ?><div class="panel panel-error"><?php echo htmlspecialchars($_SESSION['browser_error']); unset($_SESSION['browser_error']); ?></div><?php endif; ?>
  <?php if (!empty($_SESSION['browser_success'])): ?><div class="panel panel-success"><?php echo htmlspecialchars($_SESSION['browser_success']); unset($_SESSION['browser_success']); ?></div><?php endif; ?>

      <div class="panel">sdsd
        <div class="muted">Items in <strong><?php echo $requested === '' ? '/browser' : '/' . htmlspecialchars($requested); ?></strong></div>

        <form method="post" enctype="multipart/form-data" class="browser-upload-form">
          <input type="file" name="upload" id="uploadInput">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
          <button class="btn btn-primary" type="submit">Upload</button>
        </form>

        <div class="items" id="items">
          <?php foreach ($contents['dirs'] as $dir): $dirPath = ($requested === '' ? $dir : $requested . '/' . $dir); ?>
            <div class="item"><a href="?path=<?php echo rawurlencode($dirPath); ?>"><svg class="icon" viewBox="0 0 24 24" fill="#f6c85f" xmlns="http://www.w3.org/2000/svg"><path d="M10 4H4a2 2 0 0 0-2 2v2h20V8a2 2 0 0 0-2-2h-8l-2-2z"/><path d="M2 10v8a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-8H2z"/></svg><div class="name"><?php echo htmlspecialchars($dir); ?></div></a>
              <?php if (userCanModify()): ?>
              <div class="item-actions">
                <form method="post" onsubmit="return confirm('Delete folder <?php echo htmlspecialchars($dir); ?>?');" class="inline-form">
                  <input type="hidden" name="op" value="delete">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
                  <input type="hidden" name="target" value="<?php echo htmlspecialchars($dirPath); ?>">
                  <button class="btn btn-danger btn-sm" type="submit" aria-label="<?php echo htmlspecialchars('Delete folder ' . $dir); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M3 6h18v2H3V6zm2 3h14l-1 11H6L5 9zm3-6h6l1 2H7l1-2z"/></svg>
                  </button>
                </form>
                <button class="btn btn-rename btn-sm" type="button" onclick="renameItem(decodeURIComponent('<?php echo rawurlencode($dirPath); ?>'))" aria-label="<?php echo htmlspecialchars('Rename ' . $dir); ?>" title="Rename">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/></svg>
                </button>
              </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>

          <?php foreach ($contents['files'] as $file): $relPath = (encodePathForUrl($requested) !== '' ? encodePathForUrl($requested) . '/' : '') . rawurlencode($file); $fileUrl = 'browser/' . $relPath; $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)); $previewable = in_array($ext, ['png','jpg','jpeg','gif','svg','txt','md','csv','pdf','html']); ?>
            <div class="item">
              <a href="<?php echo $fileUrl; ?>" <?php if ($previewable) echo 'class="preview-link" data-path="' . htmlspecialchars($fileUrl) . '" data-ext="' . $ext . '"'; ?> target="<?php echo $previewable ? '_self' : '_blank'; ?>">
                <?php echo getFileIconSvg($ext); ?>
                <div class="name"><?php echo htmlspecialchars($file); ?></div>
              </a>
              <?php if (userCanModify()): ?>
              <div class="item-actions">
                <form method="post" onsubmit="return confirm('Delete file <?php echo htmlspecialchars($file); ?>?');" class="inline-form">
                  <input type="hidden" name="op" value="delete">
                  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">
                  <input type="hidden" name="target" value="<?php echo htmlspecialchars(($requested === '' ? $file : $requested . '/' . $file)); ?>">
                  <button class="btn btn-danger btn-sm" type="submit" aria-label="<?php echo htmlspecialchars('Delete file ' . $file); ?>">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M3 6h18v2H3V6zm2 3h14l-1 11H6L5 9zm3-6h6l1 2H7l1-2z"/></svg>
                  </button>
                </form>
                <button class="btn btn-rename btn-sm" type="button" onclick="renameItem(decodeURIComponent('<?php echo rawurlencode(($requested === '' ? $file : $requested . '/' . $file)); ?>'))" aria-label="<?php echo htmlspecialchars('Rename ' . $file); ?>" title="Rename">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1.003 1.003 0 0 0 0-1.42l-2.34-2.34a1.003 1.003 0 0 0-1.42 0l-1.83 1.83 3.75 3.75 1.84-1.82z"/></svg>
                </button>
              </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

    </div>
  </div>

</div>







  <script>const BROWSER_CSRF = '<?php echo htmlspecialchars($csrf); ?>';</script>

  <div id="previewModal" class="preview-modal"><div class="preview-content"><button id="closePreview" class="close-btn">Close</button><div id="previewContent"></div></div></div>

  <script>
  document.addEventListener('click', function(e){
    const a = e.target.closest('a.preview-link');
    if (!a) return;
    e.preventDefault();
    const path = a.getAttribute('data-path');
    const ext = a.getAttribute('data-ext');
    const modal = document.getElementById('previewModal');
    const content = document.getElementById('previewContent');
    content.innerHTML = '';
    if (['png','jpg','jpeg','gif','svg'].includes(ext)) {
      const media = document.createElement('div');
      media.className = 'preview-media';
      const img = document.createElement('img');
      img.src = path;
      media.appendChild(img);
      content.appendChild(media);
    } else if (ext === 'pdf') {
      const media = document.createElement('div');
      media.className = 'preview-media';
      const iframe = document.createElement('iframe');
      iframe.src = path;
      iframe.className = 'preview-iframe';
      media.appendChild(iframe);
      content.appendChild(media);
    } else {
      fetch(path).then(r=>r.text()).then(t=>{
        const media = document.createElement('div');
        media.className = 'preview-media';
        const pre = document.createElement('pre');
        pre.textContent = t.slice(0,20000);
        media.appendChild(pre);
        content.appendChild(media);
      }).catch(()=>{
        const media = document.createElement('div');
        media.className = 'preview-media';
        media.textContent = 'Unable to load preview.';
        content.appendChild(media);
      });
    }
    modal.classList.add('open');
  });
  document.getElementById('closePreview').addEventListener('click', function(){ document.getElementById('previewModal').classList.remove('open'); });

  function renameItem(rel) { try { rel = decodeURIComponent(rel); } catch(e) {} var base = rel.split('/'); var oldName = base.pop(); var newName = prompt('Rename "' + oldName + '" to:', oldName); if (!newName) return; newName = newName.replace(/[^A-Za-z0-9._ -]/g, '_'); var form = document.createElement('form'); form.method='post'; form.style.display='none'; var i1 = document.createElement('input'); i1.name='op'; i1.value='rename'; form.appendChild(i1); var i2 = document.createElement('input'); i2.name='target'; i2.value=rel; form.appendChild(i2); var i3 = document.createElement('input'); i3.name='newname'; i3.value=newName; form.appendChild(i3); var i4 = document.createElement('input'); i4.name='csrf_token'; i4.value=BROWSER_CSRF; form.appendChild(i4); document.body.appendChild(form); form.submit(); }
  </script>

</body>


</html>