<?php
// ============================================
// REGISTER USER - PAVELK
// ============================================
require_once __DIR__ . '/config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($nama) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Semua field registrasi wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format alamat email tidak valid.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal harus memiliki 6 karakter.";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        try {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Alamat email ini sudah terdaftar.";
            } else {
                // Hash Password using password_hash()
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert into Database
                $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')");
                $stmt->execute([$nama, $email, $hashed_password]);

                setFlash('success', 'Registrasi berhasil! Silakan login menggunakan akun Anda.');
                redirect('login.php');
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
    <title>Daftar Akun Baru - Pavelk</title>
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
            <h2>CREATE ACCOUNT</h2>
            <p>Daftar akun Pavelk baru untuk memulai berbelanja</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger border-0 text-white rounded-3 bg-danger bg-opacity-25 small mb-4 py-2 px-3">
                <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Register Form -->
        <form action="" method="POST" autocomplete="off">
            <div class="pvl-form-group">
                <label for="nama">NAMA LENGKAP</label>
                <input type="text" id="nama" name="nama" class="pvl-form-control" placeholder="Nama Anda" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>" required>
            </div>

            <div class="pvl-form-group">
                <label for="email">ALAMAT EMAIL</label>
                <input type="email" id="email" name="email" class="pvl-form-control" placeholder="nama@email.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
            </div>
            
            <div class="pvl-form-group">
                <label for="password">PASSWORD (MIN. 6 KARAKTER)</label>
                <input type="password" id="password" name="password" class="pvl-form-control" placeholder="••••••••" required>
            </div>

            <div class="pvl-form-group mb-4">
                <label for="confirm_password">KONFIRMASI PASSWORD</label>
                <input type="password" id="confirm_password" name="confirm_password" class="pvl-form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="pvl-btn pvl-btn-primary w-100 justify-content-center py-2 mb-3">
                Daftar Sekarang <i class="fas fa-user-plus ms-2"></i>
            </button>
        </form>

        <div class="text-center mt-3">
            <p class="text-secondary small">Sudah punya akun? <a href="<?= $base_url ?>/login.php" class="text-blue fw-medium">Masuk Sekarang</a></p>
        </div>
    </div>
</div>

<!-- Bootstrap 5 Bundle with Popper JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
