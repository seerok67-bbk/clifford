<?php
/**
 * Admin Dashboard Overview & Statistics
 * SineSine - Movie Rating and Review Website
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pdo = getDBConnection();

// 1. Total Movies
$totalMovies = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();

// 2. Total Registered Users
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

// 3. Total Reviews
$totalReviews = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();

// 4. Average Website Movie Rating
$avgRating = $pdo->query("SELECT AVG(rating) FROM reviews")->fetchColumn();
$avgRatingDisplay = ($avgRating !== null && $avgRating > 0) ? number_format($avgRating, 1) : 'N/A';

// Recent 5 Movies
$recentMovies = $pdo->query("SELECT * FROM movies ORDER BY id DESC LIMIT 5")->fetchAll();

// Recent 5 Reviews
$recentReviews = $pdo->query("
    SELECT r.*, u.username, m.title AS movie_title 
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN movies m ON r.movie_id = m.id
    ORDER BY r.created_at DESC
    LIMIT 5
")->fetchAll();

define('ADMIN_TITLE', 'Admin Dashboard - SineSine');
include_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-white fw-bold mb-1">
            <i class="fa-solid fa-gauge text-warning me-2"></i> System Overview
        </h2>
        <p class="text-secondary mb-0">Platform performance and statistics summary</p>
    </div>
    <div>
        <a href="/sinesine/admin/movie-add.php" class="btn btn-gold">
            <i class="fa-solid fa-plus me-1"></i> Add New Movie
        </a>
    </div>
</div>

<!-- 4 Statistics Cards -->
<div class="row g-4 mb-5">
    <!-- Total Movies -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon gold">
                <i class="fa-solid fa-film"></i>
            </div>
            <div>
                <h3 class="stat-val"><?= $totalMovies ?></h3>
                <p class="stat-title">Total Movies</p>
            </div>
        </div>
    </div>

    <!-- Total Registered Users -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <h3 class="stat-val"><?= $totalUsers ?></h3>
                <p class="stat-title">Registered Users</p>
            </div>
        </div>
    </div>

    <!-- Total Reviews -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fa-solid fa-comments"></i>
            </div>
            <div>
                <h3 class="stat-val"><?= $totalReviews ?></h3>
                <p class="stat-title">Total Reviews</p>
            </div>
        </div>
    </div>

    <!-- Average Website Movie Rating -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <h3 class="stat-val"><?= $avgRatingDisplay ?> <span class="fs-6 text-secondary"><?= ($avgRatingDisplay !== 'N/A') ? '/10' : '' ?></span></h3>
                <p class="stat-title">Avg Website Rating</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tables Row -->
<div class="row g-4">
    <!-- Recent Movies -->
    <div class="col-lg-6">
        <div class="card bg-dark border-secondary rounded-4 shadow p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-white mb-0"><i class="fa-solid fa-clapperboard text-warning me-2"></i> Recently Added Movies</h5>
                <a href="/sinesine/admin/movies.php" class="btn btn-sm btn-outline-warning">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Poster</th>
                            <th>Title</th>
                            <th>Year</th>
                            <th>Genre</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentMovies as $m): ?>
                            <tr>
                                <td style="width: 50px;">
                                    <img src="/sinesine/assets/images/movies/<?= sanitize($m['poster']) ?>" alt="poster" class="rounded" style="width: 40px; height: 55px; object-fit: cover;">
                                </td>
                                <td class="fw-bold text-white"><?= sanitize($m['title']) ?></td>
                                <td><?= $m['release_year'] ?></td>
                                <td><span class="badge bg-secondary"><?= sanitize($m['genre']) ?></span></td>
                                <td>
                                    <a href="/sinesine/admin/movie-edit.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-info py-0 px-2" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Reviews -->
    <div class="col-lg-6">
        <div class="card bg-dark border-secondary rounded-4 shadow p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-white mb-0"><i class="fa-solid fa-comments text-warning me-2"></i> Recent Community Reviews</h5>
                <a href="/sinesine/admin/reviews.php" class="btn btn-sm btn-outline-warning">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Movie</th>
                            <th>Rating</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentReviews)): ?>
                            <tr><td colspan="4" class="text-center text-secondary py-3">No reviews submitted yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentReviews as $r): ?>
                                <tr>
                                    <td class="text-warning">@<?= sanitize($r['username']) ?></td>
                                    <td class="text-white"><?= sanitize($r['movie_title']) ?></td>
                                    <td><span class="badge bg-warning text-dark fw-bold"><i class="fa-solid fa-star"></i> <?= $r['rating'] ?>/10</span></td>
                                    <td class="small text-secondary"><?= date('M d', strtotime($r['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/footer.php'; ?>

