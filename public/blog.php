<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once 'header.php';
require_once 'nav.php';


$page_title = "Animal IQ | Blogs1";

$searchTerm = $_GET['search'] ?? '';
$blogs = getApprovedBlogs($pdo, null, $searchTerm);
?>

<style>
/* Premium Blog Listing Styling */
.blog-listing-page {
  max-width: 1200px;
  margin: 3rem auto;
  padding: 0 2rem;
}

.blog-header {
  text-align: center;
  margin-bottom: 3rem;
  position: relative;
}

.blog-header h1 {
  font-size: 3rem;
  color: var(--color-primary);
  margin-bottom: 1.5rem;
  position: relative;
  display: inline-block;
}

.blog-header h1::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 25%;
  width: 50%;
  height: 3px;
  background: linear-gradient(90deg, var(--color-primary), var(--color-accent-primary), var(--color-primary));
  border-radius: 3px;
}

.search-form {
  max-width: 600px;
  margin: 0 auto 3rem;
  position: relative;
}

.search-input {
  width: 100%;
  padding: 1rem 1.5rem;
  border: 2px solid var(--color-border-light);
  border-radius: 50px;
  font-size: 1.1rem;
  transition: all 0.3s ease;
  background: var(--color-bg-primary);
  box-shadow: 0 4px 12px var(--color-shadow);
}

.search-input:focus {
  outline: none;
  border-color: var(--color-primary-light);
  box-shadow: 0 6px 16px rgba(1, 50, 33, 0.1);
}

.search-button {
  position: absolute;
  right: 5px;
  top: 5px;
  background: var(--color-primary);
  color: white;
  border: none;
  border-radius: 50px;
  padding: 0.7rem 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.search-button:hover {
  background: var(--color-primary-dark);
  transform: translateY(-2px);
}

.blog-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 2rem;
  margin-top: 2rem;
}

.blog-card {
  background: var(--color-bg-primary);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(30, 24, 17, 0.08);
  transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
  display: flex;
  flex-direction: column;
  height: 100%;
}

.blog-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 32px rgba(30, 24, 17, 0.15);
}

.blog-content {
  padding: 1.5rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.blog-title {
  font-size: 1.5rem;
  color: var(--color-primary);
  margin-bottom: 0.5rem;
  line-height: 1.3;
}

.blog-meta {
  color: var(--color-text-muted);
  font-size: 0.9rem;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.blog-meta::before {
  content: '';
  display: inline-block;
  width: 4px;
  height: 4px;
  background: var(--color-text-muted);
  border-radius: 50%;
}

.blog-summary {
  color: var(--color-text-secondary);
  margin-bottom: 1.5rem;
  line-height: 1.6;
  flex-grow: 1;
}

.read-more {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--color-primary);
  font-weight: bold;
  text-decoration: none;
  transition: all 0.2s ease;
  margin-top: auto;
}

.read-more:hover {
  color: var(--color-primary-dark);
}

.read-more::after {
  content: '→';
  transition: transform 0.2s ease;
}

.read-more:hover::after {
  transform: translateX(3px);
}

.no-posts {
  text-align: center;
  padding: 3rem;
  color: var(--color-text-muted);
  font-size: 1.2rem;
  grid-column: 1 / -1;
}

.blog-card-image {
  height: 200px;
  background: linear-gradient(135deg, var(--color-primary-light), var(--color-primary-lighter));
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 2rem;
  font-weight: bold;
}

@media (max-width: 768px) {
  .blog-header h1 {
    font-size: 2.2rem;
  }
  
  .blog-grid {
    grid-template-columns: 1fr;
  }
  
  .search-form {
    margin-bottom: 2rem;
  }
}
</style>

<div class="blog-listing-page">
  <header class="blog-header">
    <h1>Blogs, Articles & Stories</h1>
    <form method="get" action="blog.php" class="search-form">
      <input type="text" name="search" placeholder="Search blogs..." value="<?php echo htmlspecialchars($searchTerm); ?>" class="search-input">
      <button type="submit" class="search-button">Search</button>
    </form>
  </header>

  <?php if (count($blogs) > 0): ?>
    <div class="blog-grid">
      <?php foreach($blogs as $post): ?>
        <article class="blog-card">
          <div class="blog-card-image">
            <?php echo substr(htmlspecialchars($post['title']), 0, 1); ?>
          </div>
          <div class="blog-content">
            <h2 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h2>
            <div class="blog-meta">
              <span>By <?php echo htmlspecialchars($post['author_name']); ?></span>
              <span>•</span>
              <span><?php echo date('F j, Y ~ g:i a', strtotime($post['created_at'])); ?></span>
            </div>
            <p class="blog-summary"><?php echo nl2br(htmlspecialchars($post['summary'])); ?></p>
            <a href="blog_post.php?id=<?php echo $post['id']; ?>" class="read-more">Read more</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="no-posts">No blog posts found. <?php echo $searchTerm ? 'Try a different search term.' : 'Check back soon for new content!'; ?></p>
  <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>