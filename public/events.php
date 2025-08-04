<?php
require_once '../includes/db.php';
require_once 'header.php';
require_once 'nav.php';
$page_title = "Events";

// Match nav.php style
$is_logged_in = isset($_SESSION['user']);
$user_id = $is_logged_in ? $_SESSION['user']['id'] : null;

$successMessage = '';
$errorMessage = '';

// Handle signup if user is logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id']) && $is_logged_in) {
    $event_id = $_POST['event_id'];

    // Check if user already signed up
    $checkStmt = $pdo->prepare("SELECT id FROM event_signups WHERE user_id = ? AND event_id = ?");
    $checkStmt->execute([$user_id, $event_id]);

    if ($checkStmt->rowCount() === 0) {
        $signupStmt = $pdo->prepare("INSERT INTO event_signups (user_id, event_id) VALUES (?, ?)");
        $signupStmt->execute([$user_id, $event_id]);
        $successMessage = "You have successfully signed up for the event!";
    } else {
        $errorMessage = "You've already signed up for this event.";
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


<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<link rel="stylesheet" href="assets/css/pages.css">
<style>
  .meta-item i {
  margin-right: 6px;
  color: #444;
}
.signed-up-label i {
  color: green;
  margin-right: 4px;
}

</style>
<div class="events-container">
  <header class="events-header">
    <h1>Community Events</h1>
    <p>Discover, participate, and connect with our community</p>
  </header>

  <?php if ($successMessage): ?>
    <div class="notification success-notification"><?php echo htmlspecialchars($successMessage); ?></div>
  <?php elseif ($errorMessage): ?>
    <div class="notification error-notification"><?php echo htmlspecialchars($errorMessage); ?></div>
  <?php endif; ?>

  <?php
  function renderEventSection($title, $events, $user_id) {
  if (count($events) === 0) return;

  echo '<div class="event-section">';
  echo '<div class="section-header">' . htmlspecialchars($title) . '</div>';
  echo '<div class="event-grid">';

  foreach ($events as $event) {
    $dateFormatted = date('F j, Y \a\t g:i A', strtotime($event['event_date']));
    $isSignedUp = $event['user_signed_up'];

    echo '<article class="event-card">';
    echo '<div class="event-header">';
    echo '<h3 class="event-title">' . htmlspecialchars($event['title']) . '</h3>';
    echo '<span class="event-date">' . $dateFormatted . '</span>';
    echo '</div>';

    echo '<div class="event-body">';
    echo '<div class="event-meta">';
    echo '<span class="meta-item"><i class="fas fa-map-marker-alt"></i> ' . htmlspecialchars($event['location']) . '</span>';
    echo '<span class="meta-item"><i class="fas fa-calendar-alt"></i> ' . htmlspecialchars($event['type']) . '</span>';
    echo '<span class="meta-item"><i class="fas fa-user"></i> ' . htmlspecialchars($event['creator']) . '</span>';
    echo '</div>';

    echo '<p class="event-description">' . nl2br(htmlspecialchars($event['description'])) . '</p>';
    echo '</div>';

    echo '<div class="event-footer">';
    echo '<span class="attendees-count">' . (int)$event['signup_count'] . ' attending</span>';

    if ($user_id) {
      if ($isSignedUp) {
        echo '<span class="signed-up-label"><i class="fas fa-check-circle"></i> You\'re attending</span>';
      } else {
        echo '<form method="post">';
        echo '<input type="hidden" name="event_id" value="' . (int)$event['id'] . '">';
        echo '<button type="submit" class="signup-button">Register for Event</button>';
        echo '</form>';
      }
    } else {
      echo '<span class="login-prompt"><a href="login.php">Login</a> to register</span>';
    }

    echo '</div>';
    echo '</article>';
  }

  echo '</div>';
  echo '</div>';
}


  renderEventSection("Happening Today", $ongoingEvents, $user_id);
  renderEventSection("Upcoming Events", $upcomingEvents, $user_id);
  renderEventSection("Past Events", $pastEvents, $user_id);
  ?>
</div>

<?php require_once 'footer.php'; ?>