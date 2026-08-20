<?php
// simpan.php
require_once "../../config/database.php";

// Pastikan request datang dari form POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../paket.php');
    exit;
}

try {

    // =========================================================
    // KONEKSI DATABASE
    // =========================================================
    $db = (new Database())->getConnection();

    // =========================================================
    // AMBIL DATA FORM
    // =========================================================
    $nama_paket      = trim($_POST['nama_paket'] ?? '');
    $slug            = trim($_POST['slug'] ?? '');
    $destinasi       = trim($_POST['destinasi'] ?? '');
    $durasi          = trim($_POST['durasi'] ?? '');
    $minimal_peserta = (int)($_POST['minimal_peserta'] ?? 1);

    $harga_normal = (float)($_POST['harga_normal'] ?? 0);

    $harga_diskon = null;
    if (isset($_POST['harga_diskon']) && $_POST['harga_diskon'] !== '') {
        $harga_diskon = (float)$_POST['harga_diskon'];
    }

    $diskon_persen = (int)($_POST['diskon_persen'] ?? 0);

    $deskripsi      = trim($_POST['deskripsi'] ?? '');
    $itinerary      = trim($_POST['itinerary'] ?? '');
    $fasilitas      = trim($_POST['fasilitas'] ?? '');
    $termasuk       = trim($_POST['termasuk'] ?? '');
    $tidak_termasuk = trim($_POST['tidak_termasuk'] ?? '');

    $kuota   = (int)($_POST['kuota'] ?? 0);
    $tersisa = (int)($_POST['tersisa'] ?? 0);

    $status        = $_POST['status'] ?? 'aktif';
    $is_featured   = (int)($_POST['is_featured'] ?? 0);
    $is_flash_sale = (int)($_POST['is_flash_sale'] ?? 0);


    // =========================================================
    // VALIDASI DATA WAJIB
    // =========================================================
    if ($nama_paket === '') {
        die('Nama paket wajib diisi.');
    }

    if ($slug === '') {
        die('Slug wajib diisi.');
    }

    if ($destinasi === '') {
        die('Destinasi wajib diisi.');
    }

    if ($durasi === '') {
        die('Durasi wajib diisi.');
    }

    if ($harga_normal <= 0) {
        die('Harga normal harus lebih dari 0.');
    }


    // =========================================================
    // VALIDASI SLUG
    // =========================================================
    $stmtCheck = $db->prepare("
        SELECT id
        FROM paket_wisata
        WHERE slug = :slug
        LIMIT 1
    ");

    $stmtCheck->execute([
        ':slug' => $slug
    ]);

    if ($stmtCheck->fetch()) {
        die('Error: Slug sudah digunakan. Silakan gunakan slug yang berbeda.');
    }


    // =========================================================
    // FOLDER GAMBAR
    // =========================================================
    // Lokasi file:
    //
    // BPW/
    // ├── images/
    // │   └── paket/
    // └── admin/
    //     └── paket/
    //         └── simpan.php
    //
    // Dari admin/paket/simpan.php
    // ../../images/paket/ = BPW/images/paket/

    $uploadDir = '../../images/paket/';

    // Buat folder jika belum ada
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            die('Gagal membuat folder images/paket/.');
        }
    }


    // =========================================================
    // VALIDASI FILE
    // =========================================================
    $allowedExtensions = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];

    $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];


    // =========================================================
    // UPLOAD GAMBAR UTAMA
    // =========================================================
    $gambar_utama = '';

    if (
        isset($_FILES['gambar_utama']) &&
        $_FILES['gambar_utama']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES['gambar_utama']['error'] !== UPLOAD_ERR_OK) {
            die('Gagal mengupload gambar utama.');
        }

        // Maksimal 5 MB
        if ($_FILES['gambar_utama']['size'] > 5 * 1024 * 1024) {
            die('Gambar utama maksimal berukuran 5 MB.');
        }

        $tmpName = $_FILES['gambar_utama']['tmp_name'];
        $originalName = $_FILES['gambar_utama']['name'];

        // Cek MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpName);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            die('Format gambar utama tidak valid. Gunakan JPG, PNG, atau WEBP.');
        }

        // Ambil ekstensi
        $extension = strtolower(
            pathinfo($originalName, PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $allowedExtensions, true)) {
            die('Ekstensi gambar utama tidak valid.');
        }

        // Nama file baru
        $newFileName =
            'paket_' .
            date('YmdHis') .
            '_' .
            uniqid() .
            '.' .
            $extension;

        $destination = $uploadDir . $newFileName;

        if (!move_uploaded_file($tmpName, $destination)) {
            die('Gagal menyimpan gambar utama.');
        }

        // Yang disimpan ke database hanya nama file
        $gambar_utama = $newFileName;
    } else {
        die('Gambar utama wajib diupload.');
    }


    // =========================================================
    // UPLOAD GAMBAR LAIN
    // =========================================================
    $gambarLain = [];

    if (
        isset($_FILES['gambar_lain']) &&
        isset($_FILES['gambar_lain']['name']) &&
        is_array($_FILES['gambar_lain']['name'])
    ) {

        foreach ($_FILES['gambar_lain']['name'] as $index => $originalName) {

            // Lewati jika tidak ada file
            if (
                !isset($_FILES['gambar_lain']['error'][$index]) ||
                $_FILES['gambar_lain']['error'][$index] === UPLOAD_ERR_NO_FILE
            ) {
                continue;
            }

            if ($_FILES['gambar_lain']['error'][$index] !== UPLOAD_ERR_OK) {
                continue;
            }

            // Maksimal 5 MB per gambar
            if ($_FILES['gambar_lain']['size'][$index] > 5 * 1024 * 1024) {
                continue;
            }

            $tmpName = $_FILES['gambar_lain']['tmp_name'][$index];

            // Cek MIME
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tmpName);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedMimeTypes, true)) {
                continue;
            }

            // Ekstensi
            $extension = strtolower(
                pathinfo($originalName, PATHINFO_EXTENSION)
            );

            if (!in_array($extension, $allowedExtensions, true)) {
                continue;
            }

            // Nama file baru
            $newFileName =
                'paket_' .
                date('YmdHis') .
                '_' .
                uniqid() .
                '.' .
                $extension;

            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($tmpName, $destination)) {
                $gambarLain[] = $newFileName;
            }
        }
    }


    // =========================================================
    // GABUNG NAMA GAMBAR LAIN
    // =========================================================
    // Database akan menyimpan seperti:
    //
    // foto1.jpg,foto2.jpg,foto3.jpg
    //
    $gambar_lain = !empty($gambarLain)
        ? implode(',', $gambarLain)
        : null;


    // =========================================================
    // INSERT DATABASE
    // =========================================================
    $sql = "
        INSERT INTO paket_wisata (
            nama_paket,
            slug,
            destinasi,
            durasi,
            minimal_peserta,
            harga_normal,
            harga_diskon,
            diskon_persen,
            deskripsi,
            itinerary,
            fasilitas,
            termasuk,
            tidak_termasuk,
            gambar_utama,
            gambar_lain,
            kuota,
            tersisa,
            status,
            is_featured,
            is_flash_sale
        )
        VALUES (
            :nama_paket,
            :slug,
            :destinasi,
            :durasi,
            :minimal_peserta,
            :harga_normal,
            :harga_diskon,
            :diskon_persen,
            :deskripsi,
            :itinerary,
            :fasilitas,
            :termasuk,
            :tidak_termasuk,
            :gambar_utama,
            :gambar_lain,
            :kuota,
            :tersisa,
            :status,
            :is_featured,
            :is_flash_sale
        )
    ";

    $stmt = $db->prepare($sql);

    $stmt->execute([

        ':nama_paket'       => $nama_paket,
        ':slug'             => $slug,
        ':destinasi'        => $destinasi,
        ':durasi'           => $durasi,
        ':minimal_peserta'  => $minimal_peserta,

        ':harga_normal'     => $harga_normal,
        ':harga_diskon'     => $harga_diskon,
        ':diskon_persen'    => $diskon_persen,

        ':deskripsi'        => $deskripsi,
        ':itinerary'        => $itinerary,
        ':fasilitas'        => $fasilitas,
        ':termasuk'         => $termasuk,
        ':tidak_termasuk'   => $tidak_termasuk,

        ':gambar_utama'     => $gambar_utama,
        ':gambar_lain'      => $gambar_lain,

        ':kuota'            => $kuota,
        ':tersisa'          => $tersisa,

        ':status'           => $status,
        ':is_featured'      => $is_featured,
        ':is_flash_sale'    => $is_flash_sale
    ]);


    // =========================================================
    // BERHASIL
    // =========================================================
    header('Location: ../paket.php?status=success');
    exit;


} catch (PDOException $e) {

    // Jika database gagal, tampilkan error
    die(
        'Gagal menyimpan data paket wisata: ' .
        htmlspecialchars($e->getMessage())
    );
}
?>