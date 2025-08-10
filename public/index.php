<?php
// index.php - Homepage
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../cron/rotate_daily_animal.php';
require_once 'header.php';
require_once 'nav.php';

$timezone; 
$animal = getAnimalOfTheDay($pdo, $timezone);
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

// Fetch all partners
$stmt = $pdo->query("SELECT * FROM partners ORDER BY partners_since DESC");
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
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


<!-- Caurosell -->
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



<!-- about us -->

<h2 class="section-heading">Discover the Animal Kingdom</h2>

<main class="about-section">
    <!-- About Us - Image Left -->
    <?php if (!empty($settings['homepage_message'])): ?>
    <div class="about-row">
        <div class="about-image-container">
            <?php if (!empty($settings['about_image'])): ?>
                <img src="<?= htmlspecialchars($settings['about_image']) ?>" alt="About Us" class="about-image">
            <?php else: ?>
                <img src="assets/img/elephant.webp" alt="African Savanah Elephant" class="about-image">
            <?php endif; ?>
        </div>
        <div class="about-content">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <h3 class="about-title">About Us</h3>
            <div class="about-text"><?= nl2br(htmlspecialchars($settings['homepage_message'])) ?></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Vision - Image Right -->
    <?php if (!empty($settings['site_vision'])): ?>
    <div class="about-row reverse">
        <div class="about-image-container">
            <?php if (!empty($settings['vision_image'])): ?>
                <img src="<?= htmlspecialchars($settings['vision_image']) ?>" alt="Our Vision" class="about-image">
            <?php else: ?>
                <img src="assets/img/dolphin.jpeg" alt="Pandas" class="about-image">
            <?php endif; ?>
        </div>
        <div class="about-content">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M12 8v4l3 3"></path>
                </svg>
            </div>
            <h3 class="about-title">Our Vision</h3>
            <div class="about-text"><?= nl2br(htmlspecialchars($settings['site_vision'])) ?></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Mission - Image Left -->
    <?php if (!empty($settings['site_mission'])): ?>
    <div class="about-row">
        <div class="about-image-container">
            <?php if (!empty($settings['mission_image'])): ?>
                <img src="<?= htmlspecialchars($settings['mission_image']) ?>" alt="Our Mission" class="about-image">
            <?php else: ?>
                <img src="assets/img/tiger.avif" alt="Jungle Tiger" class="about-image">
            <?php endif; ?>
        </div>
        <div class="about-content">
            <div class="card-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
            </div>
            <h3 class="about-title">Our Mission</h3>
            <div class="about-text"><?= nl2br(htmlspecialchars($settings['site_mission'])) ?></div>
        </div>
    </div>
    <?php endif; ?>
</main>


<!-- Partners -->
    
<section class="partners-compact">
    <div class="partners-container">
        <h2 class="section-title">Our Partners</h2>
        
        <?php if ($partners): ?>
        <div class="partners-logos">
            <?php foreach ($partners as $partner): ?>
                <?php if ($partner['logo_url']): ?>
                <div class="partner-logo-wrap">
                    <img src="../<?= htmlspecialchars($partner['logo_url']) ?>" 
                         alt="<?= htmlspecialchars($partner['name']) ?> Logo" 
                         class="partner-logo"
                         loading="lazy"
                         title="<?= htmlspecialchars($partner['name']) ?>">
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="no-partners">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <p>No partners to display</p>
        </div>
        <?php endif; ?>
    </div>
</section>


 <!-- Animal of the Day -->
<section class="featured-section">
    <?php if ($animal): ?>
        <div class="featured-animal">
            <div class="featured-animal-image">
                <img src="../uploads/animals/<?= htmlspecialchars($animal['main_photo']) ?>" 
                    alt="Animal of the Day - <?= htmlspecialchars($animal['common_name']) ?>" 
                    loading="lazy">
                <div class="animal-badge">Animal of the Day</div>
            </div>
            <div class="featured-animal-content">
                <div class="animal-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 11h19M5 11l1-7h12l1 7M9 11v5M15 11v5M4 15h16a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1z"></path>
                    </svg>
                </div>
                <h2 class="animal-title">Featured Animal: <?= htmlspecialchars($animal['common_name']) ?></h2>
                <p class="animal-scientific"><?= nl2br(htmlspecialchars($animal['scientific_name'])) ?></p>
                <div class="animal-description"><?= nl2br(htmlspecialchars($animal['appearance'])) ?></div>
                <a href="animal.php?id=<?= $animal['id'] ?>" class="animal-link">
                    Learn more about <?= htmlspecialchars($animal['common_name']) ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="featured-animal empty-state">
            <div class="empty-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <h2>No Animal of the Day</h2>
            <p>Check back tomorrow for our featured animal!</p>
        </div>
    <?php endif; ?>
</section>

<!-- Did You Know? -->
<section class="trivia-section">
    <div class="trivia-container">
        <div class="trivia-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
        </div>
        <div class="trivia-content">
            <h3>Did You Know?</h3>
            <p><?= htmlspecialchars($trivia ?: "Elephants can't jump!") ?></p>
        </div>
    </div>
