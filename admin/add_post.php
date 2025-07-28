<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php'; // ensures admin session

// Handle form submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);
    $type = $_POST['type'];
    $region = $_POST['region'];
    $status = $_POST['status'];
    $author_id = $_SESSION['admin_id']; // default

    // Image handling
    $featured_image = null;
    if (!empty($_FILES['featured_image']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Invalid image format. Allowed: " . implode(', ', $allowed);
        } else {
            $imageName = uniqid('img_') . '.' . $ext;
            $targetPath = '../uploads/posts/' . $imageName;

            if (!move_uploaded_file($_FILES['featured_image']['tmp_name'], $targetPath)) {
                $errors[] = "Image upload failed.";
            } else {
                $featured_image = $imageName;
            }
        }
    }

    if (empty($title) || empty($body) || empty($type) || empty($region)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO posts (title, body, featured_image, type, region, status, author_id) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $body, $featured_image, $type, $region, $status, $author_id]);

        header("Location: manage_posts.php?success=1");
        exit;
    }
}
?>


<div class="container mt-5">
    <h2>Add New Post</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="body" class="form-control" rows="6" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Featured Image</label>
            <input type="file" name="featured_image" class="form-control">
        </div>

        <div class="mb-3">
            <label for="type">Content Type</label>
            <select name="type" id="type" class="form-select" required onchange="toggleOtherInput(this)">
                <option value="announcement">Announcement</option>
                <option value="educational">Educational</option>
                <option value="campaign">Campaign</option>
                <option value="general">General</option>
                <option value="other">Other</option>
            </select>

            <!-- Hidden input shown when 'Other' is selected -->
            <div id="otherInputWrapper" style="display:none; margin-top: 10px;">
                <input type="text" name="custom_type" placeholder="Please specify..." class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Region</label>
            <input type="text" name="region" required value="<?= isset($_POST['region']) ? htmlspecialchars($_POST['region']) : '' ?>">


             
        </div>

        <div class="mb-3">
            <label class="form-label">Initial Status</label>
            <select name="status" class="form-select" required>
                <option value="pending" <?= (isset($_POST['status']) && $_POST['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= (isset($_POST['status']) && $_POST['status'] === 'approved') ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= (isset($_POST['status']) && $_POST['status'] === 'rejected') ? 'selected' : '' ?>>Rejected</option>




            </select>
        </div>

        <button type="submit" class="btn btn-primary">Publish Post</button>
    </form>
</div>

<script>
function toggleOtherInput(select) {
    const otherInput = document.getElementById('otherInputWrapper');
    if (select.value === 'other') {
        otherInput.style.display = 'block';
    } else {
        otherInput.style.display = 'none';
    }
}
</script>


