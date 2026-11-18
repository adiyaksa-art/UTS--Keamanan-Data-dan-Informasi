<?php
require 'db.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_sym  = trim($_POST['symmetric_key'] ?? '');
    $input_priv = trim($_POST['asymmetric_key'] ?? '');

    if (!file_exists('public_lock.json')) {
        $error = "File public_lock.json belum ada. Jalankan setup_keys.php terlebih dahulu.";
    } else {
        $lock = json_decode(file_get_contents('public_lock.json'), true);
        $encrypted_sym = base64_decode($lock['encrypted_symmetric_key']);

        // Uji dekripsi tantangan menggunakan Private Key (Asimetris)
        $decrypt_success = openssl_private_decrypt($encrypted_sym, $decrypted_sym, $input_priv);

        // Validasi silang hasil dekripsi RSA dengan Kunci Simetris inputan user
        if ($decrypt_success && hash('sha256', $input_sym) === $lock['symmetric_hash'] && $decrypted_sym === $input_sym) {
            $_SESSION['logged_in'] = true;
            $_SESSION['symmetric_key'] = $input_sym; // Disimpan di sesi untuk enkripsi/dekripsi konten
            header("Location: admin.php");
            exit;
        } else {
            $error = "Otentikasi Gagal: Pasangan Kunci Simetris dan Kunci Asimetris tidak valid!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Landing Page - Sistem Informasi Kriptografi</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 0 15px; color: #000; }
        .card { border: 1px solid #000; padding: 20px; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; font-family: monospace; border: 1px solid #000; }
        textarea { height: 180px; }
        button { margin-top: 15px; padding: 10px 15px; background: #000; color: #fff; border: none; cursor: pointer; }
        .error { border: 1px solid #000; padding: 10px; margin-bottom: 15px; background: #eee; }
    </style>
</head>
<body>
    <h1>Sistem Informasi Terenkripsi</h1>
    <p>Silakan masukkan pasangan kunci kriptografi Anda untuk mengakses halaman Admin.</p>

    <?php if ($error): ?>
        <div class="error"><strong>Error:</strong> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <label for="sym">Kunci Simetris (Shared Secret / AES Key):</label>
            <input type="text" id="sym" name="symmetric_key" required placeholder="Masukkan kunci simetris...">

            <label for="asym">Kunci Asimetris (RSA Private Key):</label>
            <textarea id="asym" name="asymmetric_key" required placeholder="-----BEGIN PRIVATE KEY-----..."></textarea>

            <button type="submit">Verifikasi Kunci & Masuk Admin</button>
        </form>
    </div>
</body>
</html>