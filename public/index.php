<?php
// index.php - Homepage
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../cron/rotate_daily_animal.php';
require_once 'header.php';
require_once 'nav.php';

$animal = getAnimalOfTheDay($pdo);
$quizzes = getLatestQuizzes($pdo,  2);
$blogs = getApprovedBlogs($pdo, 1);
$events = getUpcomingEvents($pdo);
$trivia = getRandomTrivia($pdo, 2);
$endangered = getHighlightedEndangeredSpecies($pdo, 3);

// dynamic data
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}
?>

<head>
    <title><?= htmlspecialchars($settings['site_name'] ?? 'Animal IQ - Wildlife Community & Encyclopedia') ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($settings['homepage_description'] ?? 'Join Animal IQ to explore the animal kingdom, take quizzes, read blogs, and learn about endangered species.') ?>">
    <meta name="keywords" content="animals, wildlife, endangered species, animal facts, quizzes, animal blog, Animal IQ">
    <meta name="author" content="Animal IQ Community">
    <link rel="canonical" href="https://animaliq.world/index.php">
    
    <!-- Open Graph (Facebook & LinkedIn) -->
    <meta property="og:title" content="Animal IQ - Discover the Animal Kingdom">
    <meta property="og:description" content="<?= htmlspecialchars($settings['homepage_description'] ?? 'Explore animals, take quizzes, read blogs, and more.') ?>">
    <meta property="og:image" content="https://animaliq.world/uploads/images/<?= $settings['carousel_image_1'] ?? 'default.jpg' ?>">
    <meta property="og:url" content="https://animaliq.world/index.php">
    <meta property="og:type" content="website">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Animal IQ - Explore Wildlife">
    <meta name="twitter:description" content="<?= htmlspecialchars($settings['homepage_description'] ?? '') ?>">
    <meta name="twitter:image" content="https://animaliq.world/uploads/animals/<?= $settings['carousel_image_1'] ?? 'default.jpg' ?>">

    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="manifest" href="manifest.json">


    <link rel="stylesheet" href="assets/css/style.css">
</head>



<div class="carousel-container">
    <div class="carousel-wrapper">
        <?php for ($i = 1; $i <= 3; $i++): 
            $image = $settings["carousel_image_$i"] ?? '';
            if ($image): ?>
                <div class="carousel-slide">
                    <img src="../uploads/images/<?= htmlspecialchars($image) ?>" alt="Slide <?= $i ?>" loading="lazy">
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

<h1>Welcome to Animal IQ - Discover the Animal Kingdom</h1>

