<?php
/**
 * ============================================================
 * PROSES TAMBAH TESTIMONI
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/config/database.php';

// ============================================================
// HANYA IZINKAN METHOD POST
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: testimoni.php');
    exit;
}

// ============================================================
// AMBIL & BERSIHKAN INPUT
// ============================================================
$nama            = trim($_POST['nama'] ?? '');
$email           = trim($_POST['email'] ?? '');
$kota_asal       = trim($_POST['kota_asal'] ?? '');
$destinasi       = trim($_POST['destinasi'] ?? '');
$tipe_perjalanan = trim($_POST['tipe_perjalanan'] ?? 'Keluarga');
$rating          = (int) ($_POST['rating'] ?? 5);
$testimoni       = trim($_POST['testimoni'] ?? '');

// ============================================================
// VALIDASI
// ============================================================
$errors = [];

if ($nama === '') {
    $errors[] = 'Nama wajib diisi.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email tidak valid.';
}

if ($destinasi === '') {
    $errors[] = 'Destinasi wajib diisi.';
}

if ($testimoni === '') {
    $errors[] = 'Testimoni wajib diisi.';
}

if ($rating < 1 || $rating > 5) {
    $rating = 5;
}

// Validasi tipe perjalanan agar cocok dengan ENUM database
$allowedTipe = ['Keluarga', 'Couple', 'Solo', 'Teman', 'Bisnis'];
if (!in_array($tipe_perjalanan, $allowedTipe, true)) {
    $tipe_perjalanan = 'Keluarga';
}

// ============================================================
// JIKA ADA ERROR → KEMBALI KE FORM
// ============================================================
if (!empty($errors)) {
    $_SESSION['testimoni_error'] = implode(' ', $errors);
    $_SESSION['testimoni_old']   = $_POST;
    header('Location: testimoni.php#tulis');
    exit;
}

// ============================================================
// UPLOAD FOTO (OPSIONAL)
// ============================================================
$namaFileFoto = null;

if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

    $folderUpload = __DIR__ . '/uploads/testimoni/';

    if (!is_dir($folderUpload)) {
        mkdir($folderUpload, 0777, true);
    }

    $ext     = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (in_array($ext, $allowed, true)) {

        $namaBaru = 'testimoni_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $folderUpload . $namaBaru)) {
            $namaFileFoto = 'uploads/testimoni/' . $namaBaru;
        }
    }
}

// ============================================================
// SIMPAN KE DATABASE
// ============================================================
try {

    db_insert('testimoni', [
        'nama'            => $nama,
        'email'           => $email,
        'destinasi'       => $destinasi,
        'rating'          => $rating,
        'testimoni'       => $testimoni,
        'foto'            => $namaFileFoto,
        'kota_asal'       => $kota_asal,
        'tipe_perjalanan' => $tipe_perjalanan,
        'status'          => 'pending',
        'is_featured'     => 0,
    ]);

    // ========================================================
    // KIRIM NOTIFIKASI KE PESAN MASUK ADMIN
    // ========================================================
    kirim_pesan_admin(
        'testimoni',
        'Testimoni Baru: ' . $nama,
        'Testimoni untuk destinasi "' . $destinasi . '" (Rating ' . $rating . '/5). Menunggu persetujuan admin.',
        'testimoni.php?status=pending',
        $nama,
        $email
    );

    $_SESSION['testimoni_success'] = 'Terima kasih! Testimoni Anda berhasil dikirim dan akan ditinjau oleh admin.';

} catch (PDOException $e) {

    $_SESSION['testimoni_error'] = 'Gagal menyimpan testimoni. Silakan coba lagi.';
    // Untuk debugging sementara:
    // $_SESSION['testimoni_error'] = 'Error: ' . $e->getMessage();
}

// ============================================================
// REDIRECT KEMBALI
// ============================================================
header('Location: testimoni.php#tulis');
exit;