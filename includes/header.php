<?php
/**
 * Header Template
 * SineSine - Movie Rating and Review Website
 */
if (!defined('PAGE_TITLE')) {
    define('PAGE_TITLE', 'SineSine - Discover. Watch. Rate.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize(PAGE_TITLE) ?></title>
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- SineSine Custom CSS -->
    <link href="/sinesine/assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include_once __DIR__ . '/navbar.php'; ?>
<main class="py-4">
    <div class="container">
        <?php displayFlashMessage(); ?>

