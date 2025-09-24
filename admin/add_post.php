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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <style>
        /* Blend editor with site colors */
        :root {
            --cms-accent: var(--color-primary-accent, #013221);
            --cms-secondary: var(--color-secondary-accent, #e8b824);
            --cms-dark: var(--color-primary-dark, #1E1811);
            --cms-light: var(--color-primary-light, #FFF8E8);
        }
        .ql-toolbar.ql-snow {
            border-color: var(--cms-accent);
            background: rgba(1,50,33,0.04);
        }
        .ql-container.ql-snow {
            border-color: var(--cms-accent);
        }
        .ql-snow .ql-picker-options {
            border-color: var(--cms-accent);
        }
        .ql-snow .ql-stroke { stroke: var(--cms-dark); }
        .ql-snow .ql-fill { fill: var(--cms-dark); }
        .ql-snow .ql-picker.ql-expanded .ql-picker-label { color: var(--cms-dark); }
        .ql-snow .ql-picker.ql-expanded .ql-picker-options { background: var(--cms-light); }
        .ql-snow .ql-picker-label:hover, .ql-snow .ql-picker-item:hover,
        .ql-snow .ql-toolbar button:hover svg { color: var(--cms-accent); stroke: var(--cms-accent); }
        .ql-snow .ql-active svg, .ql-snow .ql-active .ql-stroke { stroke: var(--cms-secondary); }
        .ql-snow .ql-active .ql-fill { fill: var(--cms-secondary); }
    </style>
</head>
<body>

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
            <!-- Custom CMS Editor: Quill -->
            <div id="editor-toolbar">
              <span class="ql-formats">
                <select class="ql-font"></select>
                <select class="ql-size"></select>
              </span>
              <span class="ql-formats">
                <button class="ql-bold"></button>
                <button class="ql-italic"></button>
                <button class="ql-underline"></button>
                <button class="ql-strike"></button>
              </span>
              <span class="ql-formats">
                <select class="ql-color"></select>
                <select class="ql-background"></select>
              </span>
              <span class="ql-formats">
                <button class="ql-list" value="ordered"></button>
                <button class="ql-list" value="bullet"></button>
                <button class="ql-indent" value="-1"></button>
                <button class="ql-indent" value="+1"></button>
                <select class="ql-align"></select>
              </span>
              <span class="ql-formats">
                <button class="ql-link"></button>
                <button class="ql-image"></button>
                <button class="ql-code-block"></button>
              </span>
              <span class="ql-formats">
                <button class="ql-clean"></button>
              </span>
            </div>
            <div id="editor" style="height: 350px;"></div>
            <!-- Hidden textarea posted to server -->
            <textarea id="editor-html" name="body" class="d-none" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Featured Image</label>
            <input type="file" name="featured_image" class="form-control">
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Content Type</label>
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
            <input type="text" name="region" class="form-control" required value="<?= isset($_POST['region']) ? htmlspecialchars($_POST['region']) : '' ?>">
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

<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
    // Initialize Quill
    const quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: '#editor-toolbar'
        }
    });

    // On form submit, push HTML into hidden textarea
    const form = document.querySelector('form');
    form.addEventListener('submit', function () {
        document.getElementById('editor-html').value = quill.root.innerHTML.trim();
    });
</script>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
