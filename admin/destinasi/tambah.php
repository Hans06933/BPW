<?php
require_once "../../config/database.php";

$db = (new Database())->getConnection();

function getEnumValues(PDO $db, string $table, string $column): array {
    $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    $columnInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$columnInfo || !preg_match("/^enum\((.*)\)$/i", $columnInfo['Type'], $matches)) {
        return [];
    }

    return str_getcsv($matches[1], ',', "'");
}

$kategoriList = getEnumValues($db, 'destinasi', 'kategori');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Destinasi - Admin BPW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-100 p-4 md:p-6">

<div class="max-w-5xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="index.php" class="w-10 h-10 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-600 hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Destinasi</h1>
            <p class="text-sm text-slate-500">Tambahkan destinasi wisata baru.</p>
        </div>
    </div>

    <form action="simpan.php" method="POST" enctype="multipart/form-data" class="space-y-6">

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="font-bold text-slate-800">Informasi Destinasi</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Destinasi *</label>
                    <input type="text" name="nama_destinasi" required maxlength="200"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5"
                           placeholder="Contoh: Bali">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Slug *</label>
                    <input type="text" name="slug" required maxlength="200"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5"
                           placeholder="bali">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Wilayah *</label>
                    <input type="text" name="wilayah" required maxlength="100"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5"
                           placeholder="Bali, Indonesia">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                    <select name="kategori" class="w-full border border-slate-300 rounded-lg px-4 py-2.5">
                        <?php foreach ($kategoriList as $kategori): ?>
                            <option value="<?= htmlspecialchars($kategori) ?>"><?= htmlspecialchars(ucfirst($kategori)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Status</label>
                    <select name="status" class="w-full border border-slate-300 rounded-lg px-4 py-2.5">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="5"
                              class="w-full border border-slate-300 rounded-lg px-4 py-3"
                              placeholder="Deskripsi destinasi..."></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat</label>
                    <textarea name="alamat" rows="3"
                              class="w-full border border-slate-300 rounded-lg px-4 py-3"
                              placeholder="Alamat destinasi..."></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="font-bold text-slate-800">Informasi Tambahan</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Tiket Masuk</label>
                    <input type="number" name="harga_tiket_masuk" min="0"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5"
                           placeholder="0">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Jam Operasional</label>
                    <input type="text" name="jam_operasional" maxlength="100"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5"
                           placeholder="08:00 - 17:00">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Rating</label>
                    <input type="number" name="rating" min="0" max="5" step="0.1" value="0.0"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Total Review</label>
                    <input type="number" name="total_review" min="0" value="0"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Views</label>
                    <input type="number" name="views" min="0" value="0"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Gambar Utama *</label>
                    <input type="file" name="gambar_utama" required
                           accept="image/jpeg,image/png,image/webp"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 bg-white">
                    <p class="text-xs text-slate-400 mt-1">JPG, PNG, WEBP. Maks. 5 MB.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pb-8">
            <a href="index.php" class="px-5 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-700 font-semibold">
                Batal
            </a>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                <i class="fa-solid fa-save mr-1"></i> Simpan Destinasi
            </button>
        </div>
    </form>
</div>
</body>
</html>
