<?php
/**
 * Movies Listing, Search, Filter & Sort
 * SineSine - Movie Rating and Review Website
 */
define('PAGE_TITLE', 'SineSine - Browse All Movies');
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDBConnection();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$genre  = isset($_GET['genre'])  ? trim($_GET['genre'])  : '';
$sort   = isset($_GET['sort'])   ? trim($_GET['sort'])   : 'title_asc';

$filters = [];
if ($search !== '') $filters['search'] = $search;
if ($genre !== '')  $filters['genre']  = $genre;

$movies = getMovies($pdo, $filters, $sort);

$availableGenres = [
    'Action', 'Adventure', 'Animation', 'Comedy', 
    'Drama', 'Horror', 'Romance', 'Science Fiction', 'Thriller'
];

include_once __DIR__ . '/includes/header.php';
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1 class="h3 text-white mb-1">
            <i class="fa-solid fa-clapperboard text-warning me-2"></i>
            <?= $genre ? htmlspecialchars($genre) . ' Movies' : 'Explore Movies' ?>
        </h1>
        <p class="text-secondary mb-0">
            <?= count($movies) ?> movie<?= count($movies) === 1 ? '' : 's' ?> found in the database.
        </p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <?php if ($search || $genre): ?>
            <a href="/sinesine/movies.php" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-xmark me-1"></i> Clear Filters
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Filter & Search Controls Bar -->
<div class="card bg-dark border-secondary p-3 mb-4 rounded-3 shadow-sm">
    <form method="GET" action="/sinesine/movies.php" class="row g-3 align-items-end">
        <!-- Search Input -->
        <div class="col-12 col-md-4">
            <label class="form-label small text-secondary">Search by Title or Director</label>
            <div class="input-group">
                <span class="input-group-text bg-black border-secondary text-secondary">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" class="form-control" placeholder="e.g. Inception, Nolan..." value="<?= sanitize($search) ?>">
            </div>
        </div>

        <!-- Genre Filter -->
        <div class="col-6 col-md-3">
            <label class="form-label small text-secondary">Filter by Genre</label>
            <select name="genre" class="form-select">
                <option value="">All Genres</option>
                <?php foreach ($availableGenres as $g): ?>
                    <option value="<?= $g ?>" <?= ($genre === $g) ? 'selected' : '' ?>><?= $g ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Sorting Options -->
        <div class="col-6 col-md-3">
            <label class="form-label small text-secondary">Sort Results</label>
            <select name="sort" class="form-select">
                <option value="title_asc" <?= ($sort === 'title_asc') ? 'selected' : '' ?>>Title (A to Z)</option>
                <option value="title_desc" <?= ($sort === 'title_desc') ? 'selected' : '' ?>>Title (Z to A)</option>
                <option value="newest" <?= ($sort === 'newest') ? 'selected' : '' ?>>Newest Release</option>
                <option value="oldest" <?= ($sort === 'oldest') ? 'selected' : '' ?>>Oldest Release</option>
                <option value="rating_desc" <?= ($sort === 'rating_desc') ? 'selected' : '' ?>>Highest Rated</option>
                <option value="reviews_desc" <?= ($sort === 'reviews_desc') ? 'selected' : '' ?>>Most Reviewed</option>
            </select>
        </div>

        <!-- Submit Button -->
        <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-gold w-100">
                <i class="fa-solid fa-sliders me-1"></i> Apply
            </button>
        </div>
    </form>
</div>

<!-- Movies Grid -->
<?php if (empty($movies)): ?>
    <div class="text-center py-5">
        <i class="fa-solid fa-film-slash text-secondary fa-4x mb-3"></i>
        <h4 class="text-white">No Movies Found</h4>
        <p class="text-secondary">We couldn't find any movie matching your search criteria.</p>
        <a href="/sinesine/movies.php" class="btn btn-gold mt-2">
            <i class="fa-solid fa-arrow-rotate-left me-1"></i> View All Movies
        </a>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($movies as $movie): ?>
            <?php include __DIR__ . '/includes/movie-card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

