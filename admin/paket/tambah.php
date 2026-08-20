<?php
require_once '../../config/database.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Paket Wisata - Admin BPW</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="bg-slate-100">

<div class="flex min-h-screen">

    <main class="flex-1 p-4 md:p-6">

        <!-- HEADER -->
        <div class="mb-6">

            <div class="flex items-center gap-3 mb-2">

                <a href="../paket.php"
                   class="w-9 h-9 flex items-center justify-center
                          bg-white rounded-lg border border-slate-200
                          text-slate-600 hover:bg-slate-50">

                    <i class="fa-solid fa-arrow-left"></i>

                </a>

                <div>
                    <h1 class="text-2xl font-bold text-slate-800">
                        Tambah Paket Wisata
                    </h1>

                    <p class="text-sm text-slate-500">
                        Tambahkan paket wisata baru ke website.
                    </p>
                </div>

            </div>

        </div>


        <!-- FORM -->
        <form action="simpan.php"
              method="POST"
              enctype="multipart/form-data"
              id="formPaket">


            <!-- INFORMASI UTAMA -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6">

                <div class="px-6 py-4 border-b border-slate-200">

                    <h2 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-suitcase text-blue-600"></i>
                        Informasi Paket
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        Informasi utama mengenai paket wisata.
                    </p>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                        <!-- NAMA PAKET -->
                        <div class="md:col-span-2">

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Nama Paket
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="nama_paket"
                                id="nama_paket"
                                required
                                maxlength="200"
                                placeholder="Contoh: Bali 3 Hari 2 Malam"
                                class="w-full px-4 py-2.5 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500 focus:border-blue-500">

                        </div>


                        <!-- SLUG -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Slug
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="slug"
                                id="slug"
                                required
                                maxlength="200"
                                placeholder="bali-3-hari-2-malam"
                                class="w-full px-4 py-2.5 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500">

                            <p class="text-xs text-slate-400 mt-1">
                                Digunakan untuk URL detail paket.
                            </p>

                        </div>


                        <!-- DESTINASI -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Destinasi
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="destinasi"
                                required
                                maxlength="100"
                                placeholder="Contoh: Bali"
                                class="w-full px-4 py-2.5 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500">

                        </div>


                        <!-- DURASI -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Durasi
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="durasi"
                                required
                                maxlength="50"
                                placeholder="Contoh: 3 Hari 2 Malam"
                                class="w-full px-4 py-2.5 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500">

                        </div>


                        <!-- MINIMAL PESERTA -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Minimal Peserta
                            </label>

                            <input
                                type="number"
                                name="minimal_peserta"
                                min="1"
                                value="2"
                                class="w-full px-4 py-2.5 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500">

                        </div>

                    </div>

                </div>

            </div>


            <!-- HARGA -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6">

                <div class="px-6 py-4 border-b border-slate-200">

                    <h2 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-tags text-blue-600"></i>
                        Harga & Diskon
                    </h2>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">


                        <!-- HARGA NORMAL -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Harga Normal
                                <span class="text-red-500">*</span>
                            </label>

                            <div class="relative">

                                <span class="absolute left-4 top-1/2
                                             -translate-y-1/2
                                             text-slate-500 text-sm">
                                    Rp
                                </span>

                                <input
                                    type="number"
                                    name="harga_normal"
                                    required
                                    min="1"
                                    placeholder="2150000"
                                    class="w-full pl-12 pr-4 py-2.5 rounded-lg
                                           border border-slate-300
                                           focus:outline-none focus:ring-2
                                           focus:ring-blue-500">

                            </div>

                        </div>


                        <!-- HARGA DISKON -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Harga Diskon
                            </label>

                            <div class="relative">

                                <span class="absolute left-4 top-1/2
                                             -translate-y-1/2
                                             text-slate-500 text-sm">
                                    Rp
                                </span>

                                <input
                                    type="number"
                                    name="harga_diskon"
                                    min="0"
                                    placeholder="1850000"
                                    class="w-full pl-12 pr-4 py-2.5 rounded-lg
                                           border border-slate-300
                                           focus:outline-none focus:ring-2
                                           focus:ring-blue-500">

                            </div>

                            <p class="text-xs text-slate-400 mt-1">
                                Kosongkan jika tidak ada diskon.
                            </p>

                        </div>


                        <!-- DISKON -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Diskon
                            </label>

                            <div class="relative">

                                <input
                                    type="number"
                                    name="diskon_persen"
                                    min="0"
                                    max="100"
                                    value="0"
                                    class="w-full px-4 pr-10 py-2.5 rounded-lg
                                           border border-slate-300
                                           focus:outline-none focus:ring-2
                                           focus:ring-blue-500">

                                <span class="absolute right-4 top-1/2
                                             -translate-y-1/2
                                             text-slate-500">
                                    %
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- DESKRIPSI -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6">

                <div class="px-6 py-4 border-b border-slate-200">

                    <h2 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-lines text-blue-600"></i>
                        Deskripsi Paket
                    </h2>

                </div>


                <div class="p-6">

                    <div class="space-y-5">


                        <!-- DESKRIPSI -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Deskripsi
                            </label>

                            <textarea
                                name="deskripsi"
                                rows="5"
                                placeholder="Jelaskan mengenai paket wisata ini..."
                                class="w-full px-4 py-3 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500"></textarea>

                        </div>


                        <!-- ITINERARY -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Itinerary
                            </label>

                            <textarea
                                name="itinerary"
                                rows="7"
                                placeholder="Contoh:

