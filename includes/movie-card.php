<?php
/**
 * Movie Card Partial Component
 * SineSine - Movie Rating and Review Website
 * Expects $movie array
 */
if (!isset($movie)) return;
$hasRating = isset($movie['avg_rating']) && $movie['avg_rating'] > 0;
$ratingDisplay = $hasRating ? number_format($movie['avg_rating'], 1) : 'Not Yet Rated';
$reviewCount = $movie['review_count'] ?? 0;
?>
<div class="col-6 col-md-4 col-lg-3 col-xl-2dot4 mb-4">
    <div class="movie-card">
        <div class="movie-poster-wrap">
            <a href="/sinesine/movie-details.php?id=<?= $movie['id'] ?>">
                <img src="/sinesine/assets/images/movies/<?= sanitize($movie['poster']) ?>" 
                     onerror="this.src='/sinesine/assets/images/movies/inception.jpg'"
                     alt="<?= sanitize($movie['title']) ?>" 
                     class="movie-poster-img"
                     loading="lazy">
            </a>
            <span class="movie-genre-badge"><?= sanitize(explode(',', $movie['genre'])[0]) ?></span>
        </div>
        <div class="movie-card-body">
            <div class="movie-card-title" title="<?= sanitize($movie['title']) ?>">
                <a href="/sinesine/movie-details.php?id=<?= $movie['id'] ?>" class="text-white text-decoration-none">
                    <?= sanitize($movie['title']) ?>
                </a>
            </div>
            <div class="movie-card-meta">
                <span><i class="fa-regular fa-calendar me-1"></i><?= sanitize($movie['release_year']) ?></span>
                <span class="mx-1">•</span>
                <span><i class="fa-regular fa-clock me-1"></i><?= sanitize($movie['duration']) ?></span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-3">
                <?php if ($hasRating): ?>
                    <div class="rating-badge">
                        <i class="fa-solid fa-star text-warning"></i>
                        <span><?= $ratingDisplay ?></span>
                        <small class="text-secondary fw-normal">/10</small>
                    </div>
                <?php else: ?>
                    <div class="rating-badge empty-rating">
                        <i class="fa-regular fa-star text-secondary"></i>
                        <span>No Ratings</span>
                    </div>
                <?php endif; ?>
                <span class="badge bg-dark border border-secondary text-secondary" title="<?= $reviewCount ?> reviews">
                    <i class="fa-solid fa-comment me-1"></i><?= $reviewCount ?>
                </span>
            </div>
            <div class="movie-card-footer">
                <a href="/sinesine/movie-details.php?id=<?= $movie['id'] ?>" class="btn btn-outline-gold btn-sm w-100">
                    <i class="fa-solid fa-circle-info me-1"></i> Details
                </a>
            </div>
        </div>
    </div>
</div>

