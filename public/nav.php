<?php
require_once 'header.php';
require_once '../includes/db.php';
$is_logged_in = isset($_SESSION['user']);

// dynamic data
$stmt = $pdo->query("SELECT `key`, `value` FROM settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}
?>

<head>
<style>
  /* Navigation Base Styles */
  .navbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    background-color: var(--color-primary-dark);
    color: var(--color-primary-light);
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .brand img {
    height: 40px;
    width: auto;
  }

  .brand h2 {
    margin: 0;
    font-size: 1.5rem;
  }

  .nav-container {
    display: flex;
    align-items: center;
  }

  nav ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex;
    gap: 8px;
  }

  nav ul li {
    position: relative;
  }

  nav ul li a {
    display: block;
    text-decoration: none;
    color: var(--color-primary-light);
    padding: 10px 15px;
    border-radius: 4px;
    transition: all 0.3s ease;
  }

  nav ul li a:hover {
    background-color: var(--color-primary-accent);
  }

  /* Dropdown Styles */
  .dropdown > a::after {
    content: " ▾";
    font-size: 0.8em;
  }

  .dropdown-content {
    display: none;
    position: absolute;
    min-width: 200px;
    background-color: var(--color-primary-dark);
    border-radius: 4px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    z-index: 1000;
  }

  .dropdown-content li a {
    padding: 10px 15px;
    color: var(--color-primary-light);
    background-color: transparent;
  }

  .dropdown-content li a:hover {
    background-color: var(--color-primary-accent);
  }

  /* Show dropdown on hover for desktop */
  .dropdown:hover .dropdown-content {
    display: block;
  }

  /* Mobile menu button */
  .hamburger {
    display: none;
    background: none;
    border: none;
    color: var(--color-primary-light);
    font-size: 1.5rem;
    cursor: pointer;
    padding: 10px;
  }

  /* Account specific styles */
  .account-info {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .user-initial {
    background-color: var(--color-secondary-accent);
    color: var(--color-primary-light);
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
  }

  /* Mobile Styles */
  @media (max-width: 767px) {
    .hamburger {
      display: block;
    }

    .nav-container {
      position: fixed;
      top: 0;
      right: -100%;
      width: 80%;
      max-width: 300px;
      height: 100vh;
      background-color: var(--color-primary-dark);
      flex-direction: column;
      padding: 20px;
      box-shadow: -5px 0 15px rgba(0,0,0,0.2);
      transition: right 0.3s ease;
      z-index: 1000;
    }

    .nav-container.active {
      right: 0;
    }

    nav ul {
      flex-direction: column;
      width: 100%;
    }

    .dropdown-content {
      position: static;
      width: 100%;
      box-shadow: none;
      display: none;
    }

    .dropdown.active .dropdown-content {
      display: block;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      z-index: 999;
    }

    .overlay.active {
      display: block;
    }
  }
</style>
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<div class="navbar">
  <div class="brand">
    <?php
      $logo = $settings['site_logo'] ?? '';
      if ($logo): ?>
        <img src="../uploads/images/<?= htmlspecialchars($logo) ?>" alt="Site Logo">
    <?php endif; ?>

    <?php if (!empty($settings['site_name'])): ?>     
      <h2><?= nl2br(htmlspecialchars($settings['site_name'])) ?></h2>   
    <?php endif; ?>
  </div>

  <button class="hamburger" onclick="toggleMenu()">☰</button>
  
  <div class="overlay" onclick="closeMenu()"></div>
  
  <div class="nav-container" id="nav-container">
    <nav id="main-nav">
      <ul>
        <li><a href="index.php" onclick="closeMenu()">Home</a></li>
        
        <li class="dropdown">
          <a href="#" onclick="toggleDropdown(event)">Learn</a>
          <ul class="dropdown-content">
            <li><a href="encyclopedia.php" onclick="closeMenu()">Encyclopedia</a></li>
            <li><a href="learn.php" onclick="closeMenu()">Learn</a></li>
            <li><a href="quizzes.php" onclick="closeMenu()">Quizzes</a></li>
          </ul>
        </li>
        
        <li><a href="blog.php" onclick="closeMenu()">Blog</a></li>
        <li><a href="gallery.php" onclick="closeMenu()">Gallery</a></li>
        
        <li class="dropdown">
          <a href="#" onclick="toggleDropdown(event)">Community</a>
          <ul class="dropdown-content">
            <li><a href="events.php" onclick="closeMenu()">Events</a></li>
            <li><a href="forum.php" onclick="closeMenu()">Forum</a></li>
            <li><a href="contribute.php" onclick="closeMenu()">Contribute</a></li>
          </ul>
        </li>
        
        <li><a href="support.php" onclick="closeMenu()">Support</a></li>

        <?php if ($is_logged_in): ?>
          <li class="dropdown">
            <a href="#" onclick="toggleDropdown(event)">Account</a>
            <ul class="dropdown-content">
              <li><a href="profile.php" onclick="closeMenu()"><?= $userInitial ?>'s Profile</a></li>
              <li><a href="logout.php" onclick="closeMenu()">Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="dropdown">
            <a href="#" onclick="toggleDropdown(event)">Account</a>
            <ul class="dropdown-content">
              <li><a href="login.php" onclick="closeMenu()">Login</a></li>
              <li><a href="register.php" onclick="closeMenu()">Register</a></li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</div>

<script>
  function toggleMenu() {
    const navContainer = document.getElementById('nav-container');
    const overlay = document.querySelector('.overlay');
    navContainer.classList.toggle('active');
    overlay.classList.toggle('active');
  }

  function closeMenu() {
    const navContainer = document.getElementById('nav-container');
    const overlay = document.querySelector('.overlay');
    navContainer.classList.remove('active');
    overlay.classList.remove('active');
  }

  function toggleDropdown(event) {
    if (window.innerWidth < 768) {
      event.preventDefault();
      const dropdown = event.target.closest('.dropdown');
      dropdown.classList.toggle('active');
      
      // Close other dropdowns
      document.querySelectorAll('.dropdown').forEach(otherDropdown => {
        if (otherDropdown !== dropdown) {
          otherDropdown.classList.remove('active');
        }
      });
    }
  }

  // Close dropdowns when clicking outside on mobile
  document.addEventListener('click', function(event) {
    if (window.innerWidth < 768) {
      const dropdowns = document.querySelectorAll('.dropdown');
      dropdowns.forEach(dropdown => {
        if (!dropdown.contains(event.target)) {
          dropdown.classList.remove('active');
        }
      });
    }
  });

  // Handle window resize
  window.addEventListener('resize', function() {
    if (window.innerWidth >= 768) {
      closeMenu();
    }
  });
</script>