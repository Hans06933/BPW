<?php
/**
 * ============================================================
 * ADMIN - AKSI PEMESANAN
 * Bayu Prima Wisata
 * ============================================================
 */

ob_start();
session_start();
require_once __DIR__ . '/../../config/database.php';

// ============================================================
// HANYA IZINKAN POST
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pemesanan.php');
    exit;
}

$aksi = $_POST['aksi'] ?? '';
$id   = (int) ($_POST['id'] ?? 0);

// ============================================================
// HAPUS
// ============================================================
if ($aksi === 'hapus' && $id > 0) {
    try {
        $row = db_get("SELECT kode_booking FROM pemesanan WHERE id = :id", [':id' => $id]);

        if ($row) {
            db_delete('pemesanan', 'id', $id);
            $_SESSION['admin_msg'] = 'Pemesanan ' . $row['kode_booking'] . ' berhasil dihapus.';
            $_SESSION['admin_msg_type'] = 'success';
        } else {
            $_SESSION['admin_msg'] = 'Pemesanan tidak ditemukan.';
            $_SESSION['admin_msg_type'] = 'error';
        }
    } catch (PDOException $e) {
        $_SESSION['admin_msg'] = 'Gagal hapus: ' . $e->getMessage();
        $_SESSION['admin_msg_type'] = 'error';
    }

    header('Location: ../pemesanan.php');
    exit;
}

// ============================================================
// FALLBACK
// ============================================================
$_SESSION['admin_msg'] = 'Aksi tidak dikenali.';
$_SESSION['admin_msg_type'] = 'error';
header('Location: ../pemesanan.php');
exit;