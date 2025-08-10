<?php 
require_once '../includes/db.php';
require_once 'admin_header.php';

$uploadDir = '../uploads/partners/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$action = $_GET['action'] ?? null;
$partnerId = $_GET['id'] ?? null;

function uploadLogo($file) {
    global $uploadDir;
    if (isset($file['name']) && $file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('logo_', true) . '.' . strtolower($ext);
        $targetPath = $uploadDir . $filename;
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'uploads/partners/' . $filename;
        }
    }
    return null;
}

// ADD
if (isset($_POST['add_partner'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $partners_since = $_POST['partners_since'];
    $contact_email = $_POST['contact_email'];
    $website_url = $_POST['website_url'];
    $status = $_POST['status'];

    $logo_url = uploadLogo($_FILES['logo']);

    $insert = $pdo->prepare("
        INSERT INTO partners (name, description, location, partners_since, logo_url, contact_email, website_url, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $insert->execute([$name, $description, $location, $partners_since, $logo_url, $contact_email, $website_url, $status]);

    header("Location: manage_partners.php");
    exit;
}

// EDIT
if (isset($_POST['edit_partner'])) {
    $partnerId = $_POST['partner_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $partners_since = $_POST['partners_since'];
    $contact_email = $_POST['contact_email'];
    $website_url = $_POST['website_url'];
    $status = $_POST['status'];

    $logo_url = uploadLogo($_FILES['logo']);

    if ($logo_url) {
        $update = $pdo->prepare("
            UPDATE partners SET 
                name=?, description=?, location=?, partners_since=?, logo_url=?, 
                contact_email=?, website_url=?, status=? 
            WHERE id=?
        ");
        $update->execute([$name, $description, $location, $partners_since, $logo_url, $contact_email, $website_url, $status, $partnerId]);
    } else {
        $update = $pdo->prepare("
            UPDATE partners SET 
                name=?, description=?, location=?, partners_since=?, 
                contact_email=?, website_url=?, status=? 
            WHERE id=?
        ");
        $update->execute([$name, $description, $location, $partners_since, $contact_email, $website_url, $status, $partnerId]);
    }

    header("Location: manage_partners.php");
    exit;
}

// DELETE
if ($action === 'delete' && $partnerId) {
    $stmt = $pdo->prepare("DELETE FROM partners WHERE id = ?");
    $stmt->execute([$partnerId]);
    header("Location: manage_partners.php");
    exit;
}

// FETCH
$stmt = $pdo->query("SELECT * FROM partners ORDER BY partners_since DESC");
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container" style="padding-left: 400px;">
    <h1>Manage Partners</h1>
    <button onclick="document.getElementById('addModal').style.display='block'">Add Partner</button>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Logo</th>
                <th>Name</th>
                <th>Description</th>
                <th>Location</th>
                <th>Partners Since</th>
                <th>Contact Email</th>
                <th>Website</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($partners as $partner): ?>
                <tr>
                    <td>
                        <?php if ($partner['logo_url']): ?>
                            <img src="../<?= htmlspecialchars($partner['logo_url']) ?>" width="50">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($partner['name']) ?></td>
                    <td><?= htmlspecialchars($partner['description']) ?></td>
                    <td><?= htmlspecialchars($partner['location']) ?></td>
                    <td><?= htmlspecialchars($partner['partners_since']) ?></td>
                    <td><?= htmlspecialchars($partner['contact_email']) ?></td>
                    <td><a href="<?= htmlspecialchars($partner['website_url']) ?>" target="_blank">Visit</a></td>
                    <td><?= htmlspecialchars($partner['status']) ?></td>
                    <td>
                        <button onclick="openEditModal(<?= htmlspecialchars(json_encode($partner)) ?>)">Edit</button>
                        <a href="?action=delete&id=<?= $partner['id'] ?>" onclick="return confirm('Delete this partner?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- ADD MODAL -->
<div id="addModal" style="display:none;">
    <form method="post" enctype="multipart/form-data">
        <h2>Add Partner</h2>
        <input type="text" name="name" placeholder="Name" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="text" name="location" placeholder="Location">
        <input type="date" name="partners_since">
        <input type="file" name="logo" accept="image/*">
        <input type="email" name="contact_email" placeholder="Contact Email">
        <input type="url" name="website_url" placeholder="Website URL">
        <select name="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <button type="submit" name="add_partner">Save</button>
        <button type="button" onclick="document.getElementById('addModal').style.display='none'">Cancel</button>
    </form>
</div>

<!-- EDIT MODAL -->
<div id="editModal" style="display:none;">
    <form method="post" enctype="multipart/form-data">
        <h2>Edit Partner</h2>
        <input type="hidden" name="partner_id" id="edit_id">
        <input type="text" name="name" id="edit_name" required>
        <textarea name="description" id="edit_description"></textarea>
        <input type="text" name="location" id="edit_location">
        <input type="date" name="partners_since" id="edit_partners_since">
        <input type="file" name="logo" accept="image/*">
        <input type="email" name="contact_email" id="edit_contact_email">
        <input type="url" name="website_url" id="edit_website_url">
        <select name="status" id="edit_status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
        <button type="submit" name="edit_partner">Update</button>
        <button type="button" onclick="document.getElementById('editModal').style.display='none'">Cancel</button>
    </form>
</div>

<script>
function openEditModal(data) {
    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_name').value = data.name;
    document.getElementById('edit_description').value = data.description;
    document.getElementById('edit_location').value = data.location;
    document.getElementById('edit_partners_since').value = data.partners_since;
    document.getElementById('edit_contact_email').value = data.contact_email;
    document.getElementById('edit_website_url').value = data.website_url;
    document.getElementById('edit_status').value = data.status;
    document.getElementById('editModal').style.display = 'block';
}
</script>
