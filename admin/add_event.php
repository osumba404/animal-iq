<!-- admin/add_event.php -->

<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $type = trim($_POST['type']);
    $location = trim($_POST['location']);
    $created_by = 'admin@example.com'; // Replace with actual admin session/email

    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, type, location, created_by) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $event_date, $type, $location, $created_by]);

    header("Location: manage_events.php");
    exit;
}
?>

<h1>Add New Event</h1>

<form method="post">
    <label>Title: <input type="text" name="title" required></label><br><br>
    <label>Description:<br>
        <textarea name="description" rows="4" cols="50" required></textarea>
    </label><br><br>
    <label>Date & Time: <input type="datetime-local" name="event_date" required></label><br><br>
    <label>Type: <input type="text" name="type" required></label><br><br>
    <label>Location: <input type="text" name="location" required></label><br><br>
    <button type="submit">✅ Save Event</button>
</form>

<p><a href="manage_events.php">🔙 Back to Events</a></p>
