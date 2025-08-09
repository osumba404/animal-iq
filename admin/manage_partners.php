<?php
require_once '../includes/db.php';
require_once 'admin_header.php';


$action = $_GET['action'] ?? null;
$partnerId = $_GET['id'] ?? null;

// ADD NEW PARTNER
if (isset($_POST['add_partner'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $partners_since = $_POST['partners_since'];
    $logo_url = $_POST['logo_url'];
    $contact_email = $_POST['contact_email'];
    $website_url = $_POST['website_url'];
    $status = $_POST['status'];

    $insert = $pdo->prepare("
        INSERT INTO partners (name, description, location, partners_since, logo_url, contact_email, website_url, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $insert->execute([$name, $description, $location, $partners_since, $logo_url, $contact_email, $website_url, $status]);

    header("Location: manage_partners.php");
    exit;
}

// EDIT PARTNER
if (isset($_POST['edit_partner'])) {
    $partnerId = $_POST['partner_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $partners_since = $_POST['partners_since'];
    $logo_url = $_POST['logo_url'];
    $contact_email = $_POST['contact_email'];
    $website_url = $_POST['website_url'];
    $status = $_POST['status'];

    $update = $pdo->prepare("
        UPDATE partners SET 
            name=?, description=?, location=?, partners_since=?, logo_url=?, 
            contact_email=?, website_url=?, status=? 
        WHERE id=?
    ");
    $update->execute([$name, $description, $location, $partners_since, $logo_url, $contact_email, $website_url, $status, $partnerId]);

    header("Location: manage_partners.php");
    exit;
}

// DELETE PARTNER
if ($action === 'delete' && $partnerId) {
    $stmt = $pdo->prepare("DELETE FROM partners WHERE id = ?");
    $stmt->execute([$partnerId]);
    header("Location: manage_partners.php");
    exit;
}

// FETCH ALL PARTNERS
$stmt = $pdo->query("SELECT * FROM partners ORDER BY partners_since DESC");
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Partners</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/tables.css">
    <style>
        /* Simple modal styles */
        .modal {
            display: none; 
            position: fixed;
            z-index: 1000;
            padding-top: 50px;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto; 
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border-radius: 6px;
            width: 90%;
            max-width: 600px;
        }
        .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }
        .form-group { margin-bottom: 1rem; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 8px;
        }
    </style>
</head>
<body style="margin-left: 400px;">
<div class="admin-content-container">
    <div class="page-header">
        <h1><i class="fas fa-handshake"></i> Manage Partners</h1>
        <div class="header-actions">
            <a href="dashboard.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button onclick="openModal('addModal')" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Partner
            </button>
        </div>
    </div>

    <table class="data-table">
        <thead>
        <tr>
            <th>Logo</th>
            <th>Name</th>
            <th>Description</th>
            <th>Location</th>
            <th>Since</th>
            <th>Contact</th>
            <th>Website</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($partners as $p): ?>
            <tr>
                <td><?php if ($p['logo_url']): ?><img src="<?= htmlspecialchars($p['logo_url']) ?>" style="max-width:50px;"><?php endif; ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars(substr($p['description'], 0, 50)) ?>...</td>
                <td><?= htmlspecialchars($p['location']) ?></td>
                <td><?= htmlspecialchars($p['partners_since']) ?></td>
                <td><?= htmlspecialchars($p['contact_email']) ?></td>
                <td><?php if ($p['website_url']): ?><a href="<?= htmlspecialchars($p['website_url']) ?>" target="_blank">Visit</a><?php endif; ?></td>
                <td><?= ucfirst($p['status']) ?></td>
                <td>
                    <button onclick='openEditModal(<?= json_encode($p) ?>)' class="btn-edit"><i class="fas fa-edit"></i></button>
                    <a href="?action=delete&id=<?= $p['id'] ?>" class="btn-delete" onclick="return confirm('Delete this partner?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ADD PARTNER MODAL -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h3>Add Partner</h3>
        <form method="post">
            <input type="hidden" name="add_partner" value="1">
                        <div class="form-group">
    <label>Name</label>
    <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" required></textarea>
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location">
            </div>
            <div class="form-group">
                <label>Partners Since</label>
                <input type="date" name="partners_since">
            </div>
            <div class="form-group">
                <label>Logo URL</label>
                <input type="url" name="logo_url">
            </div>
            <div class="form-group">
                <label>Contact Email</label>
                <input type="email" name="contact_email">
            </div>
            <div class="form-group">
                <label>Website URL</label>
                <input type="url" name="website_url">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Add Partner</button>
        </form>
    </div>
</div>

<!-- EDIT PARTNER MODAL -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h3>Edit Partner</h3>
        <form method="post">
            <input type="hidden" name="edit_partner" value="1">
            <input type="hidden" name="partner_id" id="edit_partner_id">
            <?php include 'partner_form_fields.php'; ?>
            <button type="submit" class="btn btn-primary">Update Partner</button>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = "block";
    }
    function closeModal(id) {
        document.getElementById(id).style.display = "none";
    }
    function openEditModal(data) {
        openModal('editModal');
        document.getElementById('edit_partner_id').value = data.id;
        document.querySelector('#editModal [name="name"]').value = data.name;
        document.querySelector('#editModal [name="description"]').value = data.description;
        document.querySelector('#editModal [name="location"]').value = data.location;
        document.querySelector('#editModal [name="partners_since"]').value = data.partners_since;
        document.querySelector('#editModal [name="logo_url"]').value = data.logo_url;
        document.querySelector('#editModal [name="contact_email"]').value = data.contact_email;
        document.querySelector('#editModal [name="website_url"]').value = data.website_url;
        document.querySelector('#editModal [name="status"]').value = data.status;
    }
</script>
</body>
</html>
