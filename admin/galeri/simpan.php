<?php

require_once "../../config/database.php";

/*
|--------------------------------------------------------------------------
| HANYA POST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: ../galeri.php");
    exit;

}


/*
|--------------------------------------------------------------------------
| AMBIL DATA
|--------------------------------------------------------------------------
*/

$judul = trim($_POST['judul'] ?? '');
$kategori = trim($_POST['kategori'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$status = $_POST['status'] ?? 'aktif';


/*
|--------------------------------------------------------------------------
| VALIDASI
|--------------------------------------------------------------------------
*/

if ($judul === '') {

    header("Location: tambah.php?error=judul");
    exit;

}


if (!in_array($status, ['aktif', 'nonaktif'], true)) {
    $status = 'aktif';
}


/*
|--------------------------------------------------------------------------
| SLUG
|--------------------------------------------------------------------------
*/

$slug = strtolower($judul);

$slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);

$slug = trim($slug, '-');

if ($slug === '') {
    $slug = 'galeri-' . time();
}


/*
|--------------------------------------------------------------------------
| CEK SLUG
|--------------------------------------------------------------------------
*/

try {

    $check = db_get(
        "SELECT id FROM galeri WHERE slug = :slug LIMIT 1",
        [
            'slug' => $slug
        ]
    );

    if ($check) {

        $slug .= '-' . time();

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI GAMBAR
    |--------------------------------------------------------------------------
    */

    if (!isset($_FILES['gambar'])) {

        header("Location: tambah.php?error=gambar");
        exit;

    }


    $file = $_FILES['gambar'];


    if ($file['error'] !== UPLOAD_ERR_OK) {

        header("Location: tambah.php?error=upload");
        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | UKURAN MAKSIMAL 5 MB
    |--------------------------------------------------------------------------
    */

    if ($file['size'] > 5 * 1024 * 1024) {

        header("Location: tambah.php?error=size");
        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | CEK EKSTENSI
    |--------------------------------------------------------------------------
    */

    $allowedExtensions = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    $extension = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );


    if (!in_array($extension, $allowedExtensions, true)) {

        header("Location: tambah.php?error=format");
        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | CEK MIME TYPE
    |--------------------------------------------------------------------------
    */

    $allowedMime = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    $mime = mime_content_type($file['tmp_name']);

    if (!in_array($mime, $allowedMime, true)) {

        header("Location: tambah.php?error=format");
        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | FOLDER GAMBAR
    |--------------------------------------------------------------------------
    */

    $uploadDir = "../../images/galeri/";


    if (!is_dir($uploadDir)) {

        mkdir($uploadDir, 0777, true);

    }


    /*
    |--------------------------------------------------------------------------
    | NAMA FILE
    |--------------------------------------------------------------------------
    */

    $filename = 'galeri_' . time() . '_' . uniqid() . '.' . $extension;

    $targetPath = $uploadDir . $filename;


    /*
    |--------------------------------------------------------------------------
    | UPLOAD
    |--------------------------------------------------------------------------
    */

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {

        header("Location: tambah.php?error=move");
        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | PATH YANG DISIMPAN KE DATABASE
    |--------------------------------------------------------------------------
    */

    $gambar = "images/galeri/" . $filename;


    /*
    |--------------------------------------------------------------------------
    | INSERT
    |--------------------------------------------------------------------------
    */

    db_insert('galeri', [

        'judul' => $judul,
        'slug' => $slug,
        'gambar' => $gambar,
        'kategori' => $kategori !== '' ? $kategori : null,
        'deskripsi' => $deskripsi !== '' ? $deskripsi : null,
        'status' => $status

    ]);


    /*
    |--------------------------------------------------------------------------
    | KEMBALI KE GALERI
    |--------------------------------------------------------------------------
    */

    header("Location: ../galeri.php?status=saved");
    exit;


} catch (PDOException $e) {

    /*
     * Jika database gagal setelah gambar terupload,
     * hapus gambar supaya tidak menjadi file sampah.
     */

    if (
        isset($targetPath) &&
        file_exists($targetPath)
    ) {

        unlink($targetPath);

    }


    die(
        "Gagal menyimpan data galeri: " .
        htmlspecialchars($e->getMessage())
    );

}