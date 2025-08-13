<!-- public/nav.php -->



<?php
// require_once 'header.php';
require_once '../includes/db.php';
$is_logged_in = isset($_SESSION['user']);

// dynamic data
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}
?>

<style>
/* Navigation Styles */
/* ==================== */


:root {
  /* Navigation specific variables */
  --nav-glass-blur: 8px;
  --nav-glass-opacity: 0.98;
  --nav-shadow-intensity: 0.3;
  --nav-transition-duration: 0.35s;
  --nav-transition-timing: cubic-bezier(0.16, 1, 0.3, 1);
  --nav-height: 60px;
}

/* Base Navigation Styles */
.nav-container {
  position: relative;
  z-index: 1000;
}

.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 1rem;
  background: linear-gradient(
    to bottom,
    var(--color-primary) 0%,
    var(--color-primary-dark) 75%,
    var(--color-primary-dark) 75%,
    var(--color-primary-dark) 100%
   ); /* Using --color-primary with opacity */
  backdrop-filter: blur(var(--nav-glass-blur));
  -webkit-backdrop-filter: blur(var(--nav-glass-blur));
  box-shadow: 0 2px 15px rgba(0, 0, 0, calc(var(--nav-shadow-intensity) * 0.3));
  border-bottom: 1px solid rgba(232, 184, 36, 0.15); /* Gold accent border */
  transition: all var(--nav-transition-duration) var(--nav-transition-timing);
  z-index: 1000;
  height: var(--nav-height);
}

/* Logo */
.nav-logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--color-text-inverted);
  text-decoration: none;
  transition: transform 0.3s ease;
  height: 100%;
  padding: 0 0.5rem;
}

.nav-logo:hover {
  transform: scale(1.03);
}

.nav-logo-icon {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  object-fit: cover;
  transition: all 0.3s ease;
}

/* Navigation Links */
.nav-links {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  list-style: none;
  margin: 0;
  padding: 0;
  height: 100%;
}

.nav-item {
  position: relative;
  height: 100%;
  display: flex;
  align-items: center;
}

.nav-link {
  position: relative;
  display: flex;
  align-items: center;
  gap: 0.3rem;
  padding: 0 0.8rem;
  color: var(--color-text-inverted);
  text-decoration: none;
  font-weight: 500;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  height: 100%;
  border-radius: 0;
}

.nav-link::before {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: var(--color-accent-primary);
  transform: scaleX(0);
  transform-origin: right;
  transition: transform 0.3s ease;
}

.nav-link:hover::before {
  transform: scaleX(1);
  transform-origin: left;
}

.nav-link:hover {
  color: var(--color-accent-primary);
}

.nav-link.active {
  color: var(--color-accent-primary);
}

.nav-link.active::before {
  transform: scaleX(1);
}

/* Dropdown Menus */
.nav-dropdown {
  position: relative;
}

.nav-dropdown-toggle {
  cursor: pointer;
}

.nav-dropdown-toggle::after {
  content: '';
  display: inline-block;
  width: 0.5rem;
  height: 0.5rem;
  margin-left: 0.3rem;
  border-right: 2px solid var(--color-text-inverted);
  border-bottom: 2px solid var(--color-text-inverted);
  transform: rotate(45deg) translateY(-25%);
  transition: all 0.3s ease;
}

.nav-dropdown-toggle:hover::after {
  border-color: var(--color-accent-primary);
}

.nav-dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  min-width: 200px;
  padding: 0.5rem 0;
  background: rgba(1, 50, 33, 0.98);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border-radius: 0 0 0.5rem 0.5rem;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(232, 184, 36, 0.2);
  opacity: 0;
  visibility: hidden;
  transform: translateY(-5px);
  transition: all var(--nav-transition-duration) var(--nav-transition-timing);
  z-index: 1001;
}

.nav-dropdown:hover .nav-dropdown-menu,
.nav-dropdown:focus-within .nav-dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.nav-dropdown-item {
  display: block;
  padding: 0.6rem 1.2rem;
  color: var(--color-text-inverted);
  text-decoration: none;
  transition: all 0.3s ease;
  position: relative;
}

.nav-dropdown-item:hover {
  color: var(--color-accent-primary);
  background: rgba(232, 184, 36, 0.1);
}

/* Mobile Menu Toggle */
.nav-toggle {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  z-index: 1002;
}

.nav-toggle-icon {
  display: block;
  width: 1.5rem;
  height: 1.5rem;
  position: relative;
}

