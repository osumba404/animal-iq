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

$randomAnimals = getRandomAnimalsByClass($pdo);




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

</head>

<style>
/* public/assets/css/style.css */

/* Base Styles */
/* Premium Homepage Styles */
:root {
    --hero-gradient: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary-light) 100%);
    --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
    --section-spacing: 5rem;
    --content-max-width: 1200px;
}

body {
  font-family: 'Arial', sans-serif;
  line-height: 1.6;
  margin: 0;
  padding: 0;
  background-color: var( --color-bg-primary);
  color: var( --color-bg-primary);
}

main {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

/* ==================== */
/* Premium Carousel */
/* ==================== */
.carousel-container {
    position: relative;
    width: 100%;
    max-height: 60vh;
    margin: 1rem 0 2rem;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.carousel-wrapper {
    display: flex;
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    height: 100%;
}

.carousel-slide {
    min-width: 100%;
    position: relative;
    overflow: hidden;
}

.carousel-slide::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        to bottom,
        rgba(1, 50, 33, 0.2) 0%,
        rgba(0, 36, 24, 0.7) 100%
    );
    z-index: 1;
}

.carousel-slide img {
    width: 100%;
    height: 60vh;
    object-fit: cover;
    object-position: center;
    transition: transform 8s cubic-bezier(0.16, 1, 0.3, 1);
}

.carousel-slide.active img {
    transform: scale(1.03);
}

.carousel-slide .caption {
    position: absolute;
    bottom: 15%;
    left: 10%;
    max-width: 80%;
    color: var(--color-text-inverted);
    z-index: 2;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.carousel-slide h2 {
    font-size: 2rem;
    margin-bottom: 0.75rem;
    line-height: 1.2;
}

.carousel-slide p {
    font-size: 1rem;
    margin-bottom: 1.5rem;
    max-width: 600px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.carousel-slide .btn {
    padding: 0.6rem 1.2rem;
    font-size: 0.9rem;
    border-radius: 25px;
    background: var(--color-accent-primary);
    color: var(--color-primary-dark);
    text-decoration: none;
    font-weight: 600;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    display: inline-block;
}

.carousel-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    border: 1px solid rgba(232, 184, 36, 0.3);
    color: var(--color-text-inverted);
    font-size: 1.5rem;
    cursor: pointer;
    border-radius: 50%;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
}

.carousel-arrow:hover {
    background: var(--color-accent-primary);
    color: var(--color-primary-dark);
    transform: translateY(-50%) scale(1.1);
}

.prev {
    left: 2rem;
}

.next {
    right: 2rem;
}

.carousel-indicators {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.8rem;
    z-index: 10;
}

.carousel-indicators span {
    display: block;
    width: 40px;
    height: 4px;
    background: rgba(255,255,255,0.3);
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 2px;
}

.carousel-indicators span.active {
    background: var(--color-accent-primary);
    width: 60px;
}

/* Animation for slide transitions */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.carousel-slide {
    animation: fadeIn 0.8s ease-out;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .carousel-slide .caption {
        left: 5%;
        max-width: 90%;
        bottom: 5%;
    }
    
    .carousel-slide h2 {
        font-size: 2.5rem;
    }
    
    .carousel-slide p {
        font-size: 1.2rem;
    }
    
    .carousel-arrow {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }
}

@media (max-width: 768px) {
    .carousel-slide h2 {
        font-size: 2rem;
    }
    
    .carousel-slide p {
        font-size: 1rem;
    }
    
    .carousel-slide .btn {
        padding: 0.8rem 1.5rem;
    }
}


/* Featured Animal Section - Premium Redesign */
.featured-section {
    margin: 2.5rem 0;
    background: var(--color-bg-secondary);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.featured-section:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
}

.featured-animal {
    display: flex;
    flex-direction: row;
    max-height: 375px;
}

.featured-animal-image {
    position: relative;
    flex: 0 0 40%;
    max-width: 40%;
    height: auto;
    overflow: hidden;
    background: var(--color-bg-primary);
}

.featured-animal-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.featured-animal:hover .featured-animal-image img {
    transform: scale(1.03);
}

.featured-animal-content {
    flex: 1;
    padding: 2rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.animal-badge {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: var(--color-accent-primary);
    color: var(--color-primary-dark);
    padding: 0.5rem 1.25rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    z-index: 2;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    animation: pulse 2s infinite;
}

.animal-icon {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: rgba(255, 255, 255, 0.9);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    z-index: 2;
}

.animal-icon svg {
    width: 20px;
    height: 20px;
    color: var(--color-accent-primary);
}

.animal-title {
    font-size: 1.8rem;
    margin: 0 0 0.5rem 0;
    color: var(--color-primary-dark);
    line-height: 1.2;
}

.animal-scientific {
    display: block;
    font-style: italic;
    color: var(--color-text-secondary);
    margin-bottom: 1.25rem;
    font-size: 1.1rem;
}

.animal-description {
    color: var(--color-text-primary);
    line-height: 1.7;
    margin-bottom: 1.75rem;
}

.animal-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    font-size: 0.95rem;
    color: var(--color-text-secondary);
}

.meta-item svg {
    width: 16px;
    height: 16px;
    margin-right: 0.5rem;
    color: var(--color-accent-primary);
}

.animal-link {
    display: inline-flex;
    align-items: center;
    color: var(--color-accent-primary);
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    padding: 0.5rem 0;
}

.animal-link:hover {
    color: var(--color-accent-secondary);
    transform: translateX(5px);
}

.animal-link svg {
    margin-left: 0.5rem;
    transition: transform 0.3s ease;
}

.animal-link:hover svg {
    transform: translateX(3px);
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .featured-animal {
        flex-direction: column;
        min-height: auto;
    }
    
    .featured-animal-image {
        flex: 0 0 100%;
        max-width: 100%;
        height: 250px;
    }
    
    .featured-animal-content {
        padding: 1.5rem;
    }
}

@media (max-width: 576px) {
    .featured-animal-content {
        padding: 1.5rem;
    }
    
    .animal-title {
        font-size: 1.5rem;
    }
    
    .animal-scientific {
        font-size: 1rem;
    }
    
    .animal-description {
        font-size: 0.95rem;
    }
}

/* Trivia Section - Premium Redesign */
.trivia-section {
    background: var(--color-bg-secondary);
    padding: 1.5rem;
    border-radius: 8px;
    margin: 2rem 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.trivia-container {
    display: flex;
    align-items: flex-start;
    gap: 1.2rem;
}

.trivia-icon {
    flex-shrink: 0;
    background: var(--color-accent-primary);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary-dark);
}

.trivia-content h3 {
    margin: 0 0 0.5rem 0;
    color: var(--color-primary-dark);
    font-size: 1.2rem;
}

.trivia-content p {
    margin: 0;
    color: var(--color-text-primary);
    line-height: 1.5;
    font-size: 0.95rem;
}

/* Premium CTA Styles */
.cta-premium {
    padding: 5rem 2rem;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, 
        var(--color-primary-dark) 0%, 
        var(--color-primary) 100%);
}

