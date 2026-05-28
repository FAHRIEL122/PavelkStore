/* ============================================
   PAVELK - PREMIUM INTERACTIVE APPLICATION JS
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // 1. MOBILE SIDEBAR TOGGLE
    // ============================================
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && sidebar && sidebarOverlay) {
        // Toggle Active Class
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        });

        // Close when clicking overlay backdrop
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
    }

    // ============================================
    // 2. DYNAMIC AUTO-DISMISS TOAST
    // ============================================
    const toast = document.getElementById('pvlToast');
    if (toast) {
        setTimeout(function() {
            toast.style.animation = 'fadeOut 0.5s ease forwards';
            setTimeout(function() {
                toast.remove();
            }, 500);
        }, 4000);
    }

    // ============================================
    // 3. PRODUCT DETAIL QUANTITY CONTROLLERS
    // ============================================
    const btnMinus = document.querySelector('.btn-qty-minus');
    const btnPlus = document.querySelector('.btn-qty-plus');
    const qtyInput = document.querySelector('.input-qty');

    if (qtyInput) {
        const maxVal = parseInt(qtyInput.getAttribute('max')) || 999;
        
        if (btnMinus) {
            btnMinus.addEventListener('click', function() {
                let currentVal = parseInt(qtyInput.value) || 1;
                if (currentVal > 1) {
                    qtyInput.value = currentVal - 1;
                }
            });
        }

        if (btnPlus) {
            btnPlus.addEventListener('click', function() {
                let currentVal = parseInt(qtyInput.value) || 1;
                if (currentVal < maxVal) {
                    qtyInput.value = currentVal + 1;
                }
            });
        }

        // Prevent typing non-numbers or values out of bounds
        qtyInput.addEventListener('change', function() {
            let currentVal = parseInt(qtyInput.value);
            if (isNaN(currentVal) || currentVal < 1) {
                qtyInput.value = 1;
            } else if (currentVal > maxVal) {
                qtyInput.value = maxVal;
            }
        });
    }

    // ============================================
    // 4. CHECKOUT CONFIRMATION DIALOG
    // ============================================
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic Client-Side Validation
            const nama = document.getElementById('nama_penerima').value.trim();
            const no_hp = document.getElementById('no_hp').value.trim();
            const alamat = document.getElementById('alamat').value.trim();

            if (!nama || !no_hp || !alamat) {
                showDynamicToast('error', 'Semua data pengiriman wajib diisi lengkap!');
                return;
            }

            // High-fidelity luxury modal notification before submitting
            const confirmCheckout = confirm("Konfirmasi Pesanan Premium Anda:\n\nApakah Anda yakin ingin memproses pesanan ini? Detail pengiriman dan total pembayaran sudah benar.");
            
            if (confirmCheckout) {
                // Submit form if confirmed
                checkoutForm.submit();
            }
        });
    }

    // Helper to generate dynamic toast notifications on-the-fly
    function showDynamicToast(type, message) {
        // Remove existing toast first
        const existing = document.getElementById('pvlToast');
        if (existing) existing.remove();

        const toastEl = document.createElement('div');
        toastEl.className = `pvl-toast ${type}`;
        toastEl.id = 'pvlToast';

        let icon = '<i class="fas fa-info-circle"></i>';
        if (type === 'success') icon = '<i class="fas fa-check-circle"></i>';
        if (type === 'error') icon = '<i class="fas fa-exclamation-circle"></i>';
        if (type === 'warning') icon = '<i class="fas fa-exclamation-triangle"></i>';

        toastEl.innerHTML = `
            <div class="pvl-toast-icon">${icon}</div>
            <div class="pvl-toast-content">
                <h5>${type.charAt(0).toUpperCase() + type.slice(1)}</h5>
                <p>${message}</p>
            </div>
            <button class="pvl-toast-close">&times;</button>
        `;

        document.body.appendChild(toastEl);

        toastEl.querySelector('.pvl-toast-close').addEventListener('click', function() {
            toastEl.remove();
        });

        setTimeout(function() {
            toastEl.style.animation = 'fadeOut 0.5s ease forwards';
            setTimeout(function() {
                toastEl.remove();
            }, 500);
        }, 4000);
    }
});
