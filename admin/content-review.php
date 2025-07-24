$pending_posts = $pdo->query("SELECT * FROM posts WHERE status = 'pending'")->fetchAll();
$pending_animals = $pdo->query("SELECT * FROM animals WHERE status = 'pending'")->fetchAll();
$pending_gallery = $pdo->query("SELECT * FROM gallery WHERE status = 'pending'")->fetchAll();
