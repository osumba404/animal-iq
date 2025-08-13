<?php
// public/new_thread.php - Start a new thread
require_once '../includes/db.php';
require_once 'header.php'; // session + auth check here
require_once 'nav.php';

$errors = [];

// Only allow logged-in users to create threads
if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $author_id = $_SESSION['user']['id'];

    if (empty($title) || empty($content)) {
        $errors[] = "Title and content are required.";
    } else {
        // Insert into forum_threads
        $stmt = $pdo->prepare("INSERT INTO forum_threads (title, category, author_id) VALUES (?, ?, ?)");
        $stmt->execute([$title, $category, $author_id]);
        $thread_id = $pdo->lastInsertId();

        // Insert the first post into forum_posts
        $stmt = $pdo->prepare("INSERT INTO forum_posts (thread_id, author_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$thread_id, $author_id, $content]);

        header("Location: topic.php?id=" . $thread_id);
        exit;
    }
}
?>

<main>
    <h1>Start a New Thread</h1>
    <?php if (!empty($errors)): ?>
        <div style="color:red;">
            <?php foreach ($errors as $e) echo "<p>" . htmlspecialchars($e) . "</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
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
