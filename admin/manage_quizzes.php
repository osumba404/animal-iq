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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Quizzes - Admin Panel</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/tables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-content-container {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 4px;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .btn-back {
            background-color: var(--color-neutral-mid);
            color: var(--color-primary-dark);
        }
        
        .btn-back:hover {
            background-color: var(--color-primary-mid);
        }
        
        .btn-primary {
            background-color: var(--color-primary-accent);
            color: var(--color-primary-light);
        }
        
        .btn-primary:hover {
            background-color: var(--color-secondary-accent);
        }
        
        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
        
        .form-container {
            background-color: var(--color-neutral-light);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--color-primary-dark);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--color-neutral-mid);
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 100px;
        }
        
        .form-actions {
            margin-top: 1rem;
        }
        
        .difficulty-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .difficulty-easy {
            background-color: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
        }
        
        .difficulty-medium {
            background-color: rgba(241, 196, 15, 0.2);
            color: #f1c40f;
        }
        
        .difficulty-hard {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
        
        .questions-container {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--color-neutral-mid);
        }
        
        .questions-list {
            list-style-type: none;
            padding: 0;
        }
        
        .question-item {
            background-color: var(--color-neutral-light);
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        
        .question-text {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .options-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .correct-option {
            font-weight: 600;
            color: var(--color-primary-accent);
        }
        
        @media (max-width: 768px) {
            .admin-content-container {
                margin-left: 0;
                padding: 1rem;
            }
            
            .options-list {
                grid-template-columns: 1fr;
            }
            
            .data-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="admin-content-container">
        <div class="page-header">
            <h1><i class="fas fa-question-circle"></i> Manage Quizzes</h1>
            <a href="dashboard.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <!-- Add Quiz Form -->
        <div class="form-container">
            <h2><i class="fas fa-plus"></i> Add New Quiz</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="topic">Topic</label>
                    <input type="text" id="topic" name="topic" required>
                </div>
                
                <div class="form-group">
                    <label for="difficulty">Difficulty</label>
                    <select id="difficulty" name="difficulty" required>
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="add_quiz" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Quiz
                    </button>
                </div>
            </form>
        </div>

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
        <h2><i class="fas fa-list"></i> All Quizzes</h2>
        <?php if ($quizzes): ?>
            <table class="data-table">
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
                    <?php foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td><?= htmlspecialchars($quiz['title']) ?></td>
                            <td><?= htmlspecialchars($quiz['topic']) ?></td>
                            <td>
                                <span class="difficulty-badge difficulty-<?= $quiz['difficulty'] ?>">
                                    <?= ucfirst($quiz['difficulty']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($quiz['created_at'])) ?></td>
                            <td>
                                <a href="manage_quizzes.php?quiz_id=<?= $quiz['id'] ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Manage
                                </a>
                            </td>
                            <td>
                                <a href="view_quiz_scores.php?quiz_id=<?= $quiz['id'] ?>" class="btn-view">
                                    <i class="fas fa-chart-bar"></i> View
                                </a>
                            </td>
                            <td>
                                <a href="manage_quizzes.php?delete=<?= $quiz['id'] ?>" 
                                   class="btn-delete"
                                   onclick="return confirm('Are you sure you want to delete this quiz and all its questions?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-question-circle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>No quizzes found</p>
            </div>
        <?php endif; ?>

        <?php
        // Handle quiz question management view
        if (isset($_GET['quiz_id'])):
            $quiz_id = (int) $_GET['quiz_id'];
            $quiz = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
            $quiz->execute([$quiz_id]);
            $quiz = $quiz->fetch();

            if ($quiz):
        ?>
        <div class="questions-container">
            <div class="page-header">
                <h2><i class="fas fa-question"></i> Manage Questions for: <?= htmlspecialchars($quiz['title']) ?></h2>
                <a href="manage_quizzes.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Quizzes
                </a>
            </div>

            <?php
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

            <div class="form-container">
                <h3><i class="fas fa-plus"></i> Add Question</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="question">Question</label>
                        <textarea id="question" name="question" required></textarea>
                    </div>
                    
                    <div class="options-list">
                        <div class="form-group">
                            <label for="option_a">Option A</label>
                            <input type="text" id="option_a" name="option_a" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="option_b">Option B</label>
                            <input type="text" id="option_b" name="option_b" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="option_c">Option C</label>
                            <input type="text" id="option_c" name="option_c" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="option_d">Option D</label>
                            <input type="text" id="option_d" name="option_d" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="correct_option">Correct Option</label>
                        <select id="correct_option" name="correct_option" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="add_question" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Question
                        </button>
                    </div>
                </form>
            </div>

            <h3><i class="fas fa-list-ol"></i> Existing Questions</h3>
            <?php if ($questions): ?>
                <ul class="questions-list">
                    <?php foreach ($questions as $q): ?>
                        <li class="question-item">
                            <div class="question-text"><?= htmlspecialchars($q['question']) ?></div>
                            <div class="options-list">
                                <div class="<?= $q['correct_option'] === 'A' ? 'correct-option' : '' ?>">A. <?= htmlspecialchars($q['option_a']) ?></div>
                                <div class="<?= $q['correct_option'] === 'B' ? 'correct-option' : '' ?>">B. <?= htmlspecialchars($q['option_b']) ?></div>
                                <div class="<?= $q['correct_option'] === 'C' ? 'correct-option' : '' ?>">C. <?= htmlspecialchars($q['option_c']) ?></div>
                                <div class="<?= $q['correct_option'] === 'D' ? 'correct-option' : '' ?>">D. <?= htmlspecialchars($q['option_d']) ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <p>No questions found for this quiz</p>
                </div>
            <?php endif; ?>
        </div>
        <?php
            else:
                echo "<p>Quiz not found.</p>";
            endif;
        endif;
        ?>
    </div>
</body>
</html>