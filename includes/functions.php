<?php
/**
 * Common Helper Functions & Security Utilities
 * SineSine - Movie Rating and Review Website
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Escapes HTML output to prevent XSS attacks
 */
function sanitize($data) {
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF Token
 */
function getCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

/**
 * Render CSRF Hidden Input
 */
function csrfField() {
    $token = getCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Set a Flash Message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Display Flash Message if available
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        $icon = $msg['type'] === 'success' ? 'fa-check-circle' : ($msg['type'] === 'danger' ? 'fa-triangle-exclamation' : 'fa-circle-info');
        echo '<div class="alert alert-' . $msg['type'] . ' alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fa-solid ' . $icon . ' me-2"></i>
                <div>' . $msg['message'] . '</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

/**
 * Fetch movie list with optional filters and calculated rating
 */
function getMovies($pdo, $filters = [], $sort = 'title_asc', $limit = null) {
    $sql = "SELECT m.*, 
            COALESCE(AVG(r.rating), 0) AS avg_rating,
            COUNT(r.id) AS review_count
            FROM movies m
            LEFT JOIN reviews r ON m.id = r.movie_id";
    
    $where = [];
    $params = [];

    if (!empty($filters['search'])) {
        $where[] = "(m.title LIKE :search OR m.director LIKE :search2)";
        $params[':search'] = '%' . $filters['search'] . '%';
        $params[':search2'] = '%' . $filters['search'] . '%';
    }

    if (!empty($filters['genre'])) {
        $where[] = "m.genre LIKE :genre";
        $params[':genre'] = '%' . $filters['genre'] . '%';
    }

    if (!empty($filters['id'])) {
        $where[] = "m.id = :id";
        $params[':id'] = $filters['id'];
    }

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " GROUP BY m.id";

    // Sorting
    switch ($sort) {
        case 'newest':
            $sql .= " ORDER BY m.release_year DESC, m.created_at DESC";
            break;
        case 'oldest':
            $sql .= " ORDER BY m.release_year ASC, m.created_at ASC";
            break;
        case 'rating_desc':
            $sql .= " ORDER BY avg_rating DESC, review_count DESC, m.title ASC";
            break;
        case 'reviews_desc':
            $sql .= " ORDER BY review_count DESC, avg_rating DESC, m.title ASC";
            break;
        case 'title_desc':
            $sql .= " ORDER BY m.title DESC";
            break;
        case 'title_asc':
        default:
            $sql .= " ORDER BY m.title ASC";
            break;
    }

    if ($limit !== null) {
        $sql .= " LIMIT " . (int)$limit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Fetch a single movie with detailed ratings & review count
 */
function getMovieById($pdo, $movieId) {
    $sql = "SELECT m.*, 
            COALESCE(AVG(r.rating), 0) AS avg_rating,
            COUNT(r.id) AS review_count
            FROM movies m
            LEFT JOIN reviews r ON m.id = r.movie_id
            WHERE m.id = :id
            GROUP BY m.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $movieId]);
    return $stmt->fetch();
}

/**
 * Fetch reviews for a specific movie with user profile info
 */
function getMovieReviews($pdo, $movieId) {
    $sql = "SELECT r.*, u.username, u.full_name, u.avatar 
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.movie_id = :movie_id
            ORDER BY r.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':movie_id' => $movieId]);
    return $stmt->fetchAll();
}

