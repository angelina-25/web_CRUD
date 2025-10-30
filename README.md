


## web_CRUD  
Aplikasi CRUD sederhana menggunakan PHP Native + MySQL dengan koneksi menggunakan PDO.

## 📋 Deskripsi  
Proyek ini merupakan contoh aplikasi web untuk melakukan operasi Create, Read, Update dan Delete (CRUD) pada sebuah tabel data, dibangun dengan PHP Native tanpa framework. Tujuannya sebagai latihan pemrograman web dengan struktur sederhana dan koneksi database yang aman menggunakan PDO.

## 🛠 Fitur  
- Menampilkan daftar data (Read)  
- Menambah data baru (Create)  
- Melihat detail data (Show)  
- Mengubah data yang sudah ada (Update)  
- Menghapus data (Delete)  
- Validasi input sederhana  
- Koneksi database menggunakan PDO  
- Struktur folder yang jelas untuk pemisahan logika tampilan dan aksi  

## 📁 Struktur Folder  
```

/web_CRUD/
│
├─ /public/         ← Folder untuk halaman yang diakses pengguna
│   ├── index.php    ← Daftar data / halaman utama
│   ├── create.php   ← Form tambah data
│   ├── edit.php     ← Form edit data
│   └── show.php     ← Halaman detail data
│
├─ /actions/        ← Folder untuk skrip aksi (store, update, delete)
│   ├── store.php
│   ├── update.php
│   └── delete.php
│
├─ /config/         ← Konfigurasi koneksi database
│   └── database.php
│
├─ /assets/         ← Folder untuk asset seperti CSS / JS (opsional)
│
└─ README.md        ← File ini

````

## 🎯 Kebutuhan Sistem  
- PHP versi **8.0** atau lebih baru  
- MySQL / MariaDB  
- Web server seperti XAMPP / Laragon / Apache + mod_php  
- Browser modern  
- Koneksi database sudah di-setting di file `config/database.php`

## 🚀 Cara Install & Jalankan  
1. Clone repository  
   ```bash
   git clone https://github.com/angelina-25/web_CRUD.git

2. Siapkan database di MySQL

```
   * Buat database baru: misal `webcrud_db`
   * Import tabel/data dummy jika ada skrip setup (atau buat tabel manual)
```
3. Ubah konfigurasi koneksi database di `config/database.php` sesuai pengaturan lokal: host, nama database, user, password
4. Jalankan web server dan akses folder `public/` pada browser (misalnya `http://localhost/web_CRUD/public/`)
5. Gunakan aplikasi: tambah data, edit, delete via antarmuka


## 🧩 Penggunaan

* Akses **index.php** untuk melihat daftar data
* Klik “Tambah Data” untuk menambahkan entri baru
* Klik “Edit” atau “Detail” untuk melihat atau mengubah entri
* Klik “Hapus” untuk menghapus entri (pastikan ada konfirmasi)

## 🔐 Keamanan & Validasi

* Koneksi menggunakan PDO untuk mencegah SQL Injection
* Input divalidasi dan disanitasi (gunakan `htmlspecialchars`, dsb)
* Redirect atau tampil pesan jika terjadi error atau aksi berhasil

## 📌 Catatan

* Proyek ini bersifat latihan dan sederhana; untuk project produksi, sebaiknya ditambahkan:

  * Pagination
  * Sistem login / autentikasi
  * Sanitasi dan validasi yang lebih kompleks
  * Proteksi terhadap CSRF
* Struktur folder dapat dikembangkan lebih lanjut agar modular dan mudah dipelihara

## 👤 Kontributor

* Angelina (pengembang utama)
* Belum ada kontributor lain saat ini

## Environment Config (Tambahan untuk README)

```ini
# .env example (manual config)
DB_HOST=localhost
DB_NAME=webcrud_db
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
```
Atau jika pakai `config/database.php`, tulis:
```php
<?php
return [
    "host" => "localhost",
    "database" => "webcrud_db",
    "username" => "root",
    "password" => "",
    "charset" => "utf8mb4"
];
```

## screenshot
<img width="2560" height="1440" alt="Screenshot 2025-10-30 162252" src="https://github.com/user-attachments/assets/d8dd1130-0df0-4964-aa7a-0fd429bb9d53" />
<img width="1600" height="1191" alt="Screenshot 2025-10-30 162456" src="https://github.com/user-attachments/assets/771d457b-555d-4a0c-9ff6-981e0338d738" />
<img width="1378" height="1165" alt="Screenshot 2025-10-30 162515" src="https://github.com/user-attachments/assets/7fd11353-d360-4e44-b321-229c03874028" />
<img width="1403" height="1224" alt="Screenshot 2025-10-30 162538" src="https://github.com/user-attachments/assets/4c3a05ea-eed8-4189-9987-30c28dbe5198" />
<img width="1161" height="1190" alt="Screenshot 2025-10-30 162621" src="https://github.com/user-attachments/assets/408caf44-a489-4bcf-9969-f5ac715bc334" />








