-- database.sql - Database Toko Sport
-- Buat database
CREATE DATABASE IF NOT EXISTS toko_sport CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE toko_sport;

-- Buat tabel produk_olahraga
CREATE TABLE IF NOT EXISTS produk_olahraga (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(255) NOT NULL,
    kategori ENUM('Sepakbola', 'Basket', 'Badminton', 'Futsal', 'Voli', 'Tenis', 'Fitness', 'Renang', 'Lari', 'Aksesoris') NOT NULL,
    merk VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    stok INT DEFAULT 0,
    ukuran VARCHAR(50),
    warna VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert data sample produk olahraga
INSERT INTO produk_olahraga (nama_produk, kategori, merk, deskripsi, harga, stok, ukuran, warna) VALUES
('Sepatu Futsal Mercurial Vapor', 'Futsal', 'Nike', 'Sepatu futsal dengan teknologi grip maksimal untuk kontrol bola yang lebih baik di lapangan indoor', 1250000.00, 15, '40, 41, 42, 43', 'Hitam/Oranye'),
('Jersey Timnas Indonesia Home', 'Sepakbola', 'Mills', 'Jersey official timnas Indonesia kandang dengan bahan breathable dan teknologi quick dry', 350000.00, 45, 'S, M, L, XL', 'Merah/Putih'),
('Raket Badminton Astrox 99 Pro', 'Badminton', 'Yonex', 'Raket badminton profesional dengan frame ringan dan power maksimal untuk smash keras', 2850000.00, 8, 'Standard', 'Merah/Hitam'),
('Bola Basket Molten GG7X', 'Basket', 'Molten', 'Bola basket official size 7 untuk pertandingan profesional dengan grip kulit composite', 650000.00, 25, 'Size 7', 'Coklat/Krem'),
('Sepatu Lari Ultraboost 23', 'Lari', 'Adidas', 'Sepatu lari dengan teknologi Boost untuk energi return maksimal dan kenyamanan luar biasa', 2100000.00, 12, '39, 40, 41, 42, 43, 44', 'Hitam/Putih'),
('Raket Tenis Wilson Pro Staff', 'Tenis', 'Wilson', 'Raket tenis profesional dengan kontrol dan power seimbang untuk pemain intermediate-advance', 1850000.00, 10, 'Grip 3', 'Hitam/Merah'),
('Sepatu Basket LeBron 21', 'Basket', 'Nike', 'Sepatu basket signature LeBron James dengan cushioning responsif dan support pergelangan kaki', 2750000.00, 18, '41, 42, 43, 44, 45', 'Ungu/Gold'),
('Kacamata Renang Speedo Aquapulse', 'Renang', 'Speedo', 'Kacamata renang anti-fog dengan seal nyaman dan proteksi UV untuk latihan dan kompetisi', 285000.00, 40, 'One Size', 'Biru/Hitam'),
('Dumbell Set 20kg', 'Fitness', 'Kettler', 'Set dumbell adjustable 2.5kg hingga 20kg dengan grip anti-slip untuk home gym', 1650000.00, 20, '20kg', 'Hitam/Abu'),
('Bola Voli Mikasa MVA 200', 'Voli', 'Mikasa', 'Bola voli official FIVB untuk pertandingan internasional dengan kontrol dan stabilitas tinggi', 450000.00, 30, 'Standard', 'Kuning/Biru/Hijau'),
('Shuttlecock Yonex Aerosensa 50', 'Badminton', 'Yonex', 'Kok badminton premium dengan bulu angsa asli untuk durabilitas dan flight stability terbaik', 285000.00, 50, 'Speed 77', 'Putih'),
('Tas Olahraga Nike Brasilia', 'Aksesoris', 'Nike', 'Tas gym/olahraga ukuran medium dengan kompartemen sepatu terpisah dan bahan water-resistant', 425000.00, 35, 'Medium', 'Hitam'),
('Matras Yoga Premium 6mm', 'Fitness', 'Manduka', 'Matras yoga anti-slip dengan ketebalan 6mm untuk kenyamanan maksimal saat latihan', 650000.00, 28, '180x60cm', 'Ungu'),
('Pelindung Lutut Knee Pad Pro', 'Aksesoris', 'McDavid', 'Knee pad dengan teknologi compression untuk proteksi lutut saat olahraga high-impact', 320000.00, 42, 'M, L, XL', 'Hitam'),
('Botol Minum Olahraga 1L', 'Aksesoris', 'Thermos', 'Botol minum sport dengan insulasi ganda menjaga suhu 12 jam, BPA free dan leak-proof', 185000.00, 60, '1 Liter', 'Biru/Merah/Hitam');