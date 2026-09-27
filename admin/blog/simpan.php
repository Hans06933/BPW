<?php
/**
 * ============================================================
 * ADMIN - SIMPAN BLOG (CREATE + UPDATE + Quick Action)
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/../../config/database.php';

// ============================================================
// HANDLE QUICK ACTION VIA GET (publish / draft / archive)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['aksi'], $_GET['id'])) {

    $id   = (int) $_GET['id'];
    $aksi = $_GET['aksi'];

    try {
        if ($aksi === 'publish') {
            $row = db_get("SELECT published_at FROM blog WHERE id = :id", [':id' => $id]);
            $data = ['status' => 'published'];
            if (empty($row['published_at'])) {
                $data['published_at'] = date('Y-m-d H:i:s');
            }
            db_update('blog', $data, 'id', $id);
            $_SESSION['admin_msg'] = 'Artikel dipublikasikan.';
            $_SESSION['admin_msg_type'] = 'success';

        } elseif ($aksi === 'draft') {
            db_update('blog', ['status' => 'draft', 'published_at' => null], 'id', $id);
            $_SESSION['admin_msg'] = 'Artikel dikembalikan ke draft.';
            $_SESSION['admin_msg_type'] = 'info';

        } elseif ($aksi === 'archive') {
            db_update('blog', ['status' => 'archived'], 'id', $id);
            $_SESSION['admin_msg'] = 'Artikel diarsipkan.';
            $_SESSION['admin_msg_type'] = 'info';
        }
    } catch (PDOException $e) {
        $_SESSION['admin_msg'] = 'Gagal update status: ' . $e->getMessage();
        $_SESSION['admin_msg_type'] = 'error';
    }

    header('Location: ../blog.php');
    exit;
}

// ============================================================
// HANDLE POST (tambah / update)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../blog.php');
    exit;
}

$aksi = $_POST['aksi'] ?? 'tambah';
$id   = (int) ($_POST['id'] ?? 0);

$isEdit = ($aksi === 'update' && $id > 0);
$isNew  = ($aksi === 'tambah');

if (!$isEdit && !$isNew) {
    $_SESSION['admin_msg'] = 'Aksi tidak valid.';
    $_SESSION['admin_msg_type'] = 'error';
    header('Location: ../blog.php');
    exit;
}

// ============================================================
// AMBIL INPUT
// ============================================================
$judul    = trim($_POST['judul'] ?? '');
$slug     = trim($_POST['slug'] ?? '');
$konten   = trim($_POST['konten'] ?? '');
$excerpt  = trim($_POST['excerpt'] ?? '');
$kategori = $_POST['kategori'] ?? 'destinasi';
$penulis  = trim($_POST['penulis'] ?? 'Admin BPW');
$status   = $_POST['status'] ?? 'draft';

// ============================================================
// VALIDASI
// ============================================================
$errors = [];

if ($judul === '')  $errors[] = 'Judul wajib diisi.';
if ($konten === '') $errors[] = 'Konten wajib diisi.';

// Auto-slug
if ($slug === '') {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $judul));
} else {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $slug));
}
$slug = trim($slug, '-');

// Validasi enum
$allowedKategori = ['destinasi', 'tips', 'budaya', 'kuliner', 'event'];
if (!in_array($kategori, $allowedKategori, true)) $kategori = 'destinasi';

$allowedStatus = ['draft', 'published', 'archived'];
if (!in_array($status, $allowedStatus, true)) $status = 'draft';

// Cek duplikat slug
if (empty($errors)) {
    $sql = "SELECT id FROM blog WHERE slug = :slug" . ($isEdit ? " AND id != :id" : "");
    $par = $isEdit ? [':slug' => $slug, ':id' => $id] : [':slug' => $slug];
    if (db_get($sql, $par)) {
        $errors[] = 'Slug "' . $slug . '" sudah dipakai artikel lain.';
    }
}

// ============================================================
// AMBIL DATA LAMA (untuk edit)
// ============================================================
$oldData = null;
if ($isEdit) {
    $oldData = db_get("SELECT * FROM blog WHERE id = :id", [':id' => $id]);
    if (!$oldData) {
        $_SESSION['admin_msg'] = 'Artikel tidak ditemukan.';
        $_SESSION['admin_msg_type'] = 'error';
        header('Location: ../blog.php');
        exit;
    }
}

// ============================================================
// UPLOAD GAMBAR
// ============================================================
$gambarPath = $oldData['gambar_utama'] ?? '';

if (!empty($_FILES['gambar_utama']['name']) && $_FILES['gambar_utama']['error'] === UPLOAD_ERR_OK) {

    $folderUpload = __DIR__ . '/../../uploads/blog/';
    if (!is_dir($folderUpload)) mkdir($folderUpload, 0777, true);

    $ext     = strtolower(pathinfo($_FILES['gambar_utama']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $maxSize = 2 * 1024 * 1024;

    if (!in_array($ext, $allowed, true)) {
        $errors[] = 'Format gambar tidak didukung.';
    } elseif ($_FILES['gambar_utama']['size'] > $maxSize) {
        $errors[] = 'Ukuran gambar maks 2MB.';
    } else {
        $namaBaru = 'blog_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (move_uploaded_file($_FILES['gambar_utama']['tmp_name'], $folderUpload . $namaBaru)) {
            // Hapus gambar lama
            if (!empty($oldData['gambar_utama'])) {
                $oldFile = __DIR__ . '/../../' . $oldData['gambar_utama'];
                if (file_exists($oldFile)) @unlink($oldFile);
            }
            $gambarPath = 'uploads/blog/' . $namaBaru;
        } else {
            $errors[] = 'Gagal upload gambar.';
        }
    }
}

// ============================================================
// JIKA ADA ERROR → KEMBALI KE FORM
// ============================================================
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_old']    = $_POST;

    if ($isEdit) {
        header('Location: edit.php?id=' . $id);
    } else {
        header('Location: tambah.php');
    }
    exit;
}

// ============================================================
// SIMPAN
// ============================================================
try {
    $data = [
        'judul'        => $judul,
        'slug'         => $slug,
        'konten'       => $konten,
        'excerpt'      => $excerpt,
        'kategori'     => $kategori,
        'gambar_utama' => $gambarPath,
        'penulis'      => $penulis,
        'status'       => $status,
    ];

    // Handle published_at
    if ($status === 'published') {
        if ($isNew || empty($oldData['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }
    } else {
        $data['published_at'] = null;
    }

    if ($isEdit) {
        db_update('blog', $data, 'id', $id);
        $_SESSION['admin_msg'] = 'Artikel berhasil diperbarui.';
    } else {
        db_insert('blog', $data);
        $_SESSION['admin_msg'] = 'Artikel berhasil ditambahkan.';
    }
    $_SESSION['admin_msg_type'] = 'success';

} catch (PDOException $e) {
    $_SESSION['admin_msg'] = 'Gagal menyimpan: ' . $e->getMessage();
    $_SESSION['admin_msg_type'] = 'error';
}

header('Location: ../blog.php');
exit;