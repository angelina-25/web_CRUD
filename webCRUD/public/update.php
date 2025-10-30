<?php
// update.php - Form edit produk olahraga dengan prefill
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

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
            
            header("Location: index.php?msg=Produk berhasil diupdate&type=success");
            exit;
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
    <title>Edit: <?= htmlspecialchars($produk['nama_produk']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px; 
            min-height: 100vh;
        }
        .container { 
            max-width: 900px; 
            margin: 0 auto; 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2); 
        }
        h1 { 
            color: #667eea; 
            margin-bottom: 10px; 
            font-size: 32px;
        }
        .subtitle { 
            color: #666; 
            margin-bottom: 30px; 
        }
        .alert { 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 8px; 
        }
        .alert-error { 
            background: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb; 
        }
        .alert ul { 
            margin-left: 20px; 
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #333; 
        }
        input, textarea, select { 
            width: 100%; 
            padding: 12px 15px; 
            border: 2px solid #ddd; 
            border-radius: 8px; 
            font-size: 14px; 
            font-family: inherit; 
            transition: border-color 0.3s;
        }
        input:focus, textarea:focus, select:focus { 
            outline: none; 
            border-color: #ffc107; 
        }
        textarea { 
            min-height: 120px; 
            resize: vertical; 
        }
        select {
            background: white;
            cursor: pointer;
        }
        .button-group { 
            display: flex; 
            gap: 15px; 
            margin-top: 30px; 
        }
        .btn { 
            padding: 14px 30px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 15px; 
            font-weight: 600;
            text-decoration: none; 
            display: inline-block; 
            transition: all 0.3s; 
        }
        .btn-warning { 
            background: #ffc107; 
            color: #212529; 
        }
        .btn-warning:hover { 
            background: #e0a800; 
        }
        .btn-secondary { 
            background: #6c757d; 
            color: white; 
        }
        .btn-secondary:hover { 
            background: #5a6268; 
        }
        .required { 
            color: #dc3545; 
        }
        .id-info { 
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 25px; 
            color: #0277bd; 
            font-weight: 600; 
            border-left: 4px solid #667eea;
        }
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .button-group { 
                flex-direction: column; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Edit Produk</h1>
        <p class="subtitle">Perbarui informasi produk olahraga di bawah ini</p>

        <div class="id-info">📝 Mengedit produk ID: #<?= $produk['id'] ?> - <?= htmlspecialchars($produk['nama_produk']) ?></div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>❌ Error:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nama_produk">Nama Produk <span class="required">*</span></label>
                    <input type="text" id="nama_produk" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required>
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
                    <label for="merk">Merk <span class="required">*</span></label>
                    <input type="text" id="merk" name="merk" value="<?= htmlspecialchars($produk['merk']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="harga">Harga (Rp) <span class="required">*</span></label>
                    <input type="number" id="harga" name="harga" step="0.01" min="0" value="<?= htmlspecialchars($produk['harga']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="stok">Stok (Unit) <span class="required">*</span></label>
                    <input type="number" id="stok" name="stok" min="0" value="<?= htmlspecialchars($produk['stok']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="ukuran">Ukuran</label>
                    <input type="text" id="ukuran" name="ukuran" value="<?= htmlspecialchars($produk['ukuran']) ?>">
                </div>

                <div class="form-group full-width">
                    <label for="warna">Warna</label>
                    <input type="text" id="warna" name="warna" value="<?= htmlspecialchars($produk['warna']) ?>">
                </div>

                <div class="form-group full-width">
                    <label for="deskripsi">Deskripsi Produk <span class="required">*</span></label>
                    <textarea id="deskripsi" name="deskripsi" required><?= htmlspecialchars($produk['deskripsi']) ?></textarea>
                </div>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-warning">💾 Update Produk</button>
                <a href="detail.php?id=<?= $produk['id'] ?>" class="btn btn-secondary">↩️ Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>