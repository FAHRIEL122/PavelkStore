<?php
// ============================================
// CART CONTROLLER PROCESSOR - PAVELK
// ============================================
require_once __DIR__ . '/../config/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlash('warning', 'Silakan login terlebih dahulu untuk berbelanja.');
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$action = isset($_GET['action']) ? sanitize($_GET['action']) : '';
$redirect_to = isset($_GET['redirect']) ? sanitize($_GET['redirect']) : 'cart';

try {
    switch ($action) {
        // ============================================
        // ADD TO CART ACTION
        // ============================================
        case 'add':
            $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
            $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
            if ($qty < 1) $qty = 1;

            // Fetch product and check stock
            $p_stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $p_stmt->execute([$product_id]);
            $product = $p_stmt->fetch();

            if (!$product) {
                setFlash('error', 'Produk tidak ditemukan.');
                redirect('pages/products.php');
            }

            if ($product['stok'] < $qty) {
                setFlash('error', 'Stok tidak mencukupi. Hanya tersedia ' . $product['stok'] . ' unit.');
                redirect('pages/product-detail.php?id=' . $product_id);
            }

            // Check if product already exists in cart for this user
            $c_stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
            $c_stmt->execute([$user_id, $product_id]);
            $cart_item = $c_stmt->fetch();

            if ($cart_item) {
                $new_qty = $cart_item['qty'] + $qty;
                if ($product['stok'] < $new_qty) {
                    setFlash('error', 'Gagal menambah jumlah. Total keranjang Anda melebihi stok yang ada.');
                    redirect('pages/product-detail.php?id=' . $product_id);
                }
                
                // Update existing quantity
                $u_stmt = $conn->prepare("UPDATE cart SET qty = ? WHERE id = ?");
                $u_stmt->execute([$new_qty, $cart_item['id']]);
            } else {
                // Insert new cart item
                $i_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, qty) VALUES (?, ?, ?)");
                $i_stmt->execute([$user_id, $product_id, $qty]);
            }

            setFlash('success', 'Produk ' . $product['nama'] . ' berhasil ditambahkan ke keranjang!');
            
            // Redirect based on request
            if ($redirect_to === 'index') {
                redirect('index.php');
            } elseif ($redirect_to === 'products') {
                redirect('pages/products.php');
            } else {
                redirect('pages/cart.php');
            }
            break;

        // ============================================
        // UPDATE QUANTITY ACTION
        // ============================================
        case 'update':
            $cart_id = isset($_GET['cart_id']) ? (int)$_GET['cart_id'] : 0;
            $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
            if ($qty < 1) $qty = 1;

            // Fetch cart and product details to verify stock
            $c_stmt = $conn->prepare("SELECT c.*, p.nama, p.stok FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ? AND c.user_id = ?");
            $c_stmt->execute([$cart_id, $user_id]);
            $cart_item = $c_stmt->fetch();

            if (!$cart_item) {
                setFlash('error', 'Item keranjang tidak ditemukan.');
                redirect('pages/cart.php');
            }

            if ($cart_item['stok'] < $qty) {
                setFlash('error', 'Stok produk ' . $cart_item['nama'] . ' tidak mencukupi untuk jumlah ini.');
                redirect('pages/cart.php');
            }

            // Perform Update
            $u_stmt = $conn->prepare("UPDATE cart SET qty = ? WHERE id = ?");
            $u_stmt->execute([$qty, $cart_id]);

            setFlash('success', 'Jumlah belanja berhasil diperbarui.');
            redirect('pages/cart.php');
            break;

        // ============================================
        // DELETE FROM CART ACTION
        // ============================================
        case 'delete':
            $cart_id = isset($_GET['cart_id']) ? (int)$_GET['cart_id'] : 0;

            $d_stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $d_stmt->execute([$cart_id, $user_id]);

            setFlash('success', 'Item berhasil dihapus dari keranjang.');
            redirect('pages/cart.php');
            break;

        default:
            redirect('pages/cart.php');
            break;
    }
} catch (PDOException $e) {
    setFlash('error', 'Terjadi kesalahan pemrosesan keranjang: ' . $e->getMessage());
    redirect('pages/cart.php');
}
?>
