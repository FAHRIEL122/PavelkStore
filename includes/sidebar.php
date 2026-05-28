<?php
// ============================================
// SIDEBAR NAVIGATION - PAVELK
// ============================================
require_once __DIR__ . '/../config/config.php';

// Get current page name for active state
$current_page = basename($_SERVER['PHP_SELF']);

// Count items in cart
$cart_count = 0;
if (isLoggedIn()) {
    $stmt = $conn->prepare("SELECT SUM(qty) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_result = $stmt->fetch();
    $cart_count = $cart_result['total'] ?? 0;
}
?>

<!-- Mobile Sidebar Toggle -->
<button class="pvl-sidebar-toggle" id="sidebarToggle">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Backdrop Overlay -->
<div class="pvl-sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar Container -->
<aside class="pvl-sidebar" id="sidebar">
    <!-- Brand Info -->
    <div class="pvl-sidebar-brand">
        <div class="brand-icon">PVL</div>
        <div class="brand-text">PAVELK<span>.</span></div>
    </div>

    <!-- Navigation Items -->
    <nav class="pvl-sidebar-nav">
        <div class="pvl-nav-label">Main Menu</div>
        
        <a href="<?= $base_url ?>/index.php" class="pvl-nav-item <?= $current_page == 'index.php' || $current_page == '' ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>

        <a href="<?= $base_url ?>/pages/products.php" class="pvl-nav-item <?= $current_page == 'products.php' || $current_page == 'product-detail.php' ? 'active' : '' ?>">
            <i class="fas fa-motorcycle"></i>
            <span>Velg Racing</span>
        </a>

        <a href="<?= $base_url ?>/pages/cart.php" class="pvl-nav-item <?= $current_page == 'cart.php' ? 'active' : '' ?>">
            <i class="fas fa-shopping-cart"></i>
            <span>Keranjang</span>
            <?php if ($cart_count > 0): ?>
                <span class="badge"><?= $cart_count ?></span>
            <?php endif; ?>
        </a>

        <?php if (isLoggedIn()): ?>
            <div class="pvl-nav-label">Customer Area</div>
            <a href="<?= $base_url ?>/pages/orders.php" class="pvl-nav-item <?= $current_page == 'orders.php' || $current_page == 'order-detail.php' ? 'active' : '' ?>">
                <i class="fas fa-history"></i>
                <span>Riwayat Pesanan</span>
            </a>
        <?php endif; ?>

        <?php if (isAdmin()): ?>
            <div class="pvl-nav-label">Administrator</div>
            <a href="<?= $base_url ?>/admin/dashboard.php" class="pvl-nav-item <?= strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt text-blue"></i>
                <span class="text-blue fw-semibold">Admin Panel</span>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Sidebar Footer (Auth Button) -->
    <div class="pvl-sidebar-footer">
        <?php if (isLoggedIn()): ?>
            <a href="<?= $base_url ?>/logout.php" class="pvl-nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        <?php else: ?>
            <a href="<?= $base_url ?>/login.php" class="pvl-nav-item active">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login / Register</span>
            </a>
        <?php endif; ?>
    </div>
</aside>
