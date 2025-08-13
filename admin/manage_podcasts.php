<?php
// admin/manage_podcasts.php
require_once '../includes/db.php';
require_once 'admin_header.php';

// === Enable getID3 (composer) for duration detection ===
require_once __DIR__ . '/../vendor/autoload.php'; // adjust if your vendor path differs
use getID3;

// === Helpers ===
function uploads_dir() {
    // From /admin/ to /uploads/podcasts
    return realpath(__DIR__ . '/../uploads/podcasts') ?: (__DIR__ . '/../uploads/podcasts');
}
function ensure_uploads_dir() {
    $dir = uploads_dir();
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
    return $dir;
}
function sanitize_filename($name) {
    $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
    return $name;
}
function unique_filename($dir, $original) {
    $original = sanitize_filename($original);
    $ext = pathinfo($original, PATHINFO_EXTENSION);
    $base = pathinfo($original, PATHINFO_FILENAME);
    $candidate = $base . '_' . time() . '_' . mt_rand(1000,9999);
    return $candidate . ($ext ? ('.' . $ext) : '');
}
function format_duration($seconds) {
    $seconds = (int)$seconds;
    return gmdate('H:i:s', $seconds);
}

// === Fetch users for contributor dropdown (robust across schemas) ===
// $usersStmt = $pdo->query("
//     SELECT 
//         id,
//         COALESCE(name, CONCAT_WS(' ', first_name, last_name), username, email) AS display_name
//     FROM users
//     ORDER BY display_name ASC
// ");
// $users = $usersStmt ? $usersStmt->fetchAll(PDO::FETCH_ASSOC) : [];



$stmt = $pdo->query("
    SELECT 
        p.id, 
        p.title, 
        p.description, 
        p.audio_file, 
        p.length, 
        p.created_at,
        a.full_name AS posted_by
    FROM podcasts p
    JOIN admins a ON p.admin_id = a.id
    ORDER BY p.created_at DESC
");
$podcasts = $stmt->fetchAll(PDO::FETCH_ASSOC);


// === Handle Create/Update (multipart form with modals) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ensure_uploads_dir();
    $uploadsDir = uploads_dir();

    $id               = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title            = trim($_POST['title'] ?? '');
    $description      = trim($_POST['description'] ?? '');
    $tags             = trim($_POST['tags'] ?? '');
    $contributor_id   = (int)($_POST['contributor_id'] ?? 0);

    // Keep existing file names if not replaced
    $existing_audio   = trim($_POST['existing_audio'] ?? '');
    $existing_cover   = trim($_POST['existing_cover'] ?? '');

    $audio_file_name  = $existing_audio;
    $cover_file_name  = $existing_cover;
    $duration_seconds = (int)($_POST['existing_duration'] ?? 0);

    // --- Handle Cover Upload (optional) ---
    if (!empty($_FILES['cover_image']['name'])) {
        $coverTmp  = $_FILES['cover_image']['tmp_name'];
        $coverName = unique_filename($uploadsDir, $_FILES['cover_image']['name']);

        // rudimentary MIME check
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = $coverTmp ? finfo_file($finfo, $coverTmp) : '';
        finfo_close($finfo);

        if ($mime && preg_match('#^image/(jpeg|png|webp|gif)$#i', $mime)) {
            if (move_uploaded_file($coverTmp, $uploadsDir . '/' . $coverName)) {
                // remove old cover if replacing
                if ($existing_cover && file_exists($uploadsDir . '/' . $existing_cover)) {
                    @unlink($uploadsDir . '/' . $existing_cover);
                }
                $cover_file_name = $coverName;
            }
        }
    }

    // --- Handle Audio Upload (required on create, optional on edit) ---
    $newAudioUploaded = false;
    if (!empty($_FILES['audio_file']['name'])) {
        $audioTmp  = $_FILES['audio_file']['tmp_name'];
        $audioName = unique_filename($uploadsDir, $_FILES['audio_file']['name']);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = $audioTmp ? finfo_file($finfo, $audioTmp) : '';
        finfo_close($finfo);

        // Accept common audio types (mp3, mpeg, m4a, wav, ogg)
        if ($mime && preg_match('#^(audio/|application/octet-stream)#i', $mime)) {
            if (move_uploaded_file($audioTmp, $uploadsDir . '/' . $audioName)) {
                // remove old audio if replacing
                if ($existing_audio && file_exists($uploadsDir . '/' . $existing_audio)) {
                    @unlink($uploadsDir . '/' . $existing_audio);
                }
                $audio_file_name = $audioName;
                $newAudioUploaded = true;
            }
        }
    }

    // --- Compute duration (on create OR when audio replaced) ---
    if ($newAudioUploaded || ($id === 0 && $audio_file_name)) {
        $getID3 = new getID3;
        $analyze = $getID3->analyze($uploadsDir . '/' . $audio_file_name);
        $duration_seconds = isset($analyze['playtime_seconds']) ? (int)round($analyze['playtime_seconds']) : 0;
    }

    if ($id > 0) {
        // Update
        $stmt = $pdo->prepare("
            UPDATE podcasts
            SET title = ?, file_url = ?, description = ?, tags = ?, duration_seconds = ?, cover_image_url = ?, contributor_id = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $title,
            $audio_file_name,
            $description,
            $tags,
            $duration_seconds,
            $cover_file_name,
            $contributor_id,
            $id
        ]);
    } else {
        // Insert (audio is required on create)
        if (!$audio_file_name) {
            die('Audio file is required.');
        }
        $stmt = $pdo->prepare("
            INSERT INTO podcasts (title, file_url, description, tags, duration_seconds, cover_image_url, contributor_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $title,
            $audio_file_name,
            $description,
            $tags,
            $duration_seconds,
            $cover_file_name,
            $contributor_id
        ]);
    }

    header('Location: manage_podcasts.php');
    exit;
}

