<?php
// public/profile.php

require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; // Cache original URL
    header("Location: login.php");
    exit;
}

$user = getUserProfile($pdo, $_SESSION['user']['id']); // get the logged-in user

if (!$user) {
    echo "<div class='error-message'>User not found.</div>";
    require_once 'footer.php';
    exit;
}
?>


<style>
/* Premium Profile Page Styling */
.profile-container {
  max-width: 800px;
  margin: 3rem auto;
  padding: 2rem;
  background: var(--color-bg-primary);
  border-radius: 16px;
  box-shadow: 0 12px 40px rgba(30, 24, 17, 0.1);
}

.profile-header {
  text-align: center;
  margin-bottom: 2rem;
  position: relative;
}

.profile-header h1 {
  font-size: 2.5rem;
  color: var(--color-primary);
  margin-bottom: 0.5rem;
}

.profile-header h1::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 30%;
  width: 40%;
  height: 3px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-accent-primary), var(--color-primary));
  border-radius: 3px;
}

.profile-content {
  display: flex;
  gap: 3rem;
  align-items: flex-start;
}

.profile-avatar {
  flex: 0 0 200px;
  position: relative;
}

.profile-picture {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  object-fit: cover;
  border: 5px solid var(--color-primary-light);
  box-shadow: 0 8px 24px rgba(1, 50, 33, 0.15);
}

.profile-details {
  flex: 1;
}

.detail-item {
  margin-bottom: 1.5rem;
}

.detail-label {
  font-weight: bold;
  color: var(--color-primary);
  margin-bottom: 0.3rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.detail-value {
  color: var(--color-text-primary);
  padding-left: 1.8rem;
}

.edit-button {
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 50px;
  padding: 0.8rem 1.8rem;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 1rem;
  box-shadow: 0 4px 12px rgba(1, 50, 33, 0.2);
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.edit-button:hover {
  background: var(--color-primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(1, 50, 33, 0.3);
}

.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: var(--color-bg-primary);
  padding: 2.5rem;
  border-radius: 16px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 12px 40px rgba(30, 24, 17, 0.2);
  position: relative;
}

.modal-header {
  margin-bottom: 1.5rem;
}

.modal-header h2 {
  color: var(--color-primary);
  margin: 0;
}

.close-button {
  position: absolute;
  top: 1.5rem;
  right: 1.5rem;
  font-size: 1.5rem;
  cursor: pointer;
  color: var(--color-text-muted);
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--color-primary);
  font-weight: bold;
}

.form-control {
  width: 100%;
  padding: 0.8rem 1rem;
  border: 2px solid var(--color-border-light);
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-control:focus {
  outline: none;
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(1, 50, 33, 0.1);
}

.file-input {
  display: none;
}

.file-label {
  display: inline-block;
  background: var(--color-bg-secondary);
  padding: 0.8rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px dashed var(--color-border-light);
  text-align: center;
  width: 100%;
}

.file-label:hover {
  background: var(--color-neutral-lighter);
}

.submit-button {
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 50px;
  padding: 0.8rem 1.8rem;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 1rem;
}

.submit-button:hover {
  background: var(--color-primary-dark);
}

.error-message {
  color: var(--color-error);
  background: rgba(194, 59, 34, 0.1);
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  text-align: center;
  border-left: 4px solid var(--color-error);
}

@media (max-width: 768px) {
  .profile-content {
    flex-direction: column;
    align-items: center;
    gap: 2rem;
  }
  
  .profile-avatar {
    flex: 0 0 auto;
  }
  
  .profile-header h1 {
    font-size: 2rem;
  }
  
  .modal-content {
    padding: 1.5rem;
  }
}
</style>

<div class="profile-container">
  <header class="profile-header">
    <h1>My Profile</h1>
  </header>

  <?php if ($user): ?>
    <div class="profile-content">
      <div class="profile-avatar">
        <img src="uploads/profile_pics/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.png'); ?>" 
             alt="Profile Picture" 
             class="profile-picture">
      </div>
      
      <div class="profile-details">
        <div class="detail-item">
          <div class="detail-label">
            <span>👤</span> Name
          </div>
          <div class="detail-value"><?php echo htmlspecialchars($user['name']); ?></div>
        </div>
        
        <div class="detail-item">
          <div class="detail-label">
            <span>✉️</span> Email
          </div>
          <div class="detail-value"><?php echo htmlspecialchars($user['email']); ?></div>
        </div>
        
        <div class="detail-item">
          <div class="detail-label">
            <span>📅</span> Member Since
          </div>
          <div class="detail-value"><?php echo date('F j, Y g:i a', strtotime($user['registered_at'])); ?></div>
        </div>
        
        <?php if (isset($_SESSION['user_id']) && $user['id'] === $_SESSION['user_id']): ?>
          <button class="edit-button" onclick="document.getElementById('editModal').style.display='flex'">
            <span>✏️</span> Edit Profile
          </button>
        <?php endif; ?>
      </div>
    </div>
  <?php else: ?>
    <div class="error-message">
      User profile not found.
    </div>
  <?php endif; ?>
</div>

<?php if ($user && isset($_SESSION['user_id']) && $user['id'] === $_SESSION['user_id']): ?>
  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close-button" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
      
      <div class="modal-header">
        <h2>Edit Your Profile</h2>
      </div>
      
      <form method="POST" action="update_profile.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
        
        <div class="form-group">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" class="form-control" 
                 value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        
        <div class="form-group">
          <label>Profile Picture</label>
          <input type="file" id="profile_picture" name="profile_picture" class="file-input">
          <label for="profile_picture" class="file-label">
            Click to upload new profile picture
          </label>
        </div>
        
        <button type="submit" class="submit-button">Save Changes</button>
      </form>
    </div>
  </div>
  
  <script>
  // Close modal when clicking outside
  window.onclick = function(event) {
    if (event.target == document.getElementById('editModal')) {
      document.getElementById('editModal').style.display = 'none';
    }
  }
  </script>
<?php endif; ?>

<?php require_once 'footer.php'; ?>