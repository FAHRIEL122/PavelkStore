<?php
// ============================================
// ORDERS HISTORY PAGE - PAVELK
// ============================================
$page_title = "Riwayat Pesanan Anda";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlash('warning', 'Silakan login terlebih dahulu untuk mengakses riwayat pesanan.');
    echo "<script>window.location.href = '" . $base_url . "/login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$orders = [];

try {
    // Fetch all user orders
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY tanggal DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    setFlash('error', 'Gagal memuat data riwayat pesanan.');
}
?>

<section class="pvl-section">
    <div class="pvl-section-header">
        <h2>Riwayat <span>Pesanan</span></h2>
        <p>Pantau status pengiriman velg racing premium pilihan Anda di bawah ini.</p>
        <div class="pvl-section-line"></div>
    </div>

    <div class="container-fluid px-2">
        <?php if (!empty($orders)): ?>
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <?php foreach ($orders as $order): 
                        // Fetch items inside this specific order
                        try {
                            $items_stmt = $conn->prepare("SELECT oi.*, p.nama, p.gambar FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                            $items_stmt->execute([$order['id']]);
                            $items = $items_stmt->fetchAll();
                        } catch (PDOException $e) {
                            $items = [];
                        }
                    ?>
                        <div class="pvl-order-card">
                            <!-- Header detail info -->
                            <div class="pvl-order-header">
                                <div>
                                    <span class="pvl-order-id text-white">#ORDER-<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></span>
                                    <div class="text-secondary small mt-1"><i class="far fa-calendar-alt me-1"></i> Tanggal: <?= date('d M Y, H:i', strtotime($order['tanggal'])) ?> WIB</div>
                                </div>
                                
                                <!-- Status badge -->
                                <span class="pvl-status <?= htmlspecialchars($order['status']) ?>">
                                    <i class="fas <?= $order['status'] == 'pending' ? 'fa-clock' : ($order['status'] == 'diproses' ? 'fa-spinner fa-spin' : 'fa-check-circle') ?> me-1"></i>
                                    <?= htmlspecialchars(ucfirst($order['status'])) ?>
                                </span>
                            </div>

                            <!-- Purchased items row details -->
                            <div class="row align-items-center mb-3">
                                <div class="col-md-8">
                                    <div class="d-flex flex-column gap-2">
                                        <?php foreach ($items as $item): ?>
                                            <div class="d-flex align-items-center gap-3">
                                                <i class="fas fa-motorcycle text-blue small"></i>
                                                <span class="text-white-50" style="font-size: 13px;">
                                                    <strong><?= htmlspecialchars($item['nama']) ?></strong> (<?= $item['qty'] ?> unit x <?= formatRupiah($item['harga']) ?>)
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <!-- Pricing and Details -->
                                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                    <div class="text-secondary small">Total Pembayaran:</div>
                                    <div class="text-blue fw-bold fs-5 mt-1"><?= formatRupiah($order['total']) ?></div>
                                </div>
                            </div>

                            <!-- Expandable tracking information details -->
                            <div class="border-top border-secondary border-opacity-10 pt-3 mt-3">
                                <div class="row g-2" style="font-size: 12px;">
                                    <div class="col-md-6 text-secondary">
                                        <span class="text-white"><i class="fas fa-user me-1"></i> Penerima:</span> <?= htmlspecialchars($order['nama_penerima']) ?> (<?= htmlspecialchars($order['no_hp']) ?>)
                                    </div>
                                    <div class="col-md-6 text-secondary">
                                        <span class="text-white"><i class="fas fa-map-marker-alt me-1"></i> Alamat:</span> <?= htmlspecialchars($order['alamat']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Empty orders state -->
            <div class="pvl-empty border border-subtle bg-dark-2 rounded-pvl py-5">
                <i class="fas fa-history text-secondary mb-3 fs-1" style="opacity: 0.3;"></i>
                <h4>Belum Ada Riwayat Pesanan</h4>
                <p class="text-secondary">Anda belum memiliki riwayat pembelian velg racing motor premium.</p>
                <a href="<?= $base_url ?>/pages/products.php" class="pvl-btn pvl-btn-primary pvl-btn-sm mt-2">
                    <i class="fas fa-shopping-bag me-2"></i> Mulai Berbelanja
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