.cta-container {
    max-width: var(--content-max-width);
    margin: 0 auto;
    display: flex;
    align-items: center;
    gap: 4rem;
    position: relative;
    z-index: 2;
}

.cta-content {
    flex: 1;
    color: var(--color-text-inverted);
}

.cta-title {
    font-size: 3rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    letter-spacing: -0.5px;
}

.highlight {
    color: var(--color-accent-primary);
    position: relative;
    display: inline-block;
}

.highlight::after {
    content: '';
    position: absolute;
    bottom: 5px;
    left: 0;
    width: 100%;
    height: 8px;
    background: rgba(232, 184, 36, 0.3);
    z-index: -1;
    transform: skew(-15deg);
}

.cta-text {
    font-size: 1.2rem;
    line-height: 1.7;
    margin-bottom: 2.5rem;
    max-width: 600px;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.cta-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    overflow: hidden;
}

.cta-btn svg {
    margin-left: 0.75rem;
    transition: transform 0.3s ease;
}

.primary-btn {
    background: var(--color-accent-primary);
    color: var(--color-primary-dark);
    box-shadow: 0 4px 15px rgba(232, 184, 36, 0.4);
}

.primary-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(232, 184, 36, 0.5);
}

.primary-btn:hover svg {
    transform: translateX(5px);
}

.secondary-btn {
    background: transparent;
    color: var(--color-text-inverted);
    border: 2px solid var(--color-accent-primary);
    box-shadow: 0 4px 15px rgba(232, 184, 36, 0.1);
}

.secondary-btn:hover {
    background: rgba(232, 184, 36, 0.1);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(232, 184, 36, 0.2);
}

.secondary-btn:hover svg {
    transform: rotate(45deg);
}