</section>


    <!-- Premium Call to Action -->
<section class="cta-premium">
    <div class="cta-container">
        <div class="cta-content">
            <h2 class="cta-title">Join Our <span class="highlight">Community</span></h2>
            <p class="cta-text">Discover amazing facts, participate in quizzes, and connect with fellow animal enthusiasts from around the world.</p>
            <div class="cta-buttons">
                <a href="register.php" class="cta-btn primary-btn">
                    <span>Sign Up</span>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <a href="about.php" class="cta-btn secondary-btn">
                    <span>Learn More</span>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 16V12M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
        <div class="cta-decoration">
            <div class="deco-circle"></div>
            <div class="deco-dots"></div>
        </div>
    </div>
</section>




<!-- Premium Content Grid -->
<div class="premium-grid">
    <!-- New Blog Posts -->
    <section class="content-card">
        <div class="section-header">
            <svg class="section-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            <h2 class="section-title">Latest Blog Posts</h2>
        </div>
        
        <div class="card-stack">
            <?php foreach ($blogs as $blog): ?>
            <article class="premium-card">
                <div class="card-image-container">
                    <img src="../uploads/posts/<?= htmlspecialchars($blog['featured_image']) ?>" 
                         alt="<?= htmlspecialchars($blog['title']) ?>" 
                         class="card-image"
                         loading="lazy">
                    <div class="image-overlay"></div>
                </div>
                <div class="card-content">
                    <h3 class="card-title"><?= htmlspecialchars($blog['title']) ?></h3>
                    <p class="card-excerpt"><?= htmlspecialchars(substr($blog['summary'], 0, 100)) . '...'; ?></p>
                    <a href="blog_post.php?id=<?= $blog['id'] ?>" class="card-action">
                        Read more
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <a href="blog.php" class="section-cta">
            View all blog posts
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </section>

    <!-- Upcoming Events -->
    <section class="content-card">
        <div class="section-header">
            <svg class="section-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <h2 class="section-title">Upcoming Events</h2>
        </div>
        
        <div class="card-stack">
            <?php foreach ($events as $event): ?>
            <article class="premium-card event-card">
                <div class="event-date">
                    <span class="event-day"><?= date('d', strtotime($event['event_date'])) ?></span>
                    <span class="event-month"><?= date('M', strtotime($event['event_date'])) ?></span>
                </div>
                <div class="card-content">
                    <h3 class="card-title"><?= htmlspecialchars($event['title']) ?></h3>
                    <div class="event-details">
                        <div class="event-detail">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <?= date('g:i A', strtotime($event['event_date'])) ?>
                        </div>
                        <div class="event-detail">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <?= htmlspecialchars($event['location']) ?>
                        </div>
                    </div>
                    <a href="event.php?id=<?= $event['id'] ?>" class="card-action">
                        Event details
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <a href="events.php" class="section-cta">
            View all events
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </section>

    <!-- New Quizzes -->
    <section class="content-card">
        <div class="section-header">
            <svg class="section-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                <path d="M9 7h6"></path>
                <path d="M9 12h6"></path>
                <path d="M9 17h4"></path>
            </svg>
            <h2 class="section-title">Try a New Quiz</h2>
        </div>
        
        <div class="card-stack">
            <?php foreach ($quizzes as $quiz): ?>
            <article class="premium-card quiz-card">
                <div class="quiz-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                        <line x1="9" y1="9" x2="9.01" y2="9"></line>
                        <line x1="15" y1="9" x2="15.01" y2="9"></line>
                    </svg>
                </div>
                <div class="card-content">
                    <h3 class="card-title"><?= htmlspecialchars($quiz['title']) ?></h3>
                    <a href="take_quiz.php?id=<?= $quiz['id'] ?>" class="card-action">
                        Take quiz
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <a href="quizzes.php" class="section-cta">
            Explore quizzes
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </section>
</div>

<!-- Endangered Species Spotlight -->
<section class="endangered-section">
    <div class="section-header">
        <svg class="section-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <h2 class="section-title">Endangered Species Spotlight</h2>
    </div>
    
    <div class="species-grid">
        <?php foreach ($endangered as $animal): ?>
        <article class="species-card">
            <div class="species-image-container">
                <img src="assets/images/animals/<?= htmlspecialchars($animal['main_photo']) ?>" 
                     alt="<?= htmlspecialchars($animal['common_name']) ?>" 
                     class="species-image"
                     loading="lazy">
                <div class="endangered-badge">Endangered</div>
                <div class="image-overlay"></div>
            </div>
            <div class="species-content">
                <h3 class="species-name"><?= htmlspecialchars($animal['common_name']) ?></h3>
                <p class="species-scientific"><?= htmlspecialchars($animal['scientific_name']) ?></p>
                <p class="species-description"><?= htmlspecialchars(substr($animal['short_description'], 0, 100)) . '...'; ?></p>
                <a href="animal.php?id=<?= $animal['id'] ?>" class="species-link">
                    Learn more
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    
    <div class="section-footer">
        <a href="encyclopedia.php?filter=endangered" class="section-cta">
            See more endangered species
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
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
