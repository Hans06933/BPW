<?php
require_once __DIR__ . '/../../config/database.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: ../hotel.php?error=ID hotel tidak valid');
    exit;
}

try {
    // AMBIL DATA HOTEL
    $hotel = db_get(
        "SELECT id, gambar_utama FROM hotel WHERE id = ? LIMIT 1",
        [$id]
    );

    if (!$hotel) {
        header('Location: ../hotel.php?error=Data hotel tidak ditemukan');
        exit;
    }

    // HAPUS DATA DATABASE
    db_delete('hotel', 'id', $id);

    // HAPUS GAMBAR
    if (!empty($hotel['gambar_utama'])) {
        $namaFile = basename($hotel['gambar_utama']);
        $pathGambar = __DIR__ . '/../../images/hotel/' . $namaFile;

        if (file_exists($pathGambar)) {
            unlink($pathGambar);
        }
    }

    // KEMBALI
    header('Location: ../hotel.php?success=Hotel berhasil dihapus');
    exit;

} catch (Exception $e) {
    header('Location: ../hotel.php?error=' . urlencode($e->getMessage()));
    exit;
}