.cta-decoration {
    position: relative;
    flex: 0 0 40%;
    min-height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.deco-circle {
    width: 350px;
    height: 350px;
    border-radius: 50%;
    background: radial-gradient(
        circle, 
        rgba(232, 184, 36, 0.15) 0%, 
        transparent 75%
    );
    position: absolute;
    animation: float 6s ease-in-out infinite;
}

.deco-dots {
    position: absolute;
    width: 100%;
    height: 100%;
    background-image: radial-gradient(
        var(--color-accent-primary) 1px, 
        transparent 1px
    );
    background-size: 15px 15px;
    opacity: 0.5;
    animation: rotate 60s linear infinite;
}

/* Animations */
@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 992px) {
    .cta-container {
        flex-direction: column;
        text-align: center;
        gap: 2rem;
    }
    
    .cta-title {
        font-size: 2.5rem;
    }
    
    .cta-text {
        margin-left: auto;
        margin-right: auto;
    }
    
    .cta-buttons {
        justify-content: center;
    }
    
    .cta-decoration {
        display: none;
    }
}

@media (max-width: 768px) {
    .cta-premium {
        padding: 4rem 1.5rem;
    }
    
    .cta-title {
        font-size: 2rem;
    }
    
    .cta-btn {
        padding: 0.8rem 1.5rem;
        font-size: 0.9rem;
    }
}

/* Premium Content Grid Styles */
.premium-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin: 2.5rem 0;
    max-width: var(--content-max-width);
    padding: 0 2rem;
}

.content-card {
    background: var(--color-bg-secondary);
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.content-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--color-border-light);
}

.section-icon {
    width: 24px;
    height: 24px;
    margin-right: 0.75rem;
    color: var(--color-accent-primary);
}

.section-title {
    font-size: 1.25rem;
    margin: 0;
    color: var(--color-primary-dark);
}

/* Card Styles */
.card-stack {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}



.premium-card {
    background: var(--color-bg-primary);
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid var(--color-border-light);
}

.premium-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.card-image-container {
    position: relative;
    height: 160px;
    overflow: hidden;
}

.card-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
}

.premium-card:hover .card-image {
    transform: scale(1.05);
}

.card-content {
    padding: 1.25rem;
}

.card-title {
    font-size: 1.1rem;
    margin: 0 0 0.75rem 0;
    color: var(--color-primary-dark);
    line-height: 1.3;
}

.card-excerpt {
    font-size: 0.9rem;
    color: var(--color-text-secondary);
    margin-bottom: 1rem;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-action {
    display: inline-flex;
    align-items: center;
    color: var(--color-accent-primary);
    font-weight: 600;
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.card-action svg {
    margin-left: 0.5rem;
    transition: transform 0.3s ease;
    width: 16px;
    height: 16px;
}

.card-action:hover {
    color: var(--color-accent-secondary);
}

.card-action:hover svg {
    transform: translateX(3px);
}

/* Event Card Specific Styles */
.event-card {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
}

.event-date {
    background: var(--color-accent-primary);
    color: var(--color-primary-dark);
    padding: 0.75rem;
    border-radius: 6px;
    text-align: center;
    margin-right: 1rem;
    min-width: 50px;
    flex-shrink: 0;
}

.event-day {
    display: block;
    font-size: 1.25rem;
    font-weight: 700;
    line-height: 1;
}

.event-month {
    display: block;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 0.25rem;
}

.event-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin: 0.5rem 0;
}

.event-detail {
    display: flex;
    align-items: center;
    font-size: 0.85rem;
    color: var(--color-text-secondary);
}

.event-detail svg {
    margin-right: 0.5rem;
    width: 14px;
    height: 14px;
    color: var(--color-accent-primary);
}

/* Quiz Card Specific Styles */
.quiz-card {
    display: flex;
    align-items: center;
}

.quiz-icon {
    flex: 0 0 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(232, 184, 36, 0.1);
}

.quiz-icon svg {
    width: 32px;
    height: 32px;
    stroke: var(--color-accent-primary);
}

/* Section CTA Styles */
.section-cta {
    display: inline-flex;
    align-items: center;
    margin-top: 2rem;
    color: var(--color-accent-primary);
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.section-cta svg {
    margin-left: 0.5rem;
    transition: transform 0.3s ease;
    width: 18px;
    height: 18px;
}

.section-cta:hover {
    color: var(--color-accent-secondary);
}

.section-cta:hover svg {
    transform: translateX(3px);
}

/* Endangered Species Section */
.endangered-section {
    margin: 3rem 0;
    max-width: var(--content-max-width);
    margin-left: auto;
    margin-right: auto;
    padding: 0 1.5rem;
}

.species-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin: 1.5rem 0;
    width: 100%;
}

.species-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: var(--color-bg-primary);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 1px solid var(--color-border-light);
}

