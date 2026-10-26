<?php
/**
 * Submit or Update Movie Rating & Review
 * SineSine - Movie Rating and Review Website
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

// Prevent admins from submitting reviews
if (isAdmin()) {
    setFlashMessage('danger', 'Administrators are not allowed to submit or update reviews.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/sinesine/index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /sinesine/movies.php');
    exit;
}

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    setFlashMessage('danger', 'Security validation failed. Please try again.');
    header('Location: /sinesine/movies.php');
    exit;
}

$movieId = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;
$rating  = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$reviewText = trim($_POST['review_text'] ?? '');

if ($movieId <= 0) {
    setFlashMessage('danger', 'Invalid movie specified.');
    header('Location: /sinesine/movies.php');
    exit;
}

if ($rating < 1 || $rating > 10) {
    setFlashMessage('danger', 'Rating must be a whole number between 1 and 10.');
    header("Location: /sinesine/movie-details.php?id={$movieId}");
    exit;
}

if (empty($reviewText)) {
    setFlashMessage('danger', 'Please write a brief review before submitting.');
    header("Location: /sinesine/movie-details.php?id={$movieId}");
    exit;
}

$pdo = getDBConnection();

// Check if user already submitted a review for this movie
$stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = :uid AND movie_id = :mid LIMIT 1");
$stmt->execute([':uid' => $_SESSION['user_id'], ':mid' => $movieId]);
$existing = $stmt->fetch();

if ($existing) {
    // Update existing review
    $update = $pdo->prepare("
        UPDATE reviews 
        SET rating = :rating, review_text = :review_text, updated_at = CURRENT_TIMESTAMP
        WHERE id = :id AND user_id = :uid
    ");
    $update->execute([
        ':rating'      => $rating,
        ':review_text' => $reviewText,
        ':id'          => $existing['id'],
        ':uid'         => $_SESSION['user_id']
    ]);
    setFlashMessage('success', 'Your review and rating have been updated!');
} else {
    // Insert new review
    $insert = $pdo->prepare("
        INSERT INTO reviews (user_id, movie_id, rating, review_text)
        VALUES (:uid, :mid, :rating, :review_text)
    ");
    $insert->execute([
        ':uid'         => $_SESSION['user_id'],
        ':mid'         => $movieId,
        ':rating'      => $rating,
        ':review_text' => $reviewText
    ]);
    setFlashMessage('success', 'Thank you! Your rating and review have been published.');
}

header("Location: /sinesine/movie-details.php?id={$movieId}#review-section");
exit;

