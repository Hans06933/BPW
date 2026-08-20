<?php

require_once "../../config/database.php";


/*
|--------------------------------------------------------------------------
| CEK METHOD
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: ../galeri.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/

$id = (int)($_POST['id'] ?? 0);

$judul = trim($_POST['judul'] ?? '');

$kategori = trim($_POST['kategori'] ?? '');

$deskripsi = trim($_POST['deskripsi'] ?? '');

$status = $_POST['status'] ?? 'aktif';


/*
|--------------------------------------------------------------------------
| VALIDASI
|--------------------------------------------------------------------------
*/

if ($id <= 0 || $judul === '') {

    header("Location: ../galeri.php");
    exit;

}


if (!in_array($status, ['aktif', 'nonaktif'], true)) {

    $status = 'aktif';

}


try {

    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA LAMA
    |--------------------------------------------------------------------------
    */

    $oldData = db_get(
        "SELECT * FROM galeri WHERE id = :id LIMIT 1",
        [
            'id' => $id
        ]
    );


    if (!$oldData) {

        header("Location: ../galeri.php?status=notfound");
        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | BUAT SLUG
    |--------------------------------------------------------------------------
    */

    $slug = strtolower($judul);

    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);

    $slug = trim($slug, '-');

    if ($slug === '') {

        $slug = 'galeri-' . $id;

    }


    /*
    |--------------------------------------------------------------------------
    | CEK SLUG
    |--------------------------------------------------------------------------
    */

    $checkSlug = db_get(
        "SELECT id FROM galeri
         WHERE slug = :slug
         AND id != :id
         LIMIT 1",
        [
            'slug' => $slug,
            'id' => $id
        ]
    );


    if ($checkSlug) {

        $slug .= '-' . $id;

    }


    /*
    |--------------------------------------------------------------------------
    | DATA UPDATE
    |--------------------------------------------------------------------------
    */

    $dataUpdate = [

        'judul' => $judul,

        'slug' => $slug,

        'kategori' =>
            $kategori !== ''
                ? $kategori
                : null,

        'deskripsi' =>
            $deskripsi !== ''
                ? $deskripsi
                : null,

        'status' => $status

    ];


    /*
    |--------------------------------------------------------------------------
    | JIKA ADA GAMBAR BARU
    |--------------------------------------------------------------------------
    */

    $newImagePath = null;

    $oldImagePath = null;


    if (
        isset($_FILES['gambar']) &&
        $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        $file = $_FILES['gambar'];


        /*
        | ERROR UPLOAD
        */

        if ($file['error'] !== UPLOAD_ERR_OK) {

            die("Gagal mengupload gambar.");

        }


        /*
        | UKURAN
        */

        if ($file['size'] > 5 * 1024 * 1024) {

            die("Ukuran gambar maksimal 5 MB.");

        }


        /*
        | EKSTENSI
        */

        $allowedExtensions = [

            'jpg',
            'jpeg',
            'png',
            'webp'

        ];


        $extension = strtolower(
            pathinfo(
                $file['name'],
                PATHINFO_EXTENSION
            )
        );


        if (!in_array($extension, $allowedExtensions, true)) {

            die("Format gambar tidak didukung.");

        }


        /*
        | MIME
        */

        $allowedMime = [

            'image/jpeg',
            'image/png',
            'image/webp'

        ];


        $mime = mime_content_type(
            $file['tmp_name']
        );


        if (!in_array($mime, $allowedMime, true)) {

            die("File yang diupload bukan gambar yang valid.");

        }


        /*
        | FOLDER
        */

        $uploadDir = "../../images/galeri/";


        if (!is_dir($uploadDir)) {

            mkdir(
                $uploadDir,
                0777,
                true
            );

        }


        /*
        | NAMA FILE BARU
        */

        $filename =
            'galeri_' .
            time() .
            '_' .
            uniqid() .
            '.' .
            $extension;


        $targetPath =
            $uploadDir .
            $filename;


        /*
        | UPLOAD
        */

        if (!move_uploaded_file(
            $file['tmp_name'],
            $targetPath
        )) {

            die("Gagal memindahkan file gambar.");

        }


        /*
        | PATH DATABASE
        */

        $newImagePath =
            "images/galeri/" .
            $filename;


        $dataUpdate['gambar'] =
            $newImagePath;


        /*
        | SIMPAN PATH GAMBAR LAMA
        */

        if (!empty($oldData['gambar'])) {

            $oldImagePath =
                "../../" .
                ltrim(
                    $oldData['gambar'],
                    '/'
                );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE DATABASE
    |--------------------------------------------------------------------------
    */

    db_update(
        'galeri',
        $dataUpdate,
        'id',
        $id
    );


    /*
    |--------------------------------------------------------------------------
    | HAPUS GAMBAR LAMA
    |--------------------------------------------------------------------------
    */

    if (
        $newImagePath !== null &&
        $oldImagePath !== null &&
        file_exists($oldImagePath)
    ) {

        unlink($oldImagePath);

    }


    /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

    header(
        "Location: ../galeri.php?status=updated"
    );

    exit;


} catch (PDOException $e) {


    /*
    |--------------------------------------------------------------------------
    | HAPUS GAMBAR BARU JIKA DATABASE GAGAL
    |--------------------------------------------------------------------------
    */

    if (
        isset($targetPath) &&
        file_exists($targetPath)
    ) {

        unlink($targetPath);

    }


    die(
        "Gagal memperbarui data galeri: " .
        htmlspecialchars(
            $e->getMessage()
        )
    );

}