<?php
error_reporting(E_ALL);
include_once 'koneksi.php';

$error = "";

if (isset($_POST['submit'])) {
    $nama       = trim($_POST['nama']);
    $kategori   = $_POST['kategori'];
    $harga_jual = (int)$_POST['harga_jual'];
    $harga_beli = (int)$_POST['harga_beli'];
    $stok       = (int)$_POST['stok'];
    $gambar     = null;

    if ($nama === "") {
        $error = "Nama barang tidak boleh kosong.";
    } else {
        // Proses upload gambar (kalau ada file yang dipilih)
        $file_gambar = $_FILES['file_gambar'];
        if ($file_gambar['error'] == 0) {
            $ext = strtolower(pathinfo($file_gambar['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $filename = time() . '_' . str_replace(' ', '_', basename($file_gambar['name']));
                $destination = __DIR__ . '/gambar/' . $filename;
                if (move_uploaded_file($file_gambar['tmp_name'], $destination)) {
                    $gambar = 'gambar/' . $filename;
                }
            } else {
                $error = "File gambar harus berformat jpg, png, gif, atau webp.";
            }
        }

        if ($error === "") {
            // Prepared statement supaya aman dari SQL injection
            $stmt = mysqli_prepare($conn,
                "INSERT INTO data_barang (nama, kategori, harga_jual, harga_beli, stok, gambar)
                 VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssiiis", $nama, $kategori, $harga_jual, $harga_beli, $stok, $gambar);
            mysqli_stmt_execute($stmt);
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link href="style.css" rel="stylesheet" type="text/css" />
    <title>Tambah Barang</title>
</head>
<body>
<div class="container">
    <h1>Tambah Barang</h1>
    <div class="main">
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" action="tambah.php" enctype="multipart/form-data">
            <div class="input">
                <label>Nama Barang</label>
                <input type="text" name="nama" required />
            </div>
            <div class="input">
                <label>Kategori</label>
                <select name="kategori">
                    <option value="Komputer">Komputer</option>
                    <option value="Elektronik">Elektronik</option>
                    <option value="Hand Phone">Hand Phone</option>
                </select>
            </div>
            <div class="input">
                <label>Harga Jual</label>
                <input type="number" name="harga_jual" min="0" />
            </div>
            <div class="input">
                <label>Harga Beli</label>
                <input type="number" name="harga_beli" min="0" />
            </div>
            <div class="input">
                <label>Stok</label>
                <input type="number" name="stok" min="0" />
            </div>
            <div class="input">
                <label>File Gambar</label>
                <input type="file" name="file_gambar" />
            </div>
            <div class="submit">
                <input type="submit" name="submit" value="Simpan" />
                <a href="index.php">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>