<?php
// about.php - Homepage
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../cron/rotate_daily_animal.php';
require_once 'header.php';
require_once 'nav.php';


$teamMembers = getManagementTeam($pdo);

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


    <style>
        .team-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .team-member {
            flex: 1 1 250px;
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .team-member img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .team-member h3 {
            margin: 5px 0;
            font-size: 1.2rem;
        }
        .team-member p {
            font-size: 0.9rem;
            color: #555;
        }
        .team-member a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #0077b5; /* LinkedIn blue */
            font-weight: bold;
        }
    </style>


<h2 class="section-heading">The Animal IQ</h2>
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




<h1>Meet Our Management Team</h1>

<div class="team-container">
    <?php foreach ($teamMembers as $member): ?>
        <div class="team-member">
            <?php if (!empty($member['photo_url'])): ?>
                <img src="<?= htmlspecialchars($member['photo_url']); ?>" alt="<?= htmlspecialchars($member['name']); ?>">
            <?php else: ?>
                <img src="../uploads/management_team/" alt="Default Avatar">
            <?php endif; ?>

            <h3><?= htmlspecialchars($member['name']); ?></h3>
            <p><strong><?= htmlspecialchars($member['role']); ?></strong></p>
            <p><?= nl2br(htmlspecialchars($member['message'])); ?></p>

            <?php if (!empty($member['linkedin_url'])): ?>
                <a href="<?= htmlspecialchars($member['linkedin_url']); ?>" target="_blank">LinkedIn</a>
            <?php endif; ?>
            <?php if (!empty($member['ig_url'])): ?>
                <a href="<?= htmlspecialchars($member['ig_url']); ?>" target="_blank">Instagram</a>
            <?php endif; ?>
            <?php if (!empty($member['fb_url'])): ?>
                <a href="<?= htmlspecialchars($member['fb_url']); ?>" target="_blank">Facebook</a>
            <?php endif; ?>
            <?php if (!empty($member['x_url'])): ?>
                <a href="<?= htmlspecialchars($member['x_url']); ?>" target="_blank">X</a>
            <?php endif; ?>
         
        </div>
    <?php endforeach; ?>
</div>


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



<?php require_once 'footer.php'; ?>