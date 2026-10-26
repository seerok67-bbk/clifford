<?php
/**
 * Admin Add Movie
 * SineSine - Movie Rating and Review Website
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pdo = getDBConnection();

$errors = [];
$title = '';
$description = '';
$genre = '';
$releaseYear = date('Y');
$director = '';
$cast = '';
$duration = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid security session. Please submit again.';
    }

    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $genre       = trim($_POST['genre'] ?? '');
    $releaseYear = (int)($_POST['release_year'] ?? 0);
    $director    = trim($_POST['director'] ?? '');
    $cast        = trim($_POST['cast'] ?? '');
    $duration    = trim($_POST['duration'] ?? '');

    if (empty($title)) $errors[] = 'Movie title is required.';
    if (empty($description)) $errors[] = 'Description/synopsis is required.';
    if (empty($genre)) $errors[] = 'Genre is required.';
    if ($releaseYear < 1888 || $releaseYear > 2100) $errors[] = 'Enter a valid 4-digit release year.';
    if (empty($director)) $errors[] = 'Director is required.';
    if (empty($cast)) $errors[] = 'Cast information is required.';
    if (empty($duration)) $errors[] = 'Duration is required (e.g. 120 min).';

    // Poster Image Upload
    $posterFilename = 'inception.jpg'; // fallback
    if (!isset($_FILES['poster']) || $_FILES['poster']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Movie poster image is required.';
    } else {
        $file = $_FILES['poster'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Error uploading poster image.';
        } elseif ($file['size'] > 4 * 1024 * 1024) {
            $errors[] = 'Poster file must not exceed 4MB.';
        } else {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileInfo = @getimagesize($file['tmp_name']);
            if (!$fileInfo || !in_array($fileInfo['mime'], $allowedMimes)) {
                $errors[] = 'Only JPG, PNG, and WebP poster images are supported.';
            } else {
                $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                $ext = $extMap[$fileInfo['mime']];
                $cleanTitle = preg_replace('/[^a-z0-9]+/', '-', strtolower($title));
                $posterFilename = trim($cleanTitle, '-') . '-' . time() . '.' . $ext;
                $target = __DIR__ . '/../assets/images/movies/' . $posterFilename;
                
                if (!move_uploaded_file($file['tmp_name'], $target)) {
                    $errors[] = 'Failed to save poster image to disk.';
                }
            }
        }
    }

    if (empty($errors)) {
        $insert = $pdo->prepare("
            INSERT INTO movies (title, description, genre, release_year, director, cast, duration, poster)
            VALUES (:title, :description, :genre, :year, :director, :cast, :duration, :poster)
        ");
        $insert->execute([
            ':title'       => $title,
            ':description' => $description,
            ':genre'       => $genre,
            ':year'        => $releaseYear,
            ':director'    => $director,
            ':cast'        => $cast,
            ':duration'    => $duration,
            ':poster'      => $posterFilename
        ]);

        setFlashMessage('success', "Movie '" . htmlspecialchars($title) . "' has been added successfully!");
        header('Location: /sinesine/admin/movies.php');
        exit;
    }
}

define('ADMIN_TITLE', 'Add Movie - SineSine Admin');
include_once __DIR__ . '/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card bg-dark border-secondary rounded-4 shadow p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
                <h4 class="text-white mb-0">
                    <i class="fa-solid fa-square-plus text-warning me-2"></i> Add New Movie
                </h4>
                <a href="/sinesine/admin/movies.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Movie List
                </a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $err): ?>
                            <li><?= sanitize($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="/sinesine/admin/movie-add.php" method="POST" enctype="multipart/form-data">
                <?= csrfField() ?>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label">Movie Title</label>
                        <input type="text" name="title" class="form-control" value="<?= sanitize($title) ?>" placeholder="e.g. Gladiator II" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Release Year</label>
                        <input type="number" name="release_year" class="form-control" value="<?= sanitize($releaseYear) ?>" min="1888" max="2100" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Genre</label>
                        <input type="text" name="genre" class="form-control" value="<?= sanitize($genre) ?>" placeholder="e.g. Action, Sci-Fi" required>
                        <small class="text-secondary">Separate multiple genres with commas.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" class="form-control" value="<?= sanitize($duration) ?>" placeholder="e.g. 148 min" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Director</label>
                        <input type="text" name="director" class="form-control" value="<?= sanitize($director) ?>" placeholder="e.g. Ridley Scott" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Main Cast</label>
                        <input type="text" name="cast" class="form-control" value="<?= sanitize($cast) ?>" placeholder="e.g. Paul Mescal, Pedro Pascal, Denzel Washington" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description / Synopsis</label>
                    <textarea name="description" rows="4" class="form-control" placeholder="Full storyline of the movie..." required><?= sanitize($description) ?></textarea>
                </div>

                <!-- Poster Upload & Preview -->
                <div class="card bg-black border-secondary p-3 mb-4 rounded-3">
                    <label class="form-label text-warning fw-semibold mb-2">Movie Poster Image</label>
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img id="poster_preview" src="" alt="Poster Preview" class="rounded border border-secondary" style="width: 80px; height: 120px; object-fit: cover; display: none;">
                        </div>
                        <div class="col">
                            <input type="file" name="poster" id="poster_input" class="form-control" accept="image/jpeg, image/png, image/webp" required>
                            <small class="text-secondary d-block mt-1">Portrait aspect ratio recommended (e.g. 600x900px). JPG, PNG, or WebP.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/sinesine/admin/movies.php" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-gold px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Movie
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/footer.php'; ?>

