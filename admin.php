<?php
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

$sym_key = $_SESSION['symmetric_key'];
$cipher_algo = "AES-256-CBC";
$key_hash = substr(hash('sha256', $sym_key, true), 0, 32);

// Proses Simpan & Enkripsi Data Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $judul = trim($_POST['judul']);
    $isi   = trim($_POST['isi_konten']);

    if ($judul !== '' && $isi !== '') {
        $iv_len = openssl_cipher_iv_length($cipher_algo);
        $iv     = openssl_random_pseudo_bytes($iv_len);

        $encrypted = openssl_encrypt($isi, $cipher_algo, $key_hash, 0, $iv);

        $stmt = $pdo->prepare("INSERT INTO konten_terenkripsi (judul, ciphertext, iv) VALUES (?, ?, ?)");
        $stmt->execute([$judul, $encrypted, base64_encode($iv)]);

        header("Location: admin.php");
        exit;
    }
}

// Ambil seluruh data dari database
$stmt = $pdo->query("SELECT * FROM konten_terenkripsi ORDER BY id DESC");
$data_konten = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Halaman Admin - Data Terenkripsi</title>
    <style>
        body { font-family: sans-serif; max-width: 900px; margin: 30px auto; padding: 0 15px; color: #000; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .box { border: 1px solid #000; padding: 15px; margin-top: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; border: 1px solid #000; }
        button { margin-top: 12px; padding: 8px 15px; background: #000; color: #fff; border: none; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; vertical-align: top; word-break: break-all; }
        th { background: #eee; }
        .mono { font-family: monospace; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Dashboard Admin</h2>
        <a href="logout.php">Keluar (Logout)</a>
    </div>

    <div class="box">
        <h3>Tambah Konten Baru (Otomatis Dienkripsi)</h3>
        <form method="POST">
            <label>Judul Informasi:</label>
            <input type="text" name="judul" required>

            <label>Isi Data / Konten Rahasia:</label>
            <textarea name="isi_konten" rows="3" required></textarea>

            <button type="submit" name="simpan">Enkripsi & Simpan Data</button>
        </form>
    </div>

    <div class="box">
        <h3>Daftar Seluruh Konten Hasil Enkripsi</h3>
        <table>
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="20%">Judul</th>
                    <th width="35%">Ciphertext (Data di Database)</th>
                    <th width="15%">IV (Base64)</th>
                    <th width="25%">Plaintext (Hasil Dekripsi Kunci Simetris)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data_konten)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">Belum ada data tersimpan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data_konten as $row): ?>
                        <?php
                        $iv_raw = base64_decode($row['iv']);
                        $decrypted = openssl_decrypt($row['ciphertext'], $cipher_algo, $key_hash, 0, $iv_raw);
                        ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['judul']) ?></td>
                            <td class="mono"><?= htmlspecialchars($row['ciphertext']) ?></td>
                            <td class="mono"><?= htmlspecialchars($row['iv']) ?></td>
                            <td><?= htmlspecialchars($decrypted ?: '[Gagal didekripsi]') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>