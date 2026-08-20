<?php

require_once '../../config/koneksi.php';

/*
|--------------------------------------------------------------------------
| KONFIGURASI
|--------------------------------------------------------------------------
*/

$uploadDir = __DIR__ . '/../../assets/paket/';

$allowedTypes = [
    'image/jpeg',
    'image/png',
    'image/webp'
];

$maxFileSize = 5 * 1024 * 1024; // 5 MB


/*
|--------------------------------------------------------------------------
| FUNGSI REDIRECT
|--------------------------------------------------------------------------
*/

function redirectError($message)
{
    echo "
    <script>
        alert(" . json_encode($message) . ");
        history.back();
    </script>
    ";
    exit;
}


/*
|--------------------------------------------------------------------------
| CEK REQUEST
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../paket.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL ID
|--------------------------------------------------------------------------
*/

$id = isset($_POST['id'])
    ? (int) $_POST['id']
    : 0;

if ($id <= 0) {
    redirectError('ID paket tidak valid.');
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA LAMA
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT *
    FROM paket_wisata
    WHERE id = ?
    LIMIT 1
");

if (!$stmt) {
    redirectError('Gagal menyiapkan query.');
}

$stmt->bind_param('i', $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    redirectError('Data paket tidak ditemukan.');
}

$oldData = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| DATA FORM
|--------------------------------------------------------------------------
*/

$nama_paket = trim($_POST['nama_paket'] ?? '');

$slug = trim($_POST['slug'] ?? '');

$destinasi = trim($_POST['destinasi'] ?? '');

$durasi = trim($_POST['durasi'] ?? '');

$harga_normal = (float) ($_POST['harga_normal'] ?? 0);

$harga_diskon = ($_POST['harga_diskon'] ?? '') !== ''
    ? (float) $_POST['harga_diskon']
    : null;

$diskon_persen = (int) ($_POST['diskon_persen'] ?? 0);

$deskripsi = trim($_POST['deskripsi'] ?? '');

$itinerary = trim($_POST['itinerary'] ?? '');

$fasilitas = trim($_POST['fasilitas'] ?? '');

$termasuk = trim($_POST['termasuk'] ?? '');

$tidak_termasuk = trim($_POST['tidak_termasuk'] ?? '');

$kuota = (int) ($_POST['kuota'] ?? 0);

$tersisa = (int) ($_POST['tersisa'] ?? 0);

$minimal_peserta = (int) ($_POST['minimal_peserta'] ?? 2);

$status = $_POST['status'] ?? 'aktif';

$is_featured = isset($_POST['is_featured'])
    ? 1
    : 0;

$is_flash_sale = isset($_POST['is_flash_sale'])
    ? 1
    : 0;


/*
|--------------------------------------------------------------------------
| VALIDASI
|--------------------------------------------------------------------------
*/

if (
    $nama_paket === '' ||
    $slug === '' ||
    $destinasi === '' ||
    $durasi === '' ||
    $harga_normal <= 0
) {
    redirectError('Data wajib belum lengkap.');
}


/*
|--------------------------------------------------------------------------
| BERSIHKAN SLUG
|--------------------------------------------------------------------------
*/

$slug = strtolower($slug);

$slug = preg_replace(
    '/[^a-z0-9\-]+/',
    '-',
    $slug
);

$slug = trim($slug, '-');


/*
|--------------------------------------------------------------------------
| VALIDASI STATUS
|--------------------------------------------------------------------------
*/

$statusValid = [
    'aktif',
    'nonaktif',
    'habis'
];

if (!in_array($status, $statusValid, true)) {
    $status = 'aktif';
}


/*
|--------------------------------------------------------------------------
| VALIDASI ANGKA
|--------------------------------------------------------------------------
*/

if ($diskon_persen < 0) {
    $diskon_persen = 0;
}

if ($diskon_persen > 100) {
    $diskon_persen = 100;
}

if ($kuota < 0) {
    $kuota = 0;
}

if ($tersisa < 0) {
    $tersisa = 0;
}

if ($minimal_peserta < 1) {
    $minimal_peserta = 2;
}


/*
|--------------------------------------------------------------------------
| CEK SLUG DUPLIKAT
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT id
    FROM paket_wisata
    WHERE slug = ?
    AND id != ?
    LIMIT 1
");

if (!$stmt) {
    redirectError('Gagal mengecek slug.');
}

$stmt->bind_param(
    'si',
    $slug,
    $id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $stmt->close();

    redirectError(
        'Slug tersebut sudah digunakan oleh paket lain.'
    );
}

$stmt->close();


/*
|--------------------------------------------------------------------------
| PASTIKAN FOLDER UPLOAD ADA
|--------------------------------------------------------------------------
*/

if (!is_dir($uploadDir)) {

    if (!mkdir($uploadDir, 0777, true)) {
        redirectError(
            'Folder upload tidak dapat dibuat.'
        );
    }
}


/*
|--------------------------------------------------------------------------
| FUNGSI UPLOAD GAMBAR
|--------------------------------------------------------------------------
*/

function uploadImage(
    $file,
    $uploadDir,
    $allowedTypes,
    $maxFileSize
) {

    if (
        !isset($file) ||
        $file['error'] !== UPLOAD_ERR_OK
    ) {

        return [
            'success' => false,
            'message' => 'File gambar tidak valid.'
        ];
    }


    /*
    | Cek ukuran
    */

    if ($file['size'] > $maxFileSize) {

        return [
            'success' => false,
            'message' => 'Ukuran gambar maksimal 5 MB.'
        ];
    }


    /*
    | Cek MIME menggunakan file asli
    */

    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    $mimeType = finfo_file(
        $finfo,
        $file['tmp_name']
    );

    finfo_close($finfo);


    if (!in_array(
        $mimeType,
        $allowedTypes,
        true
    )) {

        return [
            'success' => false,
            'message' => 'Format gambar harus JPG, PNG, atau WEBP.'
        ];
    }


    /*
    | Tentukan ekstensi
    */

    switch ($mimeType) {

        case 'image/jpeg':
            $extension = 'jpg';
            break;

        case 'image/png':
            $extension = 'png';
            break;

        case 'image/webp':
            $extension = 'webp';
            break;

        default:
            $extension = 'jpg';
    }


    /*
    | Nama file unik
    */

    $fileName =
        uniqid('paket_', true)
        . '.'
        . $extension;


    $destination =
        $uploadDir . $fileName;


    /*
    | Pindahkan file
    */

    if (!move_uploaded_file(
        $file['tmp_name'],
        $destination
    )) {

        return [
            'success' => false,
            'message' => 'Gagal menyimpan gambar.'
        ];
    }


    return [
        'success' => true,
        'file' => $fileName
    ];
}


/*
|--------------------------------------------------------------------------
| GAMBAR UTAMA
|--------------------------------------------------------------------------
*/

$gambar_utama =
    $oldData['gambar_utama'];

$newMainImage = null;


/*
|--------------------------------------------------------------------------
| CEK APAKAH GAMBAR UTAMA DIGANTI
|--------------------------------------------------------------------------
*/

if (
    isset($_FILES['gambar_utama']) &&
    $_FILES['gambar_utama']['error'] !== UPLOAD_ERR_NO_FILE
) {

    $uploadUtama = uploadImage(
        $_FILES['gambar_utama'],
        $uploadDir,
        $allowedTypes,
        $maxFileSize
    );


    if (!$uploadUtama['success']) {

        redirectError(
            $uploadUtama['message']
        );
    }


    $gambar_utama =
        $uploadUtama['file'];

    $newMainImage =
        $uploadUtama['file'];
}


/*
|--------------------------------------------------------------------------
| GAMBAR LAIN LAMA
|--------------------------------------------------------------------------
*/

$gambarLain = [];

if (!empty($oldData['gambar_lain'])) {

    $gambarLain = array_filter(
        array_map(
            'trim',
            explode(
                ',',
                $oldData['gambar_lain']
            )
        )
    );
}


/*
|--------------------------------------------------------------------------
| TAMBAH GAMBAR LAIN
|--------------------------------------------------------------------------
*/

$newOtherImages = [];


if (
    isset($_FILES['gambar_lain']) &&
    isset($_FILES['gambar_lain']['name']) &&
    is_array($_FILES['gambar_lain']['name'])
) {

    $jumlahFile =
        count($_FILES['gambar_lain']['name']);


    for (
        $i = 0;
        $i < $jumlahFile;
        $i++
    ) {

        /*
        | Skip file kosong
        */

        if (
            $_FILES['gambar_lain']['error'][$i]
            === UPLOAD_ERR_NO_FILE
        ) {
            continue;
        }


        $file = [

            'name' =>
                $_FILES['gambar_lain']['name'][$i],

            'type' =>
                $_FILES['gambar_lain']['type'][$i],

            'tmp_name' =>
                $_FILES['gambar_lain']['tmp_name'][$i],

            'error' =>
                $_FILES['gambar_lain']['error'][$i],

            'size' =>
                $_FILES['gambar_lain']['size'][$i]

        ];


        $uploadLain =
            uploadImage(
                $file,
                $uploadDir,
                $allowedTypes,
                $maxFileSize
            );


        if (!$uploadLain['success']) {

            /*
            | Hapus gambar baru
            | jika proses upload gagal
            */

            if (
                $newMainImage &&
                file_exists(
                    $uploadDir . $newMainImage
                )
            ) {

                unlink(
                    $uploadDir . $newMainImage
                );
            }


            foreach (
                $newOtherImages as $newImage
            ) {

                if (
                    file_exists(
                        $uploadDir . $newImage
                    )
                ) {

                    unlink(
                        $uploadDir . $newImage
                    );
                }
            }


            redirectError(
                $uploadLain['message']
            );
        }


        $newOtherImages[] =
            $uploadLain['file'];
    }
}


/*
|--------------------------------------------------------------------------
| GABUNG GAMBAR LAMA + BARU
|--------------------------------------------------------------------------
*/

$allImages = array_merge(
    $gambarLain,
    $newOtherImages
);


/*
|--------------------------------------------------------------------------
| HILANGKAN DUPLIKAT
|--------------------------------------------------------------------------
*/

$allImages = array_unique(
    $allImages
);


/*
|--------------------------------------------------------------------------
| SIMPAN KE DATABASE
|--------------------------------------------------------------------------
*/

$gambar_lain =
    !empty($allImages)
    ? implode(',', $allImages)
    : null;


/*
|--------------------------------------------------------------------------
| UPDATE DATABASE
|--------------------------------------------------------------------------
*/

$sql = "
    UPDATE paket_wisata SET

        nama_paket = ?,
        slug = ?,
        destinasi = ?,
        durasi = ?,
        harga_normal = ?,
        harga_diskon = ?,
        diskon_persen = ?,
        deskripsi = ?,
        itinerary = ?,
        fasilitas = ?,
        termasuk = ?,
        tidak_termasuk = ?,
        gambar_utama = ?,
        gambar_lain = ?,
        kuota = ?,
        tersisa = ?,
        minimal_peserta = ?,
        status = ?,
        is_featured = ?,
        is_flash_sale = ?

    WHERE id = ?
";


$stmt = $conn->prepare($sql);


if (!$stmt) {

    /*
    | Hapus gambar baru jika query gagal
    */

    if (
        $newMainImage &&
        file_exists(
            $uploadDir . $newMainImage
        )
    ) {

        unlink(
            $uploadDir . $newMainImage
        );
    }


    foreach (
        $newOtherImages as $newImage
    ) {

        if (
            file_exists(
                $uploadDir . $newImage
            )
        ) {

            unlink(
                $uploadDir . $newImage
            );
        }
    }


    redirectError(
        'Gagal menyiapkan query update.'
    );
}


/*
|--------------------------------------------------------------------------
| BIND PARAMETER
|--------------------------------------------------------------------------
*/

$stmt->bind_param(
    'ssssddissssssiiiisiii',
    $nama_paket,
    $slug,
    $destinasi,
    $durasi,
    $harga_normal,
    $harga_diskon,
    $diskon_persen,
    $deskripsi,
    $itinerary,
    $fasilitas,
    $termasuk,
    $tidak_termasuk,
    $gambar_utama,
    $gambar_lain,
    $kuota,
    $tersisa,
    $minimal_peserta,
    $status,
    $is_featured,
    $is_flash_sale,
    $id
);


/*
|--------------------------------------------------------------------------
| EKSEKUSI UPDATE
|--------------------------------------------------------------------------
*/

if (!$stmt->execute()) {

    /*
    | Hapus gambar baru jika database gagal
    */

    if (
        $newMainImage &&
        file_exists(
            $uploadDir . $newMainImage
        )
    ) {

        unlink(
            $uploadDir . $newMainImage
        );
    }


    foreach (
        $newOtherImages as $newImage
    ) {

        if (
            file_exists(
                $uploadDir . $newImage
            )
        ) {

            unlink(
                $uploadDir . $newImage
            );
        }
    }


    $error =
        $stmt->error;

    $stmt->close();

    redirectError(
        'Gagal memperbarui paket: ' . $error
    );
}


$stmt->close();


/*
|--------------------------------------------------------------------------
| HAPUS GAMBAR UTAMA LAMA
|--------------------------------------------------------------------------
|
| Hanya dilakukan setelah database berhasil di-update.
|
*/

if (
    $newMainImage !== null &&
    !empty($oldData['gambar_utama']) &&
    $oldData['gambar_utama'] !== $newMainImage
) {

    $oldImage =
        $uploadDir . $oldData['gambar_utama'];


    if (file_exists($oldImage)) {
        unlink($oldImage);
    }
}


/*
|--------------------------------------------------------------------------
| REDIRECT BERHASIL
|--------------------------------------------------------------------------
*/

header(
    'Location: ../paket.php?status=updated'
);

exit;