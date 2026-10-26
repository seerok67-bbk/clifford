<?php
/**
 * User Profile Page
 * SineSine - Movie Rating and Review Website
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

// Fetch current user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: /sinesine/logout.php');
    exit;
}

// Fetch all reviews submitted by this user
$revStmt = $pdo->prepare("
    SELECT r.*, m.title AS movie_title, m.release_year, m.poster, m.genre
    FROM reviews r
    JOIN movies m ON r.movie_id = m.id
    WHERE r.user_id = :uid
    ORDER BY r.created_at DESC
");
$revStmt->execute([':uid' => $userId]);
$userReviews = $revStmt->fetchAll();

define('PAGE_TITLE', sanitize($user['full_name']) . ' - Profile | SineSine');
include_once __DIR__ . '/includes/header.php';
?>

<div class="row g-4 mb-5">
    <!-- User Profile Overview Card -->
    <div class="col-md-4">
        <div class="card bg-dark border-secondary rounded-4 shadow p-4 text-center">
            <div class="position-relative d-inline-block mx-auto mb-3">
                <img src="/sinesine/assets/images/avatars/<?= sanitize($user['avatar']) ?>" 
                     onerror="this.src='/sinesine/assets/images/default-avatar.png'"
                     alt="Avatar" 
                     class="rounded-circle border border-3 border-warning shadow"
                     style="width: 140px; height: 140px; object-fit: cover;">
            </div>

            <h4 class="text-white fw-bold mb-1"><?= sanitize($user['full_name']) ?></h4>
            <p class="text-warning small mb-3">@<?= sanitize($user['username']) ?></p>

            <div class="d-flex justify-content-center gap-2 mb-4">
                <span class="badge bg-<?= ($user['role'] === 'admin') ? 'danger' : 'secondary' ?> text-uppercase px-3 py-2">
                    <i class="fa-solid <?= ($user['role'] === 'admin') ? 'fa-shield-halved' : 'fa-user' ?> me-1"></i>
                    <?= sanitize($user['role']) ?>
                </span>
                <span class="badge bg-success px-3 py-2 text-uppercase">
                    <i class="fa-solid fa-circle-check me-1"></i> <?= sanitize($user['status']) ?>
                </span>
            </div>

            <div class="text-start bg-black p-3 rounded-3 border border-secondary mb-4">
                <div class="d-flex justify-content-between py-1 border-bottom border-secondary small">
                    <span class="text-secondary"><i class="fa-solid fa-envelope me-2"></i> Email:</span>
                    <span class="text-light text-truncate" style="max-width: 180px;"><?= sanitize($user['email']) ?></span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom border-secondary small">
                    <span class="text-secondary"><i class="fa-solid fa-calendar me-2"></i> Joined:</span>
                    <span class="text-light"><?= date('M d, Y', strtotime($user['created_at'])) ?></span>
                </div>
                <div class="d-flex justify-content-between py-1 small">
                    <span class="text-secondary"><i class="fa-solid fa-comment-dots me-2"></i> Reviews:</span>
                    <span class="text-warning fw-bold"><?= count($userReviews) ?></span>
                </div>
            </div>

            <a href="/sinesine/edit-profile.php" class="btn btn-gold w-100 mb-2">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Profile &amp; Avatar
            </a>
            <a href="/sinesine/logout.php" class="btn btn-outline-danger w-100 btn-sm">
                <i class="fa-solid fa-right-from-bracket me-1"></i> Log Out
            </a>
        </div>
    </div>

    <!-- User's Activity & Reviews -->
    <div class="col-md-8">
        <div class="card bg-dark border-secondary rounded-4 shadow p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="text-white mb-0">
                    <i class="fa-solid fa-star text-warning me-2"></i> My Ratings &amp; Reviews (<?= count($userReviews) ?>)
                </h4>
                <a href="/sinesine/movies.php" class="btn btn-sm btn-outline-gold">
                    <i class="fa-solid fa-plus me-1"></i> Review More Movies
                </a>
            </div>

            <?php if (empty($userReviews)): ?>
                <div class="text-center py-5">
                    <i class="fa-regular fa-star text-secondary fa-3x mb-3"></i>
                    <h5 class="text-white">You Haven't Rated Any Movies Yet</h5>
                    <p class="text-secondary small">Explore movies and share your thoughts with the community!</p>
                    <a href="/sinesine/movies.php" class="btn btn-gold mt-2">
                        <i class="fa-solid fa-film me-1"></i> Browse Catalog
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($userReviews as $rev): ?>
                    <div class="card bg-black border-secondary p-3 mb-3 rounded-3">
                        <div class="d-flex gap-3">
                            <a href="/sinesine/movie-details.php?id=<?= $rev['movie_id'] ?>">
                                <img src="/sinesine/assets/images/movies/<?= sanitize($rev['poster']) ?>" 
                                     alt="<?= sanitize($rev['movie_title']) ?>" 
                                     class="rounded"
                                     style="width: 70px; height: 105px; object-fit: cover;">
                            </a>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-1">
                                            <a href="/sinesine/movie-details.php?id=<?= $rev['movie_id'] ?>" class="text-white text-decoration-none">
                                                <?= sanitize($rev['movie_title']) ?>
                                            </a>
                                            <span class="text-secondary small ms-1">(<?= $rev['release_year'] ?>)</span>
                                        </h5>
                                        <span class="badge bg-dark border border-secondary text-secondary small mb-2">
                                            <?= sanitize($rev['genre']) ?>
                                        </span>
                                    </div>
                                    <span class="badge bg-warning text-dark fs-6 fw-bold">
                                        <i class="fa-solid fa-star me-1"></i> <?= $rev['rating'] ?>/10
                                    </span>
                                </div>
                                <p class="text-light small mb-2"><?= nl2br(sanitize($rev['review_text'])) ?></p>
                                <div class="d-flex justify-content-between align-items-center text-secondary small pt-2 border-top border-secondary">
                                    <span><i class="fa-regular fa-clock me-1"></i> Reviewed on <?= date('M d, Y', strtotime($rev['created_at'])) ?></span>
                                    <div class="d-flex gap-2">
                                        <a href="/sinesine/movie-details.php?id=<?= $rev['movie_id'] ?>#review-section" class="btn btn-sm btn-outline-warning py-0 px-2" title="Edit Review">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </a>
                                        <a href="/sinesine/delete-review.php?id=<?= $rev['id'] ?>&csrf_token=<?= getCSRFToken() ?>" 
                                           class="btn btn-sm btn-outline-danger py-0 px-2" 
                                           title="Delete Review"
                                           onclick="return confirm('Are you sure you want to delete this review?');">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

