<?php

require_once "../../config/database.php";

$id = (int)($_GET['id'] ?? 0);


if ($id <= 0) {

    header("Location: ../layanan.php");
    exit;

}


/* AMBIL DATA */

$data = db_get(
    "SELECT * FROM layanan WHERE id = ?",
    [$id]
);


if (!$data) {

    header("Location: ../layanan.php");
    exit;

}


/* HAPUS GAMBAR */

if (!empty($data['gambar'])) {

    $gambarPath = "../../" . $data['gambar'];

    if (file_exists($gambarPath)) {
        unlink($gambarPath);
    }

}


/* HAPUS DATA */

db_delete(
    "layanan",
    "id",
    $id
);


header("Location: ../layanan.php?status=deleted");
exit;