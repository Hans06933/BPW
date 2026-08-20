<?php
// Memanggil file database.php
require_once(__DIR__ . '/../../config/database.php');

$id = $_GET['id'] ?? null;

if ($id) {
    // Gunakan 'id' => $id agar cocok dengan placeholder :id
    $paket = db_get("SELECT gambar_utama, gambar_lain FROM paket_wisata WHERE id = :id", ['id' => $id]);

    if ($paket) {
        // Hapus gambar utama
        if (!empty($paket['gambar_utama'])) {
            $gambar_utama_path = __DIR__ . '/../../assets/paket/' . $paket['gambar_utama'];
            if (file_exists($gambar_utama_path)) {
                unlink($gambar_utama_path);
            }
        }

        // Hapus gambar lain jika ada
        if (!empty($paket['gambar_lain'])) {
            $gambar_lain_list = json_decode($paket['gambar_lain'], true);
            if (is_array($gambar_lain_list)) {
                foreach ($gambar_lain_list as $img) {
                    $img_path = __DIR__ . '/../../assets/paket/' . $img;
                    if (file_exists($img_path)) {
                        unlink($img_path);
                    }
                }
            } else {
                $img_path = __DIR__ . '/../../assets/paket/' . $paket['gambar_lain'];
                if (file_exists($img_path)) {
                    unlink($img_path);
                }
            }
        }

        // Hapus data dari database
        db_delete('paket_wisata', 'id', $id);
    }
}

header("Location: ../paket.php");
exit;
?>