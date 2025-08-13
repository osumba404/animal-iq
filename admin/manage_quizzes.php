<?php
require_once '../includes/db.php';
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ===== AJAX: Toggle Quiz Publish Status =====
if (isset($_POST['ajax']) && $_POST['ajax'] === 'toggle_status') {
    $id = (int) $_POST['id'];
    $new_status = (int) $_POST['new_status'];
    $stmt = $pdo->prepare("UPDATE quizzes SET is_published = ? WHERE id = ?");
    $stmt->execute([$new_status, $id]);
    echo json_encode(['success' => true, 'new_status' => $new_status]);
    exit;
}

// ===== ADD QUIZ =====
if (isset($_POST['add_quiz'])) {
    $stmt = $pdo->prepare("INSERT INTO quizzes (title, topic, difficulty) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['title'], $_POST['topic'], $_POST['difficulty']]);
    header("Location: manage_quizzes.php");
    exit;
}

// ===== UPDATE QUIZ =====
if (isset($_POST['update_quiz'])) {
    $stmt = $pdo->prepare("UPDATE quizzes SET title=?, topic=?, difficulty=? WHERE id=?");
    $stmt->execute([$_POST['edit_title'], $_POST['edit_topic'], $_POST['edit_difficulty'], $_POST['edit_id']]);
    header("Location: manage_quizzes.php");
    exit;
}

// ===== DELETE QUIZ =====
if (isset($_GET['delete_quiz'])) {
    $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ?");
    $stmt->execute([$_GET['delete_quiz']]);
    header("Location: manage_quizzes.php");
    exit;
}

// ===== ADD QUESTION =====
if (isset($_POST['add_question'])) {
    $stmt = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['quiz_id'], $_POST['question'], $_POST['option_a'], $_POST['option_b'], $_POST['option_c'], $_POST['option_d'], $_POST['correct_option']]);
    header("Location: manage_quizzes.php");
    exit;
}

// ===== DELETE QUESTION =====
if (isset($_GET['delete_question'])) {
    $stmt = $pdo->prepare("DELETE FROM quiz_questions WHERE id = ?");
    $stmt->execute([$_GET['delete_question']]);
    header("Location: manage_quizzes.php");
    exit;
}

// ===== FETCH QUIZZES AND QUESTIONS =====
$quizzes = $pdo->query("SELECT * FROM quizzes ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$questions_by_quiz = [];
foreach ($quizzes as $quiz) {
    $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
    $stmt->execute([$quiz['id']]);
    $questions_by_quiz[$quiz['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Quizzes</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ccc; }
        .btn { padding: 5px 10px; border: none; cursor: pointer; }
        .btn-edit { background: orange; color: white; }
        .btn-delete { background: red; color: white; }
        .btn-publish { background: blue; color: white; }
        .question-table { margin: 10px 0; background: #f9f9f9; }
        .toggle-questions { background: #666; color: white; padding: 3px 6px; cursor: pointer; }
    </style>
</head>
<body>
<h1>Manage Quizzes</h1>

<!-- Add Quiz -->
<form method="POST">
    <input type="text" name="title" placeholder="Title" required>
    <input type="text" name="topic" placeholder="Topic" required>
    <select name="difficulty">
        <option value="easy">Easy</option>
        <option value="medium">Medium</option>
        <option value="hard">Hard</option>
    </select>
    <button type="submit" name="add_quiz">Add Quiz</button>
</form>

<!-- Quizzes Table -->
<?php if ($quizzes): ?>
<table>
    <tr>
        <th>Title</th>
        <th>Topic</th>
        <th>Difficulty</th>
        <th>Created</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($quizzes as $quiz): ?>
    <tr>
        <td><?= htmlspecialchars($quiz['title']) ?></td>
        <td><?= htmlspecialchars($quiz['topic']) ?></td>
        <td><?= ucfirst($quiz['difficulty']) ?></td>
        <td><?= date('M j, Y', strtotime($quiz['created_at'])) ?></td>
        <td>
            <button class="btn btn-publish" data-id="<?= $quiz['id'] ?>" data-status="<?= $quiz['is_published'] ?>">
                <?= $quiz['is_published'] ? '✅ Published' : '❌ Unpublished' ?>
            </button>
        </td>
        <td>
            <a href="?delete_quiz=<?= $quiz['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete quiz?')">Delete</a>
            <button class="toggle-questions" data-quiz="<?= $quiz['id'] ?>">Questions (<?= count($questions_by_quiz[$quiz['id']]) ?>)</button>
        </td>
    </tr>
    <tr class="question-section" id="quiz-questions-<?= $quiz['id'] ?>" style="display:none;">
        <td colspan="6">
            <table class="question-table" width="100%">
                <tr>
                    <th>Question</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>Correct</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($questions_by_quiz[$quiz['id']] as $q): ?>
                <tr>
                    <td><?= htmlspecialchars($q['question']) ?></td>
                    <td><?= htmlspecialchars($q['option_a']) ?></td>
                    <td><?= htmlspecialchars($q['option_b']) ?></td>
                    <td><?= htmlspecialchars($q['option_c']) ?></td>
                    <td><?= htmlspecialchars($q['option_d']) ?></td>
                    <td><?= $q['correct_option'] ?></td>
                    <td><a href="?delete_question=<?= $q['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete question?')">Delete</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <!-- Add Question Form -->
            <form method="POST">
                <input type="hidden" name="quiz_id" value="<?= $quiz['id'] ?>">
                <input type="text" name="question" placeholder="Question" required>
                <input type="text" name="option_a" placeholder="Option A" required>
                <input type="text" name="option_b" placeholder="Option B" required>
                <input type="text" name="option_c" placeholder="Option C" required>
                <input type="text" name="option_d" placeholder="Option D" required>
                <select name="correct_option" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
                <button type="submit" name="add_question">Add Question</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php else: ?>
<p>No quizzes found.</p>
<?php endif; ?>

<script>
// Toggle Publish Status
document.querySelectorAll(".btn-publish").forEach(btn => {
    btn.addEventListener("click", function() {
        let id = this.dataset.id;
        let current = parseInt(this.dataset.status);
        let newStatus = current === 1 ? 0 : 1;

        fetch("manage_quizzes.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `ajax=toggle_status&id=${id}&new_status=${newStatus}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.dataset.status = data.new_status;
                this.textContent = data.new_status === 1 ? "✅ Published" : "❌ Unpublished";
            }
        });
    });
});

// Toggle Questions Section
document.querySelectorAll(".toggle-questions").forEach(btn => {
    btn.addEventListener("click", function() {
        let quizId = this.dataset.quiz;
        let section = document.getElementById("quiz-questions-" + quizId);
        section.style.display = section.style.display === "none" ? "table-row" : "none";
    });
});
</script>
</body>
</html>
