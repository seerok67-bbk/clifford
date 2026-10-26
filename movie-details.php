<?php
/**
 * Movie Details, User Rating & Community Reviews
 * SineSine - Movie Rating and Review Website
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDBConnection();

$movieId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$movie = getMovieById($pdo, $movieId);

if (!$movie) {
    setFlashMessage('danger', 'The requested movie was not found.');
    header('Location: /sinesine/movies.php');
    exit;
}

define('PAGE_TITLE', sanitize($movie['title']) . ' - SineSine');

$reviews = getMovieReviews($pdo, $movieId);

// Check if current user already reviewed this movie
$userExistingReview = null;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE movie_id = :mid AND user_id = :uid LIMIT 1");
    $stmt->execute([':mid' => $movieId, ':uid' => $_SESSION['user_id']]);
    $userExistingReview = $stmt->fetch();
}

$hasRating = $movie['avg_rating'] > 0;
$ratingScore = $hasRating ? number_format($movie['avg_rating'], 1) : null;

include_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/sinesine/index.php" class="text-secondary"><i class="fa-solid fa-house me-1"></i> Home</a></li>
        <li class="breadcrumb-item"><a href="/sinesine/movies.php" class="text-secondary">Movies</a></li>
        <li class="breadcrumb-item active text-warning" aria-current="page"><?= sanitize($movie['title']) ?></li>
    </ol>
</nav>

<!-- Main Details Section -->
<div class="row g-4 mb-5">
    <!-- Movie Poster Column -->
    <div class="col-md-4 col-lg-3">
        <img src="/sinesine/assets/images/movies/<?= sanitize($movie['poster']) ?>" 
             onerror="this.src='/sinesine/assets/images/movies/inception.jpg'"
             alt="<?= sanitize($movie['title']) ?>" 
             class="detail-poster-img mb-3">

        <!-- Rating Summary Box -->
        <div class="detail-info-card text-center">
            <span class="text-secondary small text-uppercase fw-semibold">Community Score</span>
            <div class="d-flex align-items-center justify-content-center gap-2 my-2">
                <i class="fa-solid fa-star fa-2x text-warning"></i>
                <div class="display-6 fw-bold text-white">
                    <?= $hasRating ? $ratingScore : '<span class="fs-4 text-secondary">N/A</span>' ?>
                </div>
                <span class="text-secondary fs-5">/ 10</span>
            </div>
            <p class="text-secondary small mb-0">
                <i class="fa-solid fa-user-pen me-1"></i> Based on <strong><?= (int)$movie['review_count'] ?></strong> user rating<?= ((int)$movie['review_count'] === 1) ? '' : 's' ?>
            </p>
        </div>
    </div>

    <!-- Movie Details Content Column -->
    <div class="col-md-8 col-lg-9">
        <div class="detail-info-card mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <h1 class="h2 text-white fw-bold mb-1"><?= sanitize($movie['title']) ?></h1>
                    <div class="d-flex flex-wrap gap-2 align-items-center mt-2">
                        <span class="detail-meta-pill"><i class="fa-regular fa-calendar text-warning"></i> <?= sanitize($movie['release_year']) ?></span>
                        <span class="detail-meta-pill"><i class="fa-regular fa-clock text-warning"></i> <?= sanitize($movie['duration']) ?></span>
                        <span class="detail-meta-pill"><i class="fa-solid fa-masks-theater text-warning"></i> <?= sanitize($movie['genre']) ?></span>
                    </div>
                </div>

                <?php if (isAdmin()): ?>
                    <div>
                        <a href="/sinesine/admin/movie-edit.php?id=<?= $movie['id'] ?>" class="btn btn-sm btn-outline-warning">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Movie
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <hr class="border-secondary my-4">

            <!-- Full Synopsis -->
            <div class="mb-4">
                <h5 class="text-warning mb-2"><i class="fa-solid fa-align-left me-2"></i> Storyline</h5>
                <p class="text-light leading-relaxed fs-6 mb-0"><?= nl2br(sanitize($movie['description'])) ?></p>
            </div>

            <!-- Cast & Crew -->
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="p-3 rounded bg-dark border border-secondary">
                        <span class="text-secondary small text-uppercase d-block mb-1"><i class="fa-solid fa-clapperboard text-warning me-1"></i> Director</span>
                        <strong class="text-white"><?= sanitize($movie['director']) ?></strong>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 rounded bg-dark border border-secondary">
                        <span class="text-secondary small text-uppercase d-block mb-1"><i class="fa-solid fa-users text-warning me-1"></i> Starring Cast</span>
                        <strong class="text-white"><?= sanitize($movie['cast']) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating & Review Submission Area -->
        <div class="detail-info-card mb-4" id="review-section">
            <h4 class="text-white mb-3">
                <i class="fa-solid fa-star-half-stroke text-warning me-2"></i> 
                <?= $userExistingReview ? 'Your Submitted Rating & Review' : 'Rate & Review This Movie' ?>
            </h4>

            <?php if (isAdmin()): ?>
                <div class="text-center py-4 bg-dark rounded border border-secondary">
                    <i class="fa-solid fa-shield-halved text-warning fa-2x mb-2"></i>
                    <h5 class="text-white">Administrator Accounts</h5>
                    <p class="text-secondary small mb-0">Administrators cannot submit ratings or reviews.</p>
                </div>
            <?php elseif (isLoggedIn()): ?>
                <form action="/sinesine/submit-review.php" method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label d-flex justify-content-between">
                            <span>Select Your Rating (1 to 10 Stars):</span>
                            <span id="selected_rating_label" class="text-warning fw-bold">
                                <?= $userExistingReview ? ($userExistingReview['rating'] . '/10') : 'Select a score' ?>
                            </span>
                        </label>
                        <input type="hidden" name="rating" id="rating_input" value="<?= $userExistingReview ? $userExistingReview['rating'] : '10' ?>" required>
                        
                        <div class="star-rating-widget">
                            <?php 
                            $currentScore = $userExistingReview ? (int)$userExistingReview['rating'] : 10;
                            for ($i = 1; $i <= 10; $i++): 
                            ?>
                                <button type="button" class="star-rating-btn <?= ($i <= $currentScore) ? 'active' : '' ?>" data-rating="<?= $i ?>" title="<?= $i ?>/10">
                                    <?= $i ?>
                                </button>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="review_text" class="form-label">Your Review Synopsis:</label>
                        <textarea class="form-control" name="review_text" id="review_text" rows="4" placeholder="Share your honest thoughts, standout scenes, cinematography, or acting performance..." required><?= $userExistingReview ? sanitize($userExistingReview['review_text']) : '' ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-gold">
                            <i class="fa-solid <?= $userExistingReview ? 'fa-pen' : 'fa-paper-plane' ?> me-1"></i>
                            <?= $userExistingReview ? 'Update My Review' : 'Submit Review' ?>
                        </button>

                        <?php if ($userExistingReview): ?>
                            <a href="/sinesine/delete-review.php?id=<?= $userExistingReview['id'] ?>&csrf_token=<?= getCSRFToken() ?>" 
                               class="btn btn-outline-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete your review?');">
                                <i class="fa-solid fa-trash me-1"></i> Delete Review
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-4 bg-dark rounded border border-secondary">
                    <i class="fa-solid fa-lock text-warning fa-2x mb-2"></i>
                    <h5 class="text-white">Want to share your rating or write a review?</h5>
                    <p class="text-secondary small mb-3">Guests can browse public reviews, but must log in to submit ratings.</p>
                    <a href="/sinesine/login.php" class="btn btn-gold me-2">
                        <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Login to Rate
                    </a>
                    <a href="/sinesine/register.php" class="btn btn-outline-light">
                        <i class="fa-solid fa-user-plus me-1"></i> Create Account
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Community Reviews List -->
        <div class="detail-info-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="text-white mb-0">
                    <i class="fa-solid fa-comments text-warning me-2"></i>
                    Community Reviews (<?= count($reviews) ?>)
                </h4>
            </div>

            <?php if (empty($reviews)): ?>
                <div class="text-center py-5">
                    <i class="fa-regular fa-comment-dots text-secondary fa-3x mb-3"></i>
                    <h5 class="text-white">No Reviews Yet</h5>
                    <p class="text-secondary small">Be the first to rate and write a review for <strong><?= sanitize($movie['title']) ?></strong>!</p>
                </div>
            <?php else: ?>
                <?php foreach ($reviews as $rev): ?>
                    <div class="review-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-3">
                                <img src="/sinesine/assets/images/avatars/<?= sanitize($rev['avatar']) ?>" 
                                     onerror="this.src='/sinesine/assets/images/default-avatar.png'"
                                     alt="Avatar" class="review-user-avatar">
                                <div>
                                    <div class="fw-bold text-white"><?= sanitize($rev['full_name']) ?> <small class="text-secondary fw-normal">(@<?= sanitize($rev['username']) ?>)</small></div>
                                    <small class="text-secondary">
                                        <i class="fa-regular fa-clock me-1"></i> <?= date('M d, Y', strtotime($rev['created_at'])) ?>
                                        <?php if ($rev['updated_at'] !== $rev['created_at']): ?>
                                            <span class="fst-italic">(edited)</span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-warning text-dark fs-6 fw-bold">
                                    <i class="fa-solid fa-star me-1"></i> <?= $rev['rating'] ?>/10
                                </span>
                                <?php if (isAdmin()): ?>
                                    <a href="/sinesine/admin/reviews.php?delete=<?= $rev['id'] ?>&csrf_token=<?= getCSRFToken() ?>" 
                                       class="btn btn-outline-danger btn-sm p-1 px-2"
                                       title="Admin: Delete inappropriate review"
                                       onclick="return confirm('Admin: Are you sure you want to delete this review?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p class="text-light mt-3 mb-0"><?= nl2br(sanitize($rev['review_text'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>
