<?php
// admin/view_quiz_scores.php
require_once '../includes/db.php';

$stmt = $pdo->query("
    SELECT 
        quiz_scores.*, 
        quizzes.title AS quiz_title 
    FROM quiz_scores 
    JOIN quizzes ON quiz_scores.quiz_id = quizzes.id 
    ORDER BY taken_at DESC
");
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Quiz Scores</h1>

<table border="1" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
            <th>User Email</th>
            <th>Quiz Title</th>
            <th>Score</th>
            <th>Taken At</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($scores as $score): ?>
            <tr>
                <td><?= htmlspecialchars($score['user_email']) ?></td>
                <td><?= htmlspecialchars($score['quiz_title']) ?></td>
                <td><?= htmlspecialchars($score['score']) ?></td>
                <td><?= htmlspecialchars($score['taken_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
