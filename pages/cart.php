<?php
// ============================================
// SHOPPING CART PAGE - PAVELK
// ============================================
$page_title = "Keranjang Belanja Anda";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlash('warning', 'Silakan login terlebih dahulu untuk melihat keranjang belanja.');
    echo "<script>window.location.href = '" . $base_url . "/login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_bayar = 0;

try {
    // Fetch user cart items join with products
    $stmt = $conn->prepare("SELECT c.id as cart_id, c.qty, p.* FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ? ORDER BY c.created_at DESC");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
} catch (PDOException $e) {
    setFlash('error', 'Terjadi kesalahan sistem penarikan keranjang.');
}

// Function to generate premium visual representation of a high-tech racing wheel dynamically as SVG
if (!function_exists('renderVelgSvg')) {
    function renderVelgSvg($index = 1) {
        $colors = ['#0DD8E6', '#ff4757', '#ffa502', '#2ed573', '#1e90ff', '#a55eea'];
        $color = $colors[($index - 1) % count($colors)];
        return '
        <svg viewBox="0 0 100 100" class="w-100 h-100" style="background:#111; padding:15px;">
            <circle cx="50" cy="50" r="45" fill="none" stroke="#222" stroke-width="2" />
            <circle cx="50" cy="50" r="40" fill="none" stroke="' . $color . '" stroke-width="0.5" stroke-dasharray="2,2" />
            <circle cx="50" cy="50" r="38" fill="none" stroke="#333" stroke-width="3" />
            <circle cx="50" cy="50" r="35" fill="none" stroke="#555" stroke-width="1" />
            <g stroke="' . $color . '" stroke-width="1.5" stroke-linecap="round">';
            for ($i = 0; $i < 360; $i += 45) {
                $rad = deg2rad($i);
                $x1 = 50 + 10 * cos($rad);
                $y1 = 50 + 10 * sin($rad);
                
                $rad1 = deg2rad($i - 6);
                $rad2 = deg2rad($i + 6);
                $x2_1 = 50 + 35 * cos($rad1);
                $y2_1 = 50 + 35 * sin($rad1);
                $x2_2 = 50 + 35 * cos($rad2);
                $y2_2 = 50 + 35 * sin($rad2);
                
                $html .= '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2_1 . '" y2="' . $y2_1 . '" />';
                $html .= '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2_2 . '" y2="' . $y2_2 . '" />';
            }
        $html .= '</g>
            <circle cx="50" cy="50" r="10" fill="#222" stroke="#444" stroke-width="1" />
            <circle cx="50" cy="50" r="8" fill="#111" stroke="' . $color . '" stroke-width="1.5" />
            <circle cx="50" cy="50" r="3" fill="' . $color . '" />
            <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
        </svg>';
        return $html;
    }
}
?>