Hari 1
- Penjemputan di bandara
- Check-in hotel
- Makan malam

Hari 2
- Wisata pantai
- Makan siang
- Kembali ke hotel"
                                class="w-full px-4 py-3 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500"></textarea>

                        </div>


                        <!-- FASILITAS -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Fasilitas
                            </label>

                            <textarea
                                name="fasilitas"
                                rows="5"
                                placeholder="Contoh:
Hotel
Transportasi
Makan
Tiket wisata
Tour guide"
                                class="w-full px-4 py-3 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500"></textarea>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                            <!-- TERMASUK -->
                            <div>

                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Termasuk
                                </label>

                                <textarea
                                    name="termasuk"
                                    rows="5"
                                    placeholder="Yang termasuk dalam paket..."
                                    class="w-full px-4 py-3 rounded-lg
                                           border border-slate-300
                                           focus:outline-none focus:ring-2
                                           focus:ring-blue-500"></textarea>

                            </div>


                            <!-- TIDAK TERMASUK -->
                            <div>

                                <label class="block text-sm font-semibold text-slate-700 mb-2">
                                    Tidak Termasuk
                                </label>

                                <textarea
                                    name="tidak_termasuk"
                                    rows="5"
                                    placeholder="Yang tidak termasuk dalam paket..."
                                    class="w-full px-4 py-3 rounded-lg
                                           border border-slate-300
                                           focus:outline-none focus:ring-2
                                           focus:ring-blue-500"></textarea>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- GAMBAR -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6">

                <div class="px-6 py-4 border-b border-slate-200">

                    <h2 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-images text-blue-600"></i>
                        Gambar Paket
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        Upload gambar yang akan digunakan pada halaman paket wisata.
                    </p>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                        <!-- GAMBAR UTAMA -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">

                                Gambar Utama
                                <span class="text-red-500">*</span>

                            </label>

                            <input
                                type="file"
                                name="gambar_utama"
                                required
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                class="w-full px-4 py-2.5 rounded-lg
                                       border border-slate-300
                                       bg-white text-sm">

                            <p class="text-xs text-slate-400 mt-2">
                                JPG, PNG atau WEBP. Maksimal 5 MB.
                            </p>

                        </div>


                        <!-- GAMBAR LAIN -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">

                                Gambar Lain

                            </label>

                            <input
                                type="file"
                                name="gambar_lain[]"
                                multiple
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                class="w-full px-4 py-2.5 rounded-lg
                                       border border-slate-300
                                       bg-white text-sm">

                            <p class="text-xs text-slate-400 mt-2">
                                Bisa memilih beberapa gambar sekaligus.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- KUOTA -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6">

                <div class="px-6 py-4 border-b border-slate-200">

                    <h2 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-users text-blue-600"></i>
                        Kuota Peserta
                    </h2>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                        <!-- KUOTA -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Kuota
                            </label>

                            <input
                                type="number"
                                name="kuota"
                                min="0"
                                value="0"
                                placeholder="30"
                                class="w-full px-4 py-2.5 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500">

                        </div>


                        <!-- TERSISA -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Peserta Tersisa
                            </label>

                            <input
                                type="number"
                                name="tersisa"
                                min="0"
                                value="0"
                                placeholder="30"
                                class="w-full px-4 py-2.5 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500">

                            <p class="text-xs text-slate-400 mt-1">
                                Jumlah peserta yang masih tersedia.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- PENGATURAN -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6">

                <div class="px-6 py-4 border-b border-slate-200">

                    <h2 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-gear text-blue-600"></i>
                        Pengaturan Paket
                    </h2>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">


                        <!-- STATUS -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Status
                            </label>

                            <select
                                name="status"
                                class="w-full px-4 py-2.5 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500">

                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                                <option value="habis">Habis</option>

                            </select>

                        </div>


                        <!-- FEATURED -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Paket Terpopuler
                            </label>

                            <select
                                name="is_featured"
                                class="w-full px-4 py-2.5 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500">

                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>

                            </select>

                            <p class="text-xs text-slate-400 mt-1">
                                Tampilkan sebagai paket unggulan.
                            </p>

                        </div>


                        <!-- FLASH SALE -->
                        <div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Flash Sale
                            </label>

                            <select
                                name="is_flash_sale"
                                class="w-full px-4 py-2.5 rounded-lg
                                       border border-slate-300
                                       focus:outline-none focus:ring-2
                                       focus:ring-blue-500">

                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>

                            </select>

                            <p class="text-xs text-slate-400 mt-1">
                                Tandai paket sebagai Flash Sale.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- BUTTON -->
            <div class="flex flex-col-reverse sm:flex-row
                        justify-end gap-3 mb-8">

                <a href="../paket.php"
                   class="px-6 py-3 rounded-lg
                          border border-slate-300
                          bg-white text-slate-700
                          hover:bg-slate-50
                          text-center font-semibold transition">

                    Batal

                </a>


                <button
                    type="submit"
                    class="px-6 py-3 rounded-lg
                           bg-blue-600 hover:bg-blue-700
                           text-white font-semibold
                           flex items-center justify-center gap-2
                           transition">

                    <i class="fa-solid fa-save"></i>

                    Simpan Paket

                </button>

            </div>


        </form>

    </main>

</div>


<script>

/* =========================================================
   OTOMATIS MEMBUAT SLUG DARI NAMA PAKET
========================================================= */

const namaPaket = document.getElementById('nama_paket');
const slugInput = document.getElementById('slug');

let slugManual = false;

slugInput.addEventListener('input', function () {
    slugManual = true;
});

namaPaket.addEventListener('input', function () {

    if (slugManual) {
        return;
    }

    let slug = this.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');

    slugInput.value = slug;
});


/* =========================================================
   VALIDASI HARGA DISKON
========================================================= */

document.getElementById('formPaket').addEventListener('submit', function (e) {

    const hargaNormal = parseFloat(
        document.querySelector('[name="harga_normal"]').value
    ) || 0;

    const hargaDiskonInput = document.querySelector('[name="harga_diskon"]');

    const hargaDiskon = parseFloat(hargaDiskonInput.value) || 0;

    if (hargaNormal <= 0) {
        alert('Harga normal harus lebih besar dari 0.');
        e.preventDefault();
        return;
    }

    if (hargaDiskon > 0 && hargaDiskon >= hargaNormal) {
        alert('Harga diskon harus lebih kecil dari harga normal.');
        e.preventDefault();
        return;
    }

});

</script>

</body>
</html>