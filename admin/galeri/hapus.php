<?php

require_once "../../config/database.php";


/*
|--------------------------------------------------------------------------
| AMBIL ID
|--------------------------------------------------------------------------
*/

$id = (int)($_GET['id'] ?? 0);


if ($id <= 0) {

    header("Location: ../galeri.php");
    exit;

}


try {

    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA GAMBAR
    |--------------------------------------------------------------------------
    */

    $galeri = db_get(
        "SELECT * FROM galeri WHERE id = :id LIMIT 1",
        [
            'id' => $id
        ]
    );


    if (!$galeri) {

        header("Location: ../galeri.php?status=notfound");
        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | HAPUS DATABASE
    |--------------------------------------------------------------------------
    */

    db_delete(
        'galeri',
        'id',
        $id
    );


    /*
    |--------------------------------------------------------------------------
    | HAPUS FILE GAMBAR
    |--------------------------------------------------------------------------
    */

    if (!empty($galeri['gambar'])) {

        $imagePath =
            "../../" .
            ltrim(
                $galeri['gambar'],
                '/'
            );


        if (file_exists($imagePath)) {

            unlink($imagePath);

        }

    }


    /*
    |--------------------------------------------------------------------------
    | KEMBALI KE HALAMAN GALERI
    |--------------------------------------------------------------------------
    */

    header(
        "Location: ../galeri.php?status=deleted"
    );

    exit;


} catch (PDOException $e) {

    die(
        "Gagal menghapus data galeri: " .
        htmlspecialchars(
            $e->getMessage()
        )
    );

}