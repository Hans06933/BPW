<?php
/**
 * ADMIN - EDIT PROMO
 */

ob_start();
session_start();
require_once __DIR__ . '/../../config/database.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    $_SESSION['admin_msg'] = 'ID promo tidak valid.';
    $_SESSION['admin_msg_type'] = 'error';
    header("Location: ../promo.php");
    exit;
}

$promo = db_get("SELECT * FROM promo WHERE id = :id LIMIT 1", ['id' => $id]);
if (!$promo) {
    $_SESSION['admin_msg'] = 'Promo tidak ditemukan.';
    $_SESSION['admin_msg_type'] = 'error';
    header("Location: ../promo.php");
    exit;
}

$msg  = $_SESSION['admin_msg'] ?? null;
$type = $_SESSION['admin_msg_type'] ?? 'error';
$old  = $_SESSION['form_old'] ?? [];
unset($_SESSION['admin_msg'], $_SESSION['admin_msg_type'], $_SESSION['form_old']);

// Gabungkan: pakai old input kalau ada, kalau tidak pakai data DB
$data = array_merge($promo, $old);

$kategoriOptions = [
    'flash_sale' => 'Flash Sale',
    'early_bird' => 'Early Bird',
    'group'      => 'Group Discount',
    'member'     => 'Member Exclusive',
    'umum'       => 'Umum',
];

include __DIR__ . '/../../layout/admin_header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Promo - Admin BPW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>* { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-gray-800">

<div class="container mx-auto px-4 md:px-6 max-w-4xl mt-4">
    <nav class="flex items-center gap-2 text-xs text-gray-500 flex-wrap">
        <a href="../promo.php" class="hover:text-blue-600 transition">
            <i class="fa-solid fa-tags"></i> Promo
        </a>
        <i class="fa-solid fa-chevron-right text-[8px]"></i>
        <span class="text-gray-800 font-semibold">Edit Promo</span>
    </nav>
</div>

<?php if ($msg): ?>
    <div class="container mx-auto px-4 md:px-6 max-w-4xl mt-4">
        <div class="<?= $type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700' ?> border-l-4 p-3 rounded-md text-sm">
            <i class="fa-solid <?= $type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
            <?= htmlspecialchars($msg) ?>
        </div>
    </div>
<?php endif; ?>

<section class="container mx-auto px-4 md:px-6 max-w-4xl my-6 pb-16">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-blue-600"></i> Edit Promo
            </h1>
            <p class="text-xs text-gray-400 mt-1">Perbarui informasi promo.</p>
        </div>
        <a href="../promo.php" class="text-xs text-gray-600 bg-white border border-gray-200 px-4 py-2 rounded-lg font-semibold hover:bg-gray-50 transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="simpan.php" method="POST" enctype="multipart/form-data" class="space-y-6">

        <input type="hidden" name="aksi" value="update">
        <input type="hidden" name="id" value="<?= (int) $data['id'] ?>">

        <!-- INFO UTAMA -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b">
                <h2 class="font-bold text-gray-800 text-sm">
                    <i class="fa-solid fa-circle-info text-blue-600 mr-1"></i> Informasi Promo
                </h2>
            </div>
            <div class="p-5 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Judul Promo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="judul" required maxlength="200"
                           value="<?= htmlspecialchars($data['judul'] ?? '') ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                              class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600"><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                        <select name="kategori"
                                class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                            <?php foreach ($kategoriOptions as $key => $label): ?>
                                <option value="<?= $key ?>" <?= ($data['kategori'] ?? '') === $key ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Promo</label>
                        <input type="text" name="kode_promo" maxlength="50"
                               value="<?= htmlspecialchars($data['kode_promo'] ?? '') ?>"
                               class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm font-mono uppercase focus:outline-none focus:border-blue-600">
                    </div>
                </div>

            </div>
        </div>

        <!-- DISKON -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b">
                <h2 class="font-bold text-gray-800 text-sm">
                    <i class="fa-solid fa-percent text-blue-600 mr-1"></i> Diskon
                </h2>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Diskon (%) *</label>
                    <input type="number" name="diskon_persen" required min="1" max="100"
                           value="<?= htmlspecialchars($data['diskon_persen'] ?? '') ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Minimal Pembelian</label>
                    <input type="number" name="minimal_pembelian" min="0"
                           value="<?= htmlspecialchars($data['minimal_pembelian'] ?? '0') ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Maksimal Diskon</label>
                    <input type="number" name="maksimal_diskon" min="0"
                           value="<?= htmlspecialchars($data['maksimal_diskon'] ?? '') ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>
            </div>
        </div>

        <!-- PERIODE -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b">
                <h2 class="font-bold text-gray-800 text-sm">
                    <i class="fa-regular fa-calendar text-blue-600 mr-1"></i> Periode & Kuota
                </h2>
            </div>
            <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Berlaku Mulai *</label>
                    <input type="date" name="berlaku_mulai" required
                           value="<?= htmlspecialchars($data['berlaku_mulai'] ?? '') ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Berlaku Sampai *</label>
                    <input type="date" name="berlaku_sampai" required
                           value="<?= htmlspecialchars($data['berlaku_sampai'] ?? '') ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kuota</label>
                    <input type="number" name="kuota" min="0"
                           value="<?= htmlspecialchars($data['kuota'] ?? '0') ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Sisa Kuota</label>
                    <input type="number" name="sisa_kuota" min="0"
                           value="<?= htmlspecialchars($data['sisa_kuota'] ?? '0') ?>"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                    <select name="status"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                        <option value="aktif"    <?= ($data['status'] ?? 'aktif') === 'aktif'    ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= ($data['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        <option value="habis"    <?= ($data['status'] ?? '') === 'habis'    ? 'selected' : '' ?>>Habis</option>
                    </select>
                </div>

            </div>
        </div>

        <!-- GAMBAR -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b">
                <h2 class="font-bold text-gray-800 text-sm">
                    <i class="fa-solid fa-image text-blue-600 mr-1"></i> Gambar Promo
                </h2>
            </div>
            <div class="p-5">
                <?php if (!empty($data['gambar']) && file_exists(__DIR__ . '/../../' . $data['gambar'])): ?>
                    <div class="mb-3">
                        <img src="../../<?= htmlspecialchars($data['gambar']) ?>"
                             class="w-full max-w-xs h-32 object-cover rounded-lg border border-gray-200">
                        <p class="text-[10px] text-gray-400 mt-1">Gambar saat ini</p>
                    </div>
                <?php endif; ?>

                <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp"
                       class="w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2">
                <p class="text-[10px] text-gray-400 mt-2">Kosongkan jika tidak ingin mengganti. Maks 5 MB.</p>
            </div>
        </div>

        <!-- TOMBOL -->
        <div class="sticky bottom-4 bg-white rounded-xl shadow-lg border border-gray-100 p-4 flex justify-end gap-3">
            <a href="../promo.php"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold text-sm transition">
                <i class="fa-solid fa-xmark"></i> Batal
            </a>
            <button type="submit"
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition inline-flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>

    </form>
</section>

</body>
</html>