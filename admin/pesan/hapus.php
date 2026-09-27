<?php
/**
 * ============================================================
 * ADMIN - HAPUS PESAN (DELETE)
 * Bayu Prima Wisata
 * ============================================================
 */

ob_start();
session_start();
require_once __DIR__ . '/../../config/database.php';

// ============================================================
// PROTEKSI ADMIN
// ============================================================
// if (empty($_SESSION['admin_id'])) {
//     header('Location: ../login.php');
//     exit;
// }

// ============================================================
// HAPUS 1 PESAN VIA GET
// ============================================================
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    try {
        $row = db_get("SELECT judul FROM pesan_masuk WHERE id = :id", [':id' => $id]);

        if ($row) {
            db_delete('pesan_masuk', 'id', $id);
            $_SESSION['admin_msg'] = 'Pesan "' . $row['judul'] . '" berhasil dihapus.';
            $_SESSION['admin_msg_type'] = 'success';
        } else {
            $_SESSION['admin_msg'] = 'Pesan tidak ditemukan.';
            $_SESSION['admin_msg_type'] = 'error';
        }
    } catch (PDOException $e) {
        $_SESSION['admin_msg'] = 'Gagal hapus: ' . $e->getMessage();
        $_SESSION['admin_msg_type'] = 'error';
    }

    header('Location: ../pesan-masuk.php');
    exit;
}

// ============================================================
// HANDLE POST (bulk delete)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pesan-masuk.php');
    exit;
}

$aksi = $_POST['aksi'] ?? '';

try {
    $pdo = (new Database())->getConnection();

    // ========================================================
    // HAPUS SEMUA YANG SUDAH DIBACA
    // ========================================================
    if ($aksi === 'hapus_semua_read') {
        $stmt = $pdo->exec("DELETE FROM pesan_masuk WHERE is_read = 1");
        $_SESSION['admin_msg'] = $stmt . ' pesan yang sudah dibaca berhasil dihapus.';
        $_SESSION['admin_msg_type'] = 'success';
    }

    // ========================================================
    // HAPUS SEMUA PESAN
    // ========================================================
    elseif ($aksi === 'hapus_semua') {
        $stmt = $pdo->exec("DELETE FROM pesan_masuk");
        $_SESSION['admin_msg'] = $stmt . ' pesan berhasil dihapus (semua).';
        $_SESSION['admin_msg_type'] = 'success';
    }

    // ========================================================
    // HAPUS PESAN TERPILIH (checkbox)
    // ========================================================
    elseif ($aksi === 'hapus_terpilih') {
        $ids = $_POST['ids'] ?? [];

        if (empty($ids) || !is_array($ids)) {
            $_SESSION['admin_msg'] = 'Tidak ada pesan yang dipilih.';
            $_SESSION['admin_msg_type'] = 'error';
        } else {
            // Sanitasi: hanya izinkan integer
            $ids    = array_map('intval', $ids);
            $ids    = array_filter($ids, fn($x) => $x > 0);
            $count  = count($ids);

            if ($count > 0) {
                // Build placeholder :id0, :id1, ...
                $placeholders = [];
                $params       = [];

                foreach ($ids as $index => $value) {
                    $key = ':id' . $index;
                    $placeholders[] = $key;
                    $params[$key]   = $value;
                }

                $sql  = "DELETE FROM pesan_masuk WHERE id IN (" . implode(',', $placeholders) . ")";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                $_SESSION['admin_msg'] = $count . ' pesan berhasil dihapus.';
                $_SESSION['admin_msg_type'] = 'success';
            } else {
                $_SESSION['admin_msg'] = 'Tidak ada pesan valid yang dipilih.';
                $_SESSION['admin_msg_type'] = 'error';
            }
        }
    }

    // ========================================================
    // AKSI TIDAK DIKENALI
    // ========================================================
    else {
        $_SESSION['admin_msg'] = 'Aksi tidak dikenali.';
        $_SESSION['admin_msg_type'] = 'error';
    }

} catch (PDOException $e) {
    $_SESSION['admin_msg'] = 'Gagal hapus: ' . $e->getMessage();
    $_SESSION['admin_msg_type'] = 'error';
}

header('Location: ../pesan-masuk.php');
exit;