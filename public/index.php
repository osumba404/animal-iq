<?php
// index.php - Homepage
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../cron/rotate_daily_animal.php';
require_once 'header.php';
require_once 'nav.php';

$animal = getAnimalOfTheDay($pdo);
$quizzes = getLatestQuizzes($pdo,  1);
$blogs = getApprovedBlogs($pdo, null);
$events = getUpcomingEvents($pdo, 2);
$trivia = getRandomTrivia($pdo, 2);
$endangered = getHighlightedEndangeredSpecies($pdo, 3);

// dynamic data
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}
?>

<style>
/* Premium Homepage Styles */
:root {
    --hero-gradient: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary-light) 100%);
    --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
    --section-spacing: 5rem;
    --content-max-width: 1200px;
}

body {
    font-family: 'Bahnschrift', 'DIN Alternate', sans-serif;
    line-height: 1.6;
    color: var(--color-text-primary);
}

/* =========== Hero Carousel ==========*/
.carousel-container {
    position: relative;
    width: 100%;
    max-height: 80vh;
    overflow: hidden;
    margin-bottom: var(--section-spacing);
}

.carousel-wrapper {
    display: flex;
    transition: transform 0.5s ease;
    height: 100%;
}

.carousel-slide {
    min-width: 100%;
    position: relative;
}

.carousel-slide img {
    width: 100%;
    height: 80vh;
    object-fit: cover;
    object-position: center;
}

.carousel-slide .caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    /* background: var(--color-bg-dark); */
    background: linear-gradient(to top, var(--color-bg-dark), rgba(0, 0, 0, 0));
    color: var(--color-text-inverted);
    padding: 3rem;
    max-width: 100%;
    margin: 0 auto;
}

.carousel-slide h2 {
    font-size: 4rem;
    font-weight: 900;
    margin-bottom: 0.0;
    text-shadow: 0 18px 7px rgba(0,0,0,0.3);
}

.carousel-slide p {
    font-size: 2rem;
    margin-bottom: 0.3rem;
    max-width: 600px;
}

.carousel-slide .btn {
    display: inline-block;
    background: var(--color-accent-primary);
    color: var(--color-primary-dark);
    padding: 0.8rem 1.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
}

.carousel-slide .btn:hover {
    background: var(--color-accent-secondary);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.carousel-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: white;
    font-size: 2rem;
    padding: 1rem;
    cursor: pointer;
    border-radius: 50%;
    transition: all 0.3s ease;
    z-index: 10;
}

.carousel-arrow:hover {
    background: rgba(255,255,255,0.5);
}

.prev {
    left: 2rem;
}

.next {
    right: 2rem;
}

.carousel-indicators {
    position: absolute;
    bottom: 1rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.5rem;
    z-index: 10;
}

.carousel-indicators span {
    display: block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    cursor: pointer;
}

.carousel-indicators span.active {
    background: white;
}

/* Main Content Layout */
main {
    max-width: var(--content-max-width);
    margin: 0 auto;
    padding: 0 2rem;
}

/* Section Styling */
section {
    margin-bottom: var(--section-spacing);
    position: relative;
}

section h2 {
    font-size: 2rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    position: relative;
    color: var(--color-primary-dark);
}

section h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 4px;
    background: var(--color-accent-primary);
}

/* Grid Layouts */
.grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
}

.grid-3 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

/* Cards */
.card {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--card-shadow);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.15);
}

.card-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.card-body {
    padding: 1.5rem;
}

.card-title {
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    color: var(--color-primary-dark);
}

.card-text {
    color: var(--color-text-secondary);
    margin-bottom: 1rem;
}

.card-link {
    display: inline-block;
    color: var(--color-accent-primary);
    font-weight: bold;
    text-decoration: none;
    transition: color 0.3s ease;
}

.card-link:hover {
    color: var(--color-accent-secondary);
}

/* Animal of the Day */
.animal-of-day {
    background: var(--color-bg-secondary);
    padding: 2rem;
    border-radius: 10px;
    display: flex;
    align-items: center;
    gap: 2rem;
}

.animal-of-day img {
    width: 300px;
    height: 300px;
    object-fit: cover;
    border-radius: 10px;
    border: 5px solid white;
    box-shadow: var(--card-shadow);
}

.animal-of-day-content {
    flex: 1;
}

/* Trivia Section */
.trivia-block {
    background: var(--color-primary-dark);
    color: white;
    padding: 2rem;
    border-radius: 10px;
    position: relative;
    overflow: hidden;
}

.trivia-block::before {
    content: '❝';
    position: absolute;
    top: 0;
    left: 0;
    font-size: 8rem;
    opacity: 0.1;
    line-height: 1;
}

.trivia-content {
    position: relative;
    z-index: 1;
    font-size: 1.5rem;
    font-style: italic;
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

/* Call to Action */
.cta-section {
    background: var(--hero-gradient);
    color: white;
    text-align: center;
    padding: 4rem 2rem;
    border-radius: 10px;
    margin: var(--section-spacing) 0;
}

.cta-section h2 {
    color: white;
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
}

.cta-section p {
    font-size: 1.2rem;
    max-width: 700px;
    margin: 0 auto 2rem;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.cta-button {
    display: inline-block;
    padding: 1rem 2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
}

.cta-primary {
    background: var(--color-accent-primary);
    color: var(--color-primary-dark);
}

.cta-primary:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.cta-secondary {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.cta-secondary:hover {
    background: rgba(255,255,255,0.1);
    transform: translateY(-2px);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .carousel-slide .caption {
        padding: 1.5rem;
    }
    
    .carousel-slide h2 {
        font-size: 1.8rem;
    }
    
    .animal-of-day {
        flex-direction: column;
    }
    
    .animal-of-day img {
        width: 100%;
        height: auto;
    }
    
    .grid-2, .grid-3 {
        grid-template-columns: 1fr;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
}
</style>

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
    </section>

    <!-- Animal of the Day -->
    <section>
        <?php if ($animal): ?>
            <div class="animal-of-day">
                <img src="assets/images/animals/<?= htmlspecialchars($animal['main_photo']) ?>" alt="<?= htmlspecialchars($animal['common_name']) ?>">
                <div class="animal-of-day-content">
                    <h2>🐾 Animal of the Day: <?= htmlspecialchars($animal['common_name']) ?></h2>
                    <p><?= nl2br(htmlspecialchars($animal['short_description'])) ?></p>
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
                    <img src="assets/images/blogs/<?= htmlspecialchars($blog['featured_image']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="card-img">
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($blog['title']) ?></h3>
                        <p class="card-text"><?= htmlspecialchars(substr($blog['summary'], 0, 100)) . '...'; ?></p>
                        <a href="blog_post.php?id=<?= $blog['id'] ?>" class="card-link">Read more</a>
                    </div>
                </div>
            <?php endforeach; ?>
            <a href="blog.php" class="cta-button cta-secondary" style="display: inline-block; margin-top: 1rem;">View all blog posts</a>
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
            <a href="events.php" class="cta-button cta-secondary" style="display: inline-block; margin-top: 1rem;">View all events</a>
        </section>

        <!-- New Quizzes -->
        <section>
            <h2>🧠 Try a New Quiz</h2>
            <?php foreach ($quizzes as $quiz): ?>
                <div class="card" style="margin-bottom: 1.5rem;">
                    <img src="assets/images/quizzes/<?= htmlspecialchars($quiz['featured_image']) ?>" alt="<?= htmlspecialchars($quiz['title']) ?>" class="card-img">
                    <div class="card-body">
                        <h3 class="card-title"><?= htmlspecialchars($quiz['title']) ?></h3>
                        <p class="card-text"><?= htmlspecialchars(substr($quiz['description'], 0, 100)) . '...'; ?></p>
                        <a href="take_quiz.php?id=<?= $quiz['id'] ?>" class="card-link">Take quiz</a>
                    </div>
                </div>
            <?php endforeach; ?>
            <a href="quizzes.php" class="cta-button cta-secondary" style="display: inline-block; margin-top: 1rem;">Explore quizzes</a>
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
<!-- <script src="assets/js/carousel.js"></script> -->
<script>
// Enhanced Carousel Functionality
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.querySelector('.carousel-wrapper');
    const slides = document.querySelectorAll('.carousel-slide');
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    const indicators = document.querySelector('.carousel-indicators');
    let currentIndex = 0;
    let isAnimating = false;
    const slideCount = slides.length;
    let autoSlideInterval;

    // Initialize carousel
    function initCarousel() {
        // Create indicators
        indicators.innerHTML = '';
        for (let i = 0; i < slideCount; i++) {
            const indicator = document.createElement('span');
            if (i === 0) indicator.classList.add('active');
            indicator.addEventListener('click', () => goToSlide(i));
            indicators.appendChild(indicator);
        }
        
        // Set first slide as active
        slides[0].classList.add('active');
        setTimeout(() => {
            document.querySelector('.carousel-slide.active .caption-content').style.opacity = '1';
            document.querySelector('.carousel-slide.active .caption-content').style.transform = 'translateY(0)';
        }, 100);
        
        startAutoSlide();
    }

    function updateCarousel() {
        wrapper.style.transform = `translateX(-${currentIndex * 100}%)`;
        
        // Update slide active state
        slides.forEach((slide, index) => {
            slide.classList.toggle('active', index === currentIndex);
        });
        
        // Update indicators
        document.querySelectorAll('.carousel-indicators span').forEach((ind, index) => {
            ind.classList.toggle('active', index === currentIndex);
        });
        
        // Reset animation for caption
        const activeCaption = document.querySelector('.carousel-slide.active .caption-content');
        if (activeCaption) {
            activeCaption.style.opacity = '0';
            activeCaption.style.transform = 'translateY(20px)';
            setTimeout(() => {
                activeCaption.style.opacity = '1';
                activeCaption.style.transform = 'translateY(0)';
            }, 50);
        }
    }

    function goToSlide(index) {
        if (isAnimating) return;
        
        isAnimating = true;
        currentIndex = (index + slideCount) % slideCount;
        updateCarousel();
        
        // Reset auto slide timer
        resetAutoSlide();
        
        setTimeout(() => {
            isAnimating = false;
        }, 700);
    }

    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    function prevSlide() {
        goToSlide(currentIndex - 1);
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(nextSlide, 6000);
    }

    function resetAutoSlide() {
        clearInterval(autoSlideInterval);
        startAutoSlide();
    }

    // Event listeners
    nextBtn.addEventListener('click', nextSlide);
    prevBtn.addEventListener('click', prevSlide);

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') nextSlide();
        if (e.key === 'ArrowLeft') prevSlide();
    });

    // Touch events for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    wrapper.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, {passive: true});

    wrapper.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, {passive: true});

    function handleSwipe() {
        if (touchEndX < touchStartX - 50) nextSlide();
        if (touchEndX > touchStartX + 50) prevSlide();
    }

    // Initialize
    initCarousel();
});
</script>