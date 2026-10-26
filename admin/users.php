<?php
/**
 * Admin User Management
 * SineSine - Movie Rating and Review Website
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pdo = getDBConnection();

// Handle Status Toggle (Enable/Disable User)
if (isset($_GET['action']) && isset($_GET['id']) && isset($_GET['csrf_token'])) {
    if (verifyCSRFToken($_GET['csrf_token'])) {
        $targetId = (int)$_GET['id'];
        $action = $_GET['action'];

        // Prevent admin from disabling themselves
        if ($targetId == $_SESSION['user_id']) {
            setFlashMessage('warning', 'You cannot disable your own active administrator account.');
        } else {
            $newStatus = ($action === 'disable') ? 'disabled' : 'active';
            $update = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id");
            $update->execute([':status' => $newStatus, ':id' => $targetId]);
            setFlashMessage('success', "User account status updated to '{$newStatus}'.");
        }
    } else {
        setFlashMessage('danger', 'Security token invalid.');
    }
    header('Location: /sinesine/admin/users.php');
    exit;
}

// Fetch all registered users with review count
$users = $pdo->query("
    SELECT u.*, COUNT(r.id) AS review_count
    FROM users u
    LEFT JOIN reviews r ON u.id = r.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
")->fetchAll();

define('ADMIN_TITLE', 'Manage Users - SineSine Admin');
include_once __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="text-white fw-bold mb-1">
            <i class="fa-solid fa-users text-warning me-2"></i> User Account Management
        </h2>
        <p class="text-secondary mb-0">Total <?= count($users) ?> registered users</p>
    </div>
</div>

<div class="card bg-dark border-secondary rounded-4 shadow overflow-hidden">
    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0">
            <thead class="table-secondary text-dark">
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 70px;">Avatar</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Reviews</th>
                    <th>Joined</th>
                    <th class="text-end" style="width: 140px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="text-secondary">#<?= $u['id'] ?></td>
                        <td>
                            <img src="/sinesine/assets/images/avatars/<?= sanitize($u['avatar']) ?>" 
                                 onerror="this.src='/sinesine/assets/images/default-avatar.png'"
                                 alt="Avatar" class="rounded-circle border border-secondary" 
                                 style="width: 40px; height: 40px; object-fit: cover;">
                        </td>
                        <td class="fw-bold text-white"><?= sanitize($u['full_name']) ?></td>
                        <td class="text-warning">@<?= sanitize($u['username']) ?></td>
                        <td class="text-light"><?= sanitize($u['email']) ?></td>
                        <td>
                            <span class="badge bg-<?= ($u['role'] === 'admin') ? 'danger' : 'secondary' ?> text-uppercase">
                                <?= sanitize($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($u['status'] === 'active'): ?>
                                <span class="badge bg-success text-uppercase"><i class="fa-solid fa-check me-1"></i> Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger text-uppercase"><i class="fa-solid fa-ban me-1"></i> Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-black border border-secondary text-secondary">
                                <?= $u['review_count'] ?>
                            </span>
                        </td>
                        <td class="small text-secondary"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                        <td class="text-end">
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                <?php if ($u['status'] === 'active'): ?>
                                    <a href="/sinesine/admin/users.php?action=disable&id=<?= $u['id'] ?>&csrf_token=<?= getCSRFToken() ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       title="Disable User Account"
                                       onclick="return confirm('Disable this user? They will not be able to log in.');">
                                        <i class="fa-solid fa-user-slash me-1"></i> Disable
                                    </a>
                                <?php else: ?>
                                    <a href="/sinesine/admin/users.php?action=enable&id=<?= $u['id'] ?>&csrf_token=<?= getCSRFToken() ?>" 
                                       class="btn btn-sm btn-outline-success" 
                                       title="Reactivate User Account">
                                        <i class="fa-solid fa-user-check me-1"></i> Enable
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-dark border border-secondary text-secondary">Current You</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once __DIR__ . '/footer.php'; ?>