.species-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.species-image-container {
    position: relative;
    width: 100%;
    padding-top: 66.67%; /* 3:2 aspect ratio */
    overflow: hidden;
}

.species-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.species-card:hover .species-image {
    transform: scale(1.05);
}

.species-content {
    padding: 1.25rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.species-name {
    font-size: 1.1rem;
    margin: 0 0 0.5rem 0;
    color: var(--color-primary-dark);
    line-height: 1.3;
}

.species-scientific {
    font-style: italic;
    color: var(--color-text-secondary);
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
}

.species-description {
    font-size: 0.9rem;
    color: var(--color-text-primary);
    line-height: 1.5;
    margin-bottom: 1rem;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.species-link {
    display: inline-flex;
    align-items: center;
    color: var(--color-accent-primary);
    font-weight: 600;
    text-decoration: none;
    font-size: 0.9rem;
    margin-top: auto;
    transition: all 0.3s ease;
}

.species-link:hover {
    color: var(--color-accent-secondary);
}

.species-link svg {
    margin-left: 0.5rem;
    transition: transform 0.3s ease;
    width: 16px;
    height: 16px;
}

.species-link:hover svg {
    transform: translateX(3px);
}

.endangered-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: #e74c3c;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    z-index: 2;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .species-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.25rem;
    }
    
    .species-content {
        padding: 1rem;
    }
    
    .species-name {
        font-size: 1rem;
    }
    
    .species-scientific,
    .species-description {
        font-size: 0.85rem;
    }
}

/* Animation */
@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateY(10px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

.content-card, 
.featured-section, 
.trivia-section, 
.endangered-section {
    animation: fadeIn 0.6s ease-out forwards;
    opacity: 0;
}

.content-card:nth-child(2) { 
    animation-delay: 0.1s; 
}

.content-card:nth-child(3) { 
    animation-delay: 0.2s; 
}

.trivia-section { 
    animation-delay: 0.1s; 
}

.endangered-section { 
    animation-delay: 0.2s; 
}



.species-grid {
    display: flex;
    flex-direction: row;
    gap: 1.5rem;
    flex-wrap: wrap;
    justify-content: flex-start;
}
</style>


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
<?php
if (!empty($randomAnimals)):
    foreach ($randomAnimals as $item):
        ?>

        <div class="animal-card" style="position:relative; display:inline-block; margin:10px;">
            <img src="<?php echo $item['animal_photo']; ?>" 
                 alt="<?php echo $item['animal_name']; ?>" 
                 style="width:300px; height:200px; object-fit:cover; border-radius:8px;">
            
            <!-- Top Overlay -->
            <div style="position:absolute; top:10px; left:10px; background:rgba(0,0,0,0.6); 
                        color:#fff; padding:5px 10px; border-radius:5px;">
                <?php echo $item['class_name']; ?>
            </div>

            <!-- Bottom Overlay -->
            <div style="position:absolute; bottom:10px; left:10px; background:rgba(255,255,255,0.85); 
                        color:#000; padding:5px 10px; border-radius:5px;">
                <a href="encyclopedia.php?class_id=<?php echo $item['class_id']; ?>" 
                   style="color:#000; text-decoration:none;">
                    View other <?php echo $item['class_name']; ?>
                </a>
            </div>
        </div>
<?php
    endforeach;
else:
    echo "<p>No animals found.</p>";
endif;

?>


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
                        <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                        <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
<section class="content-card">
    <div class="section-header">
        <svg class="section-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
        </svg>
        <h2 class="section-title">Endangered Species Spotlight</h2>
    </div>
    
    <div class="species-grid">
        <?php foreach ($endangered as $animal): ?>
        <article class="premium-card">
            <div class="card-image-container">
                <img src="../uploads/animals/<?= htmlspecialchars($animal['main_photo']) ?>" 
                     alt="<?= htmlspecialchars($animal['common_name']) ?>" 
                     class="card-image"
                     loading="lazy">
                <div class="endangered-badge">Endangered</div>
            </div>
            <div class="card-content">
                <h3 class="card-title"><?= htmlspecialchars($animal['common_name']) ?></h3>
                <p class="card-excerpt"><?= htmlspecialchars(substr($animal['appearance'], 0, 100)) . '...'; ?></p>
                <a href="animal.php?id=<?= $animal['id'] ?>" class="card-action">
                    Learn More
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    
    <a href="encyclopedia.php?filter=endangered" class="section-cta">
        View All Endangered Species
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
    </a>
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
