<?php
// ============================================
// ADMINISTRATOR ORDERS MANAGEMENT - PAVELK
// ============================================
require_once __DIR__ . '/../config/config.php';

// Secure check: Must be admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Akses ditolak. Anda wajib masuk sebagai Administrator.');
    redirect('admin/login.php');
}

// ----------------------------------------
// PROCESS STATUS UPDATE REQUESTS
// ----------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = sanitize($_POST['status']);

    $allowed_statuses = ['pending', 'diproses', 'selesai'];

    if (in_array($status, $allowed_statuses)) {
        try {
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $order_id]);
            
            setFlash('success', 'Status pesanan #ORDER-' . str_pad($order_id, 6, '0', STR_PAD_LEFT) . ' berhasil diperbarui menjadi ' . ucfirst($status) . '!');
        } catch (PDOException $e) {
            setFlash('error', 'Gagal memperbarui status transaksi: ' . $e->getMessage());
        }
    } else {
        setFlash('error', 'Pilihan status pengiriman tidak sah.');
    }
    redirect('admin/orders.php');
}

// ----------------------------------------
// FETCH ALL ORDERS (JOIN CUSTOMERS)
// ----------------------------------------
$orders = [];
try {
    $stmt = $conn->query("SELECT o.*, u.nama as customer_nama, u.email as customer_email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.tanggal DESC");
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    setFlash('error', 'Gagal memuat list transaksi masuk.');
}

$page_title = "Manajemen Transaksi Masuk";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="pvl-section">
    <div class="pvl-section-header">
        <h2>Kelola <span>Transaksi Masuk</span></h2>
        <p>Pantau pesanan velg racing premium, verifikasi data pengiriman, dan perbarui status kirim.</p>
        <div class="pvl-section-line"></div>
    </div>

    <div class="container-fluid px-2">
        <?php if (!empty($orders)): ?>
            <div class="row">
                <div class="col-12">
                    <?php foreach ($orders as $order): 
                        // Fetch ordered items for this transaction
                        try {
                            $items_stmt = $conn->prepare("SELECT oi.*, p.nama FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                            $items_stmt->execute([$order['id']]);
                            $order_items = $items_stmt->fetchAll();
                        } catch (PDOException $e) {
                            $order_items = [];
                        }
                    ?>
                        <div class="pvl-order-card bg-dark-2 border-subtle mb-4">
                            <!-- Card header -->
                            <div class="pvl-order-header border-secondary border-opacity-10 mb-3 pb-3">
                                <div>
                                    <span class="pvl-order-id text-white">#ORDER-<?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?></span>
                                    <div class="text-secondary small mt-1"><i class="far fa-calendar-alt me-1"></i> Tanggal Masuk: <?= date('d M Y, H:i', strtotime($order['tanggal'])) ?> WIB</div>
                                </div>

                                <!-- Dynamic badges -->
                                <div>
                                    <span class="pvl-status <?= htmlspecialchars($order['status']) ?>">
                                        <?= htmlspecialchars(ucfirst($order['status'])) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="row g-4 mb-3">
                                <!-- Customer details & address info -->
                                <div class="col-lg-4">
                                    <h4 class="h6 text-blue fw-bold mb-2"><i class="fas fa-user-circle me-1"></i> IDENTITAS PEMESAN:</h4>
                                    <ul class="list-unstyled text-secondary small mb-0" style="line-height: 1.8;">
                                        <li><strong class="text-white">Akun Register:</strong> <?= htmlspecialchars($order['customer_nama']) ?> (<?= htmlspecialchars($order['customer_email']) ?>)</li>
                                        <li><strong class="text-white">Penerima Paket:</strong> <?= htmlspecialchars($order['nama_penerima']) ?></li>
                                        <li><strong class="text-white">Nomor HP/WA:</strong> <?= htmlspecialchars($order['no_hp']) ?></li>
                                        <li><strong class="text-white">Tujuan Kirim:</strong> <?= htmlspecialchars($order['alamat']) ?></li>
                                    </ul>
                                </div>

                                <!-- Ordered Items breakdown -->
                                <div class="col-lg-5">
                                    <h4 class="h6 text-blue fw-bold mb-2"><i class="fas fa-shopping-bag me-1"></i> RINCIAN BELANJA:</h4>
                                    <div class="d-flex flex-column gap-2">
                                        <?php foreach ($order_items as $item): ?>
                                            <div class="d-flex align-items-center justify-content-between text-secondary small">
                                                <span><i class="fas fa-motorcycle text-white-50 me-2"></i><?= htmlspecialchars($item['nama']) ?></span>
                                                <span class="text-white fw-bold"><?= $item['qty'] ?> unit x <?= formatRupiah($item['harga']) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="border-top border-secondary border-opacity-10 mt-3 pt-2 text-end">
                                        <span class="text-secondary small me-2">TOTAL BAYAR:</span>
                                        <span class="text-blue fw-bold fs-5"><?= formatRupiah($order['total']) ?></span>
                                    </div>
                                </div>

                                <!-- Update Status action panel -->
                                <div class="col-lg-3 border-start border-secondary border-opacity-10">
                                    <h4 class="h6 text-blue fw-bold mb-2"><i class="fas fa-cog fa-spin me-1"></i> KONTROL STATUS:</h4>
                                    
                                    <form action="" method="POST" class="d-flex flex-column gap-3 mt-2">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <input type="hidden" name="update_status" value="1">
                                        
                                        <select name="status" class="pvl-form-control bg-dark-3 border-subtle text-white p-2 text-center" style="font-size: 13px; border-radius: 8px;">
                                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending (Menunggu)</option>
                                            <option value="diproses" <?= $order['status'] == 'diproses' ? 'selected' : '' ?>>Diproses (Packing)</option>
                                            <option value="selesai" <?= $order['status'] == 'selesai' ? 'selected' : '' ?>>Selesai (Diterima)</option>
                                        </select>
                                        
                                        <button type="submit" class="pvl-btn pvl-btn-primary pvl-btn-sm w-100 justify-content-center py-2">
                                            Update Status <i class="fas fa-check-circle ms-1"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="pvl-empty border border-subtle bg-dark-2 rounded-pvl py-5">
                <i class="fas fa-receipt text-secondary mb-3 fs-1" style="opacity: 0.3;"></i>
                <h4>Tidak Ada Transaksi</h4>
                <p class="text-secondary">Belum ada pesanan belanja masuk dari pelanggan terdata di sistem database.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
