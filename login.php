<?php
// ============================================
// LOGIN USER - PAVELK
// ============================================
require_once __DIR__ . '/config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('index.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Semua field login wajib diisi.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'user'");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Set Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                setFlash('success', 'Selamat datang kembali, ' . $user['nama'] . '!');
                redirect('index.php');
            } else {
                $error = "Email atau password user salah.";
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
    <title>Login Customer - Pavelk</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= $base_url ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="pvl-auth-wrapper">
    <div class="pvl-auth-card">
        <!-- Brand Header logo -->
        <div class="auth-logo">
            <a href="<?= $base_url ?>/index.php">
                <div class="brand-icon">PVL</div>
            </a>
            <h2>CUSTOMER LOGIN</h2>
            <p>Masuk ke akun Pavelk Anda untuk berbelanja</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger border-0 text-white rounded-3 bg-danger bg-opacity-25 small mb-4 py-2 px-3">
                <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php 
        $flash = getFlash();
        if ($flash): 
        ?>
            <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : htmlspecialchars($flash['type']) ?> border-0 text-white rounded-3 bg-<?= $flash['type'] === 'error' ? 'danger' : htmlspecialchars($flash['type']) ?> bg-opacity-25 small mb-4 py-2 px-3">
                <i class="fas fa-info-circle me-2"></i> <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="" method="POST" autocomplete="off">
            <div class="pvl-form-group">
                <label for="email">ALAMAT EMAIL</label>
                <input type="email" id="email" name="email" class="pvl-form-control" placeholder="nama@email.com" required>
            </div>
            
            <div class="pvl-form-group mb-4">
                <label for="password">PASSWORD</label>
                <input type="password" id="password" name="password" class="pvl-form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="pvl-btn pvl-btn-primary w-100 justify-content-center py-2 mb-3">
                Masuk Akun <i class="fas fa-sign-in-alt ms-2"></i>
            </button>
        </form>

        <div class="text-center mt-3">
            <p class="text-secondary small">Belum punya akun? <a href="<?= $base_url ?>/register.php" class="text-blue fw-medium">Daftar Sekarang</a></p>
        </div>

        <div class="pvl-auth-divider">ATAU</div>

        <div class="text-center">
            <a href="<?= $base_url ?>/admin/login.php" class="pvl-btn pvl-btn-outline pvl-btn-sm w-100 justify-content-center">
                <i class="fas fa-user-shield me-2 text-blue"></i> Masuk Sebagai Admin
            </a>
        </div>
    </div>
</div>

<!-- Bootstrap 5 Bundle with Popper JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
