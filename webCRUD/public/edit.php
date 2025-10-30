<?php
// edit.php - Alternative edit page dengan inline editing style
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];
$success = false;

if ($id <= 0) {
    header("Location: index.php?msg=ID tidak valid&type=error");
    exit;
}

// Ambil data untuk prefill form
$sql = "SELECT * FROM produk_olahraga WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    header("Location: index.php?msg=Produk tidak ditemukan&type=error");
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_produk = sanitize($_POST['nama_produk'] ?? '');
    $kategori = sanitize($_POST['kategori'] ?? '');
    $merk = sanitize($_POST['merk'] ?? '');
    $deskripsi = sanitize($_POST['deskripsi'] ?? '');
    $harga = sanitize($_POST['harga'] ?? '');
    $stok = sanitize($_POST['stok'] ?? '');
    $ukuran = sanitize($_POST['ukuran'] ?? '');
    $warna = sanitize($_POST['warna'] ?? '');

    // Validasi
    if ($error = validate($nama_produk, 'Nama Produk')) $errors[] = $error;
    if ($error = validate($kategori, 'Kategori')) $errors[] = $error;
    if ($error = validate($merk, 'Merk')) $errors[] = $error;
    if ($error = validate($deskripsi, 'Deskripsi')) $errors[] = $error;
    if ($error = validate($harga, 'Harga')) $errors[] = $error;
    if ($error = validate($stok, 'Stok')) $errors[] = $error;

    if (!is_numeric($harga) || $harga < 0) {
        $errors[] = "Harga harus berupa angka positif";
    }
    if (!is_numeric($stok) || $stok < 0 || $stok != (int)$stok) {
        $errors[] = "Stok harus berupa bilangan bulat positif";
    }

    if (empty($errors)) {
        try {
            $sql = "UPDATE produk_olahraga SET nama_produk = :nama_produk, kategori = :kategori, merk = :merk, 
                    deskripsi = :deskripsi, harga = :harga, stok = :stok, ukuran = :ukuran, warna = :warna 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nama_produk' => $nama_produk,
                ':kategori' => $kategori,
                ':merk' => $merk,
                ':deskripsi' => $deskripsi,
                ':harga' => $harga,
                ':stok' => $stok,
                ':ukuran' => $ukuran,
                ':warna' => $warna,
                ':id' => $id
            ]);
            
            $success = true;
            // Update produk data untuk display
            $produk['nama_produk'] = $nama_produk;
            $produk['kategori'] = $kategori;
            $produk['merk'] = $merk;
            $produk['deskripsi'] = $deskripsi;
            $produk['harga'] = $harga;
            $produk['stok'] = $stok;
            $produk['ukuran'] = $ukuran;
            $produk['warna'] = $warna;
            
        } catch (PDOException $e) {
            $errors[] = "Gagal mengupdate data: " . $e->getMessage();
        }
    } else {
        // Update $produk dengan data POST untuk prefill
        $produk['nama_produk'] = $nama_produk;
        $produk['kategori'] = $kategori;
        $produk['merk'] = $merk;
        $produk['deskripsi'] = $deskripsi;
        $produk['harga'] = $harga;
        $produk['stok'] = $stok;
        $produk['ukuran'] = $ukuran;
        $produk['warna'] = $warna;
    }
}

$kategoriList = ['Sepakbola', 'Basket', 'Badminton', 'Futsal', 'Voli', 'Tenis', 'Fitness', 'Renang', 'Lari', 'Aksesoris'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk: <?= htmlspecialchars($produk['nama_produk']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px; 
            min-height: 100vh;
        }
        .container { 
            max-width: 1100px; 
            margin: 0 auto; 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2); 
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        .header-info h1 { 
            color: #667eea; 
            margin-bottom: 5px; 
            font-size: 32px;
        }
        .header-info .subtitle { 
            color: #666; 
            font-size: 14px;
        }
        .product-id {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 18px;
        }
        .alert { 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 8px; 
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-error { 
            background: #f8d7da; 
            color: #721c24; 
            border-left: 5px solid #dc3545; 
        }
        .alert-success { 
            background: #d4edda; 
            color: #155724; 
            border-left: 5px solid #28a745; 
        }
        .alert ul { 
            margin-left: 20px; 
        }
        .form-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            border: 2px solid #e9ecef;
        }
        .form-section h3 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        .form-group:last-child {
            margin-bottom: 0;
        }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #333; 
            font-size: 14px;
        }
        input, textarea, select { 
            width: 100%; 
            padding: 12px 15px; 
            border: 2px solid #ddd; 
            border-radius: 8px; 
            font-size: 14px; 
            font-family: inherit; 
            transition: all 0.3s;
        }
        input:focus, textarea:focus, select:focus { 
            outline: none; 
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        textarea { 
            min-height: 120px; 
            resize: vertical; 
        }
        select {
            background: white;
            cursor: pointer;
        }
        .input-group {
            position: relative;
        }
        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        .price-input {
            padding-left: 40px !important;
        }
        .price-prefix {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 600;
            color: #666;
        }
        .button-group { 
            display: flex; 
            gap: 15px; 
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
        }
        .btn { 
            padding: 14px 30px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 15px; 
            font-weight: 600;
            text-decoration: none; 
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s; 
        }
        .btn-warning { 
            background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%); 
            color: white;
            flex: 1;
        }
        .btn-warning:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.4);
        }
        .btn-secondary { 
            background: #6c757d; 
            color: white; 
        }
        .btn-secondary:hover { 
            background: #5a6268; 
        }
        .btn-info { 
            background: #17a2b8; 
            color: white; 
        }
        .btn-info:hover { 
            background: #138496; 
        }
        .required { 
            color: #dc3545; 
        }
        .char-count {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
            text-align: right;
        }
        .full-width {
            grid-column: 1 / -1;
        }
        @media (max-width: 768px) {
            .form-container {
                grid-template-columns: 1fr;
            }
            .header-section {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .button-group { 
                flex-direction: column; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <div class="header-info">
                <h1>✏️ Edit Produk Olahraga</h1>
                <p class="subtitle">Perbarui informasi produk dengan form di bawah ini</p>
            </div>
            <div class="product-id">ID: #<?= $produk['id'] ?></div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>✅ Berhasil!</strong> Produk berhasil diupdate. Data telah tersimpan di database.
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>❌ Terdapat Error:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="editForm">
            <div class="form-container">
                <!-- Informasi Produk -->
                <div class="form-section">
                    <h3>📦 Informasi Produk</h3>
                    
                    <div class="form-group">
                        <label for="nama_produk">Nama Produk <span class="required">*</span></label>
                        <input type="text" id="nama_produk" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required maxlength="255">
                        <div class="char-count"><span id="nama-count"><?= strlen($produk['nama_produk']) ?></span>/255</div>
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori <span class="required">*</span></label>
                        <select id="kategori" name="kategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategoriList as $kat): ?>
                                <option value="<?= $kat ?>" <?= $produk['kategori'] === $kat ? 'selected' : '' ?>><?= $kat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="merk">Merk/Brand <span class="required">*</span></label>
                        <input type="text" id="merk" name="merk" value="<?= htmlspecialchars($produk['merk']) ?>" required placeholder="Contoh: Nike, Adidas, Yonex">
                    </div>
                </div>

                <!-- Detail & Spesifikasi -->
                <div class="form-section">
                    <h3>🔍 Detail & Spesifikasi</h3>
                    
                    <div class="form-group">
                        <label for="harga">Harga Jual (Rp) <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="price-prefix">Rp</span>
                            <input type="number" id="harga" name="harga" class="price-input" step="0.01" min="0" value="<?= htmlspecialchars($produk['harga']) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="stok">Stok Tersedia <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="number" id="stok" name="stok" min="0" value="<?= htmlspecialchars($produk['stok']) ?>" required>
                            <span class="input-icon">unit</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ukuran">Ukuran/Size</label>
                        <input type="text" id="ukuran" name="ukuran" value="<?= htmlspecialchars($produk['ukuran']) ?>" placeholder="Contoh: 40, 41, 42 atau S, M, L, XL">
                    </div>

                    <div class="form-group">
                        <label for="warna">Warna</label>
                        <input type="text" id="warna" name="warna" value="<?= htmlspecialchars($produk['warna']) ?>" placeholder="Contoh: Hitam/Putih, Merah">
                    </div>
                </div>

                <!-- Deskripsi Produk -->
                <div class="form-section full-width">
                    <h3>📝 Deskripsi Produk</h3>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Lengkap <span class="required">*</span></label>
                        <textarea id="deskripsi" name="deskripsi" required placeholder="Jelaskan fitur, spesifikasi, dan keunggulan produk secara detail..."><?= htmlspecialchars($produk['deskripsi']) ?></textarea>
                        <div class="char-count"><span id="desc-count"><?= strlen($produk['deskripsi']) ?></span> karakter</div>
                    </div>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-warning">
                    <span>💾</span> Update Produk
                </button>
                <a href="detail.php?id=<?= $produk['id'] ?>" class="btn btn-info">
                    <span>👁️</span> Lihat Detail
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <span>↩️</span> Kembali ke Katalog
                </a>
            </div>
        </form>
    </div>

    <script>
        // Character counter untuk nama produk
        document.getElementById('nama_produk').addEventListener('input', function() {
            document.getElementById('nama-count').textContent = this.value.length;
        });

        // Character counter untuk deskripsi
        document.getElementById('deskripsi').addEventListener('input', function() {
            document.getElementById('desc-count').textContent = this.value.length;
        });

        // Format harga dengan ribuan
        document.getElementById('harga').addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });

        // Konfirmasi sebelum submit
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const confirmed = confirm('Apakah Anda yakin ingin menyimpan perubahan ini?');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>