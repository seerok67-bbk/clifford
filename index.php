<?php
/**
 * Homepage
 * SineSine - Movie Rating and Review Website
 */
define('PAGE_TITLE', 'SineSine - Discover. Watch. Rate.');
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDBConnection();

// Fetch Hero Featured Movie (e.g. Inception or Dune: Part Two)
$heroMovie = getMovieById($pdo, 11); // Dune: Part Two
if (!$heroMovie) {
    $all = getMovies($pdo, [], 'title_asc', 1);
    $heroMovie = $all[0] ?? null;
}

// 1. Featured Movies (limit 5)
$featuredMovies = getMovies($pdo, [], 'newest', 5);

// 2. Top Rated Movies (ranked strictly by average user ratings, where avg_rating > 0)
$topRatedStmt = $pdo->prepare("
    SELECT m.*, 
    AVG(r.rating) AS avg_rating,
    COUNT(r.id) AS review_count
    FROM movies m
    JOIN reviews r ON m.id = r.movie_id
    GROUP BY m.id
    ORDER BY avg_rating DESC, review_count DESC
    LIMIT 5
");
$topRatedStmt->execute();
$topRatedMovies = $topRatedStmt->fetchAll();

// 3. Popular Movies (ranked by review count)
$popularMovies = getMovies($pdo, [], 'reviews_desc', 5);

// 4. Browse Recent / All Movies
$browseMovies = getMovies($pdo, [], 'title_asc', 10);

include_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<?php if ($heroMovie): ?>
<div class="hero-banner shadow" style="background: linear-gradient(90deg, rgba(12, 15, 20, 0.98) 0%, rgba(12, 15, 20, 0.7) 40%, rgba(12, 15, 20, 0.3) 70%, transparent 100%), url('/sinesine/assets/images/movies/<?= sanitize($heroMovie['poster']) ?>') center right/cover no-repeat;">
    <div class="hero-content">
        <span class="hero-badge"><i class="fa-solid fa-fire me-1"></i> Featured Spotlight</span>
        <h1 class="hero-title"><?= sanitize($heroMovie['title']) ?></h1>
        <div class="d-flex align-items-center gap-3 mb-3 text-light">
            <span><i class="fa-solid fa-calendar-day text-warning me-1"></i> <?= sanitize($heroMovie['release_year']) ?></span>
            <span><i class="fa-solid fa-masks-theater text-warning me-1"></i> <?= sanitize($heroMovie['genre']) ?></span>
            <span><i class="fa-solid fa-clock text-warning me-1"></i> <?= sanitize($heroMovie['duration']) ?></span>
        </div>
        <p class="hero-desc"><?= sanitize($heroMovie['description']) ?></p>
        <div class="d-flex gap-3">
            <a href="/sinesine/movie-details.php?id=<?= $heroMovie['id'] ?>" class="btn btn-gold btn-lg">
                <i class="fa-solid fa-circle-play me-2"></i> View Details &amp; Rate
            </a>
            <a href="/sinesine/movies.php" class="btn btn-outline-light btn-lg">
                <i class="fa-solid fa-list me-2"></i> Browse All
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Section: Featured Movies -->
<section class="mb-5">
    <div class="section-header">
        <h2 class="section-title"><i class="fa-solid fa-wand-magic-sparkles text-warning me-2"></i> Featured Releases</h2>
        <a href="/sinesine/movies.php?sort=newest" class="btn btn-sm btn-outline-gold">View More <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
    <div class="row">
        <?php foreach ($featuredMovies as $movie): ?>
            <?php include __DIR__ . '/includes/movie-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<!-- Section: Top Rated Movies -->
<?php if (!empty($topRatedMovies)): ?>
<section class="mb-5">
    <div class="section-header">
        <h2 class="section-title"><i class="fa-solid fa-trophy text-warning me-2"></i> Top Rated by Community</h2>
        <a href="/sinesine/movies.php?sort=rating_desc" class="btn btn-sm btn-outline-gold">View Ranking <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
    <div class="row">
        <?php foreach ($topRatedMovies as $movie): ?>
            <?php include __DIR__ . '/includes/movie-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Section: Popular Movies -->
<section class="mb-5">
    <div class="section-header">
        <h2 class="section-title"><i class="fa-solid fa-comments text-warning me-2"></i> Most Discussed Movies</h2>
        <a href="/sinesine/movies.php?sort=reviews_desc" class="btn btn-sm btn-outline-gold">See Popular <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
    <div class="row">
        <?php foreach ($popularMovies as $movie): ?>
            <?php include __DIR__ . '/includes/movie-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<!-- Section: Browse All Movies -->
<section class="mb-5">
    <div class="section-header">
        <h2 class="section-title"><i class="fa-solid fa-film text-warning me-2"></i> Explore CineSine Collection</h2>
        <a href="/sinesine/movies.php" class="btn btn-sm btn-outline-gold">Browse Complete Catalog <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
    <div class="row">
        <?php foreach ($browseMovies as $movie): ?>
            <?php include __DIR__ . '/includes/movie-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

