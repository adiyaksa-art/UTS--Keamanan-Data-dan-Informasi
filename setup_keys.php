<?php
// 1. Buat Kunci Simetris (Secret Passphrase)
$symmetric_key = "KUNCI-SIMETRIS-RAHASIA-2026";

// 2. Generate Pasangan Kunci Asimetris (RSA 2048-bit)
$config = [
    "digest_alg" => "sha256",
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
    // Baris di bawah ini ditambahkan khusus untuk Windows/Laragon:
    "config" => "C:/laragon/bin/php/php-8.3.26-nts-Win32-vs16-x64/extras/ssl/openssl.cnf"
];

$res = openssl_pkey_new($config);
if (!$res) {
    die("Gagal membuat kunci RSA. Pastikan ekstensi OpenSSL aktif di php.ini.");
}

openssl_pkey_export($res, $private_key, null, $config);
$pub_details = openssl_pkey_get_details($res);
$public_key  = $pub_details["key"];

// 3. Enkripsi Kunci Simetris menggunakan Public Key untuk disimpan di server
openssl_public_encrypt($symmetric_key, $encrypted_sym, $public_key);

$lock_data = [
    'public_key' => $public_key,
    'encrypted_symmetric_key' => base64_encode($encrypted_sym),
    'symmetric_hash' => hash('sha256', $symmetric_key)
];

file_put_contents('public_lock.json', json_encode($lock_data, JSON_PRETTY_PRINT));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Setup Kunci Kriptografi</title>
    <style>
        body { font-family: monospace; padding: 20px; line-height: 1.5; }
        textarea { width: 100%; height: 220px; }
        .box { border: 1px solid #000; padding: 15px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Inisialisasi Kunci Berhasil</h2>
    <p>Simpan kedua kunci di bawah ini untuk login di halaman utama (Landing Page):</p>

    <div class="box">
        <strong>1. Kunci Simetris (AES Passphrase):</strong><br>
        <code><?= htmlspecialchars($symmetric_key) ?></code>
    </div>

    <div class="box">
        <strong>2. Kunci Asimetris (RSA Private Key):</strong><br>
        <textarea readonly><?= htmlspecialchars($private_key) ?></textarea>
    </div>

    <a href="index.php">Ke Landing Page (Login Admin) &rarr;</a>
</body>
</html>