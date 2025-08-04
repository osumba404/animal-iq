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
  .navbar {
    background-color: var(--color-primary-dark);
    color: var(--color-text-inverted);
    padding: 0.5rem 1rem;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .nav-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .brand {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .brand img {
    height: 40px;
    width: auto;
  }
  
  .brand h2 {
    margin: 0;
    color: var(--color-text-inverted);
    font-size: 1.25rem;
  }
  
  .nav-links {
    display: flex;
    gap: 1rem;
    list-style: none;
    margin: 0;
    padding: 0;
  }
  
  .nav-links li {
    position: relative;
  }
  
  .nav-links a {
    color: var(--color-text-inverted);
    text-decoration: none;
    padding: 0.5rem 0.75rem;
    border-radius: 4px;
    transition: all 0.2s ease;
  }
  
  .nav-links a:hover {
    background-color: var(--color-primary-light);
    color: var(--color-accent-primary);
  }
  
  .dropdown-content {
    display: none;
    position: absolute;
    background-color: var(--color-primary-dark);
    min-width: 160px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    z-index: 1;
    border-radius: 4px;
    padding: 0.5rem 0;
  }
  
  .dropdown:hover .dropdown-content {
    display: block;
  }
  
  .dropdown-content a {
    padding: 0.75rem 1rem;
    display: block;
    color: var(--color-text-inverted);
  }
  
  .dropdown-content a:hover {
    background-color: var(--color-primary-light);
  }
  
  .hamburger {
    display: none;
    background: none;
    border: none;
    color: var(--color-text-inverted);
    font-size: 1.5rem;
    cursor: pointer;
  }
  
  @media (max-width: 768px) {
    .hamburger {
      display: block;
    }
    
    .nav-links {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background-color: var(--color-primary-dark);
      flex-direction: column;
      padding: 1rem;
      gap: 0.5rem;
    }
    
    .nav-links.active {
      display: flex;
    }
    
    .dropdown-content {
      position: static;
      box-shadow: none;
    }
  }
  .logoo{
    border-radius: 50% !important;
  }
</style>

<div class="navbar">
  <div class="nav-container">
    <div class="brand">
      <span>
        <?php if (!empty($settings['site_logo'])): ?>
          <img class="logoo" src="../uploads/images/<?= htmlspecialchars($settings['site_logo']) ?>" alt="Site Logo">
        <?php endif; ?>
      </span>
      
      <?php if (!empty($settings['site_name'])): ?>
        <h2><?= htmlspecialchars($settings['site_name']) ?></h2>
      <?php endif; ?>
    </div>
    
    <button class="hamburger" id="hamburger">☰</button>
    
    <ul class="nav-links" id="navLinks">
      <li><a href="index.php">Home</a></li>
      
      <li class="dropdown">
        <a href="#">Learn ▾</a>
        <div class="dropdown-content">
          <a href="encyclopedia.php">Explore Wildlife</a>
          <!-- <a href="learn.php">Learn</a> -->
          <a href="quizzes.php">Quizzes</a>
        </div>
      </li>
      
      <li><a href="blog.php">Blog</a></li>
      <!-- <li><a href="gallery.php">Gallery</a></li> -->
      
      <li class="dropdown">
        <a href="#">Community ▾</a>
        <div class="dropdown-content">
          <a href="events.php">Events</a>
          <a href="support.php">Support</a>
          <!-- <a href="forum.php">Forum</a> -->
          <!-- <a href="contribute.php">Contribute</a> -->
        </div>
      </li>
      
    

      <?php if ($is_logged_in): ?>
  <li class="dropdown">
    <a href="#">Account ▾</a>
    <div class="dropdown-content">
      <?php if (!empty($userInitial) && isset($_SESSION['user']['id'])): ?>
        <a href="profile.php?id=<?= $_SESSION['user']['id'] ?>">
          <div class="user-initial-circle"><?= $userInitial ?></div>My Profile
        </a>
      <?php endif; ?>
      <a href="logout.php">Logout</a>
    </div>
  </li>
<?php else: ?>
  <li class="dropdown">
    <a href="#">Account ▾</a>
    <div class="dropdown-content">
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    </div>
  </li>
<?php endif; ?>

    </ul>
  </div>
</div>

<script>
  // Mobile menu toggle
  document.getElementById('hamburger').addEventListener('click', function() {
    document.getElementById('navLinks').classList.toggle('active');
  });
</script>