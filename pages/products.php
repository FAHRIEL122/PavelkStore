<?php
// ============================================
// PRODUCTS CATALOG WITH SEARCH & PAGINATION - PAVELK
// ============================================
$page_title = "Koleksi Velg Racing Premium";
require_once __DIR__ . '/../includes/header.php';

// Pagination setup
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Search query setup
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

try {
    if (!empty($search)) {
        // Query with search
        $count_stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE nama LIKE ? OR deskripsi LIKE ?");
        $count_stmt->execute(["%$search%", "%$search%"]);
        $total_rows = $count_stmt->fetchColumn();

        $stmt = $conn->prepare("SELECT * FROM products WHERE nama LIKE ? OR deskripsi LIKE ? ORDER BY id DESC LIMIT ? OFFSET ?");
        // Convert parameters properly for PDO execute
        $stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->bindValue(4, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll();
    } else {
        // Simple query
        $total_rows = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();

        $stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $products = $stmt->fetchAll();
    }

    $total_pages = ceil($total_rows / $limit);
} catch (PDOException $e) {
    $products = [];
    $total_rows = 0;
    $total_pages = 0;
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

<!-- Products Section Header -->
<section class="pvl-section">
    <div class="pvl-section-header">
        <h2>Koleksi <span>Velg Racing</span></h2>
        <p>
            <?php if (!empty($search)): ?>
                Hasil pencarian untuk: "<strong><?= htmlspecialchars($search) ?></strong>" (<?= $total_rows ?> produk ditemukan)
            <?php else: ?>
                Jelajahi jajaran velg motor racing forged kualitas ekstrim dengan desain sporty termodern.
            <?php endif; ?>
        </p>
        <div class="pvl-section-line"></div>
    </div>

    <!-- Product Grid Showcase -->
    <div class="container-fluid px-2">
        <?php if (!empty($products)): ?>
            <div class="pvl-product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="pvl-product-card animate-card">
                        <!-- Product Visual Media -->
                        <div class="pvl-product-img">
                            <?php 
                            $imagePath = __DIR__ . '/../assets/images/' . $product['gambar'];
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
                                    <a href="<?= $base_url ?>/pages/cart-process.php?action=add&product_id=<?= $product['id'] ?>&qty=1&redirect=products" class="pvl-btn-icon" title="Tambah Ke Keranjang">
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
            </div>

            <!-- Pagination Render -->
            <?php if ($total_pages > 1): ?>
                <div class="pvl-pagination">
                    <?php if ($page > 1): ?>
                        <a href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>" title="Sebelumnya"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>" title="Selanjutnya"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Empty Showcase State -->
            <div class="pvl-empty border border-subtle bg-dark-2 rounded-pvl py-5">
                <i class="fas fa-motorcycle text-secondary mb-3 fs-1"></i>
                <h4>Produk Tidak Ditemukan</h4>
                <p class="text-secondary">Mohon maaf, velg racing dengan kriteria pencarian Anda belum tersedia.</p>
                <a href="<?= $base_url ?>/pages/products.php" class="pvl-btn pvl-btn-primary pvl-btn-sm mt-2">
                    <i class="fas fa-redo me-2"></i> Reset Pencarian
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