<main>
    <!-- About Sections -->
    <section class="grid-2">
        <?php if (!empty($settings['homepage_message'])): ?>
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">About Us</h2>
                    <p class="card-text"><?= nl2br(htmlspecialchars($settings['homepage_message'])) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($settings['site_vision'])): ?>
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">🌟 Our Vision</h2>
                    <p class="card-text"><?= nl2br(htmlspecialchars($settings['site_vision'])) ?></p>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($settings['site_mission'])): ?>
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">🌟 Our Mission</h2>
                    <p class="card-text"><?= nl2br(htmlspecialchars($settings['site_mission'])) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Animal of the Day -->
    <section>
        <?php if ($animal): ?>
            <div class="animal-of-day">
                <img src="uploads/animals/<?= htmlspecialchars($animal['main_photo']) ?>" 
                    alt="Animal of the Day - <?= htmlspecialchars($animal['common_name']) ?>" loading="lazy">
                <div class="animal-of-day-content">
                    <h2>🐾 Animal of the Day: <?= htmlspecialchars($animal['common_name']) ?></h2>
                    <p><?= nl2br(htmlspecialchars($animal['scientific_name'])) ?></p>
                    <p><?= nl2br(htmlspecialchars($animal['appearance'])) ?></p>
                    <a href="animal.php?id=<?= $animal['id'] ?>" class="card-link">Learn more about <?= htmlspecialchars($animal['common_name']) ?></a>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <h2>No Animal of the Day</h2>
                    <p>Check back tomorrow for our featured animal!</p>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Did You Know? -->
    <section>
        <div class="trivia-block">
            <div class="trivia-content">
                <?= htmlspecialchars($trivia ?: "Elephants can't jump!") ?>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <h2>Join Our Community</h2>
        <p>Discover amazing facts, participate in quizzes, and connect with fellow animal enthusiasts from around the world.</p>
        <div class="cta-buttons">
            <a href="register.php" class="cta-button cta-primary">Sign Up Free</a>
            <a href="about.php" class="cta-button cta-secondary">Learn More</a>
        </div>
    </section>

    <!-- Content Sections Grid -->
    <div class="grid-3">
        <!-- New Blog Posts -->
        <section>
            <h2>📝 Latest Blog Posts</h2>
            <?php foreach ($blogs as $blog): ?>
                <div class="card" style="margin-bottom: 1.5rem;">
                    <img src="../uploads/posts/<?= htmlspecialchars($blog['featured_image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="card-img" loading="lazy">
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($blog['title']) ?></h3>
                        <p class="card-text"><?= htmlspecialchars(substr($blog['summary'], 0, 100)) . '...'; ?></p>
                        <a href="blog_post.php?id=<?= $blog['id'] ?>" class="card-link">Read more</a>
                    </div>
                </div>
            <?php endforeach; ?>
            <a href="blog.php" class="cta-button cta-primary" style="display: inline-block; margin-top: 1rem;">View all blog posts</a>
        </section>

        <!-- Upcoming Events -->
        <section>
            <h2>📅 Upcoming Events</h2>
            <?php foreach ($events as $event): ?>
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <p class="card-text">
                            <strong>When:</strong> <?= date('M j, Y H:i', strtotime($event['event_date'])) ?><br>
                            <strong>Where:</strong> <?= htmlspecialchars($event['location']) ?>
                        </p>
                        <a href="event.php?id=<?= $event['id'] ?>" class="card-link">Event details</a>
                    </div>
                </div>
            <?php endforeach; ?>
            <a href="events.php" class="cta-button cta-primary" style="display: inline-block; margin-top: 1rem;">View all events</a>
        </section>

        <!-- New Quizzes -->
        <section>
            <h2>🧠 Try a New Quiz</h2>
            <?php foreach ($quizzes as $quiz): ?>
                <div class="card" style="margin-bottom: 1.5rem;">
                    <!-- <img src="assets/images/quizzes/<?= htmlspecialchars($quiz['featured_image']) ?>" alt="<?= htmlspecialchars($quiz['title']) ?>" class="card-img"> -->
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($quiz['title']) ?></h3>
                        <!-- <p class="card-text"><?= htmlspecialchars(substr($quiz['description'], 0, 100)) . '...'; ?></p> -->
                        <a href="take_quiz.php?id=<?= $quiz['id'] ?>" class="card-link">Take quiz</a>
                    </div>
                </div>
            <?php endforeach; ?>
            <a href="quizzes.php" class="cta-button cta-primary" style="display: inline-block; margin-top: 1rem;">Explore quizzes</a>
        </section>
    </div>

    <!-- Highlighted Endangered Species -->
    <section>
        <h2>🚨 Endangered Species Spotlight</h2>
        <div class="grid-3">
            <?php foreach ($endangered as $animal): ?>
                <div class="card">
                    <img src="assets/images/animals/<?= htmlspecialchars($animal['main_photo']) ?>" alt="<?= htmlspecialchars($animal['common_name']) ?>" class="card-img">
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($animal['common_name']) ?></h3>
                        <p class="card-text"><?= htmlspecialchars(substr($animal['short_description'], 0, 100)) . '...'; ?></p>
                        <a href="animal.php?id=<?= $animal['id'] ?>" class="card-link">Learn more</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 2rem;">
            <a href="encyclopedia.php?filter=endangered" class="cta-button cta-primary">See more endangered species</a>
        </div>
    </section>
</main>

<?php require_once 'footer.php'; ?>
<script src="assets/js/carousel.js"></script>
<script>
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Animal IQ",
  "url": "https://yourdomain.com",
  "logo": "https://yourdomain.com/assets/images/logo.png",
  "sameAs": [
    "https://www.facebook.com/yourpage",
    "https://www.instagram.com/yourpage"
  ]
}
</script>
