// public/assets/css/carousel.css 

document.addEventListener('DOMContentLoaded', () => {
  const slides = document.querySelectorAll('.carousel-slide');
  const wrapper = document.querySelector('.carousel-wrapper');
  const dotsContainer = document.querySelector('.carousel-indicators');
  const prevBtn = document.querySelector('.carousel-arrow.prev');
  const nextBtn = document.querySelector('.carousel-arrow.next');
  let current = 0;
  const intervalTime = 5000;
  let autoSlide;

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.remove('active');
      dotsContainer.children[i]?.classList.remove('active');
      if (i === index) {
        slide.classList.add('active');
        dotsContainer.children[i]?.classList.add('active');
      }
    });

    adjustHeight(index);
    current = index;
  }

  function adjustHeight(index) {
    const activeSlide = slides[index];
    wrapper.style.height = activeSlide.offsetHeight + 'px';
  }

  function nextSlide() {
    const next = (current + 1) % slides.length;
    showSlide(next);
  }

  function prevSlide() {
    const prev = (current - 1 + slides.length) % slides.length;
    showSlide(prev);
  }

  // Build dots
  slides.forEach((_, i) => {
    const dot = document.createElement('span');
    dot.className = 'dot';
    if (i === 0) dot.classList.add('active');
    dot.addEventListener('click', () => {
      clearInterval(autoSlide);
      showSlide(i);
      autoSlide = setInterval(nextSlide, intervalTime);
    });
    dotsContainer.appendChild(dot);
  });

  // Arrows
  prevBtn.addEventListener('click', () => {
    clearInterval(autoSlide);
    prevSlide();
    autoSlide = setInterval(nextSlide, intervalTime);
  });

  nextBtn.addEventListener('click', () => {
    clearInterval(autoSlide);
    nextSlide();
    autoSlide = setInterval(nextSlide, intervalTime);
  });

  // Touch support
  let startX = 0;
  wrapper.addEventListener('touchstart', e => startX = e.touches[0].clientX);
  wrapper.addEventListener('touchend', e => {
    let deltaX = e.changedTouches[0].clientX - startX;
    if (Math.abs(deltaX) > 50) {
      clearInterval(autoSlide);
      deltaX < 0 ? nextSlide() : prevSlide();
      autoSlide = setInterval(nextSlide, intervalTime);
    }
  });

  // Init
  showSlide(current);
  autoSlide = setInterval(nextSlide, intervalTime);

  window.addEventListener('resize', () => adjustHeight(current));
});

