<?php
session_start();
require('../db.php');

$featuredSql = "SELECT posts.*, users.username, users.profile_image 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                ORDER BY created_at DESC LIMIT 3";
$featuredPosts = $conn->query($featuredSql);

$latestSql = "SELECT posts.*, users.username, users.profile_image 
              FROM posts 
              JOIN users ON posts.user_id = users.id 
              ORDER BY created_at DESC LIMIT 6 OFFSET 3";
$latestPosts = $conn->query($latestSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlogSpace | Modern Blog Platform</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="../main.js" defer></script>
</head>
<body>
<?php include ('../layouts/navbar.php'); ?>

    <section class="hero">
        <div class="container hero-content">
            <h1>Discover Stories, Ideas, and Inspiration</h1>
            <p>Welcome to BlogSpace, your go-to destination for thought-provoking articles, creative ideas, and inspiring content.</p>
            <a href="#latest" class="btn">Start Reading</a>
        </div>
    </section>

    <section class="featured-posts">
        <div class="container">
            <h2 class="section-title">Featured Posts</h2>
            <div class="posts-grid">
            <?php while ($row = $featuredPosts->fetch(PDO::FETCH_ASSOC)): ?>
                <?php
                $likeStmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
                $likeStmt->execute([$row['id']]);
                $likeCount = $likeStmt->fetchColumn();

                $commentStmt = $conn->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
                $commentStmt->execute([$row['id']]);
                $commentCount = $commentStmt->fetchColumn();

                $userLiked = false;
                if (isset($_SESSION['user_id'])) {
                    $checkLike = $conn->prepare("SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?");
                    $checkLike->execute([$row['id'], $_SESSION['user_id']]);
                    $userLiked = $checkLike->fetch() ? true : false;
                }
                ?>
                <div class="post-card">
                    <div class="post-img">
                        <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="Post Image">
                    </div>
                    <div class="post-content">
                        <span class="post-category"><?= htmlspecialchars($row['category']) ?></span>
                        <h3 class="post-title">
                            <a href="post.php?id=<?= $row['id'] ?>">
                                <?= htmlspecialchars($row['title']) ?>
                            </a>
                        </h3>
                        <p class="post-excerpt"><?= substr(strip_tags($row['content']), 0, 120) ?>...</p>

                        <div class="blog-stats">
                            <span class="stat"><i class="far fa-eye"></i> <?= rand(200, 5000) ?></span>
                            <span class="stat"><i class="far fa-comment"></i> <?= $commentCount ?></span>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button class="like-btn <?= $userLiked ? 'liked' : '' ?>" data-postid="<?= $row['id'] ?>">
                                    <i class="<?= $userLiked ? 'fas' : 'far' ?> fa-heart"></i> 
                                    <span><?= $likeCount ?></span>
                                </button>
                            <?php else: ?>
                                <button class="like-btn disabled">
                                    <i class="far fa-heart"></i> <span><?= $likeCount ?></span>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="post-meta">
                            <img src="../uploads/<?= $row['profile_image'] ?: 'default-user.png' ?>" alt="Author">
                            <div>
                                <span><?= htmlspecialchars($row['username']) ?></span> • 
                                <span><?= date("M d, Y", strtotime($row['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
        </div>
    </section>

    <section id="latest" class="blog-section">
        <div class="container blog-container">
            <div class="main-posts">
                <h2 class="section-title">Latest Articles</h2>
                <?php while ($row = $latestPosts->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php
                    $likeStmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
                    $likeStmt->execute([$row['id']]);
                    $likeCount = $likeStmt->fetchColumn();

                    $commentStmt = $conn->prepare("SELECT comments.*, users.username 
                                                   FROM comments 
                                                   JOIN users ON comments.user_id = users.id 
                                                   WHERE post_id = ? 
                                                   ORDER BY comments.created_at DESC LIMIT 3");
                    $commentStmt->execute([$row['id']]);
                    $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);

                    $userLiked = false;
                    if (isset($_SESSION['user_id'])) {
                        $checkLike = $conn->prepare("SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?");
                        $checkLike->execute([$row['id'], $_SESSION['user_id']]);
                        $userLiked = $checkLike->fetch() ? true : false;
                    }
                    ?>
                    <div class="post-card">
                        <div class="post-img">
                            <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="Post Image">
                        </div>
                        <div class="post-content">
                            <span class="post-category"><?= htmlspecialchars($row['category']) ?></span>
                            <h3 class="post-title">
                                <a href="post.php?id=<?= $row['id'] ?>">
                                    <?= htmlspecialchars($row['title']) ?>
                                </a>
                            </h3>
                            <p class="post-excerpt"><?= substr(strip_tags($row['content']), 0, 150) ?>...</p>

                            <div class="blog-stats">
                                <span class="stat"><i class="far fa-eye"></i> <?= rand(100, 3000) ?></span>
                                <span class="stat"><i class="far fa-comment"></i> <?= count($comments) ?></span>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <button class="like-btn <?= $userLiked ? 'liked' : '' ?>" data-postid="<?= $row['id'] ?>">
                                        <i class="<?= $userLiked ? 'fas' : 'far' ?> fa-heart"></i> 
                                        <span><?= $likeCount ?></span>
                                    </button>
                                <?php else: ?>
                                    <button class="like-btn disabled">
                                        <i class="far fa-heart"></i> <span><?= $likeCount ?></span>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div class="comments-section">
                                <h4 class="comments-title">Comments (<?= count($comments) ?>)</h4>
                                <div class="comment-list">
                                    <?php foreach ($comments as $c): ?>
                                        <div class="comment">
                                            <strong><?= htmlspecialchars($c['username']) ?>:</strong>
                                            <?= htmlspecialchars($c['comment']) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form class="comment-form" method="POST" data-postid="<?= $row['id'] ?>">
                                        <textarea name="comment" class="comment-input" rows="2" placeholder="Write a comment..." required></textarea>
                                        <button type="submit" class="comment-btn">💬 Comment</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="sidebar">
                <div class="sidebar-widget">
                    <h3 class="sidebar-title">About BlogSpace</h3>
                    <p>BlogSpace is a platform for sharing ideas, stories, and expertise. We cover a wide range of topics from technology to lifestyle.</p>
                </div>
            </div>
        </div>
    </section>

<?php include ('../layouts/footer.php'); ?>
</body>
</html>
