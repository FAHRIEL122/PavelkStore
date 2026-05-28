-- ============================================
-- DATABASE: pavelk
-- E-Commerce Velk Motor Racing - PAVELK
-- ============================================

CREATE DATABASE IF NOT EXISTS pavelk;
USE pavelk;

-- ============================================
-- TABEL USERS
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABEL PRODUCTS
-- ============================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(200) NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255) DEFAULT 'default.jpg',
    stok INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABEL CART
-- ============================================
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    qty INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABEL ORDERS
-- ============================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nama_penerima VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'diproses', 'selesai') DEFAULT 'pending',
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABEL ORDER ITEMS
-- ============================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    qty INT NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- DATA DUMMY: PRODUCTS (Velg Motor Racing)
-- ============================================
INSERT INTO products (nama, harga, deskripsi, gambar, stok) VALUES 
('Velg Racing Axio CB150R', 1850000.00, 'Velg racing Axio untuk Honda CB150R dengan desain palang 5 premium. Material alloy berkualitas tinggi dengan finishing chrome polish yang memukau. Ringan, kuat, dan tahan lama. Cocok untuk daily use maupun modifikasi racing.', 'velg1.jpg', 25),
('Velg Power Vario 160', 1650000.00, 'Velg Power untuk Honda Vario 160 dengan model Y-Spoke racing. Dibuat dari bahan aluminium alloy grade A yang ringan namun super kuat. Finishing matte black dengan aksen gold yang elegan.', 'velg2.jpg', 30),
('Velg Rossi NMAX Aerox', 2100000.00, 'Velg Rossi WR series untuk Yamaha NMAX/Aerox. Desain multi-spoke futuristik dengan teknologi forged alloy. Tampilan sporty premium dengan pilihan warna titanium grey. Presisi tinggi dan balancing sempurna.', 'velg3.jpg', 20),
('Velg TDR Sport PCX 160', 2350000.00, 'Velg TDR Sport series untuk Honda PCX 160. Model 6 palang dengan desain agresif dan aerodinamis. Material forged aluminum dengan finishing two-tone black red. Performa racing dengan tampilan mewah.', 'velg4.jpg', 15),
('Velg RCB Ninja ZX-25R', 3200000.00, 'Velg Racing Boy (RCB) premium untuk Kawasaki Ninja ZX-25R. Desain 10 palang double layer dengan teknologi flow-forming. Material super ringan dengan ketahanan maksimal. Warna galaxy blue metallic eksklusif.', 'velg5.jpg', 10),
('Velg Chemco R15 V4', 1950000.00, 'Velg Chemco racing untuk Yamaha R15 V4. Model twisted spoke yang aerodinamis dengan finishing brushed silver. Teknologi gravity casting premium untuk keseimbangan sempurna di kecepatan tinggi.', 'velg6.jpg', 18);
