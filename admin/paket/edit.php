<?php
// =========================================================
// EDIT PAKET WISATA
// =========================================================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../../config/database.php');

// =========================================================
// AMBIL ID
// =========================================================
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ../paket.php");
    exit;
}

// =========================================================
// AMBIL DATA PAKET
// =========================================================
$paket = db_get(
    "SELECT * FROM paket_wisata WHERE id = :id",
    ['id' => $id]
);

if (!$paket) {
    echo "Data paket dengan ID " . htmlspecialchars($id) . " tidak ditemukan.";
    exit;
}

// =========================================================
// FOLDER GAMBAR
// =========================================================
$uploadDir = __DIR__ . '/../../images/paket/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// =========================================================
// DATA GAMBAR LAIN
// =========================================================
$gambarLain = [];

if (!empty($paket['gambar_lain'])) {
    $gambarLain = array_filter(array_map('trim', explode(',', $paket['gambar_lain'])));
}

// =========================================================
// PROSES UPDATE
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // DATA FORM
    $nama_paket      = trim($_POST['nama_paket'] ?? '');
    $slug            = trim($_POST['slug'] ?? '');
    $destinasi       = trim($_POST['destinasi'] ?? '');
    $durasi          = trim($_POST['durasi'] ?? '');
    $harga_normal    = (float)($_POST['harga_normal'] ?? 0);
    $harga_diskon    = (isset($_POST['harga_diskon']) && $_POST['harga_diskon'] !== '') ? (float)$_POST['harga_diskon'] : null;
    $diskon_persen   = (int)($_POST['diskon_persen'] ?? 0);
    $minimal_peserta = (int)($_POST['minimal_peserta'] ?? 1);
    $deskripsi       = trim($_POST['deskripsi'] ?? '');
    $itinerary       = trim($_POST['itinerary'] ?? '');
    $fasilitas       = trim($_POST['fasilitas'] ?? '');
    $termasuk        = trim($_POST['termasuk'] ?? '');
    $tidak_termasuk  = trim($_POST['tidak_termasuk'] ?? '');
    $kuota           = (int)($_POST['kuota'] ?? 0);
    $tersisa         = (int)($_POST['tersisa'] ?? 0);
    $status          = $_POST['status'] ?? 'aktif';
    $is_featured     = (int)($_POST['is_featured'] ?? 0);
    $is_flash_sale   = (int)($_POST['is_flash_sale'] ?? 0);

    // CEK SLUG
    $cekSlug = db_get(
        "SELECT id FROM paket_wisata WHERE slug = :slug AND id != :id LIMIT 1",
        ['slug' => $slug, 'id' => $id]
    );

    if ($cekSlug) {
        die('<div style="font-family: Arial; padding: 30px; text-align: center;">
            <h2 style="color:red;">Slug sudah digunakan!</h2>
            <p>Silakan gunakan slug yang berbeda.</p>
            <a href="javascript:history.back()">Kembali</a>
        </div>');
    }

    // GAMBAR UTAMA LAMA
    $gambar_utama = $paket['gambar_utama'] ?? '';

    // UPLOAD GAMBAR UTAMA BARU
    if (isset($_FILES['gambar_utama']) && $_FILES['gambar_utama']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['gambar_utama'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowedExtensions, true)) {
            die('Format gambar utama tidak diperbolehkan.');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            die('Ukuran gambar utama maksimal 5 MB.');
        }

        $newFilename = time() . '_' . uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $newFilename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            die('Gagal mengupload gambar utama.');
        }

        // Hapus gambar utama lama
        if (!empty($gambar_utama)) {
            $oldImagePath = $uploadDir . $gambar_utama;
            if (file_exists($oldImagePath) && is_file($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $gambar_utama = $newFilename;
    }

    // HAPUS GAMBAR LAIN
    $gambarLainSekarang = $gambarLain;

    if (isset($_POST['hapus_gambar_lain']) && is_array($_POST['hapus_gambar_lain'])) {
        foreach ($_POST['hapus_gambar_lain'] as $gambarHapus) {
            $gambarHapus = basename($gambarHapus);

            $key = array_search($gambarHapus, $gambarLainSekarang, true);
            if ($key !== false) {
                unset($gambarLainSekarang[$key]);
            }

            $fileHapus = $uploadDir . $gambarHapus;
            if (file_exists($fileHapus) && is_file($fileHapus)) {
                unlink($fileHapus);
            }
        }

        $gambarLainSekarang = array_values($gambarLainSekarang);
    }

    // UPLOAD GAMBAR LAIN BARU
    if (isset($_FILES['gambar_lain']) && isset($_FILES['gambar_lain']['name']) && is_array($_FILES['gambar_lain']['name'])) {
        $jumlahFile = count($_FILES['gambar_lain']['name']);

        for ($i = 0; $i < $jumlahFile; $i++) {
            if ($_FILES['gambar_lain']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmpName = $_FILES['gambar_lain']['tmp_name'][$i];
            $fileName = $_FILES['gambar_lain']['name'][$i];
            $fileSize = $_FILES['gambar_lain']['size'][$i];
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($extension, $allowedExtensions, true)) {
                continue;
            }

            if ($fileSize > 5 * 1024 * 1024) {
                continue;
            }

            $newFilename = time() . '_' . uniqid() . '_' . $i . '.' . $extension;
            $targetPath = $uploadDir . $newFilename;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $gambarLainSekarang[] = $newFilename;
            }
        }
    }

    // BUAT STRING GAMBAR LAIN
    $gambar_lain = !empty($gambarLainSekarang) ? implode(',', $gambarLainSekarang) : null;

    // DATA UPDATE
    $data_update = [
        'nama_paket'      => $nama_paket,
        'slug'            => $slug,
        'destinasi'       => $destinasi,
        'durasi'          => $durasi,
        'harga_normal'    => $harga_normal,
        'harga_diskon'    => $harga_diskon,
        'diskon_persen'   => $diskon_persen,
        'minimal_peserta' => $minimal_peserta,
        'deskripsi'       => $deskripsi,
        'itinerary'       => $itinerary,
        'fasilitas'       => $fasilitas,
        'termasuk'        => $termasuk,
        'tidak_termasuk'  => $tidak_termasuk,
        'gambar_utama'    => $gambar_utama,
        'gambar_lain'     => $gambar_lain,
        'kuota'           => $kuota,
        'tersisa'         => $tersisa,
        'status'          => $status,
        'is_featured'     => $is_featured,
        'is_flash_sale'   => $is_flash_sale
    ];

    // UPDATE DATABASE
    db_update('paket_wisata', $data_update, 'id', $id);

    // REDIRECT
    header("Location: ../paket.php?status=updated");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Paket Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-100 p-6">
    <div class="max-w-5xl mx-auto">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">

            <!-- HEADER -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Edit Paket Wisata</h1>
                    <p class="text-sm text-slate-500 mt-1">Perbarui informasi paket wisata.</p>
                </div>
                <a href="../paket.php" class="text-slate-500 hover:text-slate-700 font-medium">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">

                <!-- INFORMASI UTAMA -->
                <div class="border border-slate-200 rounded-xl p-5">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">
                        <i class="fa-solid fa-suitcase text-blue-600 mr-2"></i>
                        Informasi Paket
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Paket</label>
                            <input type="text" name="nama_paket" value="<?= htmlspecialchars($paket['nama_paket'] ?? '') ?>" required class="w-full border border-slate-300 rounded-lg p-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="<?= htmlspecialchars($paket['slug'] ?? '') ?>" required class="w-full border border-slate-300 rounded-lg p-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Destinasi</label>
                            <input type="text" name="destinasi" value="<?= htmlspecialchars($paket['destinasi'] ?? '') ?>" required class="w-full border border-slate-300 rounded-lg p-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Durasi</label>
                            <input type="text" name="durasi" value="<?= htmlspecialchars($paket['durasi'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Minimal Peserta</label>
                            <input type="number" name="minimal_peserta" min="1" value="<?= htmlspecialchars($paket['minimal_peserta'] ?? 1) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                        </div>
                    </div>
                </div>

                <!-- HARGA -->
                <div class="border border-slate-200 rounded-xl p-5">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">
                        <i class="fa-solid fa-tags text-blue-600 mr-2"></i>
                        Harga & Diskon
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Harga Normal</label>
                            <input type="number" name="harga_normal" value="<?= htmlspecialchars($paket['harga_normal'] ?? 0) ?>" required min="0" class="w-full border border-slate-300 rounded-lg p-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Harga Diskon</label>
                            <input type="number" name="harga_diskon" value="<?= htmlspecialchars($paket['harga_diskon'] ?? '') ?>" min="0" class="w-full border border-slate-300 rounded-lg p-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Diskon Persen</label>
                            <input type="number" name="diskon_persen" min="0" max="100" value="<?= htmlspecialchars($paket['diskon_persen'] ?? 0) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                        </div>
                    </div>
                </div>

                <!-- DESKRIPSI -->
                <div class="border border-slate-200 rounded-xl p-5">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">
                        <i class="fa-solid fa-file-lines text-blue-600 mr-2"></i>
                        Deskripsi Paket
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" rows="5" class="w-full border border-slate-300 rounded-lg p-3"><?= htmlspecialchars($paket['deskripsi'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Itinerary</label>
                            <textarea name="itinerary" rows="7" class="w-full border border-slate-300 rounded-lg p-3"><?= htmlspecialchars($paket['itinerary'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Fasilitas</label>
                            <textarea name="fasilitas" rows="5" class="w-full border border-slate-300 rounded-lg p-3"><?= htmlspecialchars($paket['fasilitas'] ?? '') ?></textarea>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Termasuk</label>
                                <textarea name="termasuk" rows="5" class="w-full border border-slate-300 rounded-lg p-3"><?= htmlspecialchars($paket['termasuk'] ?? '') ?></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tidak Termasuk</label>
                                <textarea name="tidak_termasuk" rows="5" class="w-full border border-slate-300 rounded-lg p-3"><?= htmlspecialchars($paket['tidak_termasuk'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GAMBAR -->
                <div class="border border-slate-200 rounded-xl p-5">
                    <h2 class="text-lg font-bold text-slate-800 mb-1">
                        <i class="fa-solid fa-images text-blue-600 mr-2"></i>
                        Gambar Paket
                    </h2>
                    <p class="text-xs text-slate-500 mb-5">Kelola gambar utama dan gambar galeri paket wisata.</p>

                    <!-- GAMBAR UTAMA -->
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Gambar Utama</label>
                        <?php if (!empty($paket['gambar_utama'])): ?>
                            <div class="mb-3">
                                <img src="../../images/paket/<?= htmlspecialchars($paket['gambar_utama']) ?>" class="w-full max-w-md h-52 object-cover rounded-xl border border-slate-200" onerror="this.onerror=null; this.src='https://via.placeholder.com/600x400?text=Gambar+Tidak+Ada';">
                                <p class="text-xs text-slate-400 mt-2"><?= htmlspecialchars($paket['gambar_utama']) ?></p>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="gambar_utama" accept="image/jpeg,image/png,image/webp" class="w-full text-sm text-slate-500 border border-slate-300 rounded-lg p-2">
                        <p class="text-xs text-slate-400 mt-2">Pilih gambar baru jika ingin mengganti gambar utama. Maksimal 5 MB.</p>
                    </div>

                    <!-- GAMBAR LAIN -->
                    <div class="border-t border-slate-200 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-semibold text-slate-800">Gambar Lain</h3>
                                <p class="text-xs text-slate-400 mt-1">Gambar yang sudah tersimpan di galeri.</p>
                            </div>
                            <span class="bg-blue-50 text-blue-600 text-xs font-semibold px-3 py-1 rounded-full"><?= count($gambarLain) ?> gambar</span>
                        </div>

                        <?php if (!empty($gambarLain)): ?>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
                                <?php foreach ($gambarLain as $index => $gambar): ?>
                                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                                        <div class="relative h-36">
                                            <img src="../../images/paket/<?= htmlspecialchars($gambar) ?>" alt="Gambar <?= $index + 1 ?>" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=Gambar+Tidak+Ada';">
                                        </div>
                                        <div class="p-3">
                                            <p class="text-[10px] text-slate-400 truncate mb-2" title="<?= htmlspecialchars($gambar) ?>"><?= htmlspecialchars($gambar) ?></p>
                                            <label class="flex items-center gap-2 text-xs text-red-600 cursor-pointer">
                                                <input type="checkbox" name="hapus_gambar_lain[]" value="<?= htmlspecialchars($gambar) ?>" class="rounded border-slate-300 text-red-600">
                                                <span>Hapus gambar</span>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="bg-slate-50 border border-dashed border-slate-300 rounded-xl p-8 text-center mb-6">
                                <i class="fa-solid fa-image text-3xl text-slate-300 mb-2"></i>
                                <p class="text-sm text-slate-500">Belum ada gambar galeri.</p>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Tambah Gambar Lain</label>
                            <input type="file" name="gambar_lain[]" multiple accept="image/jpeg,image/png,image/webp" class="w-full text-sm text-slate-500 border border-slate-300 rounded-lg p-2">
                            <p class="text-xs text-slate-400 mt-2">Bisa memilih beberapa gambar sekaligus. JPG, PNG atau WEBP. Maksimal 5 MB per gambar.</p>
                        </div>
                    </div>
                </div>

                <!-- KUOTA -->
                <div class="border border-slate-200 rounded-xl p-5">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">
                        <i class="fa-solid fa-users text-blue-600 mr-2"></i>
                        Kuota Peserta
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Kuota</label>
                            <input type="number" name="kuota" min="0" value="<?= htmlspecialchars($paket['kuota'] ?? 0) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Peserta Tersisa</label>
                            <input type="number" name="tersisa" min="0" value="<?= htmlspecialchars($paket['tersisa'] ?? 0) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Minimal Peserta</label>
                            <input type="number" name="minimal_peserta" min="1" value="<?= htmlspecialchars($paket['minimal_peserta'] ?? 1) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                        </div>
                    </div>
                </div>

                <!-- PENGATURAN -->
                <div class="border border-slate-200 rounded-xl p-5">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">
                        <i class="fa-solid fa-gear text-blue-600 mr-2"></i>
                        Pengaturan Paket
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                            <select name="status" class="w-full border border-slate-300 rounded-lg p-2.5">
                                <option value="aktif" <?= ($paket['status'] ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= ($paket['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                <option value="habis" <?= ($paket['status'] ?? '') === 'habis' ? 'selected' : '' ?>>Habis</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Paket Terpopuler</label>
                            <select name="is_featured" class="w-full border border-slate-300 rounded-lg p-2.5">
                                <option value="0" <?= (int)($paket['is_featured'] ?? 0) === 0 ? 'selected' : '' ?>>Tidak</option>
                                <option value="1" <?= (int)($paket['is_featured'] ?? 0) === 1 ? 'selected' : '' ?>>Ya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Flash Sale</label>
                            <select name="is_flash_sale" class="w-full border border-slate-300 rounded-lg p-2.5">
                                <option value="0" <?= (int)($paket['is_flash_sale'] ?? 0) === 0 ? 'selected' : '' ?>>Tidak</option>
                                <option value="1" <?= (int)($paket['is_flash_sale'] ?? 0) === 1 ? 'selected' : '' ?>>Ya</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- BUTTON -->
                <div class="flex justify-end gap-3 pb-6">
                    <a href="../paket.php" class="px-5 py-2.5 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 font-semibold">Batal</a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</body>
</html>