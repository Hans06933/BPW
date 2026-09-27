<?php
/**
 * ============================================================
 * ADMIN - EDIT PAKET WISATA
 * Bayu Prima Wisata
 * ============================================================
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../../config/database.php');

// ============================================================
// AMBIL ID
// ============================================================
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ../paket.php");
    exit;
}

// ============================================================
// AMBIL DATA PAKET
// ============================================================
$paket = db_get(
    "SELECT * FROM paket_wisata WHERE id = :id",
    ['id' => $id]
);

if (!$paket) {
    $_SESSION['admin_msg'] = 'Data paket dengan ID ' . htmlspecialchars($id) . ' tidak ditemukan.';
    $_SESSION['admin_msg_type'] = 'error';
    header("Location: ../paket.php");
    exit;
}

// ============================================================
// FOLDER GAMBAR
// ============================================================
$uploadDir = __DIR__ . '/../../images/paket/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// ============================================================
// DATA GAMBAR LAIN
// ============================================================
$gambarLain = [];

if (!empty($paket['gambar_lain'])) {
    $gambarLain = array_filter(array_map('trim', explode(',', $paket['gambar_lain'])));
}

// ============================================================
// PROSES UPDATE (SEMUA LOGIKA DI ATAS — SEBELUM INCLUDE HEADER)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --------------------------------------------------------
    // DATA FORM
    // --------------------------------------------------------
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

    // --------------------------------------------------------
    // VALIDASI
    // --------------------------------------------------------
    $errors = [];

    if ($nama_paket === '') $errors[] = 'Nama paket wajib diisi.';
    if ($slug === '')       $errors[] = 'Slug wajib diisi.';

    // Cek slug duplikat
    if (empty($errors)) {
        $cekSlug = db_get(
            "SELECT id FROM paket_wisata WHERE slug = :slug AND id != :id LIMIT 1",
            ['slug' => $slug, 'id' => $id]
        );

        if ($cekSlug) {
            $errors[] = 'Slug "' . $slug . '" sudah digunakan paket lain.';
        }
    }

    // Kalau ada error → redirect balik ke form dengan notifikasi
    if (!empty($errors)) {
        $_SESSION['admin_msg'] = implode(' ', $errors);
        $_SESSION['admin_msg_type'] = 'error';
        header("Location: edit.php?id=" . $id);
        exit;
    }

    // --------------------------------------------------------
    // GAMBAR UTAMA
    // --------------------------------------------------------
    $gambar_utama = $paket['gambar_utama'] ?? '';

    if (isset($_FILES['gambar_utama']) && $_FILES['gambar_utama']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['gambar_utama'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowedExtensions, true)) {
            $_SESSION['admin_msg'] = 'Format gambar utama tidak diperbolehkan.';
            $_SESSION['admin_msg_type'] = 'error';
            header("Location: edit.php?id=" . $id);
            exit;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['admin_msg'] = 'Ukuran gambar utama maksimal 5 MB.';
            $_SESSION['admin_msg_type'] = 'error';
            header("Location: edit.php?id=" . $id);
            exit;
        }

        $newFilename = time() . '_' . uniqid() . '.' . $extension;
        $targetPath  = $uploadDir . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Hapus gambar utama lama
            if (!empty($gambar_utama) && file_exists($uploadDir . $gambar_utama)) {
                unlink($uploadDir . $gambar_utama);
            }
            $gambar_utama = $newFilename;
        }
    }

    // --------------------------------------------------------
    // HAPUS GAMBAR LAIN
    // --------------------------------------------------------
    $gambarLainSekarang = $gambarLain;

    if (isset($_POST['hapus_gambar_lain']) && is_array($_POST['hapus_gambar_lain'])) {
        foreach ($_POST['hapus_gambar_lain'] as $gambarHapus) {
            $gambarHapus = basename($gambarHapus);

            $key = array_search($gambarHapus, $gambarLainSekarang, true);
            if ($key !== false) unset($gambarLainSekarang[$key]);

            $fileHapus = $uploadDir . $gambarHapus;
            if (file_exists($fileHapus) && is_file($fileHapus)) unlink($fileHapus);
        }
        $gambarLainSekarang = array_values($gambarLainSekarang);
    }

    // --------------------------------------------------------
    // UPLOAD GAMBAR LAIN BARU
    // --------------------------------------------------------
    if (isset($_FILES['gambar_lain']['name']) && is_array($_FILES['gambar_lain']['name'])) {
        $jumlahFile = count($_FILES['gambar_lain']['name']);

        for ($i = 0; $i < $jumlahFile; $i++) {
            if ($_FILES['gambar_lain']['error'][$i] !== UPLOAD_ERR_OK) continue;

            $tmpName   = $_FILES['gambar_lain']['tmp_name'][$i];
            $fileName  = $_FILES['gambar_lain']['name'][$i];
            $fileSize  = $_FILES['gambar_lain']['size'][$i];
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($extension, $allowed, true)) continue;
            if ($fileSize > 5 * 1024 * 1024) continue;

            $newFilename = time() . '_' . uniqid() . '_' . $i . '.' . $extension;
            if (move_uploaded_file($tmpName, $uploadDir . $newFilename)) {
                $gambarLainSekarang[] = $newFilename;
            }
        }
    }

    $gambar_lain = !empty($gambarLainSekarang) ? implode(',', $gambarLainSekarang) : null;

    // --------------------------------------------------------
    // UPDATE DATABASE
    // --------------------------------------------------------
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
        'is_flash_sale'   => $is_flash_sale,
    ];

    db_update('paket_wisata', $data_update, 'id', $id);

    // --------------------------------------------------------
    // REDIRECT SUKSES
    // --------------------------------------------------------
    $_SESSION['admin_msg'] = 'Paket "' . $nama_paket . '" berhasil diperbarui.';
    $_SESSION['admin_msg_type'] = 'success';
    header("Location: ../paket.php");
    exit;
}

// ============================================================
// INCLUDE HEADER ADMIN (SETELAH SEMUA LOGIKA POST)
// ============================================================
include __DIR__ . '/../../layout/admin_header.php';

// Ambil notifikasi
$msg     = $_SESSION['admin_msg'] ?? null;
$msgType = $_SESSION['admin_msg_type'] ?? 'success';
unset($_SESSION['admin_msg'], $_SESSION['admin_msg_type']);
?>

<!-- ============================================================
     BREADCRUMB
============================================================ -->
<div class="container mx-auto px-4 md:px-6 max-w-5xl mt-4">
    <nav class="flex items-center gap-2 text-xs text-gray-500 flex-wrap">
        <a href="../paket.php" class="hover:text-blue-600 transition">
            <i class="fa-solid fa-suitcase"></i> Paket Wisata
        </a>
        <i class="fa-solid fa-chevron-right text-[8px]"></i>
        <span class="text-gray-800 font-semibold">Edit Paket</span>
    </nav>
</div>

<!-- NOTIFIKASI -->
<?php if ($msg): ?>
    <div id="adminNotif" class="container mx-auto px-4 md:px-6 max-w-5xl mt-4">
        <div class="<?= $msgType === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700' ?> border-l-4 p-3 rounded-md text-sm flex items-center gap-2">
            <i class="fa-solid <?= $msgType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
            <?= htmlspecialchars($msg) ?>
        </div>
    </div>
    <script>setTimeout(() => document.getElementById('adminNotif')?.remove(), 4000);</script>
<?php endif; ?>

<!-- ============================================================
     FORM EDIT
============================================================ -->
<section class="container mx-auto px-4 md:px-6 max-w-5xl my-6 pb-16">

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                Edit Paket Wisata
            </h1>
            <p class="text-xs text-gray-400 mt-1">Perbarui informasi paket wisata.</p>
        </div>
        <a href="../paket.php"
           class="text-xs text-gray-600 hover:text-gray-800 bg-white border border-gray-200 px-4 py-2 rounded-lg font-semibold transition inline-flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">

        <!-- INFORMASI PAKET -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b">
                <h2 class="font-bold text-gray-800 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-suitcase text-blue-600"></i>
                    Informasi Paket
                </h2>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Nama Paket <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_paket"
                           value="<?= htmlspecialchars($paket['nama_paket'] ?? '') ?>" required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Slug <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="slug"
                           value="<?= htmlspecialchars($paket['slug'] ?? '') ?>" required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Destinasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="destinasi"
                           value="<?= htmlspecialchars($paket['destinasi'] ?? '') ?>" required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Durasi</label>
                    <input type="text" name="durasi"
                           value="<?= htmlspecialchars($paket['durasi'] ?? '') ?>"
                           placeholder="Contoh: 3 Hari 2 Malam"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Minimal Peserta</label>
                    <input type="number" name="minimal_peserta" min="1"
                           value="<?= htmlspecialchars($paket['minimal_peserta'] ?? 1) ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
            </div>
        </div>

        <!-- HARGA & DISKON -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b">
                <h2 class="font-bold text-gray-800 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-tags text-blue-600"></i> Harga & Diskon
                </h2>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Harga Normal <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="harga_normal" min="0"
                           value="<?= htmlspecialchars($paket['harga_normal'] ?? 0) ?>" required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Diskon</label>
                    <input type="number" name="harga_diskon" min="0"
                           value="<?= htmlspecialchars($paket['harga_diskon'] ?? '') ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Diskon Persen (%)</label>
                    <input type="number" name="diskon_persen" min="0" max="100"
                           value="<?= htmlspecialchars($paket['diskon_persen'] ?? 0) ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
            </div>
        </div>

        <!-- DESKRIPSI PAKET -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b">
                <h2 class="font-bold text-gray-800 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-file-lines text-blue-600"></i> Deskripsi Paket
                </h2>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="5"
                              class="w-full border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:border-blue-600"><?= htmlspecialchars($paket['deskripsi'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Itinerary</label>
                    <textarea name="itinerary" rows="7"
                              class="w-full border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:border-blue-600"><?= htmlspecialchars($paket['itinerary'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fasilitas</label>
                    <textarea name="fasilitas" rows="5"
                              class="w-full border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:border-blue-600"><?= htmlspecialchars($paket['fasilitas'] ?? '') ?></textarea>
                    <p class="text-[10px] text-gray-400 mt-1">Satu item per baris.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Termasuk</label>
                        <textarea name="termasuk" rows="5"
                                  class="w-full border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:border-blue-600"><?= htmlspecialchars($paket['termasuk'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tidak Termasuk</label>
                        <textarea name="tidak_termasuk" rows="5"
                                  class="w-full border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:border-blue-600"><?= htmlspecialchars($paket['tidak_termasuk'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- GAMBAR PAKET -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b">
                <h2 class="font-bold text-gray-800 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-images text-blue-600"></i> Gambar Paket
                </h2>
            </div>
            <div class="p-5">
                <!-- GAMBAR UTAMA -->
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Gambar Utama</label>
                    <?php if (!empty($paket['gambar_utama'])): ?>
                        <div class="mb-3">
                            <img src="../../images/paket/<?= htmlspecialchars($paket['gambar_utama']) ?>"
                                 class="w-full max-w-xs h-40 object-cover rounded-lg border border-gray-200"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=No+Image';">
                            <p class="text-[10px] text-gray-400 mt-2"><?= htmlspecialchars($paket['gambar_utama']) ?></p>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="gambar_utama" accept="image/jpeg,image/png,image/webp"
                           class="w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2">
                    <p class="text-[10px] text-gray-400 mt-2">Kosongkan jika tidak ingin mengganti. Maks 5 MB.</p>
                </div>

                <!-- GAMBAR GALERI -->
                <div class="border-t border-gray-100 pt-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm">Gambar Galeri</h3>
                            <p class="text-[10px] text-gray-400 mt-0.5">Gambar yang sudah tersimpan.</p>
                        </div>
                        <span class="bg-blue-50 text-blue-600 text-[10px] font-semibold px-2.5 py-1 rounded-full">
                            <?= count($gambarLain) ?> gambar
                        </span>
                    </div>

                    <?php if (!empty($gambarLain)): ?>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-5">
                            <?php foreach ($gambarLain as $index => $gambar): ?>
                                <div class="border border-gray-200 rounded-lg overflow-hidden bg-slate-50">
                                    <div class="relative h-28">
                                        <img src="../../images/paket/<?= htmlspecialchars($gambar) ?>"
                                             alt="Gambar <?= $index + 1 ?>"
                                             class="w-full h-full object-cover"
                                             onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=No+Image';">
                                    </div>
                                    <div class="p-2.5">
                                        <p class="text-[9px] text-slate-400 truncate mb-1.5"><?= htmlspecialchars($gambar) ?></p>
                                        <label class="flex items-center gap-1.5 text-[10px] text-red-600 cursor-pointer">
                                            <input type="checkbox" name="hapus_gambar_lain[]"
                                                   value="<?= htmlspecialchars($gambar) ?>"
                                                   class="rounded border-slate-300 text-red-600">
                                            <span>Hapus</span>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-slate-50 border border-dashed border-slate-300 rounded-lg p-6 text-center mb-5">
                            <i class="fa-solid fa-image text-2xl text-slate-300 mb-2"></i>
                            <p class="text-xs text-slate-500">Belum ada gambar galeri.</p>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Tambah Gambar Galeri</label>
                        <input type="file" name="gambar_lain[]" multiple accept="image/jpeg,image/png,image/webp"
                               class="w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2">
                        <p class="text-[10px] text-gray-400 mt-2">Bisa pilih beberapa gambar sekaligus. Maks 5 MB per gambar.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- KUOTA PESERTA -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b">
                <h2 class="font-bold text-gray-800 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-users text-blue-600"></i> Kuota Peserta
                </h2>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kuota</label>
                    <input type="number" name="kuota" min="0"
                           value="<?= htmlspecialchars($paket['kuota'] ?? 0) ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Peserta Tersisa</label>
                    <input type="number" name="tersisa" min="0"
                           value="<?= htmlspecialchars($paket['tersisa'] ?? 0) ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
            </div>
        </div>

        <!-- PENGATURAN PAKET -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b">
                <h2 class="font-bold text-gray-800 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-gear text-blue-600"></i> Pengaturan Paket
                </h2>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                        <option value="aktif"    <?= ($paket['status'] ?? '') === 'aktif'    ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= ($paket['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        <option value="habis"    <?= ($paket['status'] ?? '') === 'habis'    ? 'selected' : '' ?>>Habis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Paket Terpopuler</label>
                    <select name="is_featured" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                        <option value="0" <?= (int)($paket['is_featured'] ?? 0) === 0 ? 'selected' : '' ?>>Tidak</option>
                        <option value="1" <?= (int)($paket['is_featured'] ?? 0) === 1 ? 'selected' : '' ?>>Ya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Flash Sale</label>
                    <select name="is_flash_sale" class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                        <option value="0" <?= (int)($paket['is_flash_sale'] ?? 0) === 0 ? 'selected' : '' ?>>Tidak</option>
                        <option value="1" <?= (int)($paket['is_flash_sale'] ?? 0) === 1 ? 'selected' : '' ?>>Ya</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- TOMBOL -->
        <div class="sticky bottom-4 bg-white rounded-xl shadow-lg border border-gray-100 p-4 flex justify-end gap-3">
            <a href="../paket.php"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold text-sm transition inline-flex items-center gap-2">
                <i class="fa-solid fa-xmark"></i> Batal
            </a>
            <button type="submit"
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition inline-flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>

    </form>
</section>
