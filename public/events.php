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

<style>
/* Premium Events Page Styling */
.events-container {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 2rem;
}

.events-header {
  text-align: center;
  margin-bottom: 3rem;
  position: relative;
}

.events-header h1 {
  font-size: 3rem;
  color: var(--color-primary);
  margin-bottom: 1.5rem;
  position: relative;
  display: inline-block;
}

.events-header h1::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 25%;
  width: 50%;
  height: 3px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-accent-primary), var(--color-primary));
  border-radius: 3px;
}

.notification {
  padding: 1rem;
  margin: 1rem auto;
  max-width: 800px;
  border-radius: 8px;
  text-align: center;
  font-weight: bold;
}

.success-notification {
  background-color: rgba(26, 138, 106, 0.1);
  color: var(--color-success);
  border-left: 4px solid var(--color-success);
}

.error-notification {
  background-color: rgba(194, 59, 34, 0.1);
  color: var(--color-error);
  border-left: 4px solid var(--color-error);
}

.event-section {
  margin-bottom: 4rem;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  font-size: 1.8rem;
  color: var(--color-primary);
  margin-bottom: 1.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid var(--color-border-light);
}

.event-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
}

.event-card {
  background: var(--color-bg-primary);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(30, 24, 17, 0.08);
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
  display: flex;
  flex-direction: column;
  height: 100%;
  border: 1px solid var(--color-border-light);
}

.event-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 32px rgba(30, 24, 17, 0.15);
}

.event-header {
  padding: 1.5rem 1.5rem 0;
}

.event-title {
  font-size: 1.5rem;
  color: var(--color-primary);
  margin-bottom: 0.5rem;
  line-height: 1.3;
}

.event-date {
  display: inline-block;
  background: var(--color-primary-light);
  color: white;
  padding: 0.3rem 0.8rem;
  border-radius: 50px;
  font-size: 0.9rem;
  margin-bottom: 1rem;
}

.event-body {
  padding: 0 1.5rem 1.5rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.event-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.8rem;
  margin-bottom: 1rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  font-size: 0.9rem;
  color: var(--color-text-muted);
}

.meta-item i {
  color: var(--color-primary);
}

.event-description {
  color: var(--color-text-secondary);
  margin-bottom: 1.5rem;
  line-height: 1.6;
}

.event-footer {
  margin-top: auto;
  padding: 1rem 1.5rem;
  background: var(--color-bg-secondary);
  border-top: 1px solid var(--color-border-light);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.attendees-count {
  font-size: 0.9rem;
  color: var(--color-text-muted);
}

.signup-button {
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 50px;
  padding: 0.5rem 1.2rem;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: bold;
}

.signup-button:hover {
  background: var(--color-primary-dark);
  transform: translateY(-2px);
}

.signed-up-label {
  color: var(--color-success);
  font-weight: bold;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.login-prompt {
  color: var(--color-text-muted);
  font-style: italic;
}

@media (max-width: 768px) {
  .events-header h1 {
    font-size: 2.2rem;
  }
  
  .event-grid {
    grid-template-columns: 1fr;
  }
  
  .section-header {
    font-size: 1.5rem;
  }
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
    echo '<div class="section-header">' . $title . '</div>';
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
      echo '<span class="meta-item"><i>📍</i>' . htmlspecialchars($event['location']) . '</span>';
      echo '<span class="meta-item"><i>🎭</i>' . htmlspecialchars($event['type']) . '</span>';
      echo '<span class="meta-item"><i>👤</i>' . htmlspecialchars($event['creator']) . '</span>';
      echo '</div>';
      
      echo '<p class="event-description">' . nl2br(htmlspecialchars($event['description'])) . '</p>';
      echo '</div>';
      
      echo '<div class="event-footer">';
      echo '<span class="attendees-count">' . (int)$event['signup_count'] . ' attending</span>';
      
      if ($user_id) {
        if ($isSignedUp) {
          echo '<span class="signed-up-label"><i>✓</i> You\'re attending</span>';
        } else {
          echo '<form method="post">';
          echo '<input type="hidden" name="event_id" value="' . $event['id'] . '">';
          echo '<button type="submit" class="signup-button">Attend</button>';
          echo '</form>';
        }
      } else {
        echo '<span class="login-prompt"><a href="login.php">Login</a> to attend</span>';
      }
      
      echo '</div>';
      echo '</article>';
    }
    
    echo '</div>';
    echo '</div>';
  }

  renderEventSection("🟢 Happening Today", $ongoingEvents, $user_id);
  renderEventSection("🔜 Upcoming Events", $upcomingEvents, $user_id);
  renderEventSection("✅ Past Events", $pastEvents, $user_id);
  ?>
</div>

<?php require_once 'footer.php'; ?>