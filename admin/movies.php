<?php
/**
 * Admin Movie Management Listing
 * SineSine - Movie Rating and Review Website
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pdo = getDBConnection();

// Fetch movies with review count and avg rating
$movies = getMovies($pdo, [], 'newest');

define('ADMIN_TITLE', 'Manage Movies - SineSine Admin');
include_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-white fw-bold mb-1">
            <i class="fa-solid fa-clapperboard text-warning me-2"></i> Movie Management
        </h2>
        <p class="text-secondary mb-0">Total <?= count($movies) ?> movies in catalog</p>
    </div>
    <a href="/sinesine/admin/movie-add.php" class="btn btn-gold">
        <i class="fa-solid fa-plus me-1"></i> Add New Movie
    </a>
</div>

<div class="card bg-dark border-secondary rounded-4 shadow overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead class="table-secondary text-dark">
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th style="width: 80px;">Poster</th>
                    <th>Movie Title</th>
                    <th>Genre</th>
                    <th>Year</th>
                    <th>Director</th>
                    <th>Avg Rating</th>
                    <th>Reviews</th>
                    <th class="text-end" style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movies as $m): ?>
                    <tr>
                        <td class="text-secondary">#<?= $m['id'] ?></td>
                        <td>
                            <img src="/sinesine/assets/images/movies/<?= sanitize($m['poster']) ?>" 
                                 onerror="this.src='/sinesine/assets/images/movies/inception.jpg'"
                                 alt="poster" class="rounded border border-secondary" 
                                 style="width: 48px; height: 68px; object-fit: cover;">
                        </td>
                        <td>
                            <div class="fw-bold text-white"><?= sanitize($m['title']) ?></div>
                            <small class="text-secondary"><?= sanitize($m['duration']) ?></small>
                        </td>
                        <td><span class="badge bg-secondary"><?= sanitize($m['genre']) ?></span></td>
                        <td><?= $m['release_year'] ?></td>
                        <td class="text-light"><?= sanitize($m['director']) ?></td>
                        <td>
                            <?php if ($m['avg_rating'] > 0): ?>
                                <span class="badge bg-warning text-dark fw-bold">
                                    <i class="fa-solid fa-star"></i> <?= number_format($m['avg_rating'], 1) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-secondary small">Not Rated</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-black border border-secondary text-light">
                                <i class="fa-solid fa-comments me-1"></i><?= $m['review_count'] ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="/sinesine/movie-details.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-light me-1" title="View Public Page" target="_blank">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="/sinesine/admin/movie-edit.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-warning me-1" title="Edit Movie">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <a href="/sinesine/admin/movie-delete.php?id=<?= $m['id'] ?>&csrf_token=<?= getCSRFToken() ?>" 
                               class="btn btn-sm btn-outline-danger" 
                               title="Delete Movie"
                               onclick="return confirm('Are you sure you want to delete this movie? This will also remove all its reviews.');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once __DIR__ . '/footer.php'; ?>

