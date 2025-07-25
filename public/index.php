<?php
// index.php - Homepage
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../cron/rotate_daily_animal.php';
require_once 'header.php';
require_once 'nav.php';

$animal = getAnimalOfTheDay($pdo);
$quizzes = getLatestQuizzes($pdo);
$blogs = getApprovedBlogs($pdo, 3);
$events = getUpcomingEvents($pdo, 3);
$trivia = getRandomTrivia($pdo);
$endangered = getHighlightedEndangeredSpecies($pdo);


// dynamic data
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal IQ</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>

<div class="carousel-container">
    <div class="carousel-wrapper">
        <?php for ($i = 1; $i <= 3; $i++): 
            $image = $settings["carousel_image_$i"] ?? '';
            if ($image): ?>
                <div class="carousel-slide">
                    <img src="../uploads/images/<?= htmlspecialchars($image) ?>" alt="Slide <?= $i ?>">
                    <div class="caption">
                        <h2><?= htmlspecialchars($settings["carousel_headline_$i"] ?? '') ?></h2>
                        <p><?= htmlspecialchars($settings["carousel_text_$i"] ?? '') ?></p>
                        <a href="<?= htmlspecialchars($settings["carousel_cta_link_$i"] ?? '#') ?>" class="btn">
                            <?= htmlspecialchars($settings["carousel_cta_text_$i"] ?? 'Learn More') ?>
                        </a>
                    </div>
                </div>
            <?php endif;
        endfor; ?>
    </div>

    <button class="carousel-arrow prev" aria-label="Previous slide">&#10094;</button>
    <button class="carousel-arrow next" aria-label="Next slide">&#10095;</button>
    <div class="carousel-indicators"></div>
</div>

<main>
  <h1>Welcome to Animal IQ</h1>

   <?php if (!empty($settings['homepage_message'])): ?>
    <section>
      <h2>About Us</h2>
      <p><?= nl2br(htmlspecialchars($settings['homepage_message'])) ?></p>
    </section>
  <?php endif; ?>

  <?php if (!empty($settings['site_vision'])): ?>
    <section>
      <h2>🌟 Our Vision</h2>
      <p><?= nl2br(htmlspecialchars($settings['site_vision'])) ?></p>
    </section>
  <?php endif; ?>

  <?php if (!empty($settings['site_mission'])): ?>
    <section>
      <h2>🎯 Our Mission</h2>
      <p><?= nl2br(htmlspecialchars($settings['site_mission'])) ?></p>
    </section>
  <?php endif; ?>

  <!-- Animal of the Day -->
  <section>
    <?php if ($animal): ?>
      <h2>🐾 Animal of the Day: <?php echo htmlspecialchars($animal['common_name']); ?></h2>
      <img src="assets/images/animals/<?php echo htmlspecialchars($animal['main_photo']); ?>" alt="<?php echo htmlspecialchars($animal['common_name']); ?>" width="300">
      <!-- <p><?php echo nl2br(htmlspecialchars($animal['short_description'])); ?></p> -->
    <?php else: ?>
      <h2>No Animal of the Day</h2>
    <?php endif; ?>
  </section>

  <!-- Did You Know? -->
  <section>
    <h2>📚 Did You Know?</h2>
    <blockquote style="font-style: italic;">
      <?php echo htmlspecialchars($trivia ?: "Elephants can’t jump!"); ?>
    </blockquote>
  </section>

  <!-- New Blog Posts -->
  <section>
    <h2>📝 Latest Blog Posts</h2>
    <?php foreach ($blogs as $blog): ?>
      <article>
        <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
        <p><?php echo htmlspecialchars(substr($blog['summary'], 0, 100)) . '...'; ?></p>
        <a href="blog_post.php?id=<?php echo $blog['id']; ?>">Read more</a>
      </article>
    <?php endforeach; ?>
    <a href="blog.php">View all blog posts</a>
  </section>

  <!-- Upcoming Events -->
  <section>
    <h2>📅 Upcoming Events</h2>
    <ul>
      <?php foreach ($events as $event): ?>
        <li>
          <strong><?php echo htmlspecialchars($event['title']); ?></strong> –
          <?php echo date('M j, Y H:i', strtotime($event['event_date'])); ?> @ <?php echo htmlspecialchars($event['location']); ?>
        </li>
      <?php endforeach; ?>
    </ul>
    <a href="events.php">View all events</a>
  </section>

  <!-- New Quizzes -->
  <section>
    <h2>🧠 Try a New Quiz</h2>
    <ul>
      <?php foreach ($quizzes as $quiz): ?>
        <li><a href="take_quiz.php?id=<?php echo $quiz['id']; ?>"><?php echo htmlspecialchars($quiz['title']); ?></a></li>
      <?php endforeach; ?>
    </ul>
    <a href="quizzes.php">Explore quizzes</a>
  </section>

  <!-- Highlighted Endangered Species -->
  <section>
    <h2>🚨 Endangered Species Spotlight</h2>
    <?php foreach ($endangered as $animal): ?>
      <article>
        <h3><?php echo htmlspecialchars($animal['common_name']); ?></h3>
        <img src="assets/images/animals/<?php echo htmlspecialchars($animal['main_photo']); ?>" width="200">
        <!-- <p><?php echo htmlspecialchars(substr($animal['short_description'], 0, 100)) . '...'; ?></p> -->
      </article>
    <?php endforeach; ?>
    <a href="encyclopedia.php?filter=endangered">See more endangered species</a>
  </section>
</main>

<?php require_once 'footer.php'; ?>
<script src="assets/js/carousel.js"></script>

