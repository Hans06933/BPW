<?php
/**
 * ============================================================
 * ADMIN - SIMPAN PROMO (Create/Update/Status)
 * ============================================================
 */

ob_start();
session_start();
require_once __DIR__ . '/../../config/database.php';

// ============================================================
// HANDLE QUICK ACTION (aktifkan/nonaktifkan via GET)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['aksi'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $aksi = $_GET['aksi'];

    try {
        if ($aksi === 'aktifkan') {
            db_update('promo', ['status' => 'aktif'], 'id', $id);
            $_SESSION['admin_msg'] = 'Promo diaktifkan.';
            $_SESSION['admin_msg_type'] = 'success';
        } elseif ($aksi === 'nonaktif') {
            db_update('promo', ['status' => 'nonaktif'], 'id', $id);
            $_SESSION['admin_msg'] = 'Promo dinonaktifkan.';
            $_SESSION['admin_msg_type'] = 'info';
        }
    } catch (PDOException $e) {
        $_SESSION['admin_msg'] = 'Gagal: ' . $e->getMessage();
        $_SESSION['admin_msg_type'] = 'error';
    }

    header("Location: ../promo.php");
    exit;
}

// ============================================================
// HANYA IZINKAN POST
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../promo.php");
    exit;
}

$aksi = $_POST['aksi'] ?? 'tambah';
$id   = (int) ($_POST['id'] ?? 0);

$isEdit = ($aksi === 'update' && $id > 0);
$isNew  = ($aksi === 'tambah');

// ============================================================
// AMBIL INPUT
// ============================================================
$judul            = trim($_POST['judul'] ?? '');
$deskripsi        = trim($_POST['deskripsi'] ?? '');
$kode_promo       = strtoupper(trim($_POST['kode_promo'] ?? ''));
$kategori         = $_POST['kategori'] ?? 'umum';
$diskon_persen    = (int) ($_POST['diskon_persen'] ?? 0);
$minimal_pembelian = (float) ($_POST['minimal_pembelian'] ?? 0);
$maksimal_diskon  = ($_POST['maksimal_diskon'] ?? '') !== '' ? (float) $_POST['maksimal_diskon'] : null;
$berlaku_mulai    = $_POST['berlaku_mulai'] ?? date('Y-m-d');
$berlaku_sampai   = $_POST['berlaku_sampai'] ?? date('Y-m-d', strtotime('+30 days'));
$kuota            = (int) ($_POST['kuota'] ?? 0);
$status           = $_POST['status'] ?? 'aktif';

// ============================================================
// VALIDASI
// ============================================================
$errors = [];

if ($judul === '')      $errors[] = 'Judul wajib diisi.';
if ($diskon_persen < 1 || $diskon_persen > 100) $errors[] = 'Diskon harus 1-100%.';
if ($berlaku_mulai === '') $errors[] = 'Tanggal mulai wajib diisi.';
if ($berlaku_sampai === '') $errors[] = 'Tanggal berakhir wajib diisi.';
if ($berlaku_mulai !== '' && $berlaku_sampai !== '' && strtotime($berlaku_sampai) < strtotime($berlaku_mulai)) {
    $errors[] = 'Tanggal berakhir harus setelah tanggal mulai.';
}

// Validasi kategori
$allowedKategori = ['flash_sale', 'early_bird', 'group', 'member', 'umum'];
if (!in_array($kategori, $allowedKategori, true)) $kategori = 'umum';

// Validasi status
$allowedStatus = ['aktif', 'nonaktif', 'habis'];
if (!in_array($status, $allowedStatus, true)) $status = 'aktif';

if (!empty($errors)) {
    $_SESSION['admin_msg'] = implode(' ', $errors);
    $_SESSION['admin_msg_type'] = 'error';
    $_SESSION['form_old'] = $_POST;

    header("Location: " . ($isEdit ? "edit.php?id=$id" : "tambah.php"));
    exit;
}

