<?php
// ============================================
// LANDING PAGE - PAVELK
// ============================================
$page_title = "Welcome to Pavelk - Luxury Racing Wheels";
require_once __DIR__ . '/includes/header.php';

// Fetch featured products (limit 3 for premium showcases on hero/landing, or show all 6)
try {
    $stmt = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 6");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
}

// Function to generate premium visual representation of a high-tech racing wheel dynamically as SVG
function renderVelgSvg($index = 1) {
    $colors = ['#0DD8E6', '#ff4757', '#ffa502', '#2ed573', '#1e90ff', '#a55eea'];
    $color = $colors[($index - 1) % count($colors)];
    return '
    <svg viewBox="0 0 100 100" class="w-100 h-100" style="background:#111; padding:15px;">
        <circle cx="50" cy="50" r="45" fill="none" stroke="#222" stroke-width="2" />
        <circle cx="50" cy="50" r="40" fill="none" stroke="' . $color . '" stroke-width="0.5" stroke-dasharray="2,2" />
        <!-- Rim -->
        <circle cx="50" cy="50" r="38" fill="none" stroke="#333" stroke-width="3" />
        <circle cx="50" cy="50" r="35" fill="none" stroke="#555" stroke-width="1" />
        <!-- Spokes (Velg Racing Model) -->
        <g stroke="' . $color . '" stroke-width="1.5" stroke-linecap="round">';
        for ($i = 0; $i < 360; $i += 45) {
            $rad = deg2rad($i);
            $x1 = 50 + 10 * cos($rad);
            $y1 = 50 + 10 * sin($rad);
            $x2 = 50 + 35 * cos($rad);
            $y2 = 50 + 35 * sin($rad);
            
            // Double spoke design
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
        <!-- Center Hub -->
        <circle cx="50" cy="50" r="10" fill="#222" stroke="#444" stroke-width="1" />
        <circle cx="50" cy="50" r="8" fill="#111" stroke="' . $color . '" stroke-width="1.5" />
        <circle cx="50" cy="50" r="3" fill="' . $color . '" />
        
        <!-- Luxury Accent Lines -->
        <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1" />
    </svg>';
    return $html;
}
?>

<!-- Hero Section -->
<section class="pvl-hero">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="pvl-hero-content">
                    <span class="pvl-hero-badge">
                        <i class="fas fa-gem me-2"></i> Supreme Performance Wheels
                    </span>
                    <h1 class="pvl-hero-title">
                        Welcome to <br><span>Pavelk</span>
                    </h1>
                    <p class="pvl-hero-subtitle">
                        Belanja mudah, cepat, dan terpercaya. Kami menyediakan koleksi eksklusif velg motor racing forged kualitas premium berstandar internasional untuk performa handal dan estetika visual tingkat tinggi.
                    </p>
                    <div class="pvl-hero-actions">
                        <a href="<?= $base_url ?>/pages/products.php" class="pvl-btn pvl-btn-primary pvl-btn-lg">
                            <i class="fas fa-shopping-cart"></i> Belanja Sekarang
                        </a>
                        <a href="#featured-products" class="pvl-btn pvl-btn-outline pvl-btn-lg">
                            Pelajari Produk <i class="fas fa-arrow-down ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Hero Side Image (Racing wheel dynamic visualization) -->
            <div class="col-lg-5 d-none d-lg-block position-relative" style="height: 600px;">
                <div class="pvl-hero-img">
                    <svg viewBox="0 0 100 100" style="width: 460px; height: 460px; filter: drop-shadow(0 20px 50px rgba(13, 216, 230, 0.45));">
                        <circle cx="50" cy="50" r="46" fill="none" stroke="#222" stroke-width="3" />
                        <circle cx="50" cy="50" r="44" fill="none" stroke="#0DD8E6" stroke-width="1" stroke-dasharray="3,3" />
                        <!-- Outer Rim -->
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#FFFFFF" stroke-width="2" opacity="0.9" />
                        <!-- Tire thread indicators -->
                        <g stroke="rgba(255,255,255,0.1)" stroke-width="2">
                            <?php for($i=0; $i<360; $i+=10): $r=deg2rad($i); ?>
                                <line x1="<?= 50+43*cos($r) ?>" y1="<?= 50+43*sin($r) ?>" x2="<?= 50+46*cos($r) ?>" y2="<?= 50+46*sin($r) ?>" />
                            <?php endfor; ?>
                        </g>
                        <!-- Spokes -->
                        <g stroke="#0DD8E6" stroke-width="2" stroke-linecap="round">
                            <?php for($i=0; $i<360; $i+=30): $r=deg2rad($i); ?>
                                <line x1="50" y1="50" x2="<?= 50+38*cos($r) ?>" y2="<?= 50+38*sin($r) ?>" />
                                <circle cx="<?= 50+28*cos($r) ?>" cy="<?= 50+28*sin($r) ?>" r="1.5" fill="#000" stroke="#FFFFFF" stroke-width="0.8" />
                            <?php endfor; ?>
                        </g>
                        <!-- Bolt Patterns -->
                        <circle cx="50" cy="50" r="12" fill="#111" stroke="#333" stroke-width="2" />
                        <g fill="#FFFFFF">
                            <?php for($i=0; $i<360; $i+=72): $r=deg2rad($i); ?>
                                <circle cx="<?= 50+7*cos($r) ?>" cy="<?= 50+7*sin($r) ?>" r="1.2" />
                            <?php endfor; ?>
                        </g>
                        <circle cx="50" cy="50" r="3" fill="#000" stroke="#0DD8E6" stroke-width="1.5" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid Banner -->
<section class="py-5 bg-dark-2 border-top border-bottom border-subtle">
    <div class="container-fluid px-5">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="p-4 rounded-3 border border-subtle h-100 bg-dark-3">
                    <i class="fas fa-shipping-fast text-blue fs-2 mb-3"></i>
                    <h4 class="h5 fw-bold mb-2">Pengiriman Cepat</h4>
                    <p class="text-secondary small mb-0">Layanan ekspedisi terpercaya ke seluruh penjuru Nusantara dengan garansi keselamatan penuh.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-3 border border-subtle h-100 bg-dark-3">
                    <i class="fas fa-shield-alt text-blue fs-2 mb-3"></i>
                    <h4 class="h5 fw-bold mb-2">100% Produk Original</h4>
                    <p class="text-secondary small mb-0">Garansi keaslian merek kelas dunia dari authorized distributor dengan material bersertifikat.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 rounded-3 border border-subtle h-100 bg-dark-3">
                    <i class="fas fa-headset text-blue fs-2 mb-3"></i>
                    <h4 class="h5 fw-bold mb-2">Dukungan CS Eksklusif</h4>
                    <p class="text-secondary small mb-0">Konsultasi gratis ukuran, jenis, dan spesifikasi velg motor Anda bersama teknisi berpengalaman.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="pvl-section" id="featured-products">
    <div class="pvl-section-header">
        <h2>Koleksi <span>Velg Unggulan</span></h2>
        <p>Tingkatkan estetika dan kedinamisan berkendara Anda dengan pilihan velg motor racing premium terbaik kami.</p>
        <div class="pvl-section-line"></div>
    </div>

    <div class="container-fluid px-2">
        <div class="pvl-product-grid">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $index => $product): ?>
                    <div class="pvl-product-card">
                        <!-- Product Visual Media -->
                        <div class="pvl-product-img">
                            <?php 
                            $imagePath = __DIR__ . '/assets/images/' . $product['gambar'];
                            if (!empty($product['gambar']) && file_exists($imagePath)): ?>
                                <img src="<?= $base_url ?>/assets/images/<?= htmlspecialchars($product['gambar']) ?>" alt="<?= htmlspecialchars($product['nama']) ?>">
                            <?php else: ?>
                                <?= renderVelgSvg($product['id']) ?>
                            <?php endif; ?>
                            
                            <!-- Action overlay buttons -->
                            <div class="pvl-product-overlay">
                                <a href="<?= $base_url ?>/pages/product-detail.php?id=<?= $product['id'] ?>" class="pvl-btn-icon" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($product['stok'] > 0): ?>
                                    <a href="<?= $base_url ?>/pages/cart-process.php?action=add&product_id=<?= $product['id'] ?>&qty=1&redirect=index" class="pvl-btn-icon" title="Tambah Ke Keranjang">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Info details -->
                        <div class="pvl-product-info">
                            <h3 class="pvl-product-name"><?= htmlspecialchars($product['nama']) ?></h3>
                            <div class="pvl-product-price"><?= formatRupiah($product['harga']) ?></div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div class="pvl-product-stock">
                                    <span class="dot <?= $product['stok'] == 0 ? 'out' : ($product['stok'] < 5 ? 'low' : '') ?>"></span>
                                    <?= $product['stok'] == 0 ? 'Stok Habis' : 'Stok: ' . $product['stok'] . ' Unit' ?>
                                </div>
                                <a href="<?= $base_url ?>/pages/product-detail.php?id=<?= $product['id'] ?>" class="text-blue small fw-semibold">Detail <i class="fas fa-chevron-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-secondary">Belum ada produk velg racing yang tersedia saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="<?= $base_url ?>/pages/products.php" class="pvl-btn pvl-btn-outline pvl-btn-lg">
                Lihat Seluruh Koleksi <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
