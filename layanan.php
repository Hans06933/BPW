<?php
require_once __DIR__ . '/config/database.php';

try {
    $layanan = db_get_all("
        SELECT 
            id,
            nama_layanan,
            deskripsi,
            gambar,
            status
        FROM layanan
        WHERE status = 'Aktif'
        ORDER BY id DESC
    ");
} catch (Exception $e) {
    $layanan = [];
}

// ============================================================
// FUNGSI UNTUK MENENTUKAN PATH GAMBAR
// ============================================================
function getLayananImage($gambar)
{
    if (empty($gambar)) {
        return null;
    }

    $gambar = trim($gambar);

    if (
        strpos($gambar, 'images/') === 0 ||
        strpos($gambar, 'uploads/') === 0 ||
        strpos($gambar, 'http://') === 0 ||
        strpos($gambar, 'https://') === 0
    ) {
        return $gambar;
    }

    return 'images/layanan/' . ltrim($gambar, '/');
}

// ============================================================
// FUNGSI ICON LAYANAN
// ============================================================
function getLayananIcon($nama)
{
    $nama = strtolower($nama);

    if (strpos($nama, 'bus') !== false) {
        return 'fa-bus';
    }

    if (strpos($nama, 'wisata') !== false || strpos($nama, 'tour') !== false || strpos($nama, 'paket') !== false) {
        return 'fa-suitcase-rolling';
    }

    if (strpos($nama, 'hotel') !== false || strpos($nama, 'penginapan') !== false) {
        return 'fa-hotel';
    }

    if (strpos($nama, 'pesawat') !== false || strpos($nama, 'kereta') !== false || strpos($nama, 'tiket') !== false) {
        return 'fa-ticket';
    }

    if (strpos($nama, 'mice') !== false || strpos($nama, 'event') !== false || strpos($nama, 'corporate') !== false) {
        return 'fa-users-gear';
    }

    if (strpos($nama, 'guide') !== false || strpos($nama, 'pemandu') !== false) {
        return 'fa-user-tie';
    }

    if (strpos($nama, 'transport') !== false || strpos($nama, 'kendaraan') !== false) {
        return 'fa-car';
    }

    return 'fa-suitcase-rolling';
}

include "layout/header.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased">

    <!-- HERO -->
    <section class="relative w-full min-h-[480px] bg-gradient-to-r from-[#002244] via-[#003366] to-transparent flex items-center overflow-hidden">
        <div class="absolute right-0 top-0 h-full w-full lg:w-1/2 bg-cover bg-center opacity-40 lg:opacity-100 z-0 hidden md:block" style="background-image: url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?q=80&w=1200&auto=format&fit=crop');">
            <div class="absolute inset-0 bg-gradient-to-r from-[#003366] via-transparent to-transparent"></div>
        </div>
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10 py-16">
            <span class="bg-blue-600/30 text-sky-400 border border-sky-500/30 text-xs font-semibold uppercase tracking-wider px-3 py-1.5 rounded-md">
                Layanan Lengkap
            </span>
            <h1 class="text-3xl md:text-5xl font-bold text-white mt-5 leading-tight max-w-2xl">
                Layanan Lengkap
                <br>
                untuk
                <span class="text-sky-400">Perjalanan Anda</span>
                yang Tak Terlupakan.
            </h1>
            <p class="text-slate-300 font-light mt-4 max-w-xl text-sm md:text-base leading-relaxed">
                Dapatkan Layanan Terbaik Dari Sewa Transportasi
                Hingga Pemandu Wisata Berlisensi yang siap melayani
                segala kebutuhan perjalanan Anda di seluruh nusantara.
            </p>
        </div>
    </section>

    <!-- DAFTAR LAYANAN DARI DATABASE -->
    <section class="w-full py-20 bg-[#f8fafc]">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Judul -->
            <div class="text-center md:text-left mb-12">
                <p class="text-blue-600 font-semibold tracking-wider text-xs uppercase">Pilihan Layanan Kami</p>
                <h2 class="text-2xl md:text-3xl font-bold text-[#002244] mt-2">Berbagai Pilihan untuk Perjalanan Anda</h2>
            </div>

            <!-- CARD DINAMIS DARI DATABASE -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (!empty($layanan)): ?>
                    <?php foreach ($layanan as $item): ?>
                        <?php
                        $nama = htmlspecialchars($item['nama_layanan'] ?? 'Layanan Wisata', ENT_QUOTES, 'UTF-8');
                        $deskripsi = htmlspecialchars($item['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8');
                        $gambar = getLayananImage($item['gambar'] ?? '');
                        $icon = getLayananIcon($item['nama_layanan'] ?? '');
                        ?>
                        <!-- CARD -->
                        <div class="bg-white rounded-xl shadow-md border border-slate-100 overflow-hidden group hover:shadow-lg transition duration-300">
                            <!-- GAMBAR -->
                            <div class="h-48 w-full bg-slate-200 bg-cover bg-center relative" <?php if ($gambar): ?> style="background-image: url('<?= htmlspecialchars($gambar, ENT_QUOTES, 'UTF-8') ?>');" <?php endif; ?>>
                                <?php if (!$gambar): ?>
                                    <div class="absolute inset-0 flex items-center justify-center text-slate-400">
                                        <div class="text-center">
                                            <i class="fa-solid fa-image text-4xl mb-2"></i>
                                            <p class="text-xs">Gambar Tidak Tersedia</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <!-- ICON -->
                                <div class="absolute bottom-4 left-4 w-12 h-12 rounded-full bg-[#003366] text-white flex items-center justify-center border-2 border-white shadow-md">
                                    <i class="fa-solid <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?> text-lg"></i>
                                </div>
                            </div>
                            <!-- ISI CARD -->
                            <div class="p-6 pt-5">
                                <h3 class="text-lg font-bold text-[#002244]">
                                    <?= $nama ?>
                                </h3>

                                <p class="text-slate-500 font-light text-xs mt-2 leading-relaxed">
                                    <?= $deskripsi ?>
                                </p>

                                <a href="detail_layanan.php?id=<?= (int)$item['id'] ?>" 
                                class="inline-flex items-center space-x-2 text-xs font-semibold text-blue-600 mt-4 hover:text-blue-800 transition">
                                    <span>Selengkapnya</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- JIKA DATA KOSONG -->
                    <div class="col-span-full text-center py-16">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-600 mb-4">
                            <i class="fa-solid fa-concierge-bell text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-[#002244]">Belum Ada Layanan</h3>
                        <p class="text-sm text-slate-400 mt-2">Belum ada layanan aktif yang ditambahkan melalui halaman admin.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- PROMO LAYANAN -->
    <section class="w-full pb-20 bg-[#f8fafc]">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <p class="text-blue-600 font-semibold text-xs uppercase tracking-wider">Destina Layantial</p>
                <h2 class="text-xl md:text-2xl font-bold text-[#002244] mt-1">Promo Layanan Spesial</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- PROMO 1 -->
                <div class="lg:col-span-6 bg-gradient-to-r from-[#002244] to-[#004488] text-white rounded-xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between shadow-md relative overflow-hidden">
                    <div class="space-y-4 z-10 max-w-xs">
                        <h3 class="text-xl font-bold leading-tight">Sewa Transportasi Pribadi</h3>
                        <p class="text-xs text-slate-300 font-light leading-relaxed">Dapatkan layanan transportasi pribadi untuk perjalanan yang nyaman dan aman.</p>
                        <a href="#" class="inline-block bg-white text-[#002244] font-semibold text-xs px-4 py-2.5 rounded hover:bg-slate-100 transition shadow-sm">Selengkapnya &rarr;</a>
                    </div>
                    <div class="w-full md:w-48 h-32 bg-cover bg-center rounded-lg mt-6 md:mt-0 shadow-md" style="background-image: url('https://images.unsplash.com/photo-1549399542-7e3f8b79c341?q=80&w=400&auto=format&fit=crop');"></div>
                </div>

                <!-- PROMO 2 -->
                <div class="lg:col-span-3 bg-white rounded-xl p-6 border border-slate-100 shadow-md flex flex-col justify-between group hover:shadow-lg transition">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-shield-halved text-base"></i>
                        </div>
                        <h4 class="font-bold text-[#002244] text-sm">Asuransi Perjalanan</h4>
                    </div>
                    <div class="w-full h-28 bg-cover bg-center rounded-lg my-4" style="background-image: url('https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=400&auto=format&fit=crop');"></div>
                    <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition flex items-center justify-between">
                        <span>Selengkapnya</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <!-- PROMO 3 -->
                <div class="lg:col-span-3 bg-white rounded-xl p-6 border border-slate-100 shadow-md flex flex-col justify-between group hover:shadow-lg transition">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-passport text-base"></i>
                        </div>
                        <h4 class="font-bold text-[#002244] text-sm">Dokumen Perjalanan</h4>
                    </div>
                    <div class="w-full h-28 bg-cover bg-center rounded-lg my-4" style="background-image: url('https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?q=80&w=400&auto=format&fit=crop');"></div>
                    <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition flex items-center justify-between">
                        <span>Selengkapnya</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FORM KONSULTASI -->
    <section class="w-full pb-24 bg-[#f8fafc]">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="w-full bg-[#002244] rounded-2xl p-6 md:p-10 shadow-xl text-white">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-4 space-y-2">
                        <p class="text-sky-400 uppercase text-xs font-semibold tracking-wider">Konsultasi Form</p>
                        <h3 class="text-xl md:text-2xl font-bold leading-tight">Butuh Bantuan Memilih Layanan yang Tepat?</h3>
                    </div>

                    <div class="lg:col-span-8 w-full">
                        <form action="#" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                            <!-- LAYANAN -->
                            <div class="space-y-1.5">
                                <label class="text-[11px] text-slate-300 font-light block">Layanan yang Diinginkan</label>
                                <select name="layanan" class="w-full bg-white text-slate-800 text-xs px-3 py-3 rounded-lg border border-slate-700 focus:outline-none font-medium appearance-none">
                                    <?php if (!empty($layanan)): ?>
                                        <?php foreach ($layanan as $item): ?>
                                            <option value="<?= htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($item['nama_layanan'], ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option>Layanan Pariwisata</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- JUMLAH PESERTA -->
                            <div class="space-y-1.5">
                                <label class="text-[11px] text-slate-300 font-light block">Jumlah Peserta</label>
                                <select name="jumlah_peserta" class="w-full bg-white text-slate-800 text-xs px-3 py-3 rounded-lg border border-slate-700 focus:outline-none font-medium">
                                    <option>1 - 5 Orang</option>
                                    <option>6 - 15 Orang</option>
                                    <option>Rombongan Besar (15+)</option>
                                </select>
                            </div>

                            <!-- BUTTON -->
                            <div class="lg:row-span-2 flex items-end">
                                <button type="submit" class="w-full bg-white text-[#002244] hover:bg-slate-100 font-bold text-xs py-3.5 px-6 rounded-lg transition duration-200 shadow flex items-center justify-center space-x-2">
                                    <span>Konsultasi Sekarang</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </button>
                            </div>

                            <!-- TANGGAL -->
                            <div class="space-y-1.5">
                                <label class="text-[11px] text-slate-300 font-light block">Rencana Tanggal</label>
                                <input type="date" name="tanggal" class="w-full bg-white text-slate-800 text-xs px-3 py-2.5 rounded-lg border border-slate-700 focus:outline-none font-medium">
                            </div>

                            <!-- PESAN -->
                            <div class="space-y-1.5">
                                <label class="text-[11px] text-slate-300 font-light block">Pesan</label>
                                <input type="text" name="pesan" placeholder="Catatan opsional..." class="w-full bg-white text-slate-800 text-xs px-3 py-3 rounded-lg border border-slate-700 focus:outline-none font-light placeholder-slate-400">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include "layout/footer.php"; ?>

</body>
</html>