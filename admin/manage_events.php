<?php
require_once '../includes/db.php';
require_once 'admin_header.php';

$action = $_GET['action'] ?? null;
$eventId = $_GET['id'] ?? null;

// Handle deletion
if ($action === 'delete' && $eventId) {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    header("Location: manage_events.php");
    exit;
}

// Handle edit request
if ($action === 'edit' && $eventId) {
    $editStmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $editStmt->execute([$eventId]);
    $editEvent = $editStmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $type = $_POST['type'];
        $description = $_POST['description'];
        $event_date = $_POST['event_date'];
        $location = $_POST['location'];

        $updateStmt = $pdo->prepare("UPDATE events SET title=?, type=?, description=?, event_date=?, location=? WHERE id=?");
        $updateStmt->execute([$title, $type, $description, $event_date, $location, $eventId]);

        header("Location: manage_events.php");
        exit;
    }
}

// View signups
$signups = [];
if ($action === 'view_signups' && $eventId) {
    $signupStmt = $pdo->prepare("SELECT s.*, u.name FROM event_signups s JOIN users u ON s.user_id = u.id WHERE s.event_id = ?");
    $signupStmt->execute([$eventId]);
    $signups = $signupStmt->fetchAll(PDO::FETCH_ASSOC);
}

$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Admin Panel</title>
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
        
        .edit-form {
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--color-neutral-mid);
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 150px;
        }
        
        .form-actions {
            margin-top: 1.5rem;
            display: flex;
            gap: 1rem;
        }
        
        .signups-container {
            margin-bottom: 2rem;
        }
        
        .signups-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .event-count {
            background-color: var(--color-primary-accent);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.85rem;
        }
        
        @media (max-width: 768px) {
            .admin-content-container {
                margin-left: 0;
                padding: 1rem;
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
            <h1><i class="fas fa-calendar-alt"></i> Manage Events</h1>
            <div class="header-actions">
                <a href="dashboard.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
                <a href="add_event.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Event
                </a>
            </div>
        </div>

        <?php if ($action === 'edit' && $editEvent): ?>
            <div class="edit-form">
                <h3><i class="fas fa-edit"></i> Edit Event</h3>
                <form method="post">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($editEvent['title']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="type">Type</label>
                        <input type="text" id="type" name="type" value="<?= htmlspecialchars($editEvent['type']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?= htmlspecialchars($editEvent['description']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="event_date">Date</label>
                        <input type="date" id="event_date" name="event_date" value="<?= htmlspecialchars($editEvent['event_date']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" id="location" name="location" value="<?= htmlspecialchars($editEvent['location']) ?>" required>
                    </div>
                    
                    <div class="form-actions">
                        <a href="manage_events.php" class="btn btn-back">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Event</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($action === 'view_signups'): ?>
            <div class="signups-container">
                <div class="signups-header">
                    <h3><i class="fas fa-users"></i> Event Signups</h3>
                    <span class="event-count"><?= count($signups) ?> signups</span>
                </div>
                
                <?php if ($signups): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Signed Up At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($signups as $signup): ?>
                                <tr>
                                    <td><?= htmlspecialchars($signup['name']) ?></td>
                                    <td><?= date('M j, Y g:i a', strtotime($signup['signed_up_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No signups found for this event.</p>
                    </div>
                <?php endif; ?>
                <br>
                <a href="manage_events.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Events
                </a>
            </div>
        <?php endif; ?>

        <h3><i class="fas fa-list"></i> All Events</h3>
        <?php if ($events): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Location</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Signups</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['title']) ?></td>
                            <td><?= htmlspecialchars($event['type']) ?></td>
                            <td><?= htmlspecialchars(substr($event['description'], 0, 50)) . (strlen($event['description']) > 50 ? '...' : '') ?></td>
                            <td><?= date('M j, Y', strtotime($event['event_date'])) ?></td>
                            <td><?= htmlspecialchars($event['location']) ?></td>
                            <td><?= htmlspecialchars($event['created_by']) ?></td>
                            <td><?= date('M j, Y', strtotime($event['created_at'])) ?></td>
                            <td>
                                <?php
                                $signupStmt = $pdo->prepare("SELECT COUNT(*) FROM event_signups WHERE event_id = ?");
                                $signupStmt->execute([$event['id']]);
                                $count = $signupStmt->fetchColumn();
                                ?>
                                <span class="event-count"><?= $count ?></span>
                                <a href="?action=view_signups&id=<?= $event['id'] ?>" class="btn-view">
                                    <i class="fas fa-users"></i> View
                                </a>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="?action=edit&id=<?= $event['id'] ?>" class="btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?action=delete&id=<?= $event['id'] ?>" 
                                       class="btn-delete" 
                                       title="Delete"
                                       onclick="return confirm('Are you sure you want to delete this event?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>No events found</p>
                <a href="add_event.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Event
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>