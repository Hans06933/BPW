<?php
/**
 * ============================================================
 * ADMIN - HAPUS PROMO
 * ============================================================
 */

ob_start();
session_start();
require_once __DIR__ . '/../../config/database.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['admin_msg'] = 'ID promo tidak valid.';
    $_SESSION['admin_msg_type'] = 'error';
    header("Location: ../promo.php");
    exit;
}

try {
    $promo = db_get("SELECT judul, gambar FROM promo WHERE id = :id", ['id' => $id]);

    if (!$promo) {
        $_SESSION['admin_msg'] = 'Promo tidak ditemukan.';
        $_SESSION['admin_msg_type'] = 'error';
        header("Location: ../promo.php");
        exit;
    }

    // Hapus gambar
    if (!empty($promo['gambar'])) {
        $file = __DIR__ . '/../../' . $promo['gambar'];
        if (file_exists($file)) @unlink($file);
    }

    db_delete('promo', 'id', $id);

    $_SESSION['admin_msg'] = 'Promo "' . $promo['judul'] . '" berhasil dihapus.';
    $_SESSION['admin_msg_type'] = 'success';

} catch (PDOException $e) {
    $_SESSION['admin_msg'] = 'Gagal hapus: ' . $e->getMessage();
    $_SESSION['admin_msg_type'] = 'error';
}

header("Location: ../promo.php");
exit;