<section class="pvl-section">
    <div class="pvl-section-header">
        <h2>Keranjang <span>Belanja</span></h2>
        <p>Ringkasan produk velg racing premium yang ingin Anda beli.</p>
        <div class="pvl-section-line"></div>
    </div>

    <div class="container-fluid px-2">
        <?php if (!empty($cart_items)): ?>
            <div class="row g-5">
                
                <!-- Table of items Column -->
                <div class="col-lg-8">
                    <div class="table-responsive rounded-3 border border-subtle bg-dark-2">
                        <table class="pvl-cart-table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): 
                                    $subtotal = $item['harga'] * $item['qty'];
                                    $total_bayar += $subtotal;
                                ?>
                                    <tr>
                                        <!-- Product item details -->
                                        <td>
                                            <div class="pvl-cart-item">
                                                <div style="width: 70px; height: 70px; border-radius: var(--pvl-radius-sm); overflow: hidden; background:#111; flex-shrink:0;">
                                                    <?php 
                                                    $imagePath = __DIR__ . '/../assets/images/' . $item['gambar'];
                                                    if (!empty($item['gambar']) && file_exists($imagePath)): ?>
                                                        <img src="<?= $base_url ?>/assets/images/<?= htmlspecialchars($item['gambar']) ?>" alt="<?= htmlspecialchars($item['nama']) ?>" class="w-100 h-100 object-fit-cover">
                                                    <?php else: ?>
                                                        <?= renderVelgSvg($item['id']) ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <a href="<?= $base_url ?>/pages/product-detail.php?id=<?= $item['id'] ?>" class="pvl-cart-item-name text-white hover-blue"><?= htmlspecialchars($item['nama']) ?></a>
                                                    <div class="text-secondary small mt-1">Stok: <?= $item['stok'] ?> unit</div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Item Price -->
                                        <td>
                                            <span class="text-white-50 fw-medium"><?= formatRupiah($item['harga']) ?></span>
                                        </td>

                                        <!-- Item Quantity Updater -->
                                        <td>
                                            <form action="<?= $base_url ?>/pages/cart-process.php" method="GET" class="d-flex align-items-center">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                                
                                                <select name="qty" onchange="this.form.submit()" class="pvl-form-control bg-dark-3 border-subtle text-white text-center py-1 px-2" style="width: 70px; font-size:13px; border-radius:6px;">
                                                    <?php for ($i = 1; $i <= min($item['stok'], 10); $i++): ?>
                                                        <option value="<?= $i ?>" <?= $item['qty'] == $i ? 'selected' : '' ?>><?= $i ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </form>
                                        </td>

                                        <!-- Subtotal price -->
                                        <td>
                                            <span class="text-blue fw-bold"><?= formatRupiah($subtotal) ?></span>
                                        </td>

                                        <!-- Actions delete -->
                                        <td>
                                            <a href="<?= $base_url ?>/pages/cart-process.php?action=delete&cart_id=<?= $item['cart_id'] ?>" class="text-danger hover-grow" title="Hapus dari Keranjang" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        <a href="<?= $base_url ?>/pages/products.php" class="pvl-btn pvl-btn-outline pvl-btn-sm">
                            <i class="fas fa-arrow-left me-2"></i> Lanjutkan Belanja
                        </a>
                    </div>
                </div>

                <!-- Shopping Summary Card Column -->
                <div class="col-lg-4">
                    <div class="pvl-cart-summary">
                        <h3>Ringkasan Pembelian</h3>
                        
                        <div class="pvl-cart-summary-row">
                            <span>Subtotal Produk</span>
                            <span class="text-white"><?= formatRupiah($total_bayar) ?></span>
                        </div>
                        
                        <div class="pvl-cart-summary-row">
                            <span>Biaya Pengiriman</span>
                            <span class="text-success text-opacity-75">GRATIS (Promo)</span>
                        </div>

                        <div class="pvl-cart-summary-row">
                            <span>PPN (11%)</span>
                            <span class="text-secondary">Termasuk</span>
                        </div>

                        <div class="pvl-cart-summary-total">
                            <span>Total</span>
                            <span class="amount"><?= formatRupiah($total_bayar) ?></span>
                        </div>

                        <div class="mt-4">
                            <a href="<?= $base_url ?>/pages/checkout.php" class="pvl-btn pvl-btn-primary w-100 justify-content-center py-3">
                                Lanjut Ke Checkout <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        <?php else: ?>
            <!-- Empty state layout -->
            <div class="pvl-empty border border-subtle bg-dark-2 rounded-pvl py-5">
                <i class="fas fa-shopping-bag text-secondary mb-3 fs-1" style="opacity: 0.3;"></i>
                <h4>Keranjang Belanja Kosong</h4>
                <p class="text-secondary">Anda belum menambahkan velg racing ke keranjang belanja Anda.</p>
                <a href="<?= $base_url ?>/pages/products.php" class="pvl-btn pvl-btn-primary pvl-btn-sm mt-2">
                    <i class="fas fa-shopping-cart me-2"></i> Mulai Belanja
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
