<?php
/**
 * ============================================================
 * ADMIN - UPDATE LAYANAN
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once "../../config/database.php";

// ============================================================
// HANYA IZINKAN POST
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../layanan.php");
    exit;
}

// ============================================================
// AMBIL INPUT
// ============================================================
$id           = (int)($_POST['id'] ?? 0);
$nama_layanan = trim($_POST['nama_layanan'] ?? '');
$deskripsi    = trim($_POST['deskripsi'] ?? '');
$status       = $_POST['status'] ?? 'Aktif';

// ============================================================
// VALIDASI
// ============================================================
$errors = [];

if ($id <= 0)                      $errors[] = 'ID layanan tidak valid.';
if ($nama_layanan === '')          $errors[] = 'Nama layanan wajib diisi.';
if ($deskripsi === '')             $errors[] = 'Deskripsi wajib diisi.';

// Validasi status
$allowedStatus = ['Aktif', 'Nonaktif'];
if (!in_array($status, $allowedStatus, true)) {
    $status = 'Aktif';
}

// Kalau ada error → redirect balik ke edit
if (!empty($errors)) {
    $_SESSION['admin_msg'] = implode(' ', $errors);
    $_SESSION['admin_msg_type'] = 'error';
    header("Location: edit.php?id=" . $id);
    exit;
}

// ============================================================
// AMBIL DATA LAMA
// ============================================================
$dataLama = db_get("SELECT * FROM layanan WHERE id = :id LIMIT 1", ['id' => $id]);

if (!$dataLama) {
    $_SESSION['admin_msg'] = 'Data layanan tidak ditemukan.';
    $_SESSION['admin_msg_type'] = 'error';
    header("Location: ../layanan.php");
    exit;
}

$gambarPath = $dataLama['gambar'] ?? '';

// ============================================================
// UPLOAD GAMBAR BARU (OPSIONAL)
// ============================================================
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {

    $folderUpload = __DIR__ . '/../../images/layanan/';
    if (!is_dir($folderUpload)) mkdir($folderUpload, 0777, true);

    $extension = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $allowed   = ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize   = 5 * 1024 * 1024; // 5 MB

    // Validasi format
    if (!in_array($extension, $allowed, true)) {
        $_SESSION['admin_msg'] = 'Format gambar tidak didukung. Gunakan JPG, PNG, atau WEBP.';
        $_SESSION['admin_msg_type'] = 'error';
        header("Location: edit.php?id=" . $id);
        exit;
    }

    // Validasi ukuran
    if ($_FILES['gambar']['size'] > $maxSize) {
        $_SESSION['admin_msg'] = 'Ukuran gambar maksimal 5 MB.';
        $_SESSION['admin_msg_type'] = 'error';
        header("Location: edit.php?id=" . $id);
        exit;
    }

    // Generate nama baru
    $filename = time() . '_' . uniqid() . '.' . $extension;
    $target   = $folderUpload . $filename;

    // Upload
    if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        $_SESSION['admin_msg'] = 'Gagal mengunggah gambar. Silakan coba lagi.';
        $_SESSION['admin_msg_type'] = 'error';
        header("Location: edit.php?id=" . $id);
        exit;
    }

    // Hapus gambar lama (kalau ada & file exist)
    if (!empty($dataLama['gambar'])) {
        $gambarLama = __DIR__ . '/../../' . $dataLama['gambar'];
        if (file_exists($gambarLama) && is_file($gambarLama)) {
            @unlink($gambarLama);
        }
    }

    $gambarPath = 'images/layanan/' . $filename;
}

// ============================================================
// UPDATE DATABASE
// ============================================================
try {
    $sql = "UPDATE layanan SET
                nama_layanan = :nama_layanan,
                deskripsi    = :deskripsi,
                gambar       = :gambar,
                status       = :status
            WHERE id = :id";

    db_query($sql, [
        ':nama_layanan' => $nama_layanan,
        ':deskripsi'    => $deskripsi,
        ':gambar'       => $gambarPath,
        ':status'       => $status,
        ':id'           => $id,
    ]);

    $_SESSION['admin_msg'] = 'Layanan "' . $nama_layanan . '" berhasil diperbarui.';
    $_SESSION['admin_msg_type'] = 'success';

} catch (PDOException $e) {
    $_SESSION['admin_msg'] = 'Gagal update: ' . $e->getMessage();
    $_SESSION['admin_msg_type'] = 'error';
    header("Location: edit.php?id=" . $id);
    exit;
}

// ============================================================
// REDIRECT
// ============================================================
header("Location: ../layanan.php");
exit;