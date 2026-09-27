<?php
/**
 * ============================================================
 * ADMIN - AKSI PESAN (mark read, star, mark all)
 * Bayu Prima Wisata
 * ============================================================
 */

ob_start();
session_start();
require_once __DIR__ . '/../../config/database.php';

// ============================================================
// HANYA POST
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pesan-masuk.php');
    exit;
}

$aksi     = $_POST['aksi'] ?? '';
$id       = (int) ($_POST['id'] ?? 0);
$redirect = $_POST['redirect'] ?? ''; // 'detail' atau kosong (default ke list)

try {
    // ========================================================
    // MARK READ (single)
    // ========================================================
    if ($aksi === 'mark_read' && $id > 0) {
        db_update('pesan_masuk', ['is_read' => 1], 'id', $id);
        $_SESSION['admin_msg'] = 'Pesan ditandai sudah dibaca.';
        $_SESSION['admin_msg_type'] = 'info';
    }

    // ========================================================
    // MARK UNREAD (single)
    // ========================================================
    elseif ($aksi === 'mark_unread' && $id > 0) {
        db_update('pesan_masuk', ['is_read' => 0], 'id', $id);
        $_SESSION['admin_msg'] = 'Pesan ditandai belum dibaca.';
        $_SESSION['admin_msg_type'] = 'info';
    }

    // ========================================================
    // MARK ALL READ
    // ========================================================
    elseif ($aksi === 'mark_all_read') {
        $pdo = (new Database())->getConnection();
        $pdo->exec("UPDATE pesan_masuk SET is_read = 1 WHERE is_read = 0");
        $_SESSION['admin_msg'] = 'Semua pesan ditandai sudah dibaca.';
        $_SESSION['admin_msg_type'] = 'success';
    }

    // ========================================================
    // TOGGLE STAR
    // ========================================================
    elseif ($aksi === 'star' && $id > 0) {
        $row = db_get("SELECT is_starred FROM pesan_masuk WHERE id = :id", [':id' => $id]);
        if ($row) {
            $newStar = empty($row['is_starred']) ? 1 : 0;
            db_update('pesan_masuk', ['is_starred' => $newStar], 'id', $id);
            $_SESSION['admin_msg'] = $newStar ? 'Pesan ditandai penting.' : 'Tanda penting dihapus.';
            $_SESSION['admin_msg_type'] = 'info';
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
    $_SESSION['admin_msg'] = 'Gagal: ' . $e->getMessage();
    $_SESSION['admin_msg_type'] = 'error';
}

// ============================================================
// REDIRECT
// ============================================================
if ($redirect === 'detail' && $id > 0) {
    header('Location: detail.php?id=' . $id);
} else {
    header('Location: ../pesan-masuk.php');
}
exit;