.nav-toggle-icon span {
  display: block;
  width: 100%;
  height: 2px;
  background: var(--color-text-inverted);
  position: absolute;
  left: 0;
  transition: all 0.3s ease;
}

.nav-toggle-icon span:nth-child(1) {
  top: 0.25rem;
}

.nav-toggle-icon span:nth-child(2) {
  top: 50%;
  transform: translateY(-50%);
}

.nav-toggle-icon span:nth-child(3) {
  bottom: 0.25rem;
}

.nav-toggle.active .nav-toggle-icon span:nth-child(1) {
  transform: translateY(0.5rem) rotate(45deg);
}

.nav-toggle.active .nav-toggle-icon span:nth-child(2) {
  opacity: 0;
}

.nav-toggle.active .nav-toggle-icon span:nth-child(3) {
  transform: translateY(-0.5rem) rotate(-45deg);
}


/* Mobile Menu Styles */
@media (max-width: 992px) {
  .navbar {
    padding: 0 1rem;
  }
  
  .nav-links {
    position: fixed;
    top: var(--nav-height);
    right: -100%;
    width: 80%;
    max-width: 300px;
    height: calc(100vh - var(--nav-height));
    background: rgba(1, 50, 33, 0.98); /* --color-primary with high opacity */
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    flex-direction: column;
    align-items: flex-start;
    padding: 1rem 0;
    box-shadow: -5px 0 20px rgba(0, 0, 0, 0.3);
    transition: right 0.4s var(--nav-transition-timing);
    z-index: 1001;
    border-left: 1px solid rgba(232, 184, 36, 0.2); /* Gold accent border */
  }
  
  .nav-links.active {
    right: 0;
  }
  
  .nav-item {
    width: 100%;
    height: auto;
    border-bottom: 1px solid rgba(232, 184, 36, 0.1); /* Subtle gold divider */
  }
  
  .nav-link {
    width: 100%;
    padding: 1rem 1.5rem;
    color: var(--color-text-inverted);
  }
  
  .nav-link:hover {
    color: var(--color-accent-primary);
    background: rgba(232, 184, 36, 0.1); /* Gold tint on hover */
  }
  
  .nav-dropdown-menu {
    position: static;
    width: 100%;
    box-shadow: none;
    background: rgba(0, 36, 24, 0.5); /* --color-primary-dark with opacity */
    border: none;
    border-radius: 0;
    opacity: 1;
    visibility: visible;
    max-height: 0;
    overflow: hidden;
    transform: none;
    transition: max-height 0.4s ease;
  }
  
  .nav-dropdown.active .nav-dropdown-menu {
    max-height: 500px;
  }
  
  .nav-dropdown-item {
    color: var(--color-text-inverted);
    padding-left: 2.5rem; /* Indent dropdown items */
  }
  
  .nav-dropdown-item:hover {
    color: var(--color-accent-primary);
    background: rgba(232, 184, 36, 0.15);
  }
  
  .nav-toggle {
    display: block;
  }
  
  /* Overlay when menu is open */
  .nav-overlay {
    position: fixed;
    top: var(--nav-height);
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 1000;
  }
  
  .nav-overlay.active {
    opacity: 1;
    visibility: visible;
  }
  
  /* Adjust toggle icon color for visibility */
  .nav-toggle-icon span {
    background: var(--color-text-inverted);
  }
  
  /* Animate menu items when opening */
  .nav-links.active .nav-item {
    animation: navItemFadeIn 0.4s ease forwards;
  }
  
  .nav-links.active .nav-item:nth-child(1) { animation-delay: 0.1s; }
  .nav-links.active .nav-item:nth-child(2) { animation-delay: 0.2s; }
  .nav-links.active .nav-item:nth-child(3) { animation-delay: 0.3s; }
  .nav-links.active .nav-item:nth-child(4) { animation-delay: 0.4s; }
  .nav-links.active .nav-item:nth-child(5) { animation-delay: 0.5s; }
}

