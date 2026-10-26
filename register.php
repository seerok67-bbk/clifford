<?php
/**
 * User Registration Page
 * SineSine - Movie Rating and Review Website
 */
define('PAGE_TITLE', 'Register - SineSine');
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// If already logged in, redirect to home
if (isLoggedIn()) {
    header('Location: /sinesine/index.php');
    exit;
}

$errors = [];
$fullName = '';
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid security session. Please submit the form again.';
    }

    $fullName = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($fullName)) {
        $errors[] = 'Full name is required.';
    }

    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        $errors[] = 'Username must be 3-30 alphanumeric characters or underscores.';
    }

    if (empty($email)) {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $pdo = getDBConnection();
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE username = :username OR email = :email LIMIT 1");
        $stmt->execute([':username' => $username, ':email' => $email]);
        $existing = $stmt->fetch();

        if ($existing) {
            if (strtolower($existing['username']) === strtolower($username)) {
                $errors[] = 'Username is already taken. Please choose another.';
            }
            if (strtolower($existing['email']) === strtolower($email)) {
                $errors[] = 'Email is already registered. Try logging in instead.';
            }
        } else {
            // Hash password securely with password_hash()
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert user (default role is strictly 'user', never admin)
            $insert = $pdo->prepare("
                INSERT INTO users (full_name, username, email, password, avatar, role, status)
                VALUES (:full_name, :username, :email, :password, 'default-avatar.png', 'user', 'active')
            ");
            $insert->execute([
                ':full_name' => $fullName,
                ':username'  => $username,
                ':email'     => $email,
                ':password'  => $hashedPassword
            ]);

            setFlashMessage('success', 'Registration successful! You can now log in.');
            header('Location: /sinesine/login.php');
            exit;
        }
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card bg-dark border-secondary rounded-4 shadow-lg p-4 my-3">
            <div class="text-center mb-4">
                <div class="navbar-brand-logo justify-content-center fs-2 mb-1">
                    <i class="fa-solid fa-film text-warning"></i> Sine<span>Sine</span>
                </div>
                <h4 class="text-white fw-bold">Create Your Account</h4>
                <p class="text-secondary small">Join our community to rate movies and write reviews</p>
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

            <form action="/sinesine/register.php" method="POST">
                <?= csrfField() ?>

                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary text-secondary"><i class="fa-solid fa-id-card"></i></span>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?= sanitize($fullName) ?>" placeholder="e.g. John Doe" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary text-secondary"><i class="fa-solid fa-at"></i></span>
                        <input type="text" class="form-control" id="username" name="username" value="<?= sanitize($username) ?>" placeholder="e.g. jdoe99" required>
                    </div>
                    <small class="text-secondary">Letters, numbers, and underscores only.</small>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary text-secondary"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" value="<?= sanitize($email) ?>" placeholder="e.g. john@example.com" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary text-secondary"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="At least 6 characters" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-black border-secondary text-secondary"><i class="fa-solid fa-lock-check"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-type password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold w-100 py-2 fs-6">
                    <i class="fa-solid fa-user-plus me-1"></i> Sign Up
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top border-secondary">
                <span class="text-secondary">Already have an account?</span>
                <a href="/sinesine/login.php" class="ms-1 text-warning fw-semibold">Log In here</a>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

