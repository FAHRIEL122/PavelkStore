# PavelkStore

# 🏁 PAVELK - Premium Motor Racing Wheels E-Commerce

Aplikasi web e-commerce premium, modern, dan mewah khusus penjualan Velg Motor Racing. Dibangun menggunakan **PHP Native (Fullstack)** dengan integrasi **Bootstrap 5**, **Custom CSS**, **JavaScript Modern**, dan database **MySQL**.

---

## 💎 Identitas Brand & Desain
- **Merek**: PAVELK (`PVL.`)
- **Konsep**: Clean, Minimalist, Luxury Premium Brand
- **Palet Warna**:
  - 🖤 Hitam (`#000000` / `#0A0A0A` / `#111111`)
  - 🤍 Putih (`#FFFFFF` / `#CCCCCC`)
  - 💎 Biru Glowing Cyan (`#0DD8E6` / `rgb(13, 216, 230)`)
- **Typography**: Poppins & Montserrat (Google Fonts)
- **Desain UI/UX**: Sidebar Navigation Responsive, Glassmorphism elements, Smooth Hover Zoom, Custom shadows, Interactive quantity buttons, and Auto-dismiss notification toasts.

---

## 🛠️ Fitur Utama
1. **Sistem Autentikasi Dual-Role**:
   - Register akun user baru (password di-hash menggunakan `password_hash()`).
   - Login user biasa (`login.php`).
   - Login administrator terpisah (`admin/login.php`).
   - PHP Session Management yang aman.
2. **Halaman Customer**:
   - **Landing Page**: Hero section premium, visualisasi interaktif velg 3D-SVG, grid fitur unggulan, dan slider koleksi produk terpopuler.
   - **Katalog Produk**: Cari velg (`Search`) & navigasi halaman (`Pagination`).
   - **Detail Produk**: Deskripsi produk, spesifikasi teknis premium, live stock badge checker, dan quantity counter.
   - **Keranjang Belanja**: Tambah, update kuantitas dinamis, subtotasi otomatis, dan hapus item.
   - **Checkout**: Form pengiriman lengkap, rekapitulasi pembayaran gratis ongkir, dan notifikasi dialog konfirmasi pengiriman interaktif.
   - **Riwayat Pesanan**: Cek detail & status kirim (`pending`, `diproses`, `selesai`).
3. **Halaman Administrator (Admin Panel)**:
   - **Dashboard**: Statistik visual jumlah produk, total user, dan total pesanan masuk, serta rekap transaksi terbaru.
   - **CRUD Produk**: Tambah velg baru (dengan upload gambar), edit spesifikasi, update stok, dan hapus produk (auto-delete file gambar lama).
   - **Kelola Pesanan**: Tinjau alamat kirim penerima, detail belanja, dan ubah status pengiriman (`pending` ➡️ `diproses` ➡️ `selesai`).
   - **Kelola User**: Daftar database seluruh akun pelanggan terdaftar dan peran akses.

---

## 📂 Struktur Folder
```text
/pavelkstore
│
├── /admin/
│   ├── dashboard.php        # Dashboard admin
│   ├── login.php            # Login khusus administrator
│   ├── orders.php           # Kelola pesanan & status kirim
│   ├── products.php         # CRUD produk velg racing
│   └── users.php            # List database pelanggan
│
├── /assets/
│   ├── /css/
│   │   └── style.css        # Premium stylesheet custom
│   ├── /js/
│   │   └── main.js          # Skrip interaktif & konfirmasi checkout
│   └── /images/             # Uploaded/default gambar produk
│
├── /config/
│   └── config.php           # Koneksi PDO & fungsi pembantu (helper)
│
├── /database/
│   └── pavelk.sql           # Skema MySQL, relasi tabel & data dummy
│
├── /includes/
│   ├── header.php           # Header template HTML & Toast Notification
│   ├── sidebar.php          # Sidebar interaktif responsif
│   └── footer.php           # Footer, kontak, & script load
│
├── /pages/
│   ├── cart-process.php     # Proses add/update/delete keranjang
│   ├── cart.php             # Review keranjang belanja
│   ├── checkout-process.php # Proses transaksi database (MySQL Transaction)
│   ├── checkout.php         # Form input alamat kirim
│   ├── orders.php           # Riwayat pesanan user
│   ├── product-detail.php   # Detail & spesifikasi velg
│   └── products.php         # Katalog filter keyword & paging
│
├── index.php                # Landing page utama
├── login.php                # Login pelanggan
├── register.php             # Registrasi pelanggan
└── logout.php               # Hapus session & keluar
```

---

## ⚙️ Cara Menjalankan di Localhost (XAMPP / Laragon)

### 📌 Persiapan
1. Pastikan Anda sudah menginstal web server lokal seperti **XAMPP** atau **Laragon** di komputer Anda.

### 🔌 Langkah 1: Pindahkan Project ke Web Root
- **XAMPP**: Pindahkan folder `PavelkStore` ke dalam folder `C:\xampp\htdocs\` sehingga alamatnya menjadi `C:\xampp\htdocs\PavelkStore\`.
- **Laragon**: Pindahkan folder `PavelkStore` ke dalam folder `C:\laragon\www\` sehingga alamatnya menjadi `C:\laragon\www\PavelkStore\`.

### 🛢️ Langkah 2: Import Database MySQL
1. Buka browser dan ketik alamat: [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Buat database baru dengan nama **`pavelk`**.
3. Pilih database **`pavelk`**, lalu klik tab **Import** di bagian atas menu.
4. Klik **Choose File** / **Telusuri**, lalu pilih file SQL schema yang berada di: `PavelkStore/database/pavelk.sql`.
5. Scroll ke bawah dan klik tombol **Import** (atau **Go**).
6. Proses selesai! Tabel `users`, `products`, `cart`, `orders`, dan `order_items` beserta data dummy premium akan terbentuk secara otomatis.

### 🚀 Langkah 3: Jalankan Aplikasi
1. Nyalakan Apache dan MySQL di XAMPP Control Panel atau Laragon.
2. Buka browser Anda lalu ketik alamat berikut:
   - **Landing Page (Customer)**: [http://localhost/PavelkStore](http://localhost/PavelkStore)
   - **Login Administrator**: [http://localhost/PavelkStore/admin/login.php](http://localhost/PavelkStore/admin/login.php)

---

## 🔑 Akun Uji Coba (Dummy Account Credentials)

### 🧑‍💼 Akun Administrator (Admin Panel Access)
- **Email**: `admin@pavelk.com`
- **Password**: `admin123`

### 🧑 Akun Pelanggan (User Access)
- **Email**: `budi@gmail.com`
- **Password**: `user123`

*(Anda juga dapat mendaftarkan akun baru menggunakan menu **Register** pada halaman masuk).*

---
**PAVELK Premium Racing Wheels** — *Style meets supreme road performance.*
