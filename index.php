<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Bayu Prima Wisata - Tour & Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }
        .bg-sky-gradient {
            background: linear-gradient(to bottom, rgba(0,51,102,0.85), rgba(0,51,102,0.75)), url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
        /* Perbaikan khusus untuk hero section di mobile */
        @media (max-width: 768px) {
            .hero-mobile {
                min-height: auto;
                padding-top: 2rem;
                padding-bottom: 2rem;
            }
            .form-card-mobile {
                margin-top: 1rem;
            }
        }
        /* Fallback untuk gambar yang gagal loading */
        img {
            background-color: #e2e8f0;
        }
    </style>
</head>
<?php
include "layout/header.php";
?>
<body class="bg-slate-50 text-gray-800">

    <!-- HERO SECTION -->
    <section class="bg-sky-gradient hero-mobile text-white relative py-8 md:py-12 lg:min-h-[600px] flex items-center">
        <div class="container mx-auto px-4 md:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 items-center">
                
                <!-- KONTEN TEKS HERO -->
                <div class="lg:col-span-7 space-y-4 md:space-y-6 text-center lg:text-left">
                    <span class="bg-cyan-500/20 text-cyan-300 px-3 md:px-4 py-1 md:py-1.5 rounded-full text-[10px] md:text-xs font-bold uppercase tracking-wider inline-block">
                        Tour & Travel
                    </span>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight">
                        Jelajahi Keindahan <br class="hidden sm:block"><span class="text-cyan-300">Indonesia</span> Bersama <br>Bayu Prima Wisata
                    </h1>
                    <p class="text-slate-200 text-sm md:text-base max-w-xl mx-auto lg:mx-0">
                        Kami siap menjadi sahabat perjalanan Anda untuk menjelajahi destinasi terbaik di seluruh Indonesia dengan kenyamanan dan layanan prima.
                    </p>
                    <div class="flex flex-wrap gap-3 md:gap-4 justify-center lg:justify-start">
                        <a href="paket_wisata.php" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 md:px-6 py-2.5 md:py-3 rounded-md flex items-center space-x-2 transition text-sm md:text-base">
                            <span>Lihat Paket Wisata</span> 
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                        
                        <a href="destinasi.php" class="bg-white/10 hover:bg-white/20 text-white font-semibold px-4 md:px-6 py-2.5 md:py-3 rounded-md flex items-center space-x-2 transition border border-white/20 text-sm md:text-base">
                            <i class="fa-solid fa-map-location-dot text-sm"></i> 
                            <span>Destinasi</span>
                        </a>
                    </div>
                </div>

                <!-- FORM PENCARIAN -->
                <div class="lg:col-span-5 form-card-mobile mt-4 lg:mt-0">
                    <div class="bg-black/40 backdrop-blur-md rounded-xl p-4 md:p-6 border border-white/10 shadow-2xl">
                        <div class="grid grid-cols-4 gap-0.5 md:gap-1 text-center text-[10px] md:text-xs font-semibold mb-4 md:mb-6 border-b border-white/10 pb-2">
                            <div class="text-cyan-400 border-b-2 border-cyan-400 pb-2 cursor-pointer">
                                <i class="fa-solid fa-suitcase block text-base md:text-lg mb-1"></i> Paket
                            </div>
                            <div class="text-slate-300 hover:text-white pb-2 cursor-pointer">
                                <i class="fa-solid fa-hotel block text-base md:text-lg mb-1"></i> Hotel
                            </div>
                            <div class="text-slate-300 hover:text-white pb-2 cursor-pointer">
                                <i class="fa-solid fa-car block text-base md:text-lg mb-1"></i> Transport
                            </div>
                            <div class="text-slate-300 hover:text-white pb-2 cursor-pointer">
                                <i class="fa-solid fa-comments block text-base md:text-lg mb-1"></i> Konsul
                            </div>
                        </div>
                        
                        <form class="space-y-3 md:space-y-4 text-gray-800">
                            <div>
                                <label class="block text-white text-[10px] md:text-xs font-medium mb-1">Tujuan Wisata</label>
                                <select class="w-full bg-white px-3 py-2 md:py-2.5 rounded text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option>Pilih Destinasi</option>
                                    <option>Bali</option>
                                    <option>Yogyakarta</option>
                                    <option>Labuan Bajo</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-3 md:gap-4">
                                <div>
                                    <label class="block text-white text-[10px] md:text-xs font-medium mb-1">Tanggal Berangkat</label>
                                    <input type="date" class="w-full bg-white px-3 py-2 md:py-2.5 rounded text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-white text-[10px] md:text-xs font-medium mb-1">Jumlah Peserta</label>
                                    <select class="w-full bg-white px-3 py-2 md:py-2.5 rounded text-xs md:text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option>2 Dewasa</option>
                                        <option>1 Dewasa</option>
                                        <option>Kelompok (5+)</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 md:py-3 rounded text-xs md:text-sm transition mt-2 shadow-lg flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-magnifying-glass"></i> <span>Cari Paket</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="bg-white py-8 border-b shadow-sm">
        <div class="container mx-auto px-4 md:px-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 md:gap-6">
                <?php
                $features = [
                    ['icon' => 'fa-shield-halved', 'title' => 'Aman & Terpercaya', 'desc' => 'Perjalanan nyaman dengan pelayanan terbaik'],
                    ['icon' => 'fa-map-location-dot', 'title' => 'Destinasi Terbaik', 'desc' => 'Pilihan destinasi wisata terindah di indonesia'],
                    ['icon' => 'fa-tags', 'title' => 'Harga Terbaik', 'desc' => 'Harga kompetitif dengan kualitas premium'],
                    ['icon' => 'fa-headset', 'title' => 'Layanan 24/7', 'desc' => 'Tim kami siap membantu kapanpun Anda butuhkan'],
                    ['icon' => 'fa-user-check', 'title' => 'Berpengalaman', 'desc' => 'Lebih dari 10 tahun melayani perjalanan Anda'],
                    ['icon' => 'fa-cubes', 'title' => 'Banyak Pilihan', 'desc' => 'Paket lengkap untuk segala kebutuhan perjalanan']
                ];
                foreach ($features as $f) {
                    echo "
                    <div class='flex flex-col items-center text-center p-2'>
                        <div class='w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-xl mb-3 shadow-inner'>
                            <i class='fa-solid {$f['icon']}'></i>
                        </div>
                        <h3 class='font-bold text-xs text-slate-900 mb-1'>{$f['title']}</h3>
                        <p class='text-[11px] text-gray-500 leading-snug hidden sm:block'>{$f['desc']}</p>
                    </div>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- TENTANG KAMI SECTION -->
    <section class="py-12 md:py-16 bg-slate-50">
        <div class="container mx-auto px-4 md:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12 items-center">
                <div class="lg:col-span-5 space-y-4 md:space-y-5">
                    <span class="text-blue-600 font-bold text-xs uppercase tracking-widest block">Tentang Kami</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-custom-blue">Bayu Prima Wisata (BPW)</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        BPW adalah perusahaan tour & travel yang berkomitmen memberikan pengalaman perjalanan terbaik di dalam negeri. Kami melayani berbagai kebutuhan perjalanan Anda, mulai dari wisata keluarga, honeymoon, gathering, study tour, hingga perjalanan dinas.
                    </p>
                    <div class="grid grid-cols-2 gap-3 md:gap-4 pt-2">
                        <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-3">
                            <i class="fa-solid fa-briefcase text-blue-600 text-lg md:text-xl"></i>
                            <div><span class="block font-extrabold text-base md:text-lg text-slate-800">10+</span><span class="text-[10px] md:text-xs text-gray-500">Tahun Pengalaman</span></div>
                        </div>
                        <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-3">
                            <i class="fa-solid fa-face-smile text-blue-600 text-lg md:text-xl"></i>
                            <div><span class="block font-extrabold text-base md:text-lg text-slate-800">5000+</span><span class="text-[10px] md:text-xs text-gray-500">Pelanggan Puas</span></div>
                        </div>
                        <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-3">
                            <i class="fa-solid fa-map text-blue-600 text-lg md:text-xl"></i>
                            <div><span class="block font-extrabold text-base md:text-lg text-slate-800">100+</span><span class="text-[10px] md:text-xs text-gray-500">Destinasi Wisata</span></div>
                        </div>
                        <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-3">
                            <i class="fa-solid fa-thumbs-up text-blue-600 text-lg md:text-xl"></i>
                            <div><span class="block font-extrabold text-base md:text-lg text-slate-800">99%</span><span class="text-[10px] md:text-xs text-gray-500">Tingkat Kepuasan</span></div>
                        </div>
                    </div>
                    <a href="tentang.php" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-5 py-3 rounded-md transition shadow inline-flex items-center space-x-2">
                        <span>Selengkapnya Tentang Kami</span> 
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
                <div class="lg:col-span-7">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-3 rounded-xl overflow-hidden h-48 md:h-64 shadow-md">
                            <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover" alt="Borobudur" loading="lazy">
                        </div>
                        <div class="rounded-xl overflow-hidden h-28 md:h-32 shadow-md">
                            <img src="https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?auto=format&fit=crop&w=400&q=80" class="w-full h-full object-cover" alt="Beach" loading="lazy">
                        </div>
                        <div class="rounded-xl overflow-hidden h-28 md:h-32 shadow-md">
                            <img src="https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=400&q=80" class="w-full h-full object-cover" alt="Raja Ampat" loading="lazy">
                        </div>
                        <div class="rounded-xl overflow-hidden h-28 md:h-32 shadow-md">
                            <img src="https://images.unsplash.com/photo-1504618223053-559bdef9dd5a?auto=format&fit=crop&w=400&q=80" class="w-full h-full object-cover" alt="Air Terjun" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PAKET WISATA POPULER SECTION -->
    <section class="py-12 md:py-16 bg-white">
        <div class="container mx-auto px-4 md:px-6">

            <!-- HEADER -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-6 md:mb-10 gap-3">
                <div>
                    <span class="text-blue-600 font-bold text-xs uppercase tracking-widest block mb-1">
                        Paket Wisata Populer
                    </span>
                    <h2 class="text-2xl md:text-3xl font-bold text-custom-blue">
                        Pilihan Paket Wisata Terbaik
                    </h2>
                </div>
                <button onclick="window.location.href='paket_wisata.php'" class="text-blue-600 hover:text-blue-700 font-bold text-xs flex items-center space-x-1 border border-blue-200 px-3 md:px-4 py-1.5 md:py-2 rounded-md hover:bg-blue-50 transition">
                    <span>Lihat Semua Paket</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

            <?php
            /* =========================================================
            AMBIL DATA PAKET DARI DATABASE
            ========================================================= */
            require_once __DIR__ . '/config/database.php';

            try {
                $db = (new Database())->getConnection();

                $stmt = $db->prepare("
                    SELECT *
                    FROM paket_wisata
                    WHERE status = 'aktif'
                    ORDER BY is_featured DESC, id DESC
                    LIMIT 4
                ");

                $stmt->execute();
                $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $packages = [];
            }
            ?>

            <!-- GRID PAKET -->
            <?php if (!empty($packages)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">

                    <?php foreach ($packages as $pkg): ?>
                        <?php
                        /* =====================================================
                        GAMBAR
                        ===================================================== */
                        $gambar = trim($pkg['gambar_utama'] ?? '');

                        if (!empty($gambar)) {
                            if (filter_var($gambar, FILTER_VALIDATE_URL) || str_starts_with($gambar, 'http://') || str_starts_with($gambar, 'https://')) {
                                $gambarUrl = $gambar;
                            } else {
                                $gambarUrl = 'images/paket/' . rawurlencode($gambar);
                            }
                        } else {
                            $gambarUrl = 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80';
                        }

                        /* =====================================================
                        HARGA
                        ===================================================== */
                        $hargaNormal = (float) ($pkg['harga_normal'] ?? 0);
                        $hargaDiskon = !empty($pkg['harga_diskon']) ? (float) $pkg['harga_diskon'] : 0;

                        if ($hargaDiskon > 0 && $hargaDiskon < $hargaNormal) {
                            $hargaTampil = $hargaDiskon;
                        } else {
                            $hargaTampil = $hargaNormal;
                        }

                        /* =====================================================
                        URL DETAIL
                        ===================================================== */
                        $detailUrl = 'detail_paket_wisata.php?slug=' . urlencode($pkg['slug']);

                        /* =====================================================
                        FASILITAS
                        ===================================================== */
                        $fasilitas = trim($pkg['fasilitas'] ?? '');

                        if (!empty($fasilitas)) {
                            $fasilitasArray = preg_split('/\r\n|\r|\n/', $fasilitas);
                            $fasilitasArray = array_values(array_filter(array_map('trim', $fasilitasArray)));
                        } else {
                            $fasilitasArray = [];
                        }
                        ?>

                        <!-- CARD PAKET -->
                        <div class="bg-white rounded-xl overflow-hidden shadow-md border border-gray-100 hover:shadow-xl transition flex flex-col group">

                            <!-- GAMBAR -->
                            <div class="relative h-48 overflow-hidden bg-gray-200">
                                <a href="<?= htmlspecialchars($detailUrl) ?>">
                                    <img src="<?= htmlspecialchars($gambarUrl) ?>" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300" 
                                        alt="<?= htmlspecialchars($pkg['nama_paket']) ?>" 
                                        loading="lazy" 
                                        onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=800&q=80';">
                                </a>

                                <!-- DESTINASI -->
                                <span class="absolute top-3 left-3 bg-blue-600 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow">
                                    <?= htmlspecialchars($pkg['destinasi']) ?>
                                </span>

                                <!-- DISKON -->
                                <?php if (!empty($pkg['diskon_persen']) && (int)$pkg['diskon_persen'] > 0): ?>
                                    <span class="absolute top-3 right-3 bg-red-500 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow">
                                        DISKON <?= (int)$pkg['diskon_persen'] ?>%
                                    </span>
                                <?php endif; ?>

                                <!-- FEATURED -->
                                <?php if ((int)($pkg['is_featured'] ?? 0) === 1): ?>
                                    <span class="absolute bottom-3 left-3 bg-yellow-400 text-yellow-900 text-[10px] font-bold px-3 py-1 rounded-full shadow">
                                        <i class="fa-solid fa-star mr-1"></i>
                                        POPULER
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- CONTENT -->
                            <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                                <div>
                                    <!-- NAMA -->
                                    <a href="<?= htmlspecialchars($detailUrl) ?>">
                                        <h3 class="font-bold text-sm text-gray-800 mb-2 group-hover:text-blue-600 transition">
                                            <?= htmlspecialchars($pkg['nama_paket']) ?>
                                        </h3>
                                    </a>

                                    <!-- INFORMASI -->
                                    <div class="text-[10px] md:text-[11px] text-gray-500 space-y-1">
                                        <!-- DURASI -->
                                        <div class="flex items-center">
                                            <i class="fa-regular fa-clock w-4 text-blue-500"></i>
                                            <?= htmlspecialchars($pkg['durasi']) ?>
                                        </div>

                                        <!-- FASILITAS 1 -->
                                        <?php if (!empty($fasilitasArray[0])): ?>
                                            <div class="flex items-center">
                                                <i class="fa-solid fa-car w-4 text-blue-500"></i>
                                                <?= htmlspecialchars($fasilitasArray[0]) ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- FASILITAS 2 -->
                                        <?php if (!empty($fasilitasArray[1])): ?>
                                            <div class="flex items-center">
                                                <i class="fa-solid fa-utensils w-4 text-blue-500"></i>
                                                <?= htmlspecialchars($fasilitasArray[1]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- HARGA -->
                                <div class="border-t pt-3 flex justify-between items-center">
                                    <div>
                                        <span class="text-[10px] text-gray-400 block uppercase font-medium tracking-wider">
                                            Mulai dari
                                        </span>

                                        <?php if ($hargaDiskon > 0 && $hargaDiskon < $hargaNormal): ?>
                                            <div>
                                                <span class="text-[10px] text-gray-400 line-through block">
                                                    Rp <?= number_format($hargaNormal, 0, ',', '.') ?>
                                                </span>
                                                <span class="text-sm font-bold text-blue-600">
                                                    Rp <?= number_format($hargaTampil, 0, ',', '.') ?>
                                                </span>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-sm font-bold text-blue-600">
                                                Rp <?= number_format($hargaTampil, 0, ',', '.') ?>
                                            </span>
                                        <?php endif; ?>

                                        <span class="text-[10px] text-gray-400">/pax</span>
                                    </div>

                                    <!-- BUTTON DETAIL -->
                                    <a href="<?= htmlspecialchars($detailUrl) ?>" class="w-8 h-8 bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white rounded-full flex items-center justify-center transition shadow-sm">
                                        <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <!-- JIKA BELUM ADA PAKET -->
                <div class="text-center py-12 border border-dashed border-slate-300 rounded-xl">
                    <i class="fa-solid fa-suitcase-rolling text-4xl text-slate-300 mb-3"></i>
                    <h3 class="font-bold text-slate-600">Belum Ada Paket Wisata</h3>
                    <p class="text-sm text-slate-400 mt-1">Paket wisata yang aktif akan tampil di sini.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- DESTINASI POPULER SECTION -->
    <section class="py-12 md:py-16 bg-slate-50 border-t border-b">
        <div class="container mx-auto px-4 md:px-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-6 md:mb-10 gap-3">
                <div>
                    <span class="text-blue-600 font-bold text-xs uppercase tracking-widest block mb-1">Destinasi Populer</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-custom-blue">Jelajahi Destinasi Impian Anda</h2>
                </div>
                <a href="destinasi.php" class="text-blue-600 hover:text-blue-700 font-bold text-xs flex items-center space-x-1 border border-blue-200 px-3 md:px-4 py-1.5 md:py-2 rounded-md hover:bg-blue-50 transition">
                    <span>Lihat Semua Destinasi</span> 
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-3 md:gap-4">
                <?php
                // Data destinasi dengan gambar dari folder lokal 'images'
                $destinations = [
                    ['name' => 'Bali', 'sub' => 'Pulau Dewata', 'img' => 'images/bali.jpg'],
                    ['name' => 'Jakarta', 'sub' => 'Kota Tua', 'img' => 'images/kota_tua.jpg'],
                    ['name' => 'Yogyakarta', 'sub' => 'Kota Budaya', 'img' => 'images/jogja.jpg'],
                    ['name' => 'Labuan Bajo', 'sub' => 'Surga di Timur', 'img' => 'images/labuan_bajo.jpg'],
                    ['name' => 'Bogor', 'sub' => 'Taman Matahari', 'img' => 'images/bogor.jpg'],
                    ['name' => 'Bandung', 'sub' => 'Kota Kembang', 'img' => 'images/bandung.jpg'],
                    ['name' => 'Danau Toba', 'sub' => 'Keajaiban Alam', 'img' => 'images/danau_toba.jpg'],
                    ['name' => 'Malang', 'sub' => 'Bromo', 'img' => 'images/bromo.jpg']
                ];

                foreach ($destinations as $dst) {
                    echo "
                    <div class='bg-white rounded-xl p-2 md:p-3 border shadow-sm text-center hover:shadow-md transition cursor-pointer group'>
                        <div class='w-full h-16 md:h-20 rounded-lg overflow-hidden mb-2 bg-gray-100'>
                            <img src='{$dst['img']}' class='w-full h-full object-cover group-hover:scale-105 transition duration-300' alt='{$dst['name']}' loading='lazy'>
                        </div>
                        <h4 class='font-bold text-xs text-gray-800 mb-0.5 group-hover:text-blue-600 transition'>{$dst['name']}</h4>
                        <p class='text-[9px] md:text-[10px] text-gray-400'>{$dst['sub']}</p>
                    </div>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- LAYANAN KAMI SECTION -->
    <section class="py-12 md:py-16 bg-white">
        <div class="container mx-auto px-4 md:px-6 text-center">
            <span class="text-blue-600 font-bold text-xs uppercase tracking-widest block mb-1">Layanan Kami</span>
            <h2 class="text-2xl md:text-3xl font-bold text-custom-blue mb-8 md:mb-12">Layanan Lengkap Untuk Perjalanan Anda</h2>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 md:gap-6">
                <?php
                $services = [
                    ['icon' => 'fa-suitcase-rolling', 'title' => 'Paket Wisata', 'desc' => 'Berbagai pilihan paket wisata menarik'],
                    ['icon' => 'fa-ticket', 'title' => 'Tiket Transportasi', 'desc' => 'Tiket pesawat, kereta, dan transportasi lainnya'],
                    ['icon' => 'fa-hotel', 'title' => 'Reservasi Hotel', 'desc' => 'Pilihan hotel terbaik sesuai kebutuhan Anda'],
                    ['icon' => 'fa-car-side', 'title' => 'Sewa Kendaraan', 'desc' => 'Sewa mobil, bus, dan kendaraan lainnya'],
                    ['icon' => 'fa-users-gear', 'title' => 'MICE & Gathering', 'desc' => 'Layanan untuk acara perusahaan & komunitas'],
                    ['icon' => 'fa-passport', 'title' => 'Visa & Dokumen', 'desc' => 'Bantuan pengurusan visa & dokumen']
                ];

                foreach ($services as $srv) {
                    echo "
                    <div class='bg-slate-50 hover:bg-blue-600 hover:text-white p-4 md:p-6 rounded-xl border transition group shadow-sm'>
                        <div class='w-10 h-10 md:w-12 md:h-12 bg-blue-100 text-blue-600 group-hover:bg-white/20 group-hover:text-white rounded-xl flex items-center justify-center text-lg md:text-xl mb-3 md:mb-4 transition shadow-inner mx-auto'>
                            <i class='fa-solid {$srv['icon']}'></i>
                        </div>
                        <h3 class='font-bold text-[11px] md:text-xs mb-1 transition'>{$srv['title']}</h3>
                        <p class='text-[9px] md:text-[11px] text-gray-400 group-hover:text-blue-100 transition leading-relaxed hidden sm:block'>{$srv['desc']}</p>
                    </div>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- PROMO SECTION -->
    <section class="pb-12 md:pb-16 bg-white">
        <div class="container mx-auto px-4 md:px-6">
            <div class="bg-gradient-to-r from-blue-700 to-cyan-500 rounded-2xl p-6 md:p-8 text-white flex flex-col md:flex-row justify-between items-center shadow-xl relative overflow-hidden">
                <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-white to-transparent pointer-events-none"></div>
                
                <div class="space-y-2 md:space-y-3 z-10 text-center md:text-left mb-4 md:mb-0">
                    <span class="bg-white/20 px-3 py-1 rounded-full text-[9px] md:text-[10px] font-bold tracking-wider uppercase inline-block">Penawaran Terbatas</span>
                    <h2 class="text-xl md:text-2xl lg:text-3xl font-extrabold">Promo Spesial untuk Perjalanan Anda!</h2>
                    <p class="text-xs md:text-sm text-slate-100 max-w-xl">Dapatkan harga terbaik untuk berbagai destinasi pilihan. Periode terbatas, pesan sekarang!</p>
                </div>
                <button class="bg-white hover:bg-slate-100 text-blue-700 font-bold text-xs px-5 md:px-6 py-2.5 md:py-3.5 rounded-full shadow transition z-10 flex items-center space-x-2">
                    <span>Lihat Promo</span> <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- TESTIMONI SECTION -->
    <section class="py-12 md:py-16 bg-slate-50 border-t">
        <div class="container mx-auto px-4 md:px-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-6 md:mb-10 gap-3">
                <div>
                    <span class="text-blue-600 font-bold text-xs uppercase tracking-widest block mb-1">Testimoni Pelanggan</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-custom-blue">Apa Kata Mereka?</h2>
                </div>
                <button onclick="window.location.href='testimoni.php'" class="text-blue-600 hover:text-blue-700 font-bold text-xs flex items-center space-x-1 border border-blue-200 px-3 md:px-4 py-1.5 md:py-2 rounded-md hover:bg-blue-50 transition">
                    <span>Lihat Semua Testimoni</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 md:gap-6">
                <?php
                $testimonials = [
                    ['name' => 'Siti Rahmawati', 'city' => 'Jakarta', 'text' => 'Pelayanan sangat memuaskan! Itinerary terencana dengan baik dan tour guide sangat ramah serta berpengalaman. Pasti akan travel lagi dengan BPW!'],
                    ['name' => 'Andi Pratama', 'city' => 'Bandung', 'text' => 'Paket wisata lengkap, harga terjangkau, fasilitas memuaskan. Liburan ke Labuan Bajo bersama BPW sangat berkesan!'],
                    ['name' => 'Dewi Lestari', 'city' => 'Surabaya', 'text' => 'Proses pemesanan mudah, admin responsif, dan perjalanan berjalan lancar. Recommended banget untuk liburan keluarga!']
                ];

                foreach ($testimonials as $tst) {
                    echo "
                    <div class='bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between'>
                        <p class='text-gray-600 text-xs italic leading-relaxed mb-4 md:mb-6'>\"{$tst['text']}\"</p>
                        <div class='flex items-center space-x-3 border-t pt-4'>
                            <div class='w-8 h-8 md:w-10 md:h-10 bg-slate-200 rounded-full flex items-center justify-center text-slate-500 font-bold text-xs md:text-sm'>
                                " . substr($tst['name'], 0, 1) . "
                            </div>
                            <div>
                                <h4 class='font-bold text-xs md:text-sm text-slate-800'>{$tst['name']}</h4>
                                <span class='text-[9px] md:text-[10px] text-gray-400 block'>{$tst['city']}</span>
                                <div class='text-amber-400 text-[9px] md:text-[10px] mt-1'>
                                    <i class='fa-solid fa-star'></i><i class='fa-solid fa-star'></i><i class='fa-solid fa-star'></i><i class='fa-solid fa-star'></i><i class='fa-solid fa-star'></i>
                                </div>
                            </div>
                        </div>
                    </div>";
                }
                ?>
            </div>
        </div>
    </section>

<?php
include "layout/footer.php";
?>

</body>
</html>