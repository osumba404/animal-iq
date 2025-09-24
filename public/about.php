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

/* Management Team - Premium Cards */
.mgmt-team-title {
  text-align: center;
  font-size: 2rem;
  color: var(--color-primary); /* green heading */
  margin: 3rem auto 1rem;
  font-weight: 800;
}

.mgmt-team-grid {
  max-width: var(--content-max-width);
  margin: 0 auto 3rem;
  padding: 0 1.5rem;
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 1.5rem;
}

.mgmt-card {
  position: relative;
  display: flex;
  flex-direction: column;
  height: 620px; /* defines card height */
  background: rgba(255,255,255,0.65); /* glassmorphism base */
  backdrop-filter: saturate(120%) blur(8px);
  -webkit-backdrop-filter: saturate(120%) blur(8px);
  border-radius: 14px;
  overflow: hidden;
  border: 1px solid var(--color-border-light);
  box-shadow: 0 14px 28px rgba(0,0,0,0.08), 0 10px 10px rgba(0,0,0,0.06); /* Cupertino-like elevation */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.mgmt-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 18px 34px rgba(0,0,0,0.12), 0 12px 12px rgba(0,0,0,0.08);
}

.mgmt-image-wrap {
  position: relative;
  flex: 0 0 60%; /* upper 60% */
  width: 100%;
  overflow: hidden;
  background: var(--color-bg-secondary);
}

.mgmt-image-wrap img, .mgmt-image-fallback {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* soft gradient fade from image (top) into content (bottom) */
.mgmt-image-fade {
  position: absolute;
  left: 0; right: 0; bottom: 0;
  height: 28%;
  background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.08) 60%, rgba(0,0,0,0.14) 100%);
  pointer-events: none;
}

.mgmt-content {
  flex: 1; /* lower ~40% */
  padding: 1rem 1rem 1.1rem;
  display: flex;
  flex-direction: column;
  background: linear-gradient(
    to bottom,
    rgba(255,255,255,0.7) 0%,
    rgba(255,255,255,0.85) 60%,
    rgba(255,255,255,0.95) 100%
  );
}

.mgmt-name {
  margin: 0 0 0.25rem 0;
  color: var(--color-primary-dark);
  font-size: 1.15rem;
  font-weight: 700;
}

.mgmt-role {
  color: var(--color-accent-primary);
  font-weight: 600;
  font-size: 0.95rem;
  margin-bottom: 0.5rem;
}

.mgmt-bio {
  color: var(--color-text-secondary);
  font-size: 0.92rem;
  line-height: 1.5;
  margin: 0 0 0.75rem 0;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
}

.mgmt-social {
  margin-top: auto;
  display: flex;
  gap: 0.5rem;
}

.social-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: var(--color-primary-dark);
  background: rgba(255,255,255,0.6);
  border: 1px solid rgba(0,0,0,0.05);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.6), 0 4px 10px rgba(0,0,0,0.08);
  transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
}

.social-btn:hover {
  transform: translateY(-2px);
  background: var(--color-accent-primary);
  color: var(--color-primary-dark);
  box-shadow: 0 6px 14px rgba(0,0,0,0.12);
}

/* Responsive tweaks */
@media (max-width: 576px) {
  .mgmt-team-title { font-size: 1.6rem; }
  .mgmt-card { height: 400px; }
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




<h1 class="mgmt-team-title">Meet Our Management Team</h1>

<div class="mgmt-team-grid">
    <?php foreach ($teamMembers as $member): ?>
        <article class="mgmt-card">
            <div class="mgmt-image-wrap">
                <?php if (!empty($member['photo_url'])): ?>
                    <img src="<?= htmlspecialchars($member['photo_url']); ?>" alt="<?= htmlspecialchars($member['name']); ?>" loading="lazy">
                <?php else: ?>
                    <div class="mgmt-image-fallback" aria-hidden="true"></div>
                <?php endif; ?>
                <div class="mgmt-image-fade"></div>
            </div>
            <div class="mgmt-content">
                <h3 class="mgmt-name"><?= htmlspecialchars($member['name']); ?></h3>
                <span class="mgmt-role"><?= htmlspecialchars($member['role']); ?></span>
                <p class="mgmt-bio"><?= nl2br(htmlspecialchars($member['message'])); ?></p>
                <div class="mgmt-social">
                    <?php if (!empty($member['linkedin_url'])): ?>
                        <a class="social-btn" href="<?= htmlspecialchars($member['linkedin_url']); ?>" target="_blank" aria-label="LinkedIn">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M4.98 3.5C4.98 4.88 3.86 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.5 8.5h4V24h-4V8.5zm7 0h3.84v2.11h.05c.53-1 1.83-2.11 3.77-2.11 4.03 0 4.78 2.65 4.78 6.1V24h-4v-7.53c0-1.8-.03-4.12-2.51-4.12-2.51 0-2.9 1.96-2.9 3.99V24h-4V8.5z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($member['ig_url'])): ?>
                        <a class="social-btn" href="<?= htmlspecialchars($member['ig_url']); ?>" target="_blank" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.9.2 2.3.4.6.2 1 .5 1.5 1 .5.5.8.9 1 1.5.2.4.3 1.1.4 2.3.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.2 1.9-.4 2.3-.2.6-.5 1-1 1.5-.5.5-.9.8-1.5 1-.4.2-1.1.3-2.3.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.9-.2-2.3-.4-.6-.2-1-.5-1.5-1-.5-.5-.8-.9-1-1.5-.2-.4-.3-1.1-.4-2.3C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.2-1.9.4-2.3.2-.6.5-1 1-1.5.5-.5.9-.8 1.5-1 .4-.2 1.1-.3 2.3-.4C8.4 2.2 8.8 2.2 12 2.2m0-2.2C8.7 0 8.3 0 7 0 5.7 0 4.9.1 4.2.3 3.4.6 2.8.9 2.2 1.5 1.6 2.1 1.3 2.7 1 3.5.8 4.2.7 5 .6 6.3.5 7.6.5 8 0 12s0 3.3.1 4.6c.1 1.3.2 2.1.4 2.8.3.8.6 1.4 1.2 2 .6.6 1.2.9 2 1.2.7.2 1.5.3 2.8.4 1.3.1 1.7.1 4.6.1s3.3 0 4.6-.1c1.3-.1 2.1-.2 2.8-.4.8-.3 1.4-.6 2-1.2.6-.6.9-1.2 1.2-2 .2-.7.3-1.5.4-2.8.1-1.3.1-1.7.1-4.6s0-3.3-.1-4.6c-.1-1.3-.2-2.1-.4-2.8-.3-.8-.6-1.4-1.2-2-.6-.6-1.2-.9-2-1.2C19.3.1 18.5 0 17.2 0 15.9 0 15.5 0 12 0z"/><path d="M12 5.8A6.2 6.2 0 1 0 12 18.2 6.2 6.2 0 1 0 12 5.8m0-2.2a8.4 8.4 0 1 1 0 16.8 8.4 8.4 0 1 1 0-16.8z"/><circle cx="18.4" cy="5.6" r="1.4"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($member['fb_url'])): ?>
                        <a class="social-btn" href="<?= htmlspecialchars($member['fb_url']); ?>" target="_blank" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M22.675 0H1.325C.593 0 0 .593 0 1.326v21.348C0 23.406.593 24 1.325 24h11.49v-9.294H9.847V11.29h2.968V8.797c0-2.937 1.793-4.54 4.415-4.54 1.255 0 2.333.093 2.646.135v3.07h-1.816c-1.425 0-1.701.677-1.701 1.67v2.158h3.402l-.443 3.416h-2.959V24h5.803C23.406 24 24 23.406 24 22.674V1.326C24 .593 23.406 0 22.675 0z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($member['x_url'])): ?>
                        <a class="social-btn" href="<?= htmlspecialchars($member['x_url']); ?>" target="_blank" aria-label="X">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M18.146 2H21l-6.49 7.413L22 22h-6.828l-4.77-6.227L4.8 22H2l7.17-8.19L2 2h6.828l4.518 5.896L18.146 2zm-2.39 18h2.223L8.35 4H6.127l9.63 16z"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </article>
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