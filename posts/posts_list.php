<?php
require('db.php');

$stmt = $conn->query("SELECT p.*, u.username 
                      FROM posts p 
                      JOIN users u ON p.user_id = u.id 
                      WHERE p.status = 'published'
                      ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Posts</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f6f9;
      color: #333;
    }

    header {
      background: #1e1e2f;
      color: #fff;
      padding: 20px;
      text-align: center;
    }

    h1 {
      margin: 0;
      font-size: 2rem;
    }

    .container {
      max-width: 1100px;
      margin: 30px auto;
      padding: 0 15px;
    }

    .posts-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }

    .post-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: transform 0.2s ease;
    }

    .post-card:hover {
      transform: translateY(-5px);
    }

    .post-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .post-content {
      padding: 15px;
    }

    .post-content h2 {
      font-size: 1.3rem;
      margin: 0 0 10px;
      color: #1e1e2f;
    }

    .post-meta {
      font-size: 0.9rem;
      color: #666;
      margin-bottom: 10px;
    }

    .post-excerpt {
      font-size: 1rem;
      line-height: 1.5;
      margin-bottom: 10px;
    }

    .read-more {
      display: inline-block;
      margin-top: 5px;
      color: #007bff;
      text-decoration: none;
      font-weight: bold;
    }

    .read-more:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <header>
    <h1>Published Posts</h1>
  </header>

  <div class="container">
    <div class="posts-grid">
      <?php while ($post = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <div class="post-card">
          <?php if ($post['image']): ?>
            <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Post image">
          <?php endif; ?>
          <div class="post-content">
            <h2><?= htmlspecialchars($post['title']) ?></h2>
            <p class="post-meta">By <?= htmlspecialchars($post['username']) ?> • <?= date("M d, Y", strtotime($post['created_at'])) ?></p>
            <p class="post-excerpt"><?= nl2br(htmlspecialchars(substr($post['content'], 0, 150))) ?>...</p>
            <a class="read-more" href="post.php?id=<?= $post['id'] ?>">Read More →</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</body>
</html>
