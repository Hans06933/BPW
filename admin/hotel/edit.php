<?php
require_once __DIR__ . '/../../config/database.php';
include __DIR__ . '/../../layout/admin_header.php';

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: ../hotel.php?error=ID hotel tidak valid');
    exit;
}

try {
    // AMBIL HOTEL
    $hotel = db_get(
        "SELECT * FROM hotel WHERE id = ? LIMIT 1",
        [$id]
    );

    if (!$hotel) {
        header('Location: ../hotel.php?error=Data hotel tidak ditemukan');
        exit;
    }

    // AMBIL GALERI
    $galeri = db_get_all(
        "SELECT * FROM hotel_galeri WHERE hotel_id = ? ORDER BY id ASC",
        [$id]
    );
} catch (Exception $e) {
    header('Location: ../hotel.php?error=' . urlencode($e->getMessage()));
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hotel - Admin BPW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .preview-image {
            width: 150px;
            height: 110px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-slate-100">
    <div class="min-h-screen">

        <!-- HEADER -->
        <header class="bg-[#002244] text-white">
            <div class="max-w-5xl mx-auto px-4">
                <div class="h-16 flex items-center justify-between">
                    <h1 class="font-bold">
                        <i class="fa-solid fa-pen-to-square mr-2"></i>
                        Edit Hotel
                    </h1>
                    <a href="../hotel.php" class="text-xs bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg">
                        <i class="fa-solid fa-arrow-left mr-1"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="max-w-5xl mx-auto px-4 py-8">
            <form action="update.php" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 md:p-8">
                
                <input type="hidden" name="id" value="<?= (int)$hotel['id'] ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- NAMA -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Nama Hotel</label>
                        <input type="text" name="nama_hotel" required maxlength="200" value="<?= htmlspecialchars($hotel['nama_hotel'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm">
                    </div>

                    <!-- DESTINASI -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Destinasi</label>
                        <input type="text" name="destinasi" required maxlength="100" value="<?= htmlspecialchars($hotel['destinasi'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm">
                    </div>

                    <!-- BINTANG -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Bintang Hotel</label>
                        <select name="bintang" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= (int)$hotel['bintang'] === $i ? 'selected' : '' ?>>
                                    <?= $i ?> Bintang
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- HARGA -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Harga Per Malam</label>
                        <input type="number" name="harga_per_malam" required min="0" step="1" value="<?= htmlspecialchars($hotel['harga_per_malam'] ?? '0', ENT_QUOTES, 'UTF-8') ?>" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm">
                    </div>

                    <!-- RATING -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Rating</label>
                        <input type="number" name="rating" min="0" max="5" step="0.1" value="<?= htmlspecialchars($hotel['rating'] ?? '0', ENT_QUOTES, 'UTF-8') ?>" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm">
                    </div>

                    <!-- REVIEW -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Total Review</label>
                        <input type="number" name="total_review" min="0" value="<?= (int)$hotel['total_review'] ?>" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm">
                    </div>

                    <!-- STATUS -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Status</label>
                        <select name="status" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm">
                            <option value="aktif" <?= strtolower($hotel['status']) === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= strtolower($hotel['status']) === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>

                    <!-- GAMBAR UTAMA BARU -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">
                            <i class="fa-solid fa-image text-blue-600 mr-1"></i>
                            Ganti Gambar Utama
                        </label>
                        <input type="file" name="gambar_utama" id="gambar_utama" accept=".jpg,.jpeg,.png,.webp" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm">
                        <p class="text-xs text-slate-400 mt-2">Kosongkan jika tidak ingin mengganti gambar utama.</p>
                        <div id="previewUtama" class="mt-3 hidden">
                            <img id="previewUtamaImage" class="preview-image border" alt="Preview">
                        </div>
                    </div>

                    <!-- ALAMAT -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">Alamat</label>
                        <textarea name="alamat" rows="3" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm"><?= htmlspecialchars($hotel['alamat'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <!-- FASILITAS -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">Fasilitas</label>
                        <textarea name="fasilitas" rows="4" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm"><?= htmlspecialchars($hotel['fasilitas'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <!-- DESKRIPSI -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="5" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm"><?= htmlspecialchars($hotel['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <!-- GAMBAR UTAMA SAAT INI -->
                    <?php if (!empty($hotel['gambar_utama'])): ?>
                        <div class="md:col-span-2">
                            <p class="text-sm font-semibold mb-3">Gambar Utama Saat Ini</p>
                            <img src="../../images/hotel/<?= htmlspecialchars($hotel['gambar_utama'], ENT_QUOTES, 'UTF-8') ?>" alt="Gambar Hotel" class="preview-image border" onerror="this.style.display='none';">
                        </div>
                    <?php endif; ?>

                    <!-- GALERI LAMA -->
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-semibold">
                                <i class="fa-solid fa-images text-blue-600 mr-1"></i>
                                Galeri Hotel
                            </p>
                        </div>

                        <?php if (!empty($galeri)): ?>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                                <?php foreach ($galeri as $gambar): ?>
                                    <div class="relative border rounded-xl overflow-hidden bg-slate-50">
                                        <img src="../../images/hotel/<?= htmlspecialchars($gambar['gambar'], ENT_QUOTES, 'UTF-8') ?>" alt="Galeri Hotel" class="w-full h-28 object-cover" onerror="this.style.display='none';">
                                        <label class="flex items-center gap-2 p-2 text-xs text-red-600 cursor-pointer">
                                            <input type="checkbox" name="hapus_galeri[]" value="<?= (int)$gambar['id'] ?>" class="rounded">
                                            Hapus
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="bg-slate-50 border rounded-lg p-5 text-sm text-slate-400">
                                Belum ada gambar galeri.
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- TAMBAH GALERI -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">
                            <i class="fa-solid fa-plus text-blue-600 mr-1"></i>
                            Tambah Gambar Galeri
                        </label>
                        <input type="file" name="gambar_galeri[]" id="gambar_galeri" accept=".jpg,.jpeg,.png,.webp" multiple class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm">
                        <p class="text-xs text-slate-400 mt-2">Bisa memilih beberapa gambar sekaligus. Maksimal 5 MB per gambar.</p>
                        <div id="galleryPreview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mt-4"></div>
                    </div>

                </div>

                <!-- BUTTON -->
                <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                    <a href="../hotel.php" class="px-5 py-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-sm font-semibold">Batal</a>
                    <button type="submit" class="px-6 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                        <i class="fa-solid fa-save mr-2"></i>
                        Update Hotel
                    </button>
                </div>

            </form>
        </main>

    </div>

    <script>
        // PREVIEW GAMBAR UTAMA
        const gambarUtama = document.getElementById('gambar_utama');
        const previewUtama = document.getElementById('previewUtama');
        const previewUtamaImage = document.getElementById('previewUtamaImage');

        gambarUtama.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) {
                previewUtama.classList.add('hidden');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('Gambar utama maksimal 5 MB.');
                this.value = '';
                previewUtama.classList.add('hidden');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewUtamaImage.src = e.target.result;
                previewUtama.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });

        // PREVIEW GALERI BARU
        const gambarGaleri = document.getElementById('gambar_galeri');
        const galleryPreview = document.getElementById('galleryPreview');

        gambarGaleri.addEventListener('change', function() {
            galleryPreview.innerHTML = '';

            Array.from(this.files).forEach(function(file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('File "' + file.name + '" lebih dari 5 MB.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const wrapper = document.createElement('div');
                    wrapper.className = 'relative bg-slate-100 rounded-xl overflow-hidden border';
                    wrapper.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-24 object-cover" alt="Preview">
                        <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[10px] px-2 py-1 truncate">
                            ${file.name}
                        </div>
                    `;
                    galleryPreview.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>

</body>
</html>