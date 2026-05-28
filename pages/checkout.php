<?php
// ============================================
// CHECKOUT FORM PAGE - PAVELK
// ============================================
$page_title = "Checkout Pembelian Premium";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlash('warning', 'Silakan login terlebih dahulu untuk melakukan checkout.');
    echo "<script>window.location.href = '" . $base_url . "/login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_bayar = 0;

try {
    // Fetch user cart
    $stmt = $conn->prepare("SELECT c.qty, p.* FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    // Redirect if cart is empty
    if (empty($cart_items)) {
        setFlash('warning', 'Keranjang Anda kosong. Silakan tambahkan produk sebelum melakukan checkout.');
        echo "<script>window.location.href = '" . $base_url . "/pages/products.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    setFlash('error', 'Terjadi kesalahan sistem penarikan data checkout.');
    redirect('pages/cart.php');
}
?>

<section class="pvl-section">
    <div class="pvl-section-header">
        <h2>Checkout <span>Pesanan</span></h2>
        <p>Lengkapi informasi pengiriman untuk memproses pembelian produk premium Anda.</p>
        <div class="pvl-section-line"></div>
    </div>

    <div class="container-fluid px-2">
        <!-- Interactive Form wrapper -->
        <div class="row g-5">
            
            <!-- Shipping Form Column -->
            <div class="col-lg-7">
                <div class="p-4 rounded-3 border border-subtle bg-dark-2">
                    <h3 class="h5 text-white fw-bold mb-4"><i class="fas fa-truck text-blue me-2"></i> Informasi Pengiriman</h3>
                    
                    <form action="<?= $base_url ?>/pages/checkout-process.php" method="POST" id="checkoutForm">
                        <!-- Nama Penerima -->
                        <div class="pvl-form-group">
                            <label for="nama_penerima">NAMA LENGKAP PENERIMA</label>
                            <input type="text" id="nama_penerima" name="nama_penerima" class="pvl-form-control border-subtle" placeholder="Masukkan nama penerima..." value="<?= htmlspecialchars($_SESSION['nama']) ?>" required>
                            <div class="form-text text-secondary" style="font-size:11px;">Gunakan nama lengkap untuk mempermudah kurir saat pengiriman.</div>
                        </div>

                        <!-- No HP Penerima -->
                        <div class="pvl-form-group">
                            <label for="no_hp">NOMOR TELEPON (WHATSAPP ACTIVE)</label>
                            <input type="text" id="no_hp" name="no_hp" class="pvl-form-control border-subtle" placeholder="Contoh: 081234567890" required>
                            <div class="form-text text-secondary" style="font-size:11px;">Kurir akan menghubungi nomor ini saat mengantarkan velg Anda.</div>
                        </div>

                        <!-- Alamat Lengkap -->
                        <div class="pvl-form-group mb-4">
                            <label for="alamat">ALAMAT LENGKAP PENGIRIMAN</label>
                            <textarea id="alamat" name="alamat" class="pvl-form-control border-subtle" placeholder="Nama Jalan, Blok, No. Rumah, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten, Kode Pos..." required></textarea>
                            <div class="form-text text-secondary" style="font-size:11px;">Tulis alamat sejelas mungkin agar pengiriman berjalan lancar dan tepat waktu.</div>
                        </div>

                        <hr class="border-secondary opacity-25 my-4">

                        <!-- Promo Badge alert (Bonus visual detail) -->
                        <div class="d-flex align-items-start gap-3 p-3 rounded-3 bg-dark-3 border border-subtle mb-4">
                            <i class="fas fa-info-circle text-blue fs-4 mt-1"></i>
                            <div>
                                <h6 class="text-white fw-bold mb-1" style="font-size:13px;">SISTEM COD & TRANSFER BANK</h6>
                                <p class="text-secondary small mb-0">Pembayaran dapat diselesaikan langsung setelah admin Pavelk menghubungi Anda melalui WhatsApp untuk verifikasi pengiriman.</p>
                            </div>
                        </div>

                        <button type="submit" class="pvl-btn pvl-btn-primary pvl-btn-lg w-100 justify-content-center py-3">
                            <i class="fas fa-lock me-2"></i> Konfirmasi & Selesaikan Pesanan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary Column -->
            <div class="col-lg-5">
                <div class="pvl-cart-summary">
                    <h3 class="h6 text-white mb-4 fw-bold text-uppercase tracking-wider"><i class="fas fa-receipt text-blue me-2"></i> Ringkasan Belanja</h3>
                    
                    <!-- Scrollable list of summary items -->
                    <div class="mb-4" style="max-height: 280px; overflow-y: auto; padding-right:5px;">
                        <?php foreach ($cart_items as $item): 
                            $subtotal = $item['harga'] * $item['qty'];
                            $total_bayar += $subtotal;
                        ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-secondary border-opacity-10">
                                <div>
                                    <h4 class="text-white fw-bold mb-0" style="font-size: 13px;"><?= htmlspecialchars($item['nama']) ?></h4>
                                    <small class="text-secondary"><?= $item['qty'] ?> unit x <?= formatRupiah($item['harga']) ?></small>
                                </div>
                                <span class="text-white small fw-bold"><?= formatRupiah($subtotal) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="pvl-cart-summary-row mt-2">
                        <span>Jumlah Pembelian</span>
                        <span class="text-white"><?= count($cart_items) ?> Jenis Barang</span>
                    </div>

                    <div class="pvl-cart-summary-row">
                        <span>Ongkos Kirim</span>
                        <span class="text-success fw-bold">FREE SHIPPING</span>
                    </div>

                    <div class="pvl-cart-summary-total">
                        <span>Total Bayar</span>
                        <span class="amount"><?= formatRupiah($total_bayar) ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
