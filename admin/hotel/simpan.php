<?php
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../hotel.php');
    exit;
}

// =========================================================
// AMBIL DATA
// =========================================================
$nama_hotel     = trim($_POST['nama_hotel'] ?? '');
$destinasi      = trim($_POST['destinasi'] ?? '');
$bintang        = (int)($_POST['bintang'] ?? 3);
$harga_per_malam = (float)($_POST['harga_per_malam'] ?? 0);
$deskripsi      = trim($_POST['deskripsi'] ?? '');
$alamat         = trim($_POST['alamat'] ?? '');
$fasilitas      = trim($_POST['fasilitas'] ?? '');
$rating         = (float)($_POST['rating'] ?? 0);
$total_review   = (int)($_POST['total_review'] ?? 0);
$status         = strtolower(trim($_POST['status'] ?? 'aktif'));

// =========================================================
// VALIDASI
// =========================================================
if ($nama_hotel === '') {
    header('Location: tambah.php?error=Nama hotel wajib diisi');
    exit;
}

if ($destinasi === '') {
    header('Location: tambah.php?error=Destinasi wajib diisi');
    exit;
}

if ($harga_per_malam < 0) {
    header('Location: tambah.php?error=Harga hotel tidak valid');
    exit;
}

if ($bintang < 1 || $bintang > 5) {
    $bintang = 3;
}

if ($rating < 0 || $rating > 5) {
    $rating = 0;
}

if ($total_review < 0) {
    $total_review = 0;
}

if (!in_array($status, ['aktif', 'nonaktif'], true)) {
    $status = 'aktif';
}

// =========================================================
// BUAT SLUG
// =========================================================
function buatSlug($text)
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

$slug = buatSlug($nama_hotel);

if ($slug === '') {
    $slug = 'hotel';
}

// =========================================================
// FOLDER GAMBAR
// =========================================================
$folder = __DIR__ . '/../../images/hotel/';

if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

// =========================================================
// FILE YANG BERHASIL DIUPLOAD
// =========================================================
$gambarUtama = null;
$gambarGaleri = [];

// =========================================================
// UPLOAD GAMBAR UTAMA
// =========================================================
try {
    $slugLama = db_get(
        "SELECT id FROM hotel WHERE slug = ? LIMIT 1",
        [$slug]
    );

    if ($slugLama) {
        $slug .= '-' . time();
    }

    if (isset($_FILES['gambar_utama']) && $_FILES['gambar_utama']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['gambar_utama']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Gagal mengupload gambar utama.');
        }

        if ($_FILES['gambar_utama']['size'] > 5 * 1024 * 1024) {
            throw new Exception('Ukuran gambar utama maksimal 5 MB.');
        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($_FILES['gambar_utama']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed, true)) {
            throw new Exception('Format gambar utama harus JPG, JPEG, PNG atau WEBP.');
        }

        $gambarUtama = time() . '_utama_' . uniqid() . '.' . $extension;
        $target = $folder . $gambarUtama;

        if (!move_uploaded_file($_FILES['gambar_utama']['tmp_name'], $target)) {
            throw new Exception('Gambar utama gagal disimpan.');
        }
    }

    // =========================================================
    // SIMPAN HOTEL
    // =========================================================
    $hotelId = db_insert('hotel', [
        'nama_hotel'      => $nama_hotel,
        'slug'            => $slug,
        'destinasi'       => $destinasi,
        'bintang'         => $bintang,
        'harga_per_malam' => $harga_per_malam,
        'deskripsi'       => $deskripsi !== '' ? $deskripsi : null,
        'alamat'          => $alamat !== '' ? $alamat : null,
        'fasilitas'       => $fasilitas !== '' ? $fasilitas : null,
        'gambar_utama'    => $gambarUtama,
        'rating'          => $rating,
        'total_review'    => $total_review,
        'status'          => $status
    ]);

    // =========================================================
    // UPLOAD GALERI
    // =========================================================
    if (isset($_FILES['gambar_galeri']) && isset($_FILES['gambar_galeri']['name']) && is_array($_FILES['gambar_galeri']['name'])) {
        $jumlahFile = count($_FILES['gambar_galeri']['name']);

        for ($i = 0; $i < $jumlahFile; $i++) {
            // Lewati file kosong
            if ($_FILES['gambar_galeri']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            // Cek error upload
            if ($_FILES['gambar_galeri']['error'][$i] !== UPLOAD_ERR_OK) {
                throw new Exception('Salah satu gambar galeri gagal diupload.');
            }

            // Maksimal 5 MB
            if ($_FILES['gambar_galeri']['size'][$i] > 5 * 1024 * 1024) {
                throw new Exception('Ukuran setiap gambar galeri maksimal 5 MB.');
            }

            // Validasi extension
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $extension = strtolower(pathinfo($_FILES['gambar_galeri']['name'][$i], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowed, true)) {
                throw new Exception('Format gambar galeri harus JPG, JPEG, PNG atau WEBP.');
            }

            // Nama file
            $namaGaleri = time() . '_galeri_' . uniqid() . '.' . $extension;
            $targetGaleri = $folder . $namaGaleri;

            // Simpan file
            if (!move_uploaded_file($_FILES['gambar_galeri']['tmp_name'][$i], $targetGaleri)) {
                throw new Exception('Gambar galeri gagal disimpan.');
            }

            // Simpan ke database
            db_insert('hotel_galeri', [
                'hotel_id' => $hotelId,
                'gambar'   => $namaGaleri
            ]);

            $gambarGaleri[] = $namaGaleri;
        }
    }

    // =========================================================
    // BERHASIL
    // =========================================================
    header('Location: ../hotel.php?success=' . urlencode('Hotel berhasil ditambahkan'));
    exit;

} catch (Exception $e) {
    // HAPUS GAMBAR UTAMA JIKA GAGAL
    if (!empty($gambarUtama) && file_exists($folder . $gambarUtama)) {
        unlink($folder . $gambarUtama);
    }

    // HAPUS GAMBAR GALERI JIKA GAGAL
    foreach ($gambarGaleri as $gambar) {
        if (file_exists($folder . $gambar)) {
            unlink($folder . $gambar);
        }
    }

    header('Location: tambah.php?error=' . urlencode($e->getMessage()));
    exit;
}