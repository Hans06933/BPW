<?php

require_once __DIR__ . '/../../config/database.php';

try {

    $db = (new Database())->getConnection();

    $id = isset($_GET['id'])
        ? (int) $_GET['id']
        : 0;


    if ($id <= 0) {

        header(
            "Location: ../destinasi.php?status=error&refresh=" . time()
        );

        exit;
    }


    // Ambil gambar
    $stmt = $db->prepare("
        SELECT gambar_utama
        FROM destinasi
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id
    ]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$data) {

        header(
            "Location: ../destinasi.php?status=error&refresh=" . time()
        );

        exit;
    }


    // Hapus database
    $stmt = $db->prepare("
        DELETE FROM destinasi
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id
    ]);


    // Hapus gambar
    if (!empty($data['gambar_utama'])) {

        $gambarPath =
            __DIR__ .
            '/../../images/destinasi/' .
            $data['gambar_utama'];


        if (file_exists($gambarPath)) {

            unlink($gambarPath);
        }
    }


    // Kembali ke destinasi.php
    header(
        "Location: ../destinasi.php?status=deleted&refresh="
        . time()
    );

    exit;


} catch (PDOException $e) {

    header(
        "Location: ../destinasi.php?status=error&refresh="
        . time()
    );

    exit;
}