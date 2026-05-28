<?php
// ============================================
// HEADER INCLUDES - PAVELK
// ============================================
require_once __DIR__ . '/../config/config.php';

// Prepare values
$user_name = $_SESSION['nama'] ?? '';
$user_initial = !empty($user_name) ? strtoupper(substr($user_name, 0, 1)) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . " - Pavelk" : "PAVELK - Premium Motor Racing Wheels" ?></title>
    
    <!-- Meta SEO -->
    <meta name="description" content="PAVELK - Toko online velg racing motor premium, mewah, modern, dan terpercaya. Temukan velg racing terbaik untuk motor kesayangan Anda di sini.">
    <meta name="keywords" content="velg motor, racing wheels, pavelk, velg racing, rcb, tdr, rossi, axio, chemco">
    <meta name="author" content="PAVELK">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= $base_url ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Render Notification/Toast if exists -->
<?php 
$flash = getFlash(); 
if ($flash): 
?>
<div class="pvl-toast <?= htmlspecialchars($flash['type']) ?>" id="pvlToast">
    <div class="pvl-toast-icon">
        <?php if ($flash['type'] === 'success'): ?>
            <i class="fas fa-check-circle"></i>
        <?php elseif ($flash['type'] === 'error'): ?>
            <i class="fas fa-exclamation-circle"></i>
        <?php elseif ($flash['type'] === 'warning'): ?>
            <i class="fas fa-exclamation-triangle"></i>
        <?php else: ?>
            <i class="fas fa-info-circle"></i>
        <?php endif; ?>
    </div>
    <div class="pvl-toast-content">
        <h5><?= htmlspecialchars(ucfirst($flash['type'] === 'error' ? 'Gagal' : $flash['type'])) ?></h5>
        <p><?= htmlspecialchars($flash['message']) ?></p>
    </div>
    <button class="pvl-toast-close" onclick="document.getElementById('pvlToast').remove()">&times;</button>
</div>
<?php endif; ?>

<!-- Include Sidebar Navigation -->
<?php require_once __DIR__ . '/sidebar.php'; ?>

<!-- Main Wrapper -->
<div class="pvl-main">
    
    <!-- Topbar Header -->
    <header class="pvl-topbar">
        <!-- Search Form -->
        <form action="<?= $base_url ?>/pages/products.php" method="GET" class="pvl-search-box">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Cari velg racing premium..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
        </form>

        <!-- Topbar Actions -->
        <div class="pvl-topbar-actions">
            <!-- Icon Cart -->
            <a href="<?= $base_url ?>/pages/cart.php" class="pvl-topbar-btn" title="Keranjang Belanja">
                <i class="fas fa-shopping-bag"></i>
                <?php 
                // Display cart counter directly
                if ($cart_count > 0) {
                    echo '<span class="cart-count">' . $cart_count . '</span>';
                }
                ?>
            </a>

            <!-- User Menu Info -->
            <?php if (isLoggedIn()): ?>
                <div class="dropdown">
                    <div class="pvl-user-avatar" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" title="<?= htmlspecialchars($user_name) ?>">
                        <?= htmlspecialchars($user_initial) ?>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark border-subtle bg-dark-2 shadow-lg rounded-pvl p-2" aria-labelledby="userMenu">
                        <li class="px-3 py-2 text-white-50 border-bottom border-secondary mb-2" style="font-size: 13px;">
                            Halo, <strong class="text-white"><?= htmlspecialchars($user_name) ?></strong>
                        </li>
                        <li><a class="dropdown-item rounded-3 py-2" href="<?= $base_url ?>/pages/orders.php"><i class="fas fa-history me-2 text-blue"></i>Riwayat Pesanan</a></li>
                        <?php if (isAdmin()): ?>
                            <li><a class="dropdown-item rounded-3 py-2 text-blue fw-semibold" href="<?= $base_url ?>/admin/dashboard.php"><i class="fas fa-cog me-2"></i>Admin Panel</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider border-secondary"></li>
                        <li><a class="dropdown-item rounded-3 py-2 text-danger" href="<?= $base_url ?>/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="<?= $base_url ?>/login.php" class="pvl-btn pvl-btn-primary pvl-btn-sm rounded-pill px-3 py-2">
                    <i class="fas fa-user-circle"></i> Login
                </a>
            <?php endif; ?>
        </div>
    </header>
