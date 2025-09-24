<!-- public/blog_post.php -->

<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT p.*, u.name AS author_name FROM posts p JOIN users u ON p.author_id = u.id WHERE p.id = ? ");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<style>
/* Blog Post Styling */
.blog-post-container {
  max-width: 900px;
  margin: 3rem auto;
  padding: 0 2rem;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.8;
  color: #333;
}

.blog-header {
  text-align: center;
  margin-bottom: 3rem;
}

.blog-title {
  font-size: 2.5rem;
  color: var(--color-primary);
  margin-bottom: 1rem;
  line-height: 1.3;
}

.blog-meta {
  color: #666;
  margin-bottom: 2rem;
  font-size: 1.1rem;
}

.featured-image-container {
  width: 100%;
  max-height: 500px;
  overflow: hidden;
  border-radius: 12px;
  margin: 2rem 0;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

.featured-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.letter-avatar {
  width: 100%;
  height: 300px;
  background: linear-gradient(135deg, var(--color-primary-light), var(--color-primary-lighter));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 8rem;
  font-weight: bold;
  color: white;
  text-transform: uppercase;
}

.blog-content {
  max-width: 800px;
  margin: 0 auto;
  font-size: 1.1rem;
  line-height: 1.8;
}

.blog-content p {
  margin-bottom: 1.5rem;
}

/* Rich content defaults */
.blog-content h1, .blog-content h2, .blog-content h3,
.blog-content h4, .blog-content h5, .blog-content h6 {
  color: var(--color-primary-dark);
  margin: 1.5rem 0 0.75rem;
}
.blog-content ul, .blog-content ol {
  padding-left: 1.5rem;
  margin: 0 0 1.25rem 0;
}
.blog-content li { margin: 0.4rem 0; }
.blog-content a { color: var(--color-accent-primary); text-decoration: underline; }
.blog-content blockquote {
  border-left: 4px solid var(--color-accent-primary);
  padding: 0.75rem 1rem;
  margin: 1.25rem 0;
  background: rgba(232, 184, 36, 0.08);
}
.blog-content pre, .blog-content code {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
.blog-content pre {
  background: #f6f8fa;
  padding: 1rem;
  border-radius: 8px;
  overflow-x: auto;
}
.blog-content img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
}

@media (max-width: 768px) {
  .blog-title {
    font-size: 2rem;
  }
  
  .blog-content {
    font-size: 1rem;
  }
  
  .letter-avatar {
    height: 200px;
    font-size: 5rem;
  }
}
</style>

<div class="blog-post-container">
  <?php if (!$post): ?>
    <div class="error-message">
      <h2>Blog Post Not Found</h2>
      <p>The requested blog post could not be found. It may have been removed or the link might be incorrect.</p>
      <a href="blog.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Blog</a>
    </div>
  <?php else: ?>
    <article>
      <header class="blog-header">
        <h1 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="blog-meta">
          <span>By <?php echo htmlspecialchars($post['author_name']); ?></span>
          <span> • </span>
          <span><?php echo date('F j, Y ~ g:i a', strtotime($post['created_at'])); ?></span>
        </div>
        
        <div class="featured-image-container">
          <?php
          $firstLetter = strtoupper(substr($post['title'], 0, 1));
          $imagePath = !empty($post['featured_image']) ? "../uploads/posts/" . htmlspecialchars($post['featured_image']) : null;
          
          if ($imagePath && file_exists($imagePath)): ?>
            <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="featured-image">
          <?php else: ?>
            <div class="letter-avatar"><?php echo $firstLetter; ?></div>
          <?php endif; ?>
        </div>
      </header>
      
      <div class="blog-content">
        <?php
          // Render trusted admin HTML with an allowlist of tags
          $rawBody = $post['body'] ?? '';
          $allowedTags = '<p><br><b><strong><i><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote><code><pre><a><img><span><div>';
          echo strip_tags($rawBody, $allowedTags);
        ?>
      </div>
      
      <a href="blog.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to All Articles</a>
    </article>
  <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
