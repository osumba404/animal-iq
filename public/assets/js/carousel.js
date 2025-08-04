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