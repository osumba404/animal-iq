<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!isset($_GET['id'])) {
    die('Post ID is required.');
}

$post_id = intval($_GET['id']);

// Fetch post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    die('Post not found.');
}

// Fetch admins
$admins = $pdo->query("SELECT id, full_name FROM admins")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Edit Post</h2>

    <form action="update_post.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($post['title']) ?>">
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
            <div id="editor" style="height: 400px;"></div>
            <!-- Hidden textarea posted to server -->
            <textarea id="editor-html" name="body" class="d-none" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Post Type</label>
            <input type="text" name="type" class="form-control" required value="<?= htmlspecialchars($post['type']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Region</label>
            <input type="text" name="region" class="form-control" required value="<?= htmlspecialchars($post['region']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="pending" <?= $post['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= $post['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= $post['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Author</label>
            <select name="author_id" class="form-select" required>
                <?php foreach ($admins as $admin): ?>
                    <option value="<?= $admin['id'] ?>" <?= $admin['id'] == $post['author_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($admin['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <label class="form-label">Current Featured Image:</label>
        <?php if ($post['featured_image']): ?>
            <div><img src="../uploads/posts/<?= htmlspecialchars($post['featured_image']) ?>" width="120" alt="Current Image"></div>
        <?php else: ?>
            <div><em>No image uploaded</em></div>
        <?php endif; ?>

        <div class="mb-3 mt-2">
            <label class="form-label">Change Featured Image</label>
            <input type="file" name="featured_image" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update Post</button>
    </form>

    <!-- Quill Editor (no API key) -->
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <style>
      :root {
        --cms-accent: var(--color-primary-accent, #013221);
        --cms-secondary: var(--color-secondary-accent, #e8b824);
        --cms-dark: var(--color-primary-dark, #1E1811);
        --cms-light: var(--color-primary-light, #FFF8E8);
      }
      .ql-toolbar.ql-snow { border-color: var(--cms-accent); background: rgba(1,50,33,0.04); }
      .ql-container.ql-snow { border-color: var(--cms-accent); }
      .ql-snow .ql-picker-options { border-color: var(--cms-accent); }
      .ql-snow .ql-stroke { stroke: var(--cms-dark); }
      .ql-snow .ql-fill { fill: var(--cms-dark); }
      .ql-snow .ql-picker.ql-expanded .ql-picker-label { color: var(--cms-dark); }
      .ql-snow .ql-picker.ql-expanded .ql-picker-options { background: var(--cms-light); }
      .ql-snow .ql-picker-label:hover, .ql-snow .ql-picker-item:hover,
      .ql-snow .ql-toolbar button:hover svg { color: var(--cms-accent); stroke: var(--cms-accent); }
      .ql-snow .ql-active svg, .ql-snow .ql-active .ql-stroke { stroke: var(--cms-secondary); }
      .ql-snow .ql-active .ql-fill { fill: var(--cms-secondary); }
    </style>
    <script>
      // Initialize Quill
      const quill = new Quill('#editor', {
        theme: 'snow',
        modules: { toolbar: '#editor-toolbar' }
      });

      // Load existing HTML content safely
      const initialHtml = <?= json_encode($post['body']) ?>;
      if (initialHtml) {
        quill.root.innerHTML = initialHtml;
      }

      // On form submit, push HTML into hidden textarea
      const form = document.querySelector('form');
      form.addEventListener('submit', function () {
        document.getElementById('editor-html').value = quill.root.innerHTML.trim();
      });
    </script>

</div>