// ============================================================
// AMBIL DATA LAMA (untuk edit)
// ============================================================
$oldData = null;
if ($isEdit) {
    $oldData = db_get("SELECT * FROM promo WHERE id = :id", ['id' => $id]);
    if (!$oldData) {
        $_SESSION['admin_msg'] = 'Promo tidak ditemukan.';
        $_SESSION['admin_msg_type'] = 'error';
        header("Location: ../promo.php");
        exit;
    }
}

// ============================================================
// UPLOAD GAMBAR
// ============================================================
$gambarPath = $oldData['gambar'] ?? '';

if (!empty($_FILES['gambar']['name']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $folderUpload = __DIR__ . '/../../images/promo/';
    if (!is_dir($folderUpload)) mkdir($folderUpload, 0777, true);

    $ext     = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed, true)) {
        $_SESSION['admin_msg'] = 'Format gambar tidak didukung.';
        $_SESSION['admin_msg_type'] = 'error';
        header("Location: " . ($isEdit ? "edit.php?id=$id" : "tambah.php"));
        exit;
    }

    if ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
        $_SESSION['admin_msg'] = 'Ukuran gambar maks 5 MB.';
        $_SESSION['admin_msg_type'] = 'error';
        header("Location: " . ($isEdit ? "edit.php?id=$id" : "tambah.php"));
        exit;
    }

    $namaBaru = 'promo_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $folderUpload . $namaBaru)) {
        // Hapus gambar lama
        if ($isEdit && !empty($oldData['gambar'])) {
            $oldFile = __DIR__ . '/../../' . $oldData['gambar'];
            if (file_exists($oldFile)) @unlink($oldFile);
        }
        $gambarPath = 'images/promo/' . $namaBaru;
    }
}

// ============================================================
// SIAPKAN DATA
// ============================================================
$data = [
    'judul'             => $judul,
    'deskripsi'         => $deskripsi,
    'kode_promo'        => $kode_promo ?: null,
    'kategori'          => $kategori,
    'diskon_persen'     => $diskon_persen,
    'minimal_pembelian' => $minimal_pembelian,
    'maksimal_diskon'   => $maksimal_diskon,
    'berlaku_mulai'     => $berlaku_mulai,
    'berlaku_sampai'    => $berlaku_sampai,
    'kuota'             => $kuota,
    'gambar'            => $gambarPath,
    'status'            => $status,
];

// Kalau CREATE → set sisa_kuota = kuota
if ($isNew) {
    $data['sisa_kuota'] = $kuota;
} else {
    // Kalau kuota berubah saat edit → sesuaikan sisa_kuota
    $kuotaLama = (int) ($oldData['kuota'] ?? 0);
    $sisaLama  = (int) ($oldData['sisa_kuota'] ?? 0);

    if ($kuota !== $kuotaLama) {
        // Hitung ulang sisa: kuota baru - (kuota lama - sisa lama)
        $terpakai = max(0, $kuotaLama - $sisaLama);
        $data['sisa_kuota'] = max(0, $kuota - $terpakai);
    }
}

// ============================================================
// SIMPAN
// ============================================================
try {
    if ($isEdit) {
        db_update('promo', $data, 'id', $id);
        $_SESSION['admin_msg'] = 'Promo "' . $judul . '" berhasil diperbarui.';
    } else {
        db_insert('promo', $data);
        $_SESSION['admin_msg'] = 'Promo "' . $judul . '" berhasil ditambahkan.';
    }
    $_SESSION['admin_msg_type'] = 'success';

} catch (PDOException $e) {
    $_SESSION['admin_msg'] = 'Gagal menyimpan: ' . $e->getMessage();
    $_SESSION['admin_msg_type'] = 'error';
    $_SESSION['form_old'] = $_POST;
    header("Location: " . ($isEdit ? "edit.php?id=$id" : "tambah.php"));
    exit;
}

header("Location: ../promo.php");
exit;