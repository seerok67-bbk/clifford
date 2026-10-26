<?php
/**
 * Delete User Review (User self-deletion or Admin action)
 * SineSine - Movie Rating and Review Website
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$reviewId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$csrfToken = $_GET['csrf_token'] ?? '';

if (!verifyCSRFToken($csrfToken)) {
    setFlashMessage('danger', 'Security validation failed.');
    header('Location: /sinesine/index.php');
    exit;
}

$pdo = getDBConnection();

// Fetch review to determine redirect and authorization
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $reviewId]);
$review = $stmt->fetch();

if (!$review) {
    setFlashMessage('danger', 'Review not found.');
    header('Location: /sinesine/index.php');
    exit;
}

// Ensure the user owns the review or is an admin
if ($review['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
    setFlashMessage('danger', 'You do not have permission to delete this review.');
    header('Location: /sinesine/movie-details.php?id=' . $review['movie_id']);
    exit;
}

// Delete review
$delete = $pdo->prepare("DELETE FROM reviews WHERE id = :id");
$delete->execute([':id' => $reviewId]);

setFlashMessage('info', 'Your review has been successfully removed.');

// Redirect back to referring page or movie details
$referrer = $_SERVER['HTTP_REFERER'] ?? "/sinesine/movie-details.php?id={$review['movie_id']}";
header("Location: " . $referrer);
exit;

