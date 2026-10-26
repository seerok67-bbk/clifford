<?php
/**
 * User & Admin Login Page
 * SineSine - Movie Rating and Review Website
 */
define('PAGE_TITLE', 'Login - SineSine');
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// If already logged in, redirect
if (isLoggedIn()) {
    header('Location: /sinesine/index.php');
    exit;
}

$errors = [];
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid security session. Please submit the form again.';
    }

    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';

    if (empty($identifier)) {
        $errors[] = 'Username or Email is required.';
    }
    if (empty($password)) {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :id1 OR email = :id2 LIMIT 1");
        $stmt->execute([':id1' => $identifier, ':id2' => $identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Check if account is disabled
            if ($user['status'] === 'disabled') {
                $errors[] = 'Your account has been disabled by an administrator. Please contact support.';
            } else {
                // Regenerate session id for security
                session_regenerate_id(true);

                // Set session data
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email']     = $user['email'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['avatar']    = $user['avatar'];

                setFlashMessage('success', 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!');

                $redirect = $_SESSION['redirect_after_login'] ?? ($user['role'] === 'admin' ? '/sinesine/admin/index.php' : '/sinesine/index.php');
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit;
            }
        } else {
            $errors[] = 'Invalid username/email or password.';
        }
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card bg-dark border-secondary rounded-4 shadow-lg p-4 my-4">
            <div class="text-center mb-4">
                <div class="navbar-brand-logo justify-content-center fs-2 mb-1">
                    <i class="fa-solid fa-film text-warning"></i> Sine<span>Sine</span>
                </div>
                <h4 class="text-white fw-bold">Sign In</h4>
                <p class="text-secondary small">Access your account, ratings, and reviews</p>
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

            <form action="/sinesine/login.php" method="POST">
                <?= csrfField() ?>

                <div class="mb-3">
                    <label for="identifier" class="form-label">Username or Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary text-secondary"><i class="fa-solid fa-user"></i></span>
                        <input type="text" class="form-control" id="identifier" name="identifier" value="<?= sanitize($identifier) ?>" placeholder="Username or Email" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary text-secondary"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold w-100 py-2 fs-6">
                    <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Sign In
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top border-secondary">
                <span class="text-secondary">Don't have an account?</span>
                <a href="/sinesine/register.php" class="ms-1 text-warning fw-semibold">Sign Up</a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

