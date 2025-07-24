<?php
// contribute.php - Contribution Page
require_once '../includes/auth.php';
require_once '../includes/header.php';
require_once 'nav.php';
?>
<h1>Contribute Content</h1>
<p>Submit animals, articles, or stories for approval.</p>
<form action="../api/submit-contribution.php" method="post" enctype="multipart/form-data">
  <!-- form fields here -->
</form>
<?php require_once '../includes/footer.php'; ?>