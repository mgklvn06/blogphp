<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<header>
    <div class="container header-container">
        <div class="logo">Blog<span>Space</span></div>
        <nav>
            <ul>
                <li><a href="/space/blog.php" class="active">Home</a></li>
                <li><a href="#">Categories</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><span>Welcome, <?= htmlspecialchars($_SESSION['username']); ?> 👋</span></li>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a href="/space/dashboard/admin.php">Admin Dashboard</a></li>
                    <?php elseif ($_SESSION['role'] === 'creator'): ?>
                        <li><a href="/space/dashboard/creator.php">Creator Dashboard</a></li>
                    <?php endif; ?>

                    <li><a href="/space/auth/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="/space/auth/register.php">Register</a></li>
                    <li><a href="/space/auth/login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
