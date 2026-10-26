<?php
/**
 * Admin Delete Movie Action
 * SineSine - Movie Rating and Review Website
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$movieId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$csrfToken = $_GET['csrf_token'] ?? '';

if (!verifyCSRFToken($csrfToken)) {
    setFlashMessage('danger', 'Security token mismatch. Delete aborted.');
    header('Location: /sinesine/admin/movies.php');
    exit;
}

$pdo = getDBConnection();

$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $movieId]);
$movie = $stmt->fetch();

if ($movie) {
    // Delete database record (foreign keys cascade to reviews)
    $delete = $pdo->prepare("DELETE FROM movies WHERE id = :id");
    $delete->execute([':id' => $movieId]);

    setFlashMessage('info', "Movie '" . htmlspecialchars($movie['title']) . "' was removed from catalog.");
} else {
    setFlashMessage('danger', "Movie not found.");
}

header('Location: /sinesine/admin/movies.php');
exit;

