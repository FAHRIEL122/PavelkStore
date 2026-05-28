<?php
// ============================================
// LOGIN ADMIN (HALAMAN TERPISAH) - PAVELK
// ============================================
require_once __DIR__ . '/../config/config.php';

// Redirect if already logged in as admin
if (isLoggedIn() && isAdmin()) {
    redirect('admin/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Semua field login wajib diisi.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Set Session
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['nama'] = $admin['nama'];
                $_SESSION['email'] = $admin['email'];
                $_SESSION['role'] = $admin['role'];

                setFlash('success', 'Selamat datang Administrator, ' . $admin['nama'] . '!');
                redirect('admin/dashboard.php');
            } else {
                $error = "Email atau password administrator salah.";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - Pavelk</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= $base_url ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="pvl-auth-wrapper bg-black">
    <div class="pvl-auth-card border-subtle" style="box-shadow: 0 20px 60px rgba(13, 216, 230, 0.15);">
        <!-- Brand Header logo -->
        <div class="auth-logo">
            <a href="<?= $base_url ?>/index.php">
                <div class="brand-icon" style="box-shadow: 0 0 30px rgba(13, 216, 230, 0.4);">PVL</div>
            </a>
            <h2 class="text-blue">ADMIN GATEWAY</h2>
            <p class="text-secondary small">Panel kontrol administratif brand Pavelk</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger border-0 text-white rounded-3 bg-danger bg-opacity-25 small mb-4 py-2 px-3">
                <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="" method="POST" autocomplete="off">
            <div class="pvl-form-group">
                <label for="email">EMAIL ADMINISTRATOR</label>
                <input type="email" id="email" name="email" class="pvl-form-control border-subtle" placeholder="admin@pavelk.com" required>
            </div>
            
            <div class="pvl-form-group mb-4">
                <label for="password">SECURE KEY PASSWORD</label>
                <input type="password" id="password" name="password" class="pvl-form-control border-subtle" placeholder="••••••••" required>
            </div>

            <button type="submit" class="pvl-btn pvl-btn-primary w-100 justify-content-center py-2 mb-3">
                Masuk Sistem <i class="fas fa-user-shield ms-2"></i>
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= $base_url ?>/login.php" class="text-secondary small fw-medium">
                <i class="fas fa-chevron-left me-1"></i> Kembali ke Login User
            </a>
        </div>
    </div>
</div>

<!-- Bootstrap 5 Bundle with Popper JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
