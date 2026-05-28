<?php
// ============================================
// PRODUCT MANAGEMENT CRUD - PAVELK
// ============================================
require_once __DIR__ . '/../config/config.php';

// Secure check: Must be admin
if (!isLoggedIn() || !isAdmin()) {
    setFlash('error', 'Akses ditolak. Anda wajib masuk sebagai Administrator.');
    redirect('admin/login.php');
}

$action = isset($_GET['action']) ? sanitize($_GET['action']) : 'list';
$error = '';
$success = '';

// Check and create upload directory if missing
$upload_dir = __DIR__ . '/../assets/images/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
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

// ============================================
// PROCESS POST REQUESTS (ADD & EDIT)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ----------------------------------------
    // SUBMIT: ADD PRODUCT
    // ----------------------------------------
    if ($action === 'add') {
        $nama = sanitize($_POST['nama']);
        $harga = (double)$_POST['harga'];
        $stok = (int)$_POST['stok'];
        $deskripsi = sanitize($_POST['deskripsi']);
        
        $gambar_name = 'default.jpg';

        if (empty($nama) || $harga <= 0 || $stok < 0) {
            $error = "Semua field input wajib diisi dengan benar.";
        } else {
            // Handle image upload if submitted
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['gambar']['tmp_name'];
                $file_name = $_FILES['gambar']['name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array($file_ext, $allowed_exts)) {
                    $new_file_name = 'velg_' . time() . '_' . uniqid() . '.' . $file_ext;
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                        $gambar_name = $new_file_name;
                    } else {
                        $error = "Gagal memindahkan file gambar terunggah.";
                    }
                } else {
                    $error = "Format file gambar ditolak. Gunakan format JPG, PNG, atau WEBP.";
                }
            }

            if (empty($error)) {
                try {
                    $stmt = $conn->prepare("INSERT INTO products (nama, harga, stok, deskripsi, gambar) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$nama, $harga, $stok, $deskripsi, $gambar_name]);
                    
                    setFlash('success', 'Produk baru ' . $nama . ' berhasil ditambahkan!');
                    redirect('admin/products.php');
                } catch (PDOException $e) {
                    $error = "Gagal menambahkan produk ke database: " . $e->getMessage();
                }
            }
        }
    }
    
    // ----------------------------------------
    // SUBMIT: EDIT PRODUCT
    // ----------------------------------------
    elseif ($action === 'edit') {
        $id = (int)$_GET['id'];
        $nama = sanitize($_POST['nama']);
        $harga = (double)$_POST['harga'];
        $stok = (int)$_POST['stok'];
        $deskripsi = sanitize($_POST['deskripsi']);

        if (empty($nama) || $harga <= 0 || $stok < 0) {
            $error = "Semua field input wajib diisi dengan benar.";
        } else {
            try {
                // Fetch old details to preserve image filename
                $stmt = $conn->prepare("SELECT gambar FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $product = $stmt->fetch();
                
                if (!$product) {
                    $error = "Produk tidak ditemukan.";
                } else {
                    $gambar_name = $product['gambar'];

                    // Handle image update if submitted
                    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                        $file_tmp = $_FILES['gambar']['tmp_name'];
                        $file_name = $_FILES['gambar']['name'];
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        
                        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
                        
                        if (in_array($file_ext, $allowed_exts)) {
                            $new_file_name = 'velg_' . time() . '_' . uniqid() . '.' . $file_ext;
                            if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                                // Delete old file if it wasn't default
                                if ($gambar_name !== 'default.jpg' && file_exists($upload_dir . $gambar_name)) {
                                    @unlink($upload_dir . $gambar_name);
                                }
                                $gambar_name = $new_file_name;
                            } else {
                                $error = "Gagal memindahkan file gambar terunggah.";
                            }
                        } else {
                            $error = "Format file gambar ditolak. Gunakan format JPG, PNG, atau WEBP.";
                        }
                    }

                    if (empty($error)) {
                        $u_stmt = $conn->prepare("UPDATE products SET nama = ?, harga = ?, stok = ?, deskripsi = ?, gambar = ? WHERE id = ?");
                        $u_stmt->execute([$nama, $harga, $stok, $deskripsi, $gambar_name, $id]);

                        setFlash('success', 'Produk ' . $nama . ' berhasil diperbarui!');
                        redirect('admin/products.php');
                    }
                }
            } catch (PDOException $e) {
                $error = "Gagal memperbarui data produk: " . $e->getMessage();
            }
        }
    }
}

// ============================================
// PROCESS GET REQUESTS (DELETE)
// ============================================
if ($action === 'delete') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    try {
        // Fetch details to delete associated image file
        $stmt = $conn->prepare("SELECT nama, gambar FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if ($product) {
            $image_file = $product['gambar'];
            
            // Delete product row
            $d_stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            $d_stmt->execute([$id]);

            // Clean up files
            if ($image_file !== 'default.jpg' && file_exists($upload_dir . $image_file)) {
                @unlink($upload_dir . $image_file);
            }

            setFlash('success', 'Produk "' . $product['nama'] . '" berhasil dihapus dari sistem.');
        } else {
            setFlash('error', 'Produk gagal dihapus karena tidak ditemukan.');
        }
    } catch (PDOException $e) {
        setFlash('error', 'Terjadi kesalahan penghapusan produk: ' . $e->getMessage());
    }
    redirect('admin/products.php');
}

// ============================================
// PREPARE DETAILS FOR EDIT MODE
// ============================================
$edit_product = null;
if ($action === 'edit') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    try {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $edit_product = $stmt->fetch();
        
        if (!$edit_product) {
            setFlash('error', 'Produk yang ingin diedit tidak ditemukan.');
            redirect('admin/products.php');
        }
    } catch (PDOException $e) {
        setFlash('error', 'Gagal mengambil data produk.');
        redirect('admin/products.php');
    }
}

// ============================================
// FETCH ALL PRODUCTS FOR DEFAULT LIST MODE
// ============================================
$products = [];
if ($action === 'list') {
    $search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
    
    try {
        if (!empty($search)) {
            $stmt = $conn->prepare("SELECT * FROM products WHERE nama LIKE ? ORDER BY id DESC");
            $stmt->execute(["%$search%"]);
        } else {
            $stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
        }
        $products = $stmt->fetchAll();
    } catch (PDOException $e) {
        setFlash('error', 'Gagal memuat list produk.');
    }
}

$page_title = "Manajemen Produk";
require_once __DIR__ . '/../includes/header.php';
?>

<section class="pvl-section">
    
    <!-- ----------------------------------------------------
         1. DISPLAY MODE: ADD NEW PRODUCT FORM
         ---------------------------------------------------- -->
    <?php if ($action === 'add'): ?>
        <div class="mb-4">
            <a href="<?= $base_url ?>/admin/products.php" class="text-secondary small fw-medium">
                <i class="fas fa-chevron-left me-1"></i> Kembali ke Daftar Produk
            </a>
        </div>

        <div class="pvl-section-header">
            <h2>Tambah <span>Produk Baru</span></h2>
            <p>Masukkan detail spesifikasi produk velg racing baru di bawah ini.</p>
            <div class="pvl-section-line"></div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="p-4 rounded-3 border border-subtle bg-dark-2">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger bg-danger bg-opacity-25 border-0 text-white rounded-3 small mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <!-- Nama Velg -->
                        <div class="pvl-form-group">
                            <label for="nama">NAMA PRODUK VELG</label>
                            <input type="text" id="nama" name="nama" class="pvl-form-control border-subtle" placeholder="Masukkan nama velg..." value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>" required>
                        </div>

                        <div class="row g-4">
                            <!-- Harga -->
                            <div class="col-md-6">
                                <div class="pvl-form-group">
                                    <label for="harga">HARGA JUAL (RP)</label>
                                    <input type="number" id="harga" name="harga" class="pvl-form-control border-subtle" placeholder="Contoh: 1500000" value="<?= isset($_POST['harga']) ? htmlspecialchars($_POST['harga']) : '' ?>" required>
                                </div>
                            </div>
                            <!-- Stok -->
                            <div class="col-md-6">
                                <div class="pvl-form-group">
                                    <label for="stok">STOK UNIT INVENTARIS</label>
                                    <input type="number" id="stok" name="stok" class="pvl-form-control border-subtle" placeholder="Contoh: 15" value="<?= isset($_POST['stok']) ? htmlspecialchars($_POST['stok']) : '' ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Gambar -->
                        <div class="pvl-form-group">
                            <label for="gambar">FILE GAMBAR VELG</label>
                            <input type="file" id="gambar" name="gambar" class="pvl-form-control border-subtle" accept="image/*">
                            <div class="form-text text-secondary" style="font-size:11px;">Gunakan file ekstensi JPG, PNG, atau WEBP. Maksimal resolusi standar.</div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="pvl-form-group mb-4">
                            <label for="deskripsi">DESKRIPSI SPESIFIKASI PRODUK</label>
                            <textarea id="deskripsi" name="deskripsi" class="pvl-form-control border-subtle" placeholder="Deskripsikan material velg, model spoke, ukuran ring, kelengkapan, dll..." required><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
                        </div>

                        <button type="submit" class="pvl-btn pvl-btn-primary py-3 px-5">
                            <i class="fas fa-save me-2"></i> Simpan Produk
                        </button>
                    </form>
                </div>
            </div>
        </div>

    <!-- ----------------------------------------------------
         2. DISPLAY MODE: EDIT PRODUCT FORM
         ---------------------------------------------------- -->
    <?php elseif ($action === 'edit' && $edit_product): ?>
        <div class="mb-4">
            <a href="<?= $base_url ?>/admin/products.php" class="text-secondary small fw-medium">
                <i class="fas fa-chevron-left me-1"></i> Kembali ke Daftar Produk
            </a>
        </div>

        <div class="pvl-section-header">
            <h2>Edit Detail <span>Produk</span></h2>
            <p>Perbarui spesifikasi produk: <strong><?= htmlspecialchars($edit_product['nama']) ?></strong></p>
            <div class="pvl-section-line"></div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="p-4 rounded-3 border border-subtle bg-dark-2">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger bg-danger bg-opacity-25 border-0 text-white rounded-3 small mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <!-- Nama Velg -->
                        <div class="pvl-form-group">
                            <label for="nama">NAMA PRODUK VELG</label>
                            <input type="text" id="nama" name="nama" class="pvl-form-control border-subtle" value="<?= htmlspecialchars($edit_product['nama']) ?>" required>
                        </div>

                        <div class="row g-4">
                            <!-- Harga -->
                            <div class="col-md-6">
                                <div class="pvl-form-group">
                                    <label for="harga">HARGA JUAL (RP)</label>
                                    <input type="number" id="harga" name="harga" class="pvl-form-control border-subtle" value="<?= (int)$edit_product['harga'] ?>" required>
                                </div>
                            </div>
                            <!-- Stok -->
                            <div class="col-md-6">
                                <div class="pvl-form-group">
                                    <label for="stok">STOK UNIT INVENTARIS</label>
                                    <input type="number" id="stok" name="stok" class="pvl-form-control border-subtle" value="<?= $edit_product['stok'] ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Info Gambar Lama & Gambar Baru -->
                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-md-2 text-center">
                                <div style="width: 80px; height: 80px; border-radius: var(--pvl-radius-sm); overflow:hidden; background:#111; border:1px solid rgba(255,255,255,0.05); display:inline-block;">
                                    <?php 
                                    $imagePath = __DIR__ . '/../assets/images/' . $edit_product['gambar'];
                                    if (!empty($edit_product['gambar']) && file_exists($imagePath)): ?>
                                        <img src="<?= $base_url ?>/assets/images/<?= htmlspecialchars($edit_product['gambar']) ?>" alt="" class="w-100 h-100 object-fit-cover">
                                    <?php else: ?>
                                        <?= renderVelgSvg($edit_product['id']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="pvl-form-group mb-0">
                                    <label for="gambar">PERBARUI FILE GAMBAR VELG (OPSIONAL)</label>
                                    <input type="file" id="gambar" name="gambar" class="pvl-form-control border-subtle" accept="image/*">
                                    <div class="form-text text-secondary" style="font-size:11px;">Biarkan kosong jika tidak ingin mengubah gambar produk yang sudah terpasang.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="pvl-form-group mb-4">
                            <label for="deskripsi">DESKRIPSI SPESIFIKASI PRODUK</label>
                            <textarea id="deskripsi" name="deskripsi" class="pvl-form-control border-subtle" required><?= htmlspecialchars($edit_product['deskripsi']) ?></textarea>
                        </div>

                        <button type="submit" class="pvl-btn pvl-btn-primary py-3 px-5">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

    <!-- ----------------------------------------------------
         3. DISPLAY MODE: DEFAULT PRODUCTS LIST TABLE
         ---------------------------------------------------- -->
    <?php else: ?>
        <div class="pvl-section-header">
            <h2>Kelola <span>Produk Velg</span></h2>
            <p>Tambah, edit spesifikasi harga, perbarui stok unit, atau hapus velg dari database katalog.</p>
            <div class="pvl-section-line"></div>
        </div>

        <div class="container-fluid px-2">
            <div class="p-4 rounded-3 border border-subtle bg-dark-2">
                <!-- Search & Add Actions controls banner -->
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <!-- Search inside CRUD -->
                    <form action="" method="GET" class="pvl-search-box flex-grow-1" style="max-width:320px;">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Cari nama velg..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </form>

                    <!-- Add button -->
                    <a href="?action=add" class="pvl-btn pvl-btn-primary">
                        <i class="fas fa-plus"></i> Tambah Produk Baru
                    </a>
                </div>

                <!-- Main CRUD Products Table -->
                <?php if (!empty($products)): ?>
                    <div class="table-responsive">
                        <table class="pvl-table">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Nama Velg</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $prod): ?>
                                    <tr>
                                        <!-- Visual Image Column -->
                                        <td>
                                            <div style="width: 50px; height: 50px; border-radius: var(--pvl-radius-sm); overflow:hidden; background:#111;">
                                                <?php 
                                                $imagePath = __DIR__ . '/../assets/images/' . $prod['gambar'];
                                                if (!empty($prod['gambar']) && file_exists($imagePath)): ?>
                                                    <img src="<?= $base_url ?>/assets/images/<?= htmlspecialchars($prod['gambar']) ?>" alt="" class="w-100 h-100 object-fit-cover">
                                                <?php else: ?>
                                                    <?= renderVelgSvg($prod['id']) ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        
                                        <!-- Nama Column -->
                                        <td>
                                            <strong class="text-white"><?= htmlspecialchars($prod['nama']) ?></strong>
                                        </td>
                                        
                                        <!-- Harga Column -->
                                        <td>
                                            <span class="text-blue fw-bold"><?= formatRupiah($prod['harga']) ?></span>
                                        </td>

                                        <!-- Stok Column -->
                                        <td>
                                            <span class="badge text-black <?= $prod['stok'] == 0 ? 'bg-danger' : ($prod['stok'] < 5 ? 'bg-warning' : 'bg-success') ?>" style="font-weight:600;">
                                                <?= $prod['stok'] ?> Unit
                                            </span>
                                        </td>

                                        <!-- Actions (Edit & Delete) -->
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="?action=edit&id=<?= $prod['id'] ?>" class="pvl-btn pvl-btn-outline pvl-btn-sm py-1 px-2 border-0" title="Edit Produk">
                                                    <i class="fas fa-edit text-blue"></i>
                                                </a>
                                                <a href="?action=delete&id=<?= $prod['id'] ?>" class="pvl-btn pvl-btn-outline pvl-btn-sm py-1 px-2 border-0" title="Hapus Produk" onclick="return confirm('Apakah Anda yakin ingin menghapus produk <?= htmlspecialchars($prod['nama']) ?> dari database? Tindakan ini tidak bisa dibatalkan.')">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-secondary">
                        Belum ada data produk velg yang terdaftar. Silakan klik tombol "Tambah Produk Baru" untuk mendaftarkan.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