// === Delete (also remove files) ===
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];

    // find filenames to unlink
    $sel = $pdo->prepare("SELECT file_url, cover_image_url FROM podcasts WHERE id = ?");
    $sel->execute([$delId]);
    $row = $sel->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $pdo->prepare("DELETE FROM podcasts WHERE id = ?")->execute([$delId]);
        $dir = uploads_dir();
        if (!empty($row['file_url']) && file_exists($dir . '/' . $row['file_url'])) {
            @unlink($dir . '/' . $row['file_url']);
        }
        if (!empty($row['cover_image_url']) && file_exists($dir . '/' . $row['cover_image_url'])) {
            @unlink($dir . '/' . $row['cover_image_url']);
        }
    }

    header('Location: manage_podcasts.php');
    exit;
}

// === Fetch all podcasts for table ===
$podcasts = $pdo->query("
    SELECT 
        p.*,
        COALESCE(u.name, CONCAT_WS(' ', u.first_name, u.last_name), u.username, u.email) AS contributor_name
    FROM podcasts p
    LEFT JOIN users u ON p.contributor_id = u.id
    ORDER BY p.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Manage Podcasts - Admin Panel</title>
<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="css/tables.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
.admin-content-container{margin-left:var(--sidebar-width);padding:2rem;}
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;gap:1rem;flex-wrap:wrap;}
.btn{display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.25rem;border-radius:6px;font-size:.95rem;text-decoration:none;cursor:pointer;border:none}
.btn-back{background:var(--color-neutral-mid);color:var(--color-primary-dark);}
.btn-back:hover{background:var(--color-primary-mid);}
.btn-primary{background:var(--color-primary-accent);color:#fff;}
.btn-primary:hover{background:var(--color-secondary-accent);}
.podcast-cover{width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid var(--color-neutral-mid);}
.audio-player{width:180px;height:36px;}
.description-cell{max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.description-cell:hover{white-space:normal;overflow:visible;}
.table-actions .icon-btn{background:transparent;border:none;cursor:pointer;margin-right:.25rem;}
.modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);justify-content:center;align-items:center;padding:1rem;z-index:1000;}
.modal.show{display:flex;}
.modal-card{background:#fff;max-width:640px;width:100%;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.2);overflow:hidden;}
.modal-header{display:flex;justify-content:space-between;align-items:center;padding:1rem 1.25rem;border-bottom:1px solid #eee;}
.modal-body{padding:1rem 1.25rem;}
.modal-footer{display:flex;justify-content:flex-end;gap:.5rem;padding:1rem 1.25rem;border-top:1px solid #eee;}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
.form-grid .full{grid-column:1 / -1;}
.input, .select, .textarea{width:100%;padding:.6rem .7rem;border:1px solid #ddd;border-radius:8px;}
.label{font-size:.9rem;margin-bottom:.25rem;display:block;color:#333;}
.note{font-size:.8rem;color:#666;}
@media (max-width: 768px){
  .admin-content-container{margin-left:0;padding:1rem;}
  .form-grid{grid-template-columns:1fr;}
}
</style>
</head>
<body>
<div class="admin-content-container">
    <div class="page-header">
        <h1><i class="fas fa-podcast"></i> Manage Podcasts</h1>
        <div class="header-actions">
            <a href="dashboard.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
            <button class="btn btn-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Add New Podcast</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Cover</th>
                <th>Title</th>
                <th>Contributor</th>
                <th>Tags</th>
                <th>Duration</th>
                <th>Audio</th>
                <th>Description</th>
                <th>Uploaded</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($podcasts): foreach ($podcasts as $i => $p): ?>
                <tr>
                    <td><?= $i+1 ?></td>
                    <td>
                        <?php if (!empty($p['cover_image_url'])): ?>
                            <img class="podcast-cover" src="<?= '../uploads/podcasts/' . htmlspecialchars($p['cover_image_url']) ?>" alt="Cover">
                        <?php else: ?>
                            <span class="light-text">No Image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['title']) ?></td>
                    <td><?= htmlspecialchars($p['contributor_name'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($p['tags']) ?></td>
                    <td><?= format_duration($p['duration_seconds']) ?></td>
                    <td>
                        <?php if (!empty($p['file_url'])): ?>
                            <audio controls class="audio-player">
                                <source src="<?= '../uploads/podcasts/' . htmlspecialchars($p['file_url']) ?>" type="audio/mpeg">
                            </audio>
                        <?php else: ?>
                            <span class="light-text">No audio</span>
                        <?php endif; ?>
                    </td>
                    <td class="description-cell" title="<?= htmlspecialchars($p['description']) ?>">
                        <?= nl2br(htmlspecialchars($p['description'])) ?>
                    </td>
                    <td><?= htmlspecialchars(date('M j, Y', strtotime($p['created_at']))) ?></td>
                    <td>
                        <div class="table-actions">
                            <button class="icon-btn" title="Edit" onclick='openEditModal(<?= json_encode($p, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <a class="icon-btn" title="Delete"
                               href="?delete=<?= (int)$p['id'] ?>"
                               onclick="return confirm('Delete this podcast? This action cannot be undone.')">
                                <i class="fas fa-trash"></i>
                            </a>
                            <a class="icon-btn" title="View public page" href="../podcast.php?id=<?= (int)$p['id'] ?>" target="_blank">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr>
                    <td colspan="10" class="data-table-empty">
                        <i class="fas fa-podcast"></i>
                        <p>No podcasts uploaded yet</p>
                        <button class="btn btn-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Add New Podcast</button>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ========== Modal (Add/Edit) ========== -->
<div class="modal" id="podcastModal" aria-hidden="true">
  <div class="modal-card">
    <div class="modal-header">
      <h2 id="modalTitle">Add Podcast</h2>
      <button class="icon-btn" onclick="closeModal()" aria-label="Close"><i class="fas fa-times"></i></button>
    </div>
    <form id="podcastForm" method="POST" enctype="multipart/form-data" class="modal-body">
      <input type="hidden" name="id" id="id">
      <input type="hidden" name="existing_audio" id="existing_audio">
      <input type="hidden" name="existing_cover" id="existing_cover">
      <input type="hidden" name="existing_duration" id="existing_duration">

      <div class="form-grid">
        <div class="full">
          <label class="label">Title *</label>
          <input class="input" type="text" name="title" id="title" required>
        </div>

        <div class="full">
          <label class="label">Description</label>
          <textarea class="textarea" name="description" id="description" rows="4"></textarea>
        </div>

        <div>
          <label class="label">Tags (comma-separated)</label>
          <input class="input" type="text" name="tags" id="tags" placeholder="e.g. wildlife, conservation">
        </div>

        <div>
          <label class="label">Contributor *</label>
          <select class="select" name="contributor_id" id="contributor_id" required>
            <option value="">-- Select Contributor --</option>
            <?php foreach ($users as $u): ?>
              <option value="<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['display_name'] ?? ('User #' . $u['id'])) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="label">Cover Image <?= /* optional */ '' ?></label>
          <input class="input" type="file" name="cover_image" id="cover_image" accept="image/*">
          <div class="note">If provided, stored in <code>../uploads/podcasts/</code></div>
        </div>

        <div>
          <label class="label">Audio File <span id="audio_required_star">*</span></label>
          <input class="input" type="file" name="audio_file" id="audio_file" accept="audio/*">
          <div class="note">Stored in <code>../uploads/podcasts/</code>. Duration auto-detected.</div>
        </div>
      </div>
    </form>
    <div class="modal-footer">
      <button class="btn btn-back" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" onclick="document.getElementById('podcastForm').submit()">Save</button>
    </div>
  </div>
</div>

<script>
function openAddModal() {
  document.getElementById('modalTitle').innerText = 'Add Podcast';
  document.getElementById('id').value = '';
  document.getElementById('existing_audio').value = '';
  document.getElementById('existing_cover').value = '';
  document.getElementById('existing_duration').value = '0';
  document.getElementById('title').value = '';
  document.getElementById('description').value = '';
  document.getElementById('tags').value = '';
  document.getElementById('contributor_id').value = '';
  document.getElementById('cover_image').value = '';
  document.getElementById('audio_file').value = '';
  document.getElementById('audio_required_star').style.display = 'inline';
  document.getElementById('podcastModal').classList.add('show');
}

function openEditModal(p) {
  document.getElementById('modalTitle').innerText = 'Edit Podcast';
  document.getElementById('id').value = p.id;
  document.getElementById('existing_audio').value = p.file_url || '';
  document.getElementById('existing_cover').value = p.cover_image_url || '';
  document.getElementById('existing_duration').value = p.duration_seconds || '0';
  document.getElementById('title').value = p.title || '';
  document.getElementById('description').value = p.description || '';
  document.getElementById('tags').value = p.tags || '';
  document.getElementById('contributor_id').value = p.contributor_id || '';
  document.getElementById('cover_image').value = '';
  document.getElementById('audio_file').value = '';
  // audio not required on edit (only if replacing)
  document.getElementById('audio_required_star').style.display = 'none';
  document.getElementById('podcastModal').classList.add('show');
}

function closeModal() {
  document.getElementById('podcastModal').classList.remove('show');
}
</script>
</body>
</html>
