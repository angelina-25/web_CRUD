<?php
// detail.php - Halaman detail produk olahraga
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php?msg=ID tidak valid&type=error");
    exit;
}

// Ambil data
$sql = "SELECT * FROM produk_olahraga WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    header("Location: index.php?msg=Produk tidak ditemukan&type=error");
    exit;
}

$kategoriClass = 'kategori-' . strtolower($produk['kategori']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail: <?= htmlspecialchars($produk['nama_produk']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px; 
            min-height: 100vh;
        }
        .container { 
            max-width: 1000px; 
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
        .detail-card { 
            background: #f8f9fa; 
            padding: 30px; 
            border-radius: 12px; 
            border-left: 5px solid #667eea; 
        }
        .detail-row { 
            display: flex; 
            padding: 18px 0; 
            border-bottom: 1px solid #dee2e6; 
        }
        .detail-row:last-child { 
            border-bottom: none; 
        }
        .detail-label { 
            font-weight: 600; 
            color: #495057; 
            width: 220px; 
            flex-shrink: 0; 
        }
        .detail-value { 
            color: #212529; 
            flex: 1; 
        }
        .price { 
            font-size: 28px; 
            font-weight: 700; 
            color: #11998e; 
        }
        .stock { 
            display: inline-block; 
            padding: 8px 20px; 
            background: #e3f2fd; 
            color: #0277bd; 
            border-radius: 20px; 
            font-weight: 600; 
            font-size: 16px;
        }
        .kategori-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .kategori-sepakbola { background: #e8f5e9; color: #2e7d32; }
        .kategori-basket { background: #fff3e0; color: #e65100; }
        .kategori-badminton { background: #fce4ec; color: #c2185b; }
        .kategori-futsal { background: #e0f2f1; color: #00695c; }
        .kategori-voli { background: #f3e5f5; color: #6a1b9a; }
        .kategori-tenis { background: #e1f5fe; color: #0277bd; }
        .kategori-fitness { background: #fff9c4; color: #f57f17; }
        .kategori-renang { background: #e0f7fa; color: #006064; }
        .kategori-lari { background: #fbe9e7; color: #bf360c; }
        .kategori-aksesoris { background: #f1f8e9; color: #558b2f; }
        .button-group { 
            display: flex; 
            gap: 15px; 
            margin-top: 30px; 
            flex-wrap: wrap; 
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
        .btn-secondary { 
            background: #6c757d; 
            color: white; 
        }
        .btn-secondary:hover { 
            background: #5a6268; 
        }
        .btn-warning { 
            background: #ffc107; 
            color: #212529; 
        }
        .btn-warning:hover { 
            background: #e0a800; 
        }
        .btn-danger { 
            background: #dc3545; 
            color: white; 
        }
        .btn-danger:hover { 
            background: #c82333; 
        }
        .timestamp { 
            font-size: 13px; 
            color: #6c757d; 
        }
        .merk-highlight {
            font-size: 18px;
            font-weight: 700;
            color: #764ba2;
        }
        @media (max-width: 768px) {
            .detail-row { 
                flex-direction: column; 
                gap: 8px; 
            }
            .detail-label { 
                width: 100%; 
            }
            .button-group { 
                flex-direction: column; 
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Detail Produk</h1>
        <p class="subtitle">Informasi lengkap tentang produk olahraga</p>

        <div class="detail-card">
            <div class="detail-row">
                <div class="detail-label">ID Produk:</div>
                <div class="detail-value"><strong>#<?= $produk['id'] ?></strong></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Nama Produk:</div>
                <div class="detail-value"><strong style="font-size: 20px;"><?= htmlspecialchars($produk['nama_produk']) ?></strong></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Kategori:</div>
                <div class="detail-value">
                    <span class="kategori-badge <?= $kategoriClass ?>"><?= $produk['kategori'] ?></span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Merk:</div>
                <div class="detail-value">
                    <span class="merk-highlight"><?= htmlspecialchars($produk['merk']) ?></span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Deskripsi:</div>
                <div class="detail-value"><?= nl2br(htmlspecialchars($produk['deskripsi'])) ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Harga:</div>
                <div class="detail-value">
                    <div class="price">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Stok Tersedia:</div>
                <div class="detail-value">
                    <span class="stock"><?= $produk['stok'] ?> unit</span>
                </div>
            </div>

            <?php if ($produk['ukuran']): ?>
            <div class="detail-row">
                <div class="detail-label">Ukuran:</div>
                <div class="detail-value"><strong><?= htmlspecialchars($produk['ukuran']) ?></strong></div>
            </div>
            <?php endif; ?>

            <?php if ($produk['warna']): ?>
            <div class="detail-row">
                <div class="detail-label">Warna:</div>
                <div class="detail-value"><strong><?= htmlspecialchars($produk['warna']) ?></strong></div>
            </div>
            <?php endif; ?>

            <div class="detail-row">
                <div class="detail-label">Tanggal Ditambahkan:</div>
                <div class="detail-value">
                    <div><?= date('l, d F Y', strtotime($produk['created_at'])) ?></div>
                    <div class="timestamp">⏰ <?= date('H:i:s', strtotime($produk['created_at'])) ?> WIB</div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Terakhir Diupdate:</div>
                <div class="detail-value">
                    <div><?= date('l, d F Y', strtotime($produk['updated_at'])) ?></div>
                    <div class="timestamp">⏰ <?= date('H:i:s', strtotime($produk['updated_at'])) ?> WIB</div>
                </div>
            </div>
        </div>

        <div class="button-group">
            <a href="index.php" class="btn btn-secondary">↩️ Kembali ke Katalog</a>
            <a href="update.php?id=<?= $produk['id'] ?>" class="btn btn-warning">✏️ Edit Produk</a>
            <a href="delete.php?id=<?= $produk['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')">🗑️ Hapus Produk</a>
        </div>
    </div>
</body>
</html>