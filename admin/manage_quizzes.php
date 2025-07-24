<?php
// admin/manage_quizzes.php

require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'admin_header.php';

// Fetch quizzes
$stmt = $pdo->query("SELECT * FROM quizzes ORDER BY created_at DESC");
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle quiz deletion
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $deleteStmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ?");
    $deleteStmt->execute([$id]);
    header("Location: manage_quizzes.php");
    exit;
}
?>
<br><a href="dashboard.php">← Back to Dashboard</a>
<h1>Manage Quizzes</h1>

<!-- Add Quiz Form -->
<h2>Add New Quiz</h2>
<form method="POST">
    <label>Title: <input type="text" name="title" required></label><br>
    <label>Topic: <input type="text" name="topic" required></label><br>
    <label>Difficulty:
        <select name="difficulty" required>
            <option value="easy">Easy</option>
            <option value="medium">Medium</option>
            <option value="hard">Hard</option>
        </select>
    </label><br>
    <button type="submit" name="add_quiz">➕ Add Quiz</button>
</form>

<?php
// Handle quiz addition
if (isset($_POST['add_quiz'])) {
    $title = $_POST['title'];
    $topic = $_POST['topic'];
    $difficulty = $_POST['difficulty'];

    $insert = $pdo->prepare("INSERT INTO quizzes (title, topic, difficulty) VALUES (?, ?, ?)");
    $insert->execute([$title, $topic, $difficulty]);
    header("Location: manage_quizzes.php");
    exit;
}
?>

<!-- Quiz Table -->
<h2>All Quizzes</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Title</th>
            <th>Topic</th>
            <th>Difficulty</th>
            <th>Created At</th>
            <th>Questions</th>
            <th>Scores</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($quizzes): ?>
            <?php foreach ($quizzes as $quiz): ?>
                <tr>
                    <td><?= htmlspecialchars($quiz['title']) ?></td>
                    <td><?= htmlspecialchars($quiz['topic']) ?></td>
                    <td><?= htmlspecialchars($quiz['difficulty']) ?></td>
                    <td><?= htmlspecialchars($quiz['created_at']) ?></td>
                    <td>
                        <a href="manage_quizzes.php?quiz_id=<?= $quiz['id'] ?>">📝 Manage</a>
                    </td>
                    <td>
                        <a href="view_quiz_scores.php?quiz_id=<?= $quiz['id'] ?>">📊 View</a>
                    </td>
                    <td>
                        <a href="manage_quizzes.php?delete=<?= $quiz['id'] ?>" onclick="return confirm('Delete this quiz and all its questions?')">🗑️ Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">No quizzes found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
// Handle quiz question management view (simplified in same file)
if (isset($_GET['quiz_id'])):
    $quiz_id = (int) $_GET['quiz_id'];
    $quiz = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
    $quiz->execute([$quiz_id]);
    $quiz = $quiz->fetch();

    if ($quiz):
        echo "<hr><h2>Manage Questions for: " . htmlspecialchars($quiz['title']) . "</h2>";

        // Add new question
        if (isset($_POST['add_question'])) {
            $question = $_POST['question'];
            $a = $_POST['option_a'];
            $b = $_POST['option_b'];
            $c = $_POST['option_c'];
            $d = $_POST['option_d'];
            $correct = $_POST['correct_option'];

            $insertQ = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insertQ->execute([$quiz_id, $question, $a, $b, $c, $d, $correct]);
        }

        // Fetch quiz questions
        $qs = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
        $qs->execute([$quiz_id]);
        $questions = $qs->fetchAll();
?>
<form method="POST">
    <h3>Add Question</h3>
    <textarea name="question" rows="3" cols="60" required></textarea><br>
    A: <input type="text" name="option_a" required><br>
    B: <input type="text" name="option_b" required><br>
    C: <input type="text" name="option_c" required><br>
    D: <input type="text" name="option_d" required><br>
    Correct:
    <select name="correct_option" required>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
    </select><br>
    <button type="submit" name="add_question">➕ Add Question</button>
</form>

<h3>Existing Questions</h3>
<ol>
    <?php foreach ($questions as $q): ?>
        <li>
            <?= htmlspecialchars($q['question']) ?><br>
            A. <?= htmlspecialchars($q['option_a']) ?> |
            B. <?= htmlspecialchars($q['option_b']) ?> |
            C. <?= htmlspecialchars($q['option_c']) ?> |
            D. <?= htmlspecialchars($q['option_d']) ?><br>
            ✅ Correct: <?= $q['correct_option'] ?>
        </li>
    <?php endforeach; ?>
</ol>

<?php
    else:
        echo "<p>Quiz not found.</p>";
    endif;
endif;
?>


