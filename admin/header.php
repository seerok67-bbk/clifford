<?php
/**
 * Admin Panel Common Header & Navigation
 * SineSine - Movie Rating and Review Website
 */
if (!defined('ADMIN_TITLE')) {
    define('ADMIN_TITLE', 'SineSine Admin Panel');
}
$currentUser = getCurrentUser();
$currentAdminScript = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize(ADMIN_TITLE) ?></title>
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <link href="/sinesine/assets/css/admin.css" rel="stylesheet">
    <link href="/sinesine/assets/css/style.css" rel="stylesheet">
</head>
<body class="admin-body">

<div class="admin-wrapper">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-brand">
            <i class="fa-solid fa-film text-warning"></i> Sine<span>Sine</span>
            <span class="badge bg-danger ms-auto fs-6 fw-normal">ADMIN</span>
        </div>

        <ul class="admin-nav">
            <li class="admin-nav-item">
                <a href="/sinesine/admin/index.php" class="admin-nav-link <?= ($currentAdminScript === 'index.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
            </li>
            <li class="admin-nav-item">
                <a href="/sinesine/admin/movies.php" class="admin-nav-link <?= (in_array($currentAdminScript, ['movies.php', 'movie-add.php', 'movie-edit.php'])) ? 'active' : '' ?>">
                    <i class="fa-solid fa-clapperboard"></i> Manage Movies
                </a>
            </li>
            <li class="admin-nav-item">
                <a href="/sinesine/admin/users.php" class="admin-nav-link <?= ($currentAdminScript === 'users.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-users"></i> Manage Users
                </a>
            </li>
            <li class="admin-nav-item">
                <a href="/sinesine/admin/reviews.php" class="admin-nav-link <?= ($currentAdminScript === 'reviews.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-comments"></i> Manage Reviews
                </a>
            </li>
            <li class="admin-nav-item mt-4 pt-3 border-top border-secondary">
                <a href="/sinesine/index.php" class="admin-nav-link text-info">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Visit Front Website
                </a>
            </li>
            <li class="admin-nav-item">
                <a href="/sinesine/logout.php" class="admin-nav-link text-danger">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Admin Topbar -->
        <header class="admin-topbar">
            <button class="btn btn-dark d-lg-none text-light" id="sidebarToggle" type="button">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="d-none d-sm-block text-secondary small">
                SineSine Administration &amp; Moderation Control Center
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-md-block">
                    <div class="fw-bold text-white"><?= sanitize($currentUser['full_name']) ?></div>
                    <small class="text-warning">Administrator</small>
                </div>
                <img src="/sinesine/assets/images/avatars/<?= sanitize($currentUser['avatar']) ?>" 
                     onerror="this.src='/sinesine/assets/images/default-avatar.png'"
                     alt="Admin Avatar" class="nav-avatar-img">
            </div>
        </header>

        <!-- Admin Content Body -->
        <div class="admin-content">
            <?php displayFlashMessage(); ?>

