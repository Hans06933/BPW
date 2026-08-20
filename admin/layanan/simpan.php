<?php

require_once "../../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../layanan.php");
    exit;
}


$nama_layanan = trim($_POST['nama_layanan'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$status = $_POST['status'] ?? 'Aktif';


if ($nama_layanan === '' || $deskripsi === '') {

    header("Location: tambah.php?status=error");
    exit;

}


$gambarPath = null;


/* UPLOAD GAMBAR */

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    $extension = strtolower(
        pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION)
    );


    if (!in_array($extension, $allowed)) {

        header("Location: tambah.php?status=format");
        exit;

    }


    $folder = "../../images/layanan/";

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }


    $filename =
        time() . '_' .
        uniqid() . '.' .
        $extension;


    $target = $folder . $filename;


    if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {

        header("Location: tambah.php?status=upload");
        exit;

    }


    $gambarPath = "images/layanan/" . $filename;
}


/* SIMPAN DATABASE */

$sql = "
    INSERT INTO layanan
    (
        nama_layanan,
        deskripsi,
        gambar,
        status
    )
    VALUES
    (
        :nama_layanan,
        :deskripsi,
        :gambar,
        :status
    )
";


db_query($sql, [
    ':nama_layanan' => $nama_layanan,
    ':deskripsi' => $deskripsi,
    ':gambar' => $gambarPath,
    ':status' => $status
]);


header("Location: ../layanan.php?status=success");
exit;