<?php
// ============================================
// CHECKOUT PROCESS CONTROLLER (TRANSACTIONAL) - PAVELK
// ============================================
require_once __DIR__ . '/../config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlash('warning', 'Silakan login terlebih dahulu untuk menyelesaikan pesanan.');
    redirect('login.php');
}

// Redirect if not POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('pages/cart.php');
}

$user_id = $_SESSION['user_id'];
$nama_penerima = sanitize($_POST['nama_penerima']);
$no_hp = sanitize($_POST['no_hp']);
$alamat = sanitize($_POST['alamat']);

if (empty($nama_penerima) || empty($no_hp) || empty($alamat)) {
    setFlash('error', 'Semua kolom informasi pengiriman wajib diisi.');
    redirect('pages/checkout.php');
}

try {
    // 1. Fetch user cart items to verify contents
    $stmt = $conn->prepare("SELECT c.id as cart_id, c.qty, p.id as product_id, p.nama, p.harga, p.stok FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();

    if (empty($cart_items)) {
        setFlash('error', 'Keranjang belanja Anda kosong. Silakan belanja terlebih dahulu.');
        redirect('pages/products.php');
    }

    // 2. Start MySQL transaction to ensure total consistency
    $conn->beginTransaction();

    $total_bayar = 0;
    
    // Check stocks for all items in advance before inserting
    foreach ($cart_items as $item) {
        if ($item['stok'] < $item['qty']) {
            throw new Exception("Stok untuk produk '" . $item['nama'] . "' tidak mencukupi. Tersedia: " . $item['stok'] . " unit, Anda meminta: " . $item['qty'] . " unit.");
        }
        $total_bayar += $item['harga'] * $item['qty'];
    }

    // 3. Create active Order row
    $order_stmt = $conn->prepare("INSERT INTO orders (user_id, nama_penerima, no_hp, alamat, total, status, tanggal) VALUES (?, ?, ?, ?, ?, 'pending', CURRENT_TIMESTAMP)");
    $order_stmt->execute([$user_id, $nama_penerima, $no_hp, $alamat, $total_bayar]);
    $order_id = $conn->lastInsertId();

    // 4. Move items from cart to order_items and update product stocks
    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, qty, harga) VALUES (?, ?, ?, ?)");
    $stock_stmt = $conn->prepare("UPDATE products SET stok = stok - ? WHERE id = ?");

    foreach ($cart_items as $item) {
        // Insert order item row
        $item_stmt->execute([$order_id, $item['product_id'], $item['qty'], $item['harga']]);
        
        // Deduct inventory stock
        $stock_stmt->execute([$item['qty'], $item['product_id']]);
    }

    // 5. Delete all items in user's cart
    $clear_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear_stmt->execute([$user_id]);

    // Commit transaction
    $conn->commit();

    setFlash('success', 'Checkout berhasil! Pesanan Anda telah diterima dan sedang menunggu konfirmasi admin.');
    redirect('pages/orders.php');

} catch (Exception $e) {
    // Rollback changes if anything went wrong
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    setFlash('error', 'Gagal memproses checkout: ' . $e->getMessage());
    redirect('pages/checkout.php');
}
?>
