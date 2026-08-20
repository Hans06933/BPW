<?php

require_once __DIR__ . '/../../config/database.php';

try {

    $db = (new Database())->getConnection();


    // =========================================================
    // AMBIL DATA FORM
    // =========================================================

    $nama_destinasi = trim($_POST['nama_destinasi'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $wilayah = trim($_POST['wilayah'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $harga_tiket_masuk = $_POST['harga_tiket_masuk'] ?? 0;
    $jam_operasional = trim($_POST['jam_operasional'] ?? '');
    $rating = $_POST['rating'] ?? 0;
    $total_review = $_POST['total_review'] ?? 0;
    $status = $_POST['status'] ?? 'aktif';
    $views = $_POST['views'] ?? 0;


    // =========================================================
    // VALIDASI
    // =========================================================

    if ($nama_destinasi === '') {
        die('Nama destinasi wajib diisi.');
    }

    if ($slug === '') {
        die('Slug wajib diisi.');
    }

    if ($wilayah === '') {
        die('Wilayah wajib diisi.');
    }


    // =========================================================
    // UPLOAD GAMBAR
    // =========================================================

    $gambar_utama = null;

    if (
        isset($_FILES['gambar_utama']) &&
        $_FILES['gambar_utama']['error'] === UPLOAD_ERR_OK
    ) {

        $file = $_FILES['gambar_utama'];

        $allowed = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];

        $extension = strtolower(
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $allowed)) {
            die('Format gambar tidak diperbolehkan.');
        }


        // Maksimal 5 MB
        if ($file['size'] > 5 * 1024 * 1024) {
            die('Ukuran gambar maksimal 5 MB.');
        }


        // Nama file unik
        $gambar_utama =
            time() . '_' .
            uniqid() . '.' .
            $extension;


        // Folder gambar
        $uploadDir = __DIR__ . '/../../images/destinasi/';


        // Buat folder jika belum ada
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }


        // Upload
        if (!move_uploaded_file(
            $file['tmp_name'],
            $uploadDir . $gambar_utama
        )) {

            die('Gagal mengupload gambar.');
        }
    }


    // =========================================================
    // INSERT DATABASE
    // =========================================================

    $sql = "
        INSERT INTO destinasi (
            nama_destinasi,
            slug,
            wilayah,
            kategori,
            deskripsi,
            alamat,
            harga_tiket_masuk,
            jam_operasional,
            gambar_utama,
            rating,
            total_review,
            status,
            views
        )
        VALUES (
            :nama_destinasi,
            :slug,
            :wilayah,
            :kategori,
            :deskripsi,
            :alamat,
            :harga_tiket_masuk,
            :jam_operasional,
            :gambar_utama,
            :rating,
            :total_review,
            :status,
            :views
        )
    ";


    $stmt = $db->prepare($sql);


    $stmt->execute([

        ':nama_destinasi' => $nama_destinasi,

        ':slug' => $slug,

        ':wilayah' => $wilayah,

        ':kategori' => $kategori,

        ':deskripsi' => $deskripsi,

        ':alamat' => $alamat,

        ':harga_tiket_masuk' => $harga_tiket_masuk,

        ':jam_operasional' => $jam_operasional,

        ':gambar_utama' => $gambar_utama,

        ':rating' => $rating,

        ':total_review' => $total_review,

        ':status' => $status,

        ':views' => $views

    ]);


    // =========================================================
    // REDIRECT KE KELOLA DESTINASI
    // Tambahkan timestamp untuk mencegah cache
    // =========================================================

    header(
        "Location: ../destinasi.php?status=success&refresh="
        . time()
    );

    exit;


} catch (PDOException $e) {

    die("
        <div style='
            font-family:Arial;
            padding:30px;
            background:#fee2e2;
            color:#991b1b;
            margin:20px;
            border-radius:10px;
        '>

            <h2>Gagal Menyimpan Data</h2>

            <p>
                " . htmlspecialchars($e->getMessage()) . "
            </p>

        </div>
    ");
}