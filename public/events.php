<?php
require_once '../includes/db.php';
require_once 'header.php';
require_once 'nav.php';
$page_title = "Events";

$user_id = $_SESSION['user_id'] ?? null;
$successMessage = '';
$errorMessage = '';

// Handle signup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id']) && $user_id) {
    $event_id = $_POST['event_id'];

    // Check if user already signed up
    $checkStmt = $pdo->prepare("SELECT id FROM event_signups WHERE user_id = ? AND event_id = ?");
    $checkStmt->execute([$user_id, $event_id]);
    
    if ($checkStmt->rowCount() === 0) {
        $signupStmt = $pdo->prepare("INSERT INTO event_signups (user_id, event_id) VALUES (?, ?)");
        $signupStmt->execute([$user_id, $event_id]);
        $successMessage = "You have successfully signed up for the event!";
    } else {
        $errorMessage = "You’ve already signed up for this event.";
    }
}

// Fetch and categorize events
function getEventsByCategory($pdo, $user_id, $condition) {
    $sql = "
        SELECT e.id, e.title, e.description, e.event_date, e.type, e.location, u.name AS creator,
               (SELECT COUNT(*) FROM event_signups WHERE event_id = e.id) AS signup_count,
               (SELECT 1 FROM event_signups WHERE event_id = e.id AND user_id = ?) AS user_signed_up
        FROM events e
        JOIN users u ON e.created_by = u.id
        WHERE $condition
        ORDER BY e.event_date ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$today = date('Y-m-d');
$pastEvents = getEventsByCategory($pdo, $user_id, "DATE(e.event_date) < '$today'");
$ongoingEvents = getEventsByCategory($pdo, $user_id, "DATE(e.event_date) = '$today'");
$upcomingEvents = getEventsByCategory($pdo, $user_id, "DATE(e.event_date) > '$today'");
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link rel="stylesheet" href="style.css">
  
</head>

<main class="container">
  <h1>Events</h1>

  <?php if ($successMessage): ?>
    <div style="color: green;"><?php echo htmlspecialchars($successMessage); ?></div>
  <?php elseif ($errorMessage): ?>
    <div style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></div>
  <?php endif; ?>

  <?php
  function renderEventSection($title, $events, $user_id) {
    if (count($events) === 0) return;

    echo "<h2>$title</h2><ul>";
    foreach ($events as $event) {
      echo "<li style='margin-bottom: 1.5em; border-bottom: 1px solid #ccc; padding-bottom: 1em;'>";
      echo "<h3>" . htmlspecialchars($event['title']) . "</h3>";
      echo "<p><strong>Date:</strong> " . date('F j, Y \a\t g:i A', strtotime($event['event_date'])) . "</p>";
      echo "<p><strong>Location:</strong> " . htmlspecialchars($event['location']) . "</p>";
      echo "<p><strong>Type:</strong> " . htmlspecialchars($event['type']) . "</p>";
      echo "<p><strong>Hosted by:</strong> " . htmlspecialchars($event['creator']) . "</p>";
      echo "<p>" . nl2br(htmlspecialchars($event['description'])) . "</p>";
      echo "<p><strong>Signed Up:</strong> " . (int)$event['signup_count'] . " attendee(s)</p>";

      if ($user_id) {
        if ($event['user_signed_up']) {
          echo "<p style='color: green;'>You’re already signed up for this event.</p>";
        } else {
          echo "<form method='post'>
                  <input type='hidden' name='event_id' value='{$event['id']}'>
                  <button type='submit'>Sign Up for this Event</button>
                </form>";
        }
      } else {
        echo "<p><em><a href='login.php'>Login</a> to sign up for events.</em></p>";
      }

      echo "</li>";
    }
    echo "</ul>";
  }

  renderEventSection("🔄 Ongoing Events", $ongoingEvents, $user_id);
  renderEventSection("🟢 Upcoming Events", $upcomingEvents, $user_id);
  renderEventSection("✅ Past Events", $pastEvents, $user_id);
  ?>
</main>

<?php require_once 'footer.php'; ?>
