<?php
/**
 * Initial Administrator Account Setup Script
 * SineSine - Movie Rating and Review Website
 *
 * For school presentation and initial deployment setup.
 */
require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();
$message = '';
$messageType = '';

// Check if an admin already exists
$adminCheck = $pdo->query("SELECT id, username, email FROM users WHERE role = 'admin' LIMIT 1")->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_admin'])) {
    $fullName = trim($_POST['full_name'] ?? 'SineSine Administrator');
    $username = trim($_POST['username'] ?? 'admin');
    $email    = trim($_POST['email'] ?? 'admin@sinesine.com');
    $password = $_POST['password'] ?? 'admin123';

    if (empty($username) || empty($email) || empty($password)) {
        $message = 'Please provide full name, username, email, and password.';
        $messageType = 'danger';
    } else {
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        // Check if username/email already taken
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
        $stmt->execute([':u' => $username, ':e' => $email]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Update to admin
            $upd = $pdo->prepare("UPDATE users SET full_name = :fn, password = :pw, role = 'admin', status = 'active' WHERE id = :id");
            $upd->execute([':fn' => $fullName, ':pw' => $hashed, ':id' => $existing['id']]);
            $message = "Admin account for '{$username}' has been updated and granted administrator privileges!";
            $messageType = 'success';
        } else {
            // Insert admin
            $ins = $pdo->prepare("
                INSERT INTO users (full_name, username, email, password, avatar, role, status)
                VALUES (:fn, :un, :em, :pw, 'default-avatar.png', 'admin', 'active')
            ");
            $ins->execute([
                ':fn' => $fullName,
                ':un' => $username,
                ':em' => $email,
                ':pw' => $hashed
            ]);
            $message = "Administrator account '{$username}' created successfully!";
            $messageType = 'success';
        }

        // Re-check
        $adminCheck = $pdo->query("SELECT id, username, email FROM users WHERE role = 'admin' LIMIT 1")->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SineSine - Admin Account Setup</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="/sinesine/assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card bg-dark border-secondary rounded-4 shadow-lg p-4">
                <div class="text-center mb-4">
                    <div class="navbar-brand-logo justify-content-center fs-2 mb-1">
                        <i class="fa-solid fa-film text-warning"></i> Sine<span>Sine</span>
                    </div>
                    <h4 class="text-white fw-bold">Admin Initialization Tool</h4>
                    <p class="text-secondary small">Set up the primary Administrator account</p>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                        <i class="fa-solid <?= ($messageType === 'success') ? 'fa-check-circle' : 'fa-triangle-exclamation' ?> me-2"></i>
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($adminCheck): ?>
                    <div class="alert alert-info border-secondary bg-black p-3 mb-4 rounded-3 text-secondary">
                        <div class="fw-bold text-white mb-1"><i class="fa-solid fa-circle-check text-success me-1"></i> Existing Administrator Found:</div>
                        <div>Username: <strong class="text-warning">@<?= htmlspecialchars($adminCheck['username']) ?></strong></div>
                        <div>Email: <strong class="text-light"><?= htmlspecialchars($adminCheck['email']) ?></strong></div>
                        <div class="mt-3">
                            <a href="/sinesine/login.php" class="btn btn-sm btn-gold">
                                <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Go to Login Page
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="setup-admin.php">
                    <input type="hidden" name="setup_admin" value="1">

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="System Administrator" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="admin" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="admin@sinesine.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Admin Password</label>
                        <input type="text" name="password" class="form-control" value="admin123" required>
                        <small class="text-secondary">Default recommendation: <code>admin123</code></small>
                    </div>

                    <button type="submit" class="btn btn-gold w-100 py-2">
                        <i class="fa-solid fa-shield-halved me-1"></i> <?= $adminCheck ? 'Reset / Update Admin' : 'Create Admin Account' ?>
                    </button>
                </form>

                <div class="text-center mt-4 pt-3 border-top border-secondary">
                    <a href="/sinesine/index.php" class="text-secondary small hover-gold">
                        <i class="fa-solid fa-house me-1"></i> Back to Website Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

