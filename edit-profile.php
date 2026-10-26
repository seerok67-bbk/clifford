<?php
/**
 * Edit User Profile & Avatar Upload
 * SineSine - Movie Rating and Review Website
 */
define('PAGE_TITLE', 'Edit Profile - SineSine');
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$pdo = getDBConnection();
$userId = $_SESSION['user_id'];

// Fetch current user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: /sinesine/logout.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid security session. Please submit again.';
    }

    $fullName = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Basic Validations
    if (empty($fullName)) {
        $errors[] = 'Full name is required.';
    }
    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        $errors[] = 'Username must be 3-30 alphanumeric characters or underscores.';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }

    // Check for unique username/email collision with other users
    $chkStmt = $pdo->prepare("SELECT id, username, email FROM users WHERE (username = :u OR email = :e) AND id != :id LIMIT 1");
    $chkStmt->execute([':u' => $username, ':e' => $email, ':id' => $userId]);
    $collision = $chkStmt->fetch();
    if ($collision) {
        if (strtolower($collision['username']) === strtolower($username)) {
            $errors[] = 'Username is already taken by another account.';
        }
        if (strtolower($collision['email']) === strtolower($email)) {
            $errors[] = 'Email is already associated with another account.';
        }
    }

    // Password change validation if filled
    $passwordToUpdate = null;
    if (!empty($newPassword)) {
        if (strlen($newPassword) < 6) {
            $errors[] = 'New password must be at least 6 characters.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'New passwords do not match.';
        } else {
            $passwordToUpdate = password_hash($newPassword, PASSWORD_BCRYPT);
        }
    }

    // Avatar upload handling
    $avatarFilename = $user['avatar'];
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['avatar'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'An error occurred during avatar upload.';
        } elseif ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
            $errors[] = 'Avatar image must not exceed 2MB in file size.';
        } else {
            // MIME Type verification using finfo or getimagesize
            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileInfo = @getimagesize($file['tmp_name']);
            if (!$fileInfo || !in_array($fileInfo['mime'], $allowedMimes)) {
                $errors[] = 'Invalid file type. Only JPG, PNG, and WebP images are permitted.';
            } else {
                $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                $ext = $extMap[$fileInfo['mime']];
                $newFilename = 'avatar_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                $targetPath = __DIR__ . '/assets/images/avatars/' . $newFilename;

                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    // Remove old avatar if not default
                    if ($user['avatar'] !== 'default-avatar.png' && file_exists(__DIR__ . '/assets/images/avatars/' . $user['avatar'])) {
                        @unlink(__DIR__ . '/assets/images/avatars/' . $user['avatar']);
                    }
                    $avatarFilename = $newFilename;
                } else {
                    $errors[] = 'Failed to save uploaded image. Check folder permissions.';
                }
            }
        }
    }

    // Save if no errors
    if (empty($errors)) {
        if ($passwordToUpdate) {
            $update = $pdo->prepare("
                UPDATE users 
                SET full_name = :fn, username = :un, email = :em, password = :pw, avatar = :av
                WHERE id = :id
            ");
            $update->execute([
                ':fn' => $fullName,
                ':un' => $username,
                ':em' => $email,
                ':pw' => $passwordToUpdate,
                ':av' => $avatarFilename,
                ':id' => $userId
            ]);
        } else {
            $update = $pdo->prepare("
                UPDATE users 
                SET full_name = :fn, username = :un, email = :em, avatar = :av
                WHERE id = :id
            ");
            $update->execute([
                ':fn' => $fullName,
                ':un' => $username,
                ':em' => $email,
                ':av' => $avatarFilename,
                ':id' => $userId
            ]);
        }

        // Update session credentials
        $_SESSION['full_name'] = $fullName;
        $_SESSION['username']  = $username;
        $_SESSION['email']     = $email;
        $_SESSION['avatar']    = $avatarFilename;

        setFlashMessage('success', 'Your profile details have been updated successfully.');
        header('Location: /sinesine/profile.php');
        exit;
    }
}

include_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card bg-dark border-secondary rounded-4 shadow p-4 my-3">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
                <h4 class="text-white mb-0">
                    <i class="fa-solid fa-user-gear text-warning me-2"></i> Edit Account Settings
                </h4>
                <a href="/sinesine/profile.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Profile
                </a>
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

            <form action="/sinesine/edit-profile.php" method="POST" enctype="multipart/form-data">
                <?= csrfField() ?>

                <!-- Avatar Preview & Upload -->
                <div class="card bg-black border-secondary p-3 mb-4 rounded-3 text-center">
                    <label class="form-label text-warning fw-semibold mb-2 d-block">Profile Avatar</label>
                    <div class="mb-3">
                        <img id="avatar_preview" 
                             src="/sinesine/assets/images/avatars/<?= sanitize($user['avatar']) ?>" 
                             onerror="this.src='/sinesine/assets/images/default-avatar.png'"
                             alt="Avatar Preview" 
                             class="rounded-circle border border-2 border-warning shadow" 
                             style="width: 110px; height: 110px; object-fit: cover;">
                    </div>
                    <div class="max-w-md mx-auto">
                        <input type="file" class="form-control form-control-sm" id="avatar_input" name="avatar" accept="image/png, image/jpeg, image/webp">
                        <small class="text-secondary d-block mt-1">Accepted: JPG, PNG, WebP (Max 2MB). Auto-previews on select.</small>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?= sanitize($user['full_name']) ?>" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= sanitize($user['username']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= sanitize($user['email']) ?>" required>
                    </div>
                </div>

                <hr class="border-secondary my-4">

                <!-- Password Change Section (Optional) -->
                <h5 class="text-white mb-2"><i class="fa-solid fa-key text-warning me-2"></i> Change Password <small class="text-secondary fs-6 fw-normal">(Leave blank to keep current)</small></h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="At least 6 characters">
                    </div>
                    <div class="col-md-6">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/sinesine/profile.php" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-gold px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/includes/footer.php'; ?>

