<?php
// ============================================
// PRODUCT DETAIL PAGE - PAVELK
// ============================================
require_once __DIR__ . '/../config/config.php';

// Fetch product by ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        setFlash('error', 'Produk tidak ditemukan atau telah dihapus.');
        redirect('pages/products.php');
    }
} catch (PDOException $e) {
    setFlash('error', 'Terjadi kesalahan sistem database.');
    redirect('pages/products.php');
}

$page_title = $product['nama'];
require_once __DIR__ . '/../includes/header.php';

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

<div class="container-fluid pvl-detail-section">
    <!-- Breadcrumb back link -->
    <div class="mb-4">
        <a href="<?= $base_url ?>/pages/products.php" class="text-secondary small fw-medium">
            <i class="fas fa-chevron-left me-1"></i> Kembali ke Katalog Velg
        </a>
    </div>

    <!-- Details Card Grid -->
    <div class="row g-5">
        <!-- Visual Gallery Column -->
        <div class="col-lg-5">
            <div class="pvl-detail-gallery">
                <?php 
                $imagePath = __DIR__ . '/../assets/images/' . $product['gambar'];
                if (!empty($product['gambar']) && file_exists($imagePath)): ?>
                    <img src="<?= $base_url ?>/assets/images/<?= htmlspecialchars($product['gambar']) ?>" alt="<?= htmlspecialchars($product['nama']) ?>" class="img-fluid rounded shadow-lg">
                <?php else: ?>
                    <div style="aspect-ratio: 1; border-radius: var(--pvl-radius); overflow:hidden;">
                        <?= renderVelgSvg($product['id']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Meta Information Details Column -->
        <div class="col-lg-7">
            <div class="pvl-detail-info">
                <!-- Stock status dynamic badge -->
                <div class="d-inline-flex align-items-center gap-2 mb-3 px-3 py-1 bg-dark-3 rounded-pill border border-subtle">
                    <span class="dot <?= $product['stok'] == 0 ? 'out' : ($product['stok'] < 5 ? 'low' : '') ?>"></span>
                    <span class="small text-secondary fw-semibold">
                        <?= $product['stok'] == 0 ? 'Out of Stock' : ($product['stok'] < 5 ? 'Stok Terbatas: ' . $product['stok'] . ' Unit' : 'Stok Tersedia: ' . $product['stok'] . ' Unit') ?>
                    </span>
                </div>

                <h1><?= htmlspecialchars($product['nama']) ?></h1>
                
                <div class="pvl-detail-price"><?= formatRupiah($product['harga']) ?></div>
                
                <hr class="border-secondary my-4 opacity-25">

                <!-- Specifications Features List (Custom luxury styling) -->
                <div class="mb-4">
                    <h5 class="text-white h6 mb-3 fw-bold">SPESIFIKASI PREMIUM:</h5>
                    <div class="row g-2 text-secondary" style="font-size: 13px;">
                        <div class="col-md-6"><i class="fas fa-check text-blue me-2"></i> Bahan: Magnesium / Forged Aluminum Alloy</div>
                        <div class="col-md-6"><i class="fas fa-check text-blue me-2"></i> Tipe: Monoblock Racing Series</div>
                        <div class="col-md-6"><i class="fas fa-check text-blue me-2"></i> Keseimbangan: High Precision CNC Machined</div>
                        <div class="col-md-6"><i class="fas fa-check text-blue me-2"></i> Finishing: Luxury Double Powder Coated</div>
                    </div>
                </div>

                <div class="pvl-detail-desc text-secondary mb-4">
                    <h5 class="text-white h6 mb-2 fw-bold">DESKRIPSI PRODUK:</h5>
                    <p><?= nl2br(htmlspecialchars($product['deskripsi'])) ?></p>
                </div>

                <hr class="border-secondary my-4 opacity-25">

                <!-- Add to Cart Shopping Action -->
                <form action="<?= $base_url ?>/pages/cart-process.php" method="GET" class="mt-4">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    
                    <div class="d-flex flex-wrap gap-4 align-items-center">
                        <?php if ($product['stok'] > 0): ?>
                            <!-- Quantity Controllers -->
                            <div class="d-flex flex-column gap-2">
                                <span class="text-secondary small fw-medium">Jumlah Pembelian:</span>
                                <div class="pvl-qty-control">
                                    <button type="button" class="btn-qty-minus"><i class="fas fa-minus"></i></button>
                                    <input type="number" name="qty" class="input-qty" value="1" min="1" max="<?= $product['stok'] ?>" readonly>
                                    <button type="button" class="btn-qty-plus"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                            
                            <!-- Add to Cart Submit Button -->
                            <div class="d-flex align-items-end h-100 mt-4">
                                <button type="submit" class="pvl-btn pvl-btn-primary pvl-btn-lg px-5">
                                    <i class="fas fa-shopping-cart"></i> Tambah Ke Keranjang
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger bg-danger bg-opacity-10 border-0 rounded-3 text-white-50 px-4 py-3">
                                <i class="fas fa-exclamation-triangle text-danger me-2"></i> Maaf, saat ini produk velg ini sedang tidak tersedia. Mohon cek kembali nanti.
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
