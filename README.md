# PavelkStore

# 🏁 PAVELK - E-Commerce Velg Motor Racing Premium

Aplikasi web e-commerce premium, modern, dan mewah untuk penjualan velg motor racing. Dibangun menggunakan **PHP Native (Fullstack)** dengan **Bootstrap 5**, **Custom CSS**, **JavaScript Modern**, dan database **MySQL**.

---

## 💎 Identitas Merek & Desain

- **Merek**: PAVELK (`PVL.`)
- **Konsep**: Clean, Minimalis, dan Premium
- **Palet Warna**:
  - Hitam (`#000000` / `#0A0A0A` / `#111111`)
  - Putih (`#FFFFFF` / `#CCCCCC`)
  - Biru Cyan Menyala (`#0DD8E6` / `rgb(13, 216, 230)`)
- **Tipografi**: Poppins & Montserrat (Google Fonts)
- **Desain UI/UX**: Navigasi sidebar responsif, elemen glassmorphism, efek hover zoom, custom shadow, tombol jumlah interaktif, dan notifikasi otomatis.

---

## 🛠️ Fitur Utama

### 1. Sistem Autentikasi Dua Peran
- Pendaftaran akun pengguna baru dengan hashing kata sandi menggunakan `password_hash()`.
- Login pengguna melalui `login.php`.
- Login administrator melalui `admin/login.php`.
- Manajemen sesi PHP yang aman.

### 2. Halaman Pelanggan
- **Halaman Utama**: Hero section premium, visualisasi velg 3D-SVG, grid fitur unggulan, dan slider produk populer.
- **Katalog Produk**: Pencarian velg dan navigasi halaman.
- **Detail Produk**: Deskripsi, spesifikasi teknis, status stok, dan pengatur jumlah produk.
- **Keranjang Belanja**: Menambah, mengubah jumlah, menghitung subtotal otomatis, dan menghapus item.
- **Checkout**: Form pengiriman, ringkasan pembayaran, dan dialog konfirmasi interaktif.
- **Riwayat Pesanan**: Melihat detail dan status pengiriman (`menunggu`, `diproses`, `selesai`).

### 3. Panel Administrator
- **Dashboard**: Statistik produk, pengguna, pesanan, dan transaksi terbaru.
- **CRUD Produk**: Menambah velg, mengunggah gambar, mengubah spesifikasi, memperbarui stok, dan menghapus produk.
- **Kelola Pesanan**: Melihat alamat penerima, detail belanja, dan mengubah status pengiriman.
- **Kelola Pengguna**: Melihat seluruh akun pelanggan dan peran akses.

---

## 📂 Struktur Folder

```text
/pavelkstore
│
├── /admin/
│   ├── dashboard.php        # Dashboard admin
│   ├── login.php            # Login administrator
│   ├── orders.php           # Kelola pesanan dan status pengiriman
│   ├── products.php         # CRUD produk velg racing
│   └── users.php            # Daftar pelanggan
│
├── /assets/
│   ├── /css/
│   │   └── style.css        # Stylesheet premium
│   ├── /js/
│   │   └── main.js          # Interaksi dan konfirmasi checkout
│   └── /images/             # Gambar produk
│
├── /config/
│   └── config.php           # Koneksi PDO dan fungsi bantuan
│
├── /database/
│   └── pavelk.sql           # Skema MySQL dan data contoh
│
├── /includes/
│   ├── header.php           # Template header dan notifikasi
│   ├── sidebar.php          # Sidebar responsif
│   └── footer.php           # Footer dan pemuatan script
│
├── /pages/
│   ├── cart-process.php     # Proses keranjang
│   ├── cart.php             # Keranjang belanja
│   ├── checkout-process.php # Proses transaksi database
│   ├── checkout.php         # Form alamat pengiriman
│   ├── orders.php           # Riwayat pesanan pengguna
│   ├── product-detail.php   # Detail dan spesifikasi velg
│   └── products.php         # Katalog produk
│
├── index.php                # Halaman utama
├── login.php                # Login pelanggan
├── register.php             # Pendaftaran pelanggan
└── logout.php               # Keluar dari akun
```

---

## ⚙️ Cara Menjalankan di Localhost

### Persiapan

Pastikan sudah terpasang web server lokal seperti **XAMPP** atau **Laragon**.

### Langkah 1: Pindahkan Proyek ke Web Root

**XAMPP:**
```text
C:\xampp\htdocs\PavelkStore\
```

**Laragon:**
```text
C:\laragon\www\PavelkStore\
```

### Langkah 2: Import Database MySQL

1. Buka `http://localhost/phpmyadmin`.
2. Buat database bernama **`pavelk`**.
3. Pilih database tersebut dan buka menu **Import**.
4. Pilih `PavelkStore/database/pavelk.sql`.
5. Jalankan proses import.
6. Tabel `users`, `products`, `cart`, `orders`, dan `order_items` akan dibuat.

### Langkah 3: Jalankan Aplikasi

1. Nyalakan Apache dan MySQL.
2. Buka:
   - Halaman utama: `http://localhost/PavelkStore`
   - Login administrator: `http://localhost/PavelkStore/admin/login.php`

---

## 🔑 Akun Uji Coba

### 🧑‍💼 Administrator
- **Email**: `admin@pavelk.com`
- **Kata sandi**: `admin123`

### 🧑 Pelanggan
- **Email**: `budi@gmail.com`
- **Kata sandi**: `user123`

Anda juga dapat membuat akun baru melalui menu **Daftar** pada halaman login.

---

**PAVELK Premium Racing Wheels** — *Gaya bertemu performa berkendara.*
