<?php
// ============================================
// ADMINISTRATOR USERS LISTING - PAVELK
// ============================================
require_once __DIR__ . '/../config/config.php';

// Secure check: Must be admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Akses ditolak. Anda wajib masuk sebagai Administrator.');
    redirect('admin/login.php');
}

$users = [];
try {
    // Fetch all users ordered by creation date
    $stmt = $conn->query("SELECT id, nama, email, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    setFlash('error', 'Gagal memuat list data user: ' . $e->getMessage());
}

$page_title = "Daftar Pengguna Sistem";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="pvl-section">
    <div class="pvl-section-header">
        <h2>Kelola <span>Pelanggan & User</span></h2>
        <p>Lihat daftar semua pengguna terdaftar, peran akses sistem, dan tanggal pendaftaran.</p>
        <div class="pvl-section-line"></div>
    </div>

    <div class="container-fluid px-2">
        <div class="p-4 rounded-3 border border-subtle bg-dark-2">
            
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                <h3 class="h5 text-white fw-bold mb-0"><i class="fas fa-users text-blue me-2"></i> Database Pengguna Terdaftar</h3>
                <span class="badge bg-dark-3 border border-subtle text-secondary py-2 px-3 fw-normal" style="font-size:12px;">
                    Total Terdaftar: <strong class="text-blue"><?= count($users) ?> Akun</strong>
                </span>
            </div>

            <?php if (!empty($users)): ?>
                <div class="table-responsive">
                    <table class="pvl-table">
                        <thead>
                            <tr>
                                <th>ID User</th>
                                <th>Nama Lengkap</th>
                                <th>Alamat Email</th>
                                <th>Akses Peran (Role)</th>
                                <th>Tanggal Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <!-- User ID -->
                                    <td>
                                        <span class="text-secondary fw-semibold">#PVL-U<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                    </td>
                                    
                                    <!-- Full Name -->
                                    <td>
                                        <strong class="text-white"><?= htmlspecialchars($user['nama']) ?></strong>
                                    </td>

                                    <!-- Email -->
                                    <td>
                                        <span class="text-secondary"><?= htmlspecialchars($user['email']) ?></span>
                                    </td>

                                    <!-- Role Badge -->
                                    <td>
                                        <span class="badge py-2 px-3 fw-bold rounded-pill text-uppercase text-black <?= $user['role'] == 'admin' ? 'bg-info' : 'bg-light bg-opacity-75' ?>" style="font-size:10px; letter-spacing:0.5px;">
                                            <i class="fas <?= $user['role'] == 'admin' ? 'fa-user-shield text-black' : 'fa-user text-black-50' ?> me-1"></i>
                                            <?= htmlspecialchars($user['role']) ?>
                                        </span>
                                    </td>

                                    <!-- Created At Date -->
                                    <td>
                                        <span class="text-secondary small">
                                            <?= date('d M Y, H:i', strtotime($user['created_at'])) ?> WIB
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-secondary small">
                    Belum ada data user terdaftar di sistem.
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
