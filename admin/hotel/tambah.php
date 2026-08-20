<?php
require_once __DIR__ . '/../../config/database.php';
include __DIR__ . '/../../layout/admin_header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Hotel - Admin BPW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .preview-image {
            width: 120px;
            height: 90px;
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
                        <i class="fa-solid fa-hotel mr-2"></i>
                        Tambah Hotel
                    </h1>
                    <a href="../hotel.php" class="text-xs bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition">
                        <i class="fa-solid fa-arrow-left mr-1"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="max-w-5xl mx-auto px-4 py-8">
            <form action="simpan.php" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 md:p-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- NAMA HOTEL -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Nama Hotel</label>
                        <input type="text" id="nama_hotel" name="nama_hotel" required maxlength="200" placeholder="Contoh: The Mulia Bali" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- DESTINASI -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Destinasi</label>
                        <input type="text" name="destinasi" required maxlength="100" placeholder="Contoh: Bali" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- SLUG (hidden) -->
                    <input type="hidden" id="slug" name="slug">

                    <!-- BINTANG -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Bintang Hotel</label>
                        <select name="bintang" required class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1">1 Bintang</option>
                            <option value="2">2 Bintang</option>
                            <option value="3" selected>3 Bintang</option>
                            <option value="4">4 Bintang</option>
                            <option value="5">5 Bintang</option>
                        </select>
                    </div>

                    <!-- HARGA -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Harga Per Malam</label>
                        <input type="number" name="harga_per_malam" required min="0" step="1" placeholder="Contoh: 2350000" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-slate-400 mt-2">Masukkan harga tanpa titik atau koma.</p>
                    </div>

                    <!-- RATING -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Rating</label>
                        <input type="number" name="rating" required min="0" max="5" step="0.1" value="0" placeholder="Contoh: 4.8" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- TOTAL REVIEW -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Total Review</label>
                        <input type="number" name="total_review" required min="0" value="0" placeholder="Contoh: 125" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- STATUS -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Status</label>
                        <select name="status" required class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="aktif" selected>Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <!-- GAMBAR UTAMA -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">
                            <i class="fa-solid fa-image mr-1 text-blue-600"></i>
                            Gambar Utama
                        </label>
                        <input type="file" name="gambar_utama" id="gambar_utama" accept=".jpg,.jpeg,.png,.webp" required class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-slate-400 mt-2">Gambar utama yang akan ditampilkan pada card hotel. Maksimal 5 MB.</p>
                        <div id="previewUtama" class="mt-3 hidden">
                            <img id="previewUtamaImage" class="preview-image border" alt="Preview gambar utama">
                        </div>
                    </div>

                    <!-- GALERI HOTEL -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">
                            <i class="fa-solid fa-images mr-1 text-blue-600"></i>
                            Galeri Hotel
                        </label>
                        <input type="file" name="gambar_galeri[]" id="gambar_galeri" accept=".jpg,.jpeg,.png,.webp" multiple class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-slate-400 mt-2">Kamu dapat memilih lebih dari satu gambar sekaligus. Format JPG, JPEG, PNG atau WEBP. Maksimal 5 MB per gambar.</p>
                        <div id="galleryPreview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mt-4"></div>
                    </div>

                    <!-- ALAMAT -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">Alamat Hotel</label>
                        <textarea name="alamat" rows="3" placeholder="Contoh: Jl. Raya Nusa Dua Selatan, Bali" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <!-- FASILITAS -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">Fasilitas</label>
                        <textarea name="fasilitas" rows="4" placeholder="Contoh: WiFi, Sarapan, Kolam Renang, Spa, Restoran" class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        <p class="text-xs text-slate-400 mt-2">Pisahkan setiap fasilitas dengan koma.</p>
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs">WiFi</span>
                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs">Sarapan</span>
                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs">Kolam Renang</span>
                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs">Spa</span>
                            <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs">Restoran</span>
                        </div>
                    </div>

                    <!-- DESKRIPSI -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold mb-2">Deskripsi Hotel</label>
                        <textarea name="deskripsi" rows="5" placeholder="Contoh: Resort mewah dengan pemandangan laut, kolam renang infinity, dan layanan kelas dunia." class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                </div>

                <!-- BUTTON -->
                <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                    <a href="../hotel.php" class="px-5 py-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition">Batal</a>
                    <button type="submit" class="px-6 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition">
                        <i class="fa-solid fa-save mr-2"></i>
                        Simpan Hotel
                    </button>
                </div>

            </form>
        </main>

    </div>

    <!-- JAVASCRIPT -->
    <script>
        // AUTO GENERATE SLUG
        const namaHotel = document.getElementById('nama_hotel');
        const slug = document.getElementById('slug');

        namaHotel.addEventListener('input', function() {
            let value = this.value.toLowerCase().trim();
            value = value.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            value = value.replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
            slug.value = value;
        });

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

        // PREVIEW MULTIPLE GALERI
        const gambarGaleri = document.getElementById('gambar_galeri');
        const galleryPreview = document.getElementById('galleryPreview');

        gambarGaleri.addEventListener('change', function() {
            galleryPreview.innerHTML = '';
            const files = Array.from(this.files);

            files.forEach((file, index) => {
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