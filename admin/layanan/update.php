<?php

require_once "../../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../layanan.php");
    exit;
}


$id = (int)($_POST['id'] ?? 0);

$nama_layanan = trim($_POST['nama_layanan'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$status = $_POST['status'] ?? 'Aktif';


if ($id <= 0 || $nama_layanan === '' || $deskripsi === '') {

    header("Location: ../layanan.php?status=error");
    exit;

}


/* AMBIL DATA LAMA */

$dataLama = db_get(
    "SELECT * FROM layanan WHERE id = ?",
    [$id]
);


if (!$dataLama) {

    header("Location: ../layanan.php");
    exit;

}


$gambarPath = $dataLama['gambar'];


/* JIKA ADA GAMBAR BARU */

if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {

    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    $extension = strtolower(
        pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION)
    );


    if (!in_array($extension, $allowed)) {

        header("Location: edit.php?id=$id&status=format");
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

        header("Location: edit.php?id=$id&status=upload");
        exit;

    }


    /* HAPUS GAMBAR LAMA */

    if (!empty($dataLama['gambar'])) {

        $gambarLama = "../../" . $dataLama['gambar'];

        if (file_exists($gambarLama)) {
            unlink($gambarLama);
        }

    }


    $gambarPath = "images/layanan/" . $filename;
}


/* UPDATE DATABASE */

$sql = "
    UPDATE layanan SET

        nama_layanan = :nama_layanan,
        deskripsi = :deskripsi,
        gambar = :gambar,
        status = :status

    WHERE id = :id
";


db_query($sql, [
    ':nama_layanan' => $nama_layanan,
    ':deskripsi' => $deskripsi,
    ':gambar' => $gambarPath,
    ':status' => $status,
    ':id' => $id
]);


header("Location: ../layanan.php?status=updated");
exit;