<?php
/**
 * ============================================================
 * ADMIN - HAPUS BLOG (DELETE)
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/../../config/database.php';

// ============================================================
// AMBIL ID
// ============================================================
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['admin_msg'] = 'ID artikel tidak valid.';
    $_SESSION['admin_msg_type'] = 'error';
    header('Location: ../blog.php');
    exit;
}

// ============================================================
// PROSES HAPUS
// ============================================================
try {
    $row = db_get("SELECT judul, gambar_utama FROM blog WHERE id = :id", [':id' => $id]);

    if (!$row) {
        $_SESSION['admin_msg'] = 'Artikel tidak ditemukan.';
        $_SESSION['admin_msg_type'] = 'error';
        header('Location: ../blog.php');
        exit;
    }

    // Hapus file gambar
    if (!empty($row['gambar_utama'])) {
        $filePath = __DIR__ . '/../../' . $row['gambar_utama'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    // Hapus dari database
    db_delete('blog', 'id', $id);

    $_SESSION['admin_msg'] = 'Artikel "' . $row['judul'] . '" berhasil dihapus.';
    $_SESSION['admin_msg_type'] = 'success';

} catch (PDOException $e) {
    $_SESSION['admin_msg'] = 'Gagal hapus: ' . $e->getMessage();
    $_SESSION['admin_msg_type'] = 'error';
}

header('Location: ../blog.php');
exit;