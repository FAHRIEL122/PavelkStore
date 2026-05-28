<?php
// ============================================
// ADMINISTRATOR DASHBOARD - PAVELK
// ============================================
require_once __DIR__ . '/../config/config.php';

// Secure check: Must be admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Akses ditolak. Anda wajib masuk sebagai Administrator.');
    redirect('admin/login.php');
}

$total_products = 0;
$total_users = 0;
$total_orders = 0;
$recent_orders = [];

try {
    // 1. Fetch total products count
    $total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
    
    // 2. Fetch total users (customers) count
    $total_users = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
    
    // 3. Fetch total orders count
    $total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();

    // 4. Fetch 5 most recent orders for showcase
    $stmt = $conn->query("SELECT o.*, u.nama as customer_nama FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.tanggal DESC LIMIT 5");
    $recent_orders = $stmt->fetchAll();
} catch (PDOException $e) {
    setFlash('error', 'Gagal memuat statistik dashboard: ' . $e->getMessage());
}

$page_title = "Admin Dashboard";
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Admin Dashboard Wrapper -->
<section class="pvl-section admin-sidebar">
    <div class="pvl-section-header">
        <h2>Admin <span>Panel Dashboard</span></h2>
        <p>Ringkasan manajemen inventaris produk, kelola pesanan masuk, dan list pelanggan.</p>
        <div class="pvl-section-line"></div>
    </div>

    <div class="container-fluid px-2">
        
        <!-- 3 Core Statistics Cards -->
        <div class="row g-4 mb-5">
            <!-- Total Produk Card -->
            <div class="col-md-4">
                <a href="<?= $base_url ?>/admin/products.php" class="d-block text-decoration-none">
                    <div class="pvl-stat-card">
                        <div class="pvl-stat-icon">
                            <i class="fas fa-motorcycle"></i>
                        </div>
                        <div class="pvl-stat-value"><?= $total_products ?></div>
                        <div class="pvl-stat-label">Total Produk Racing Velg</div>
                    </div>
                </a>
            </div>

            <!-- Total Users Card -->
            <div class="col-md-4">
                <a href="<?= $base_url ?>/admin/users.php" class="d-block text-decoration-none">
                    <div class="pvl-stat-card">
                        <div class="pvl-stat-icon">
                            <i class="fas fa-users text-warning"></i>
                        </div>
                        <div class="pvl-stat-value text-warning"><?= $total_users ?></div>
                        <div class="pvl-stat-label">Total Pelanggan Terdaftar</div>
                    </div>
                </a>
            </div>

            <!-- Total Orders Card -->
            <div class="col-md-4">
                <a href="<?= $base_url ?>/admin/orders.php" class="d-block text-decoration-none">
                    <div class="pvl-stat-card">
                        <div class="pvl-stat-icon">
                            <i class="fas fa-receipt text-info"></i>
                        </div>
                        <div class="pvl-stat-value text-info"><?= $total_orders ?></div>
                        <div class="pvl-stat-label">Total Transaksi Pesanan</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Quick Administration Links banner -->
        <div class="row g-4 mb-5 text-center">
            <div class="col-12">
                <div class="p-4 rounded-3 border border-subtle bg-dark-2 d-flex flex-wrap align-items-center justify-content-around gap-3">
                    <span class="text-white fw-bold"><i class="fas fa-tools text-blue me-2"></i> KONTROL UTAMA:</span>
                    <a href="<?= $base_url ?>/admin/products.php" class="pvl-btn pvl-btn-outline pvl-btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Produk Baru
                    </a>
                    <a href="<?= $base_url ?>/admin/orders.php" class="pvl-btn pvl-btn-outline pvl-btn-sm">
                        <i class="fas fa-shipping-fast me-1"></i> Kelola Semua Pesanan
                    </a>
                    <a href="<?= $base_url ?>/admin/users.php" class="pvl-btn pvl-btn-outline pvl-btn-sm">
                        <i class="fas fa-user-friends me-1"></i> List data User
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Transacting Orders Table -->
        <div class="row">
            <div class="col-12">
                <div class="p-4 rounded-3 border border-subtle bg-dark-2">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h5 text-white fw-bold mb-0"><i class="fas fa-clock text-blue me-2"></i> 5 Transaksi Terbaru</h3>
                        <a href="<?= $base_url ?>/admin/orders.php" class="text-blue small fw-semibold">Kelola Semua <i class="fas fa-chevron-right ms-1"></i></a>
                    </div>

                    <?php if (!empty($recent_orders)): ?>
                        <div class="table-responsive">
                            <table class="pvl-table">
                                <thead>
                                    <tr>
                                        <th>ID Order</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Total Transaksi</th>
                                        <th>Status Pesanan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-white">#ORDER-<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></strong>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($order['customer_nama']) ?>
                                            </td>
                                            <td>
                                                <?= date('d M Y, H:i', strtotime($order['tanggal'])) ?> WIB
                                            </td>
                                            <td>
                                                <span class="text-blue fw-bold"><?= formatRupiah($order['total']) ?></span>
                                            </td>
                                            <td>
                                                <span class="pvl-status <?= htmlspecialchars($order['status']) ?>" style="padding: 2px 10px; font-size:11px;">
                                                    <?= htmlspecialchars(ucfirst($order['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= $base_url ?>/admin/orders.php" class="pvl-btn pvl-btn-outline pvl-btn-sm py-1 px-3" style="font-size:12px;">
                                                    Detail & Update
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-secondary small">
                            Belum ada pesanan masuk yang terdata di sistem.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
