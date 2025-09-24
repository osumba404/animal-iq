<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

$action = $_GET['action'] ?? null;
$memberId = $_GET['id'] ?? null;

// Handle image upload
function uploadPhoto($file) {
    if (!empty($file['name'])) {
        $targetDir = "../uploads/management_team/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($file["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return "../uploads/management_team/" . $fileName; // relative path for DB
        }
    }
    return null;
}

// ADD MEMBER
if (isset($_POST['add_member'])) {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $message = $_POST['message'];
    $email = $_POST['email'];
    $linkedin_url = $_POST['linkedin_url'];
    $ig_url = $_POST['ig_url'];
    $fb_url = $_POST['fb_url'];
    $x_url = $_POST['x_url'];
    $status = $_POST['status'];
    $photo_url = uploadPhoto($_FILES['photo']);

    $insert = $pdo->prepare("
        INSERT INTO management_team (name, role, message, email, linkedin_url, ig_url, fb_url, x_url, photo_url, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $insert->execute([$name, $role, $message, $email, $linkedin_url, $ig_url, $fb_url, $x_url, $photo_url, $status]);

    header("Location: manage_team.php");
    exit;
}

// EDIT MEMBER
if (isset($_POST['edit_member'])) {
    $memberId = $_POST['member_id'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $message = $_POST['message'];
    $email = $_POST['email'];
    $linkedin_url = $_POST['linkedin_url'];
    $ig_url = $_POST['ig_url'];
    $fb_url = $_POST['fb_url'];
    $x_url = $_POST['x_url'];
    $status = $_POST['status'];

    $photo_url = uploadPhoto($_FILES['photo']);
    if ($photo_url) {
        $update = $pdo->prepare("
            UPDATE management_team SET 
                name=?, role=?, message=?, email=?, linkedin_url=?, ig_url=?, fb_url=?, x_url=?, photo_url=?, status=? 
            WHERE id=?
        ");
        $update->execute([$name, $role, $message, $email, $linkedin_url, $ig_url, $fb_url, $x_url, $photo_url, $status, $memberId]);
    } else {
        $update = $pdo->prepare("
            UPDATE management_team SET 
                name=?, role=?, message=?, email=?, linkedin_url=?, ig_url=?, fb_url=?, x_url=?, status=? 
            WHERE id=?
        ");
        $update->execute([$name, $role, $message, $email, $linkedin_url, $ig_url, $fb_url, $x_url, $status, $memberId]);
    }

    // header("Location: manage_team.php");
    // exit;
}

// DELETE MEMBER
if ($action === 'delete' && $memberId) {
    $stmt = $pdo->prepare("DELETE FROM management_team WHERE id = ?");
    $stmt->execute([$memberId]);
    header("Location: manage_team.php");
    exit;
}

// FETCH ALL MEMBERS
$stmt = $pdo->query("SELECT * FROM management_team ORDER BY id DESC");
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Management Team</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/tables.css">
    <style>
        .modal { display: none; position: fixed; z-index: 1000; padding-top: 50px; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: #fff; margin: auto; padding: 20px; border-radius: 6px; width: 90%; max-width: 600px; }
        .close { float: right; font-size: 20px; cursor: pointer; }
        .form-group { margin-bottom: 1rem; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 8px; }
    </style>
</head>
<body style="margin-left: 400px;">
<div class="admin-content-container">
    <div class="page-header">
        <h1><i class="fas fa-users"></i> Manage Management Team</h1>
        <div class="header-actions">
            <a href="dashboard.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Back</a>
            <button onclick="openModal('addModal')" class="btn btn-primary"><i class="fas fa-plus"></i> Add Member</button>
        </div>
    </div>

    <table class="data-table">
        <thead>
        <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Role</th>
            <th>Message</th>
            <th>Email</th>
            <th>LinkedIn</th>
            <th>IG</th>
            <th>FB</th>
            <th>X</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($members as $m): ?>
            <tr>
                <td><?php if ($m['photo_url']): ?><img src="<?= htmlspecialchars($m['photo_url']) ?>" style="max-width:50px;"><?php endif; ?></td>
                <td><?= htmlspecialchars($m['name']) ?></td>
                <td><?= htmlspecialchars($m['role']) ?></td>
                <td><?= htmlspecialchars(substr($m['message'], 0, 50)) ?>...</td>
                <td><?= htmlspecialchars($m['email']) ?></td>
                <td><?php if ($m['linkedin_url']): ?><a href="<?= htmlspecialchars($m['linkedin_url']) ?>" target="_blank">Profile</a><?php endif; ?></td>
                <td><?php if ($m['ig_url']): ?><a href="<?= htmlspecialchars($m['ig_url']) ?>" target="_blank">Profile</a><?php endif; ?></td>
                <td><?php if ($m['fb_url']): ?><a href="<?= htmlspecialchars($m['fb_url']) ?>" target="_blank">Profile</a><?php endif; ?></td>
                <td><?php if ($m['x_url']): ?><a href="<?= htmlspecialchars($m['x_url']) ?>" target="_blank">Profile</a><?php endif; ?></td>
                <td><?= ucfirst($m['status']) ?></td>
                <td>
                    <button onclick='openEditModal(<?= json_encode($m) ?>)' class="btn-edit"><i class="fas fa-edit"></i></button>
                    <a href="?action=delete&id=<?= $m['id'] ?>" class="btn-delete" onclick="return confirm('Delete this member?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ADD MODAL -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h3>Add Member</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="add_member" value="1">
            <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Role</label><input type="text" name="role" required></div>
            <div class="form-group"><label>Message</label><textarea name="message" required></textarea></div>
            <div class="form-group"><label>Email</label><input type="email" name="email"></div>
            <div class="form-group"><label>LinkedIn URL</label><input type="url" name="linkedin_url"></div>
            <div class="form-group"><label>IG URL</label><input type="url" name="ig_url"></div>
            <div class="form-group"><label>FB URL</label><input type="url" name="fb_url"></div>
            <div class="form-group"><label>X URL</label><input type="url" name="x_url"></div>
            <div class="form-group"><label>Photo</label><input type="file" name="photo"></div>
            <div class="form-group"><label>Status</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <button type="submit" class="btn btn-primary">Add Member</button>
        </form>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h3>Edit Member</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="edit_member" value="1">
            <input type="hidden" name="member_id" id="edit_member_id">
            <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Role</label><input type="text" name="role" required></div>
            <div class="form-group"><label>Message</label><textarea name="message" required></textarea></div>
            <div class="form-group"><label>Email</label><input type="email" name="email"></div>
            <div class="form-group"><label>LinkedIn URL</label><input type="url" name="linkedin_url"></div>
            <div class="form-group"><label>IG URL</label><input type="url" name="ig_url"></div>
            <div class="form-group"><label>FB URL</label><input type="url" name="fb_url"></div>
            <div class="form-group"><label>X URL</label><input type="url" name="x_url"></div>
            <div class="form-group"><label>Photo</label><input type="file" name="photo"></div>
            <div class="form-group"><label>Status</label><select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            <button type="submit" class="btn btn-primary">Update Member</button>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).style.display = "block"; }
function closeModal(id) { document.getElementById(id).style.display = "none"; }
function openEditModal(data) {
    openModal('editModal');
    document.getElementById('edit_member_id').value = data.id;
    document.querySelector('#editModal [name="name"]').value = data.name;
    document.querySelector('#editModal [name="role"]').value = data.role;
    document.querySelector('#editModal [name="message"]').value = data.message;
    document.querySelector('#editModal [name="email"]').value = data.email;
    document.querySelector('#editModal [name="linkedin_url"]').value = data.linkedin_url;
    document.querySelector('#editModal [name="ig_url"]').value = data.ig_url;
    document.querySelector('#editModal [name="fb_url"]').value = data.fb_url;
    document.querySelector('#editModal [name="x_url"]').value = data.x_url;
    document.querySelector('#editModal [name="status"]').value = data.status;
}
</script>
</body>
</html>
