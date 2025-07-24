<?php
// new_thread.php - Start a new thread
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title'] ?? '');
    $category = sanitize_input($_POST['category'] ?? '');
    $content = sanitize_input($_POST['content'] ?? '');
    $author_id = $_SESSION['user_id'];

    if (empty($title) || empty($content)) {
        $errors[] = "Title and content are required.";
    } else {
        // Insert into forum_threads
        $stmt = $conn->prepare("INSERT INTO forum_threads (title, category, author_id) VALUES (?, ?, ?)");
        $stmt->execute([$title, $category, $author_id]);
        $thread_id = $conn->lastInsertId();

        // Insert first post in forum_posts
        $stmt = $conn->prepare("INSERT INTO forum_posts (thread_id, author_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$thread_id, $author_id, $content]);

        redirect("topic.php?id=$thread_id");
    }
}

require_once 'header.php';
require_once 'nav.php';
?>

<main>
    <h1>Start a New Thread</h1>
    <?php if (!empty($errors)): ?>
        <div style="color:red;">
            <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="new_thread.php">
        <label for="title">Thread Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="category">Category (optional):</label><br>
        <input type="text" id="category" name="category"><br><br>

        <label for="content">Initial Post:</label><br>
        <textarea id="content" name="content" rows="6" required></textarea><br><br>

        <button type="submit">Create Thread</button>
    </form>
</main>

<?php require_once 'footer.php'; ?>