@keyframes navItemFadeIn {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>


<div class="nav-container">
  <nav class="navbar">
    <a href="/" class="nav-logo brand">
      <span>
        <?php if (!empty($settings['site_logo'])): ?>
          <img style="border-radius: 50%;" class="logoo nav-logo-icon" src="../uploads/images/<?= htmlspecialchars($settings['site_logo']) ?>" alt="Site Logo">
        <?php endif; ?>
      </span>
      
      <?php if (!empty($settings['site_name'])): ?>
        <h2><?= htmlspecialchars($settings['site_name']) ?></h2>
      <?php endif; ?>
    </a>
    
    <button class="nav-toggle" id="hamburger">
      <span class="nav-toggle-icon">
        <span></span>
        <span></span>
        <span></span>
      </span>
    </button>
    
    <ul class="nav-links" id="navLinks">
      <li class="nav-item">
        <a href="index.php" class="nav-link">Home</a>
      </li>

      <li class="nav-item">
        <a href="about.php" class="nav-link">About Us</a>
      </li>
      
      <li class="nav-item nav-dropdown">
        <a href="#" class="nav-link nav-dropdown-toggle">Learn</a>
        <ul class="nav-dropdown-menu">
          <li><a href="encyclopedia.php" class="nav-dropdown-item">Explore Wildlife</a></li>
          <li><a href="quizzes.php" class="nav-dropdown-item">Quizzes and Trivias</a></li>
        </ul>
      </li>
      
      <li class="nav-item">
        <a href="blog.php" class="nav-link">Blog</a>
      </li>

      <li class="nav-item nav-dropdown">
        <a href="#" class="nav-link nav-dropdown-toggle">Media</a>
        <ul class="nav-dropdown-menu">
          <li><a href="gallery.php" class="nav-dropdown-item">Gallery</a></li>
          <li><a href="podcast.php" class="nav-dropdown-item">Podcast</a></li>        </ul>
      </li>
      
      <li class="nav-item">
        <a href="gallery.php" class="nav-link">Gallery</a>
      </li>
      
      <li class="nav-item nav-dropdown">
        <a href="#" class="nav-link nav-dropdown-toggle">Community</a>
        <ul class="nav-dropdown-menu">
          <li><a href="events.php" class="nav-dropdown-item">Events</a></li>
          <li><a href="support.php" class="nav-dropdown-item">Support</a></li>
          <li><a href="forum.php" class="nav-dropdown-item">Forum</a></li>
        </ul>
      </li>
      
      <?php if ($is_logged_in): ?>
        <li class="nav-item nav-dropdown">
          <a href="#" class="nav-link nav-dropdown-toggle">Account</a>
          <ul class="nav-dropdown-menu">
            <?php if (!empty($userInitial) && isset($_SESSION['user']['id'])): ?>
              <li>
                <a href="profile.php?id=<?= $_SESSION['user']['id'] ?>" class="nav-dropdown-item">
                  <span class="user-initial-circle"><?= $userInitial ?></span>
                  <span>My Profile</span>
                </a>
              </li>
            <?php endif; ?>
            <li><a href="logout.php" class="nav-dropdown-item">Logout</a></li>
          </ul>
        </li>
      <?php else: ?>
        <li class="nav-item nav-dropdown">
          <a href="#" class="nav-link nav-dropdown-toggle">Account</a>
          <ul class="nav-dropdown-menu">
            <li><a href="login.php" class="nav-dropdown-item">Login</a></li>
            <li><a href="register.php" class="nav-dropdown-item">Register</a></li>
          </ul>
        </li>
      <?php endif; ?>
    </ul>
    
    <div class="nav-overlay"></div>
  </nav>
</div>

<script>
  // Mobile menu toggle
 // document.getElementById('hamburger').addEventListener('click', function() {
 //   document.getElementById('navLinks').classList.toggle('active');
 // });

 document.addEventListener('DOMContentLoaded', function() {
  const navbar = document.querySelector('.navbar');
  const navToggle = document.querySelector('.nav-toggle');
  const navLinks = document.querySelector('.nav-links');
  const navOverlay = document.querySelector('.nav-overlay');
  const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
  
  // Scroll effect
  window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
  
  // Mobile menu toggle
  navToggle.addEventListener('click', function() {
    this.classList.toggle('active');
    navLinks.classList.toggle('active');
    navOverlay.classList.toggle('active');
    document.body.style.overflow = this.classList.contains('active') ? 'hidden' : '';
  });
  
  // Close menu when clicking overlay
  navOverlay.addEventListener('click', function() {
    navToggle.classList.remove('active');
    navLinks.classList.remove('active');
    this.classList.remove('active');
    document.body.style.overflow = '';
  });
  
  // Mobile dropdown functionality
  dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      if (window.innerWidth <= 992) {
        e.preventDefault();
        const dropdown = this.parentElement;
        dropdown.classList.toggle('active');
      }
    });
  });
  
  // Animate navbar on load
  setTimeout(() => {
    navbar.style.opacity = '1';
  }, 100);
});
</script>