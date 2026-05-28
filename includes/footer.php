<?php
// ============================================
// FOOTER INCLUDES - PAVELK
// ============================================
require_once __DIR__ . '/../config/config.php';
?>

    <!-- Footer Area -->
    <footer class="pvl-footer mt-auto">
        <div class="row g-4">
            <!-- Brand Column -->
            <div class="col-lg-5 col-md-12">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="brand-icon" style="width: 32px; height: 32px; background: var(--pvl-gradient); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-family: var(--pvl-font-heading); font-weight: 800; font-size: 14px; color: var(--pvl-black);">PVL</div>
                    <span style="font-family: var(--pvl-font-heading); font-weight: 700; font-size: 18px; letter-spacing: 2px; color: var(--pvl-white);">PAVELK<span class="text-blue">.</span></span>
                </div>
                <p class="pe-lg-5 mb-4 text-secondary" style="font-size: 13px;">
                    PAVELK adalah brand e-commerce penyedia velg motor racing premium, mewah, dan terpercaya. Kami menghadirkan velg forged performa tinggi untuk kenyamanan dan keindahan berkendara Anda.
                </p>
                <div class="pvl-footer-social">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Youtube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="Tiktok"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-3 col-md-6 col-6">
                <h5>Menu Utama</h5>
                <ul class="list-unstyled">
                    <li><a href="<?= $base_url ?>/index.php">Home</a></li>
                    <li><a href="<?= $base_url ?>/pages/products.php">Semua Produk</a></li>
                    <li><a href="<?= $base_url ?>/pages/cart.php">Keranjang Belanja</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="<?= $base_url ?>/pages/orders.php">Riwayat Pesanan</a></li>
                    <?php else: ?>
                        <li><a href="<?= $base_url ?>/login.php">Login / Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Brand Contact Info -->
            <div class="col-lg-4 col-md-6 col-6">
                <h5>Hubungi Kami</h5>
                <ul class="list-unstyled text-secondary" style="font-size: 13px; line-height: 2;">
                    <li><i class="fas fa-map-marker-alt text-blue me-2"></i> Jl. Premium Boulevard No. 88, Jakarta</li>
                    <li><i class="fas fa-phone-alt text-blue me-2"></i> +62 821-3456-7890</li>
                    <li><i class="fas fa-envelope text-blue me-2"></i> support@pavelk.com</li>
                    <li><i class="fas fa-clock text-blue me-2"></i> Senin - Sabtu: 09:00 - 18:00 WIB</li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="pvl-footer-bottom text-secondary" style="font-size: 12px;">
            <div>
                &copy; <?= date('Y') ?> <span class="text-white fw-bold">PAVELK</span>. All Rights Reserved.
            </div>
            <div>
                Crafted for Supreme Performance
            </div>
        </div>
    </footer>

</div> <!-- End pvl-main -->

<!-- Bootstrap 5 Bundle with Popper JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom Application JS -->
<script src="<?= $base_url ?>/assets/js/main.js"></script>

</body>
</html>
