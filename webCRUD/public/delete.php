<?php
// delete.php - Proses hapus produk dengan konfirmasi
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: index.php?msg=ID tidak valid&type=error");
    exit;
}

// Cek apakah produk ada
$sql = "SELECT nama_produk, kategori FROM produk_olahraga WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch();

if (!$produk) {
    header("Location: index.php?msg=Produk tidak ditemukan&type=error");
    exit;
}

// Proses hapus jika konfirmasi
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    try {
        $sql = "DELETE FROM produk_olahraga WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        header("Location: index.php?msg=Produk berhasil dihapus&type=success");
        exit;
    } catch (PDOException $e) {
        header("Location: index.php?msg=Gagal menghapus produk: " . urlencode($e->getMessage()) . "&type=error");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Hapus Produk</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
        }
        .container { 
            max-width: 600px; 
            background: white; 
            padding: 50px; 
            border-radius: 15px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2); 
            text-align: center; 
        }
        .icon { 
            font-size: 80px; 
            margin-bottom: 25px; 
            animation: shake 0.5s;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        h1 { 
            color: #dc3545; 
            margin-bottom: 20px; 
            font-size: 28px;
        }
        .message { 
            color: #666; 
            margin-bottom: 15px; 
            font-size: 16px; 
        }
        .produk-info { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 10px; 
            margin: 25px 0; 
            border-left: 5px solid #667eea;
        }
        .produk-name { 
            font-weight: 700; 
            color: #333; 
            font-size: 20px; 
            margin-bottom: 10px;
        }
        .produk-kategori {
            display: inline-block;
            padding: 6px 15px;
            background: #e3f2fd;
            color: #0277bd;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 600;
        }
        .warning { 
            background: #fff3cd; 
            color: #856404; 
            padding: 20px; 
            border-radius: 10px; 
            border-left: 5px solid #ffc107; 
            margin: 25px 0; 
            text-align: left; 
        }
        .warning strong {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .button-group { 
            display: flex; 
            gap: 15px; 
            margin-top: 35px; 
            justify-content: center; 
        }
        .btn { 
            padding: 15px 35px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 15px; 
            text-decoration: none; 
            display: inline-block; 
            transition: all 0.3s; 
            font-weight: 600;
        }
        .btn-danger { 
            background: #dc3545; 
            color: white; 
        }
        .btn-danger:hover { 
            background: #c82333; 
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        .btn-secondary { 
            background: #6c757d; 
            color: white; 
        }
        .btn-secondary:hover { 
            background: #5a6268; 
            transform: translateY(-3px);
        }
        @media (max-width: 768px) {
            .button-group { 
                flex-direction: column; 
            }
            .container {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⚠️</div>
        <h1>Konfirmasi Penghapusan</h1>
        <p class="message">Anda yakin ingin menghapus produk berikut?</p>
        
        <div class="produk-info">
            <div class="produk-name">
                📦 <?= htmlspecialchars($produk['nama_produk']) ?>
            </div>
            <span class="produk-kategori"><?= htmlspecialchars($produk['kategori']) ?></span>
        </div>

        <div class="warning">
            <strong>⚠️ Perhatian!</strong>
            Produk yang dihapus tidak dapat dikembalikan. Pastikan Anda sudah yakin sebelum melanjutkan proses penghapusan ini.
        </div>

        <div class="button-group">
            <a href="delete.php?id=<?= $id ?>&confirm=yes" class="btn btn-danger">🗑️ Ya, Hapus Produk</a>
            <a href="index.php" class="btn btn-secondary">↩️ Batal</a>
        </div>
    </div>
</body>
</html>