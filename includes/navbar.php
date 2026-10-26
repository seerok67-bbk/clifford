<?php
/**
 * Navigation Bar Template
 * SineSine - Movie Rating and Review Website
 */
$currentUser = isLoggedIn() ? getCurrentUser() : null;
$currentScript = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-sinesine sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand navbar-brand-logo" href="/sinesine/index.php">
            <i class="fa-solid fa-film text-warning"></i> Sine<span>Sine</span>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler text-light border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars fa-lg"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Left Nav Links -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link <?= ($currentScript === 'index.php') ? 'active' : '' ?>" href="/sinesine/index.php">
                        <i class="fa-solid fa-house me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentScript === 'movies.php') ? 'active' : '' ?>" href="/sinesine/movies.php">
                        <i class="fa-solid fa-clapperboard me-1"></i> Movies
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="genresDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-layer-group me-1"></i> Genres
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="genresDropdown">
                        <li><a class="dropdown-item" href="/sinesine/movies.php?genre=Action"><i class="fa-solid fa-burst text-warning"></i> Action</a></li>
                        <li><a class="dropdown-item" href="/sinesine/movies.php?genre=Adventure"><i class="fa-solid fa-compass text-warning"></i> Adventure</a></li>
                        <li><a class="dropdown-item" href="/sinesine/movies.php?genre=Animation"><i class="fa-solid fa-wand-magic-sparkles text-warning"></i> Animation</a></li>
                        <li><a class="dropdown-item" href="/sinesine/movies.php?genre=Comedy"><i class="fa-solid fa-face-laugh text-warning"></i> Comedy</a></li>
                        <li><a class="dropdown-item" href="/sinesine/movies.php?genre=Drama"><i class="fa-solid fa-masks-theater text-warning"></i> Drama</a></li>
                        <li><a class="dropdown-item" href="/sinesine/movies.php?genre=Horror"><i class="fa-solid fa-ghost text-warning"></i> Horror</a></li>
                        <li><a class="dropdown-item" href="/sinesine/movies.php?genre=Romance"><i class="fa-solid fa-heart text-warning"></i> Romance</a></li>
                        <li><a class="dropdown-item" href="/sinesine/movies.php?genre=Science+Fiction"><i class="fa-solid fa-rocket text-warning"></i> Science Fiction</a></li>
                        <li><a class="dropdown-item" href="/sinesine/movies.php?genre=Thriller"><i class="fa-solid fa-skull text-warning"></i> Thriller</a></li>
                    </ul>
                </li>
            </ul>

            <!-- Search Form -->
            <form class="d-flex my-2 my-lg-0 me-lg-3" action="/sinesine/movies.php" method="GET">
                <div class="input-group">
                    <input class="form-control nav-search-input" type="search" name="search" placeholder="Search movies, directors..." value="<?= isset($_GET['search']) ? sanitize($_GET['search']) : '' ?>" aria-label="Search">
                    <button class="btn nav-search-btn px-3" type="submit" title="Search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>

            <!-- Right Auth Links / User Profile -->
            <ul class="navbar-nav mb-2 mb-lg-0 align-items-lg-center">
                <?php if ($currentUser): ?>
                    <?php if (isAdmin()): ?>
                        <li class="nav-item me-2">
                            <a class="btn btn-sm btn-outline-warning" href="/sinesine/admin/index.php">
                                <i class="fa-solid fa-gauge me-1"></i> Admin Panel
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="/sinesine/assets/images/avatars/<?= sanitize($currentUser['avatar']) ?>" 
                                 onerror="this.src='/sinesine/assets/images/default-avatar.png'"
                                 alt="Avatar" class="nav-avatar-img">
                            <span class="d-none d-sm-inline fw-semibold"><?= sanitize($currentUser['username']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="userMenu">
                            <li>
                                <div class="px-3 py-2 border-bottom border-secondary mb-1">
                                    <div class="fw-bold text-white"><?= sanitize($currentUser['full_name']) ?></div>
                                    <small class="text-secondary"><?= sanitize($currentUser['email']) ?></small>
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/sinesine/profile.php">
                                    <i class="fa-solid fa-user text-warning"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/sinesine/edit-profile.php">
                                    <i class="fa-solid fa-gear text-warning"></i> Account Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="/sinesine/logout.php">
                                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item me-2">
                        <a class="btn btn-outline-gold btn-sm" href="/sinesine/login.php">
                            <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-gold btn-sm" href="/sinesine/register.php">
                            <i class="fa-solid fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

