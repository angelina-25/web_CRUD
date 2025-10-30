<?php
// index.php - Halaman utama Toko Sport dengan Read, Keyword Search, dan Pagination
require_once 'config.php'; // PASTIKAN FILE INI TERSEDIA DAN BERISI KONEKSI PDO ($pdo)

// PASTIKAN FUNGSI SANITIZE TERSEDIA
if (!function_exists('sanitize')) {
    function sanitize($data) {
        // Membersihkan input dari spasi, slash, dan mengubah karakter khusus menjadi entitas HTML
        return htmlspecialchars(stripslashes(trim($data)));
    }
}

// ====================================================================
// 1. PENGATURAN PAGINATION
// ====================================================================
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
if ($page < 1) $page = 1; // Mencegah halaman negatif

// ====================================================================
// 2. FITUR SEARCH (Keyword Tunggal & Persiapan Binding)
// ====================================================================

$keyword = trim(sanitize($_GET['keyword'] ?? ''));
$where = '';

// Array untuk parameter binding pencarian
$params = [];
$isSearching = false;

if ($keyword !== '') {
    // Menggunakan named placeholder berbeda (:k1, :k2, :k3) untuk setiap kolom
    // karena driver PDO tertentu (seperti MySQL) mungkin tidak mendukung 
    // reuse placeholder yang sama dalam satu query.
    $where = "WHERE nama_produk LIKE :k1 OR merk LIKE :k2 OR deskripsi LIKE :k3";
    
    // Nilai binding (ditambahkan wildcards %)
    $search_value = "%$keyword%";
    $params[':k1'] = $search_value;
    $params[':k2'] = $search_value;
    $params[':k3'] = $search_value;
    $isSearching = true;
}

// ====================================================================
// 3. HITUNG TOTAL DATA (Dengan Binding Dinamis)
// ====================================================================

$countSql = "SELECT COUNT(*) FROM produk_olahraga $where";
$countStmt = $pdo->prepare($countSql);

if ($isSearching) {
    // Binding parameter pencarian menggunakan loop
    foreach ($params as $placeholder => $value) {
        $countStmt->bindValue($placeholder, $value);
    }
}

try {
    $countStmt->execute();
    $totalData = $countStmt->fetchColumn();
    $totalPages = ceil($totalData / $limit);
} catch (PDOException $e) {
    // Handle error jika query gagal
    die("Error menghitung data: " . $e->getMessage());
}

// Pastikan page tidak melebihi total halaman (jika mencari)
if ($page > $totalPages && $totalPages > 0) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}

// ====================================================================
// 4. AMBIL DATA UTAMA (Dengan Binding Dinamis & Pagination)
// ====================================================================

$sql = "SELECT * FROM produk_olahraga $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);

// 1. Binding parameter pencarian (jika ada)
if ($isSearching) {
    // Binding parameter pencarian menggunakan loop
    foreach ($params as $placeholder => $value) {
        $stmt->bindValue($placeholder, $value);
    }
}

// 2. Binding parameter LIMIT dan OFFSET (Wajib, harus INT)
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

try {
    $stmt->execute();
    $produk = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error mengambil data produk: " . $e->getMessage());
}

// Pesan sukses/error dari operasi lain (create, update, delete)
$message = isset($_GET['msg']) ? sanitize($_GET['msg']) : '';
$msgType = isset($_GET['type']) ? sanitize($_GET['type']) : '';

// Daftar kategori (untuk tampilan badge di tabel)
$kategoriList = ['Sepakbola', 'Basket', 'Badminton', 'Futsal', 'Voli', 'Tenis', 'Fitness', 'Renang', 'Lari', 'Aksesoris'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚽ Toko Sport - Perlengkapan Olahraga Lengkap</title>
    <style>
        /* CSS yang sudah Anda sediakan */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px; 
            min-height: 100vh;
        }
        .container { 
            max-width: 1400px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.2); 
        }
        .header { 
            text-align: center; 
            margin-bottom: 40px; 
            padding-bottom: 20px; 
            border-bottom: 3px solid #667eea; 
        }
        h1 { 
            color: #667eea; 
            margin-bottom: 10px; 
            font-size: 36px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .subtitle { 
            color: #666; 
            font-size: 16px; 
        }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .filter-bar { 
            display: flex; 
            gap: 15px; 
            margin-bottom: 25px; 
            flex-wrap: wrap; 
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            justify-content: space-between;
            align-items: center;
        }
        .search-box { 
            display: flex; 
            gap: 10px; 
            min-width: 400px; 
        }
        .search-box input, select { 
            padding: 12px 15px; 
            border: 2px solid #ddd; 
            border-radius: 8px; 
            font-size: 14px; 
        }
        .search-box input[type="text"] { flex: 1; }
        .search-box input:focus, select:focus { 
            outline: none; 
            border-color: #667eea; 
        }
        .search-box select { display: none; } 
        .btn { 
            padding: 12px 25px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 14px; 
            font-weight: 600;
            text-decoration: none; 
            display: inline-block; 
            transition: all 0.3s; 
        }
        .btn-primary { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
        }
        .btn-primary:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-success { 
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); 
            color: white; 
        }
        .btn-success:hover { 
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(17, 153, 142, 0.4);
        }
        .btn-info { 
            background: #17a2b8; 
            color: white; 
        }
        .btn-info:hover { 
            background: #138496; 
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
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 25px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        thead { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
        }
        th, td { 
            padding: 15px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }
        th { 
            font-weight: 600; 
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        tbody tr:hover { 
            background: #f8f9fa; 
        }
        .actions { 
            display: flex; 
            gap: 8px; 
        }
        .pagination { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            gap: 10px; 
            flex-wrap: wrap; 
        }
        .pagination a, .pagination span { 
            padding: 10px 15px; 
            border: 2px solid #667eea; 
            border-radius: 8px; 
            text-decoration: none; 
            color: #667eea; 
            font-weight: 600;
            transition: all 0.3s;
        }
        .pagination a:hover { 
            background: #667eea; 
            color: white; 
            transform: translateY(-2px);
        }
        .pagination .active { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border-color: #667eea; 
        }
        .no-data { 
            text-align: center; 
            padding: 60px; 
            color: #999; 
        }
        .no-data h3 { 
            margin-bottom: 10px;
            font-size: 24px;
        }
        .price { 
            font-weight: 700; 
            color: #11998e; 
            font-size: 16px;
        }
        .stock { 
            display: inline-block; 
            padding: 4px 12px; 
            background: #e3f2fd; 
            color: #0277bd; 
            border-radius: 15px; 
            font-size: 13px; 
            font-weight: 600;
        }
        .kategori-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        /* Style untuk badge kategori */
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
        .merk { 
            font-weight: 600; 
            color: #764ba2;
            font-size: 13px;
        }
        @media (max-width: 768px) {
            .filter-bar { flex-direction: column; align-items: stretch; }
            .search-box { min-width: 100%; }
            table { font-size: 12px; }
            th, td { padding: 8px; }
            .actions { flex-direction: row; }
            h1 { font-size: 24px; }
            .btn-success { margin-top: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚽ TOKO SPORT INDONESIA</h1>
            <p class="subtitle">Perlengkapan Olahraga Berkualitas dengan Harga Terbaik</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $msgType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="filter-bar">
            <form class="search-box" method="GET" action="">
                <input type="text" name="keyword" placeholder="🔍 Cari produk, merk, atau deskripsi..." value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit" class="btn btn-primary">Cari</button>
                <?php if ($keyword): ?>
                    <a href="index.php" class="btn btn-info">Reset</a>
                <?php endif; ?>
            </form>
            <a href="create.php" class="btn btn-success">+ Tambah Produk</a>
        </div>

        <?php if (count($produk) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th width="4%">No</th>
                        <th width="20%">Produk</th>
                        <th width="10%">Kategori</th>
                        <th width="10%">Merk</th>
                        <th width="26%">Deskripsi</th>
                        <th width="10%">Harga</th>
                        <th width="7%">Stok</th>
                        <th width="13%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = $offset + 1;
                    foreach ($produk as $item): 
                        // Membuat class CSS kategori berdasarkan nama kategori
                        $kategoriClass = 'kategori-' . strtolower(str_replace(' ', '', $item['kategori']));
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($item['nama_produk']) ?></strong></td>
                            <td><span class="kategori-badge <?= $kategoriClass ?>"><?= htmlspecialchars($item['kategori']) ?></span></td>
                            <td><span class="merk"><?= htmlspecialchars($item['merk']) ?></span></td>
                            <td><?= htmlspecialchars(substr($item['deskripsi'], 0, 70)) . (strlen($item['deskripsi']) > 70 ? '...' : '') ?></td>
                            <td class="price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                            <td><span class="stock"><?= $item['stok'] ?></span></td>
                            <td>
                                <div class="actions">
                                    <a href="detail.php?id=<?= $item['id'] ?>" class="btn btn-info" title="Detail">👁️</a>
                                    <a href="update.php?id=<?= $item['id'] ?>" class="btn btn-warning" title="Edit">✏️</a>
                                    <a href="delete.php?id=<?= $item['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')" title="Hapus">🗑️</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php 
                // BASE URL untuk pagination hanya mempertahankan parameter keyword
                $keyword_param = $keyword ? '&keyword=' . urlencode($keyword) : '';
                
                // Menghindari tampilan pagination jika hanya 1 halaman
                if ($totalPages > 1):
                ?>
                    
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?><?= $keyword_param ?>">← Previous</a>
                    <?php endif; ?>

                    <?php 
                    // Tampilkan hanya beberapa nomor halaman di sekitar halaman aktif
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);

                    if ($start > 1) {
                        echo '<a href="?page=1' . $keyword_param . '">1</a>';
                        if ($start > 2) echo '<span>...</span>';
                    }

                    for ($i = $start; $i <= $end; $i++): 
                    ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?><?= $keyword_param ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php 
                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) echo '<span>...</span>';
                        echo '<a href="?page=' . $totalPages . $keyword_param . '">' . $totalPages . '</a>';
                    }
                    ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?><?= $keyword_param ?>">Next →</a>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="no-data">
                <h3>📭 Produk Tidak Ditemukan</h3>
                <?php if ($isSearching): ?>
                    <p>Tidak ada produk yang cocok dengan kata kunci **"<?= htmlspecialchars($keyword) ?>"**.</p>
                <?php else: ?>
                    <p>Silakan tambah produk baru menggunakan tombol "Tambah Produk".</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>