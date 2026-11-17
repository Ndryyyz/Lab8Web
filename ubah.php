<?php
error_reporting(E_ALL);
include_once 'koneksi.php';

$error = "";

// Bagian 1: kalau form disubmit, simpan perubahan
if (isset($_POST['submit'])) {
    $id         = (int)$_POST['id'];
    $nama       = trim($_POST['nama']);
    $kategori   = $_POST['kategori'];
    $harga_jual = (int)$_POST['harga_jual'];
    $harga_beli = (int)$_POST['harga_beli'];
    $stok       = (int)$_POST['stok'];
    $gambar     = null;

    $file_gambar = $_FILES['file_gambar'];
    if ($file_gambar['error'] == 0) {
        $ext = strtolower(pathinfo($file_gambar['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $filename = time() . '_' . str_replace(' ', '_', basename($file_gambar['name']));
            if (move_uploaded_file($file_gambar['tmp_name'], __DIR__ . '/gambar/' . $filename)) {
                $gambar = 'gambar/' . $filename;
            }
        } else {
            $error = "File gambar harus berformat jpg, png, gif, atau webp.";
        }
    }

    if ($error === "") {
        if ($gambar !== null) {
            // Ada gambar baru, ikut diupdate
            $stmt = mysqli_prepare($conn,
                "UPDATE data_barang SET nama=?, kategori=?, harga_jual=?, harga_beli=?, stok=?, gambar=?
                 WHERE id_barang=?");
            mysqli_stmt_bind_param($stmt, "ssiiisi", $nama, $kategori, $harga_jual, $harga_beli, $stok, $gambar, $id);
        } else {
            // Tidak ada gambar baru, gambar lama dibiarkan
            $stmt = mysqli_prepare($conn,
                "UPDATE data_barang SET nama=?, kategori=?, harga_jual=?, harga_beli=?, stok=?
                 WHERE id_barang=?");
            mysqli_stmt_bind_param($stmt, "ssiiii", $nama, $kategori, $harga_jual, $harga_beli, $stok, $id);
        }
        mysqli_stmt_execute($stmt);
        header('Location: index.php');
        exit;
    }
}

// Bagian 2: ambil data lama untuk ditampilkan di form
$id = isset($_POST['id']) ? (int)$_POST['id'] : (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM data_barang WHERE id_barang = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$data) {
    die('Error: Data tidak tersedia');
}

// Fungsi kecil untuk menandai opsi dropdown yang sedang dipilih
function is_select($var, $val) {
    return ($var == $val) ? 'selected="selected"' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link href="style.css" rel="stylesheet" type="text/css" />
    <title>Ubah Barang</title>
</head>
<body>
<div class="container">
    <h1>Ubah Barang</h1>
    <div class="main">
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" action="ubah.php" enctype="multipart/form-data">
            <div class="input">
                <label>Nama Barang</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" required />
            </div>
            <div class="input">
                <label>Kategori</label>
                <select name="kategori">
                    <option <?= is_select('Komputer', $data['kategori']); ?> value="Komputer">Komputer</option>
                    <option <?= is_select('Elektronik', $data['kategori']); ?> value="Elektronik">Elektronik</option>
                    <option <?= is_select('Hand Phone', $data['kategori']); ?> value="Hand Phone">Hand Phone</option>
                </select>
            </div>
            <div class="input">
                <label>Harga Jual</label>
                <input type="number" name="harga_jual" min="0" value="<?= (int)$data['harga_jual']; ?>" />
            </div>
            <div class="input">
                <label>Harga Beli</label>
                <input type="number" name="harga_beli" min="0" value="<?= (int)$data['harga_beli']; ?>" />
            </div>
            <div class="input">
                <label>Stok</label>
                <input type="number" name="stok" min="0" value="<?= (int)$data['stok']; ?>" />
            </div>
            <div class="input">
                <label>File Gambar</label>
                <input type="file" name="file_gambar" />
            </div>
            <div class="submit">
                <input type="hidden" name="id" value="<?= (int)$data['id_barang']; ?>" />
                <input type="submit" name="submit" value="Simpan" />
                <a href="index.php">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>