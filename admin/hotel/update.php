<?php
require_once __DIR__ . '/../../config/database.php';

// =========================================================
// CEK METHOD
// =========================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../hotel.php');
    exit;
}

// =========================================================
// AMBIL DATA
// =========================================================
$id              = (int)($_POST['id'] ?? 0);
$nama_hotel      = trim($_POST['nama_hotel'] ?? '');
$destinasi       = trim($_POST['destinasi'] ?? '');
$bintang         = (int)($_POST['bintang'] ?? 3);
$harga_per_malam = (float)($_POST['harga_per_malam'] ?? 0);
$deskripsi       = trim($_POST['deskripsi'] ?? '');
$alamat          = trim($_POST['alamat'] ?? '');
$fasilitas       = trim($_POST['fasilitas'] ?? '');
$rating          = (float)($_POST['rating'] ?? 0);
$total_review    = (int)($_POST['total_review'] ?? 0);
$status          = strtolower(trim($_POST['status'] ?? 'aktif'));

// =========================================================
// VALIDASI ID
// =========================================================
if ($id <= 0) {
    header('Location: ../hotel.php?error=' . urlencode('ID hotel tidak valid'));
    exit;
}

// =========================================================
// VALIDASI DATA
// =========================================================
if ($nama_hotel === '') {
    header('Location: edit.php?id=' . $id . '&error=' . urlencode('Nama hotel wajib diisi'));
    exit;
}

if ($destinasi === '') {
    header('Location: edit.php?id=' . $id . '&error=' . urlencode('Destinasi wajib diisi'));
    exit;
}

if ($harga_per_malam < 0) {
    header('Location: edit.php?id=' . $id . '&error=' . urlencode('Harga hotel tidak valid'));
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
// FOLDER GAMBAR
// =========================================================
$folder = __DIR__ . '/../../images/hotel/';

if (!is_dir($folder)) {
    if (!mkdir($folder, 0777, true)) {
        header('Location: edit.php?id=' . $id . '&error=' . urlencode('Folder gambar gagal dibuat'));
        exit;
    }
}

// =========================================================
// FILE BARU
// =========================================================
$gambarUtamaBaru = null;
$gambarGaleriBaru = [];

// =========================================================
// FUNCTION SLUG
// =========================================================
function buatSlug($text)
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// =========================================================
// PROSES UPDATE
// =========================================================
try {
    // AMBIL DATA HOTEL LAMA
    $hotelLama = db_get(
        "SELECT * FROM hotel WHERE id = ? LIMIT 1",
        [$id]
    );

    if (!$hotelLama) {
        throw new Exception('Data hotel tidak ditemukan.');
    }

    $gambarUtamaLama = $hotelLama['gambar_utama'] ?? '';

    // BUAT SLUG
    $slug = buatSlug($nama_hotel);

    if ($slug === '') {
        $slug = 'hotel';
    }

    $cekSlug = db_get(
        "SELECT id FROM hotel WHERE slug = ? AND id != ? LIMIT 1",
        [$slug, $id]
    );

    if ($cekSlug) {
        $slug .= '-' . time();
    }

    // GAMBAR UTAMA
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

        $gambarUtamaBaru = time() . '_utama_' . uniqid() . '.' . $extension;
        $target = $folder . $gambarUtamaBaru;

        if (!move_uploaded_file($_FILES['gambar_utama']['tmp_name'], $target)) {
            throw new Exception('Gambar utama gagal disimpan.');
        }
    } else {
        $gambarUtamaBaru = $gambarUtamaLama;
    }

    // UPDATE DATA HOTEL
    $dataHotel = [
        'nama_hotel'      => $nama_hotel,
        'slug'            => $slug,
        'destinasi'       => $destinasi,
        'bintang'         => $bintang,
        'harga_per_malam' => $harga_per_malam,
        'deskripsi'       => $deskripsi !== '' ? $deskripsi : null,
        'alamat'          => $alamat !== '' ? $alamat : null,
        'fasilitas'       => $fasilitas !== '' ? $fasilitas : null,
        'gambar_utama'    => $gambarUtamaBaru,
        'rating'          => $rating,
        'total_review'    => $total_review,
        'status'          => $status
    ];

    $hasilUpdate = db_update('hotel', $dataHotel, 'id', $id);

    if (!$hasilUpdate) {
        throw new Exception('Data hotel gagal diperbarui.');
    }

    // HAPUS GAMBAR UTAMA LAMA
    if ($gambarUtamaBaru !== $gambarUtamaLama && !empty($gambarUtamaLama)) {
        $fileLama = $folder . $gambarUtamaLama;
        if (file_exists($fileLama)) {
            unlink($fileLama);
        }
    }

    // HAPUS GALERI YANG DIPILIH
    if (isset($_POST['hapus_galeri']) && is_array($_POST['hapus_galeri'])) {
        foreach ($_POST['hapus_galeri'] as $galeriId) {
            $galeriId = (int)$galeriId;

            if ($galeriId <= 0) {
                continue;
            }

            $gambar = db_get(
                "SELECT * FROM hotel_galeri WHERE id = ? AND hotel_id = ? LIMIT 1",
                [$galeriId, $id]
            );

            if (!$gambar) {
                continue;
            }

            $hapusGaleri = db_query(
                "DELETE FROM hotel_galeri WHERE id = ? AND hotel_id = ?",
                [$galeriId, $id]
            );

            if ($hapusGaleri && !empty($gambar['gambar'])) {
                $fileGaleri = $folder . $gambar['gambar'];
                if (file_exists($fileGaleri)) {
                    unlink($fileGaleri);
                }
            }
        }
    }

    // TAMBAH GALERI BARU
    if (isset($_FILES['gambar_galeri']) && isset($_FILES['gambar_galeri']['name']) && is_array($_FILES['gambar_galeri']['name'])) {
        $jumlahFile = count($_FILES['gambar_galeri']['name']);

        for ($i = 0; $i < $jumlahFile; $i++) {
            if ($_FILES['gambar_galeri']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($_FILES['gambar_galeri']['error'][$i] !== UPLOAD_ERR_OK) {
                throw new Exception('Salah satu gambar galeri gagal diupload.');
            }

            if ($_FILES['gambar_galeri']['size'][$i] > 5 * 1024 * 1024) {
                throw new Exception('Ukuran setiap gambar galeri maksimal 5 MB.');
            }

            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $extension = strtolower(pathinfo($_FILES['gambar_galeri']['name'][$i], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowed, true)) {
                throw new Exception('Format gambar galeri harus JPG, JPEG, PNG atau WEBP.');
            }

            $namaGaleri = time() . '_galeri_' . uniqid() . '.' . $extension;
            $target = $folder . $namaGaleri;

            if (!move_uploaded_file($_FILES['gambar_galeri']['tmp_name'][$i], $target)) {
                throw new Exception('Gambar galeri gagal disimpan.');
            }

            $idGaleri = db_insert('hotel_galeri', [
                'hotel_id' => $id,
                'gambar'   => $namaGaleri
            ]);

            if (!$idGaleri) {
                if (file_exists($target)) {
                    unlink($target);
                }
                throw new Exception('Gambar galeri gagal disimpan ke database.');
            }

            $gambarGaleriBaru[] = $namaGaleri;
        }
    }

    // BERHASIL
    header('Location: ../hotel.php?success=' . urlencode('Data hotel berhasil diperbarui'));
    exit;

} catch (Exception $e) {
    // HAPUS GAMBAR UTAMA BARU JIKA PROSES GAGAL
    if (!empty($gambarUtamaBaru) && isset($hotelLama) && $gambarUtamaBaru !== ($hotelLama['gambar_utama'] ?? '')) {
        $file = $folder . $gambarUtamaBaru;
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // HAPUS GALERI BARU JIKA PROSES GAGAL
    foreach ($gambarGaleriBaru as $gambar) {
        $file = $folder . $gambar;
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // KEMBALI KE EDIT
    header('Location: edit.php?id=' . $id . '&error=' . urlencode($e->getMessage()));
    exit;
}