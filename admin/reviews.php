<?php
/**
 * Admin Review Moderation
 * SineSine - Movie Rating and Review Website
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pdo = getDBConnection();

// Handle deletion of inappropriate review
if (isset($_GET['delete']) && isset($_GET['csrf_token'])) {
    if (verifyCSRFToken($_GET['csrf_token'])) {
        $deleteId = (int)$_GET['delete'];
        $delStmt = $pdo->prepare("DELETE FROM reviews WHERE id = :id");
        $delStmt->execute([':id' => $deleteId]);
        setFlashMessage('success', 'The review was removed from the system.');
    } else {
        setFlashMessage('danger', 'Security validation failed.');
    }
    header('Location: /sinesine/admin/reviews.php');
    exit;
}

// Fetch all reviews with movie title and author
$reviews = $pdo->query("
    SELECT r.*, u.username, u.full_name, u.avatar, m.title AS movie_title
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN movies m ON r.movie_id = m.id
    ORDER BY r.created_at DESC
")->fetchAll();

define('ADMIN_TITLE', 'Manage Reviews - SineSine Admin');
include_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-white fw-bold mb-1">
            <i class="fa-solid fa-comments text-warning me-2"></i> Review Moderation
        </h2>
        <p class="text-secondary mb-0">Total <?= count($reviews) ?> submitted community reviews</p>
    </div>
</div>

<div class="card bg-dark border-secondary rounded-4 shadow overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead class="table-secondary text-dark">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>User</th>
                    <th>Movie</th>
                    <th style="width: 100px;">Rating</th>
                    <th>Review Content</th>
                    <th style="width: 120px;">Date</th>
                    <th class="text-end" style="width: 90px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reviews)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-5">
                            <i class="fa-regular fa-comment-dots fa-3x mb-2 d-block"></i>
                            No reviews found in database.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reviews as $r): ?>
                        <tr>
                            <td class="text-secondary">#<?= $r['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="/sinesine/assets/images/avatars/<?= sanitize($r['avatar']) ?>" 
                                         onerror="this.src='/sinesine/assets/images/default-avatar.png'"
                                         alt="avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-white small"><?= sanitize($r['full_name']) ?></div>
                                        <div class="text-warning small">@<?= sanitize($r['username']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="/sinesine/movie-details.php?id=<?= $r['movie_id'] ?>" target="_blank" class="text-white fw-semibold text-decoration-none">
                                    <?= sanitize($r['movie_title']) ?> <i class="fa-solid fa-arrow-up-right-from-square small text-secondary"></i>
                                </a>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark fw-bold fs-6">
                                    <i class="fa-solid fa-star me-1"></i> <?= $r['rating'] ?>/10
                                </span>
                            </td>
                            <td>
                                <div class="text-light small text-break" style="max-width: 400px;">
                                    <?= nl2br(sanitize($r['review_text'])) ?>
                                </div>
                            </td>
                            <td class="small text-secondary"><?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                            <td class="text-end">
                                <a href="/sinesine/admin/reviews.php?delete=<?= $r['id'] ?>&csrf_token=<?= getCSRFToken() ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   title="Delete Inappropriate Review"
                                   onclick="return confirm('Delete this review permanently?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once __DIR__ . '/footer.php'; ?>

