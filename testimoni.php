<?php
/**
 * ============================================================
 * HALAMAN TESTIMONI PELANGGAN
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/config/database.php';

// ============================================================
// AMBIL TESTIMONI APPROVED DARI DATABASE
// ============================================================
$testimoniList = [];

try {
    $sql = "
        SELECT
            id, nama, kota_asal, destinasi, tipe_perjalanan,
            rating, testimoni, foto, is_featured, created_at
        FROM testimoni
        WHERE status = 'approved'
        ORDER BY is_featured DESC, created_at DESC
    ";
    $testimoniList = db_get_all($sql);
} catch (PDOException $e) {
    $testimoniList = [];
}

// ============================================================
// STATISTIK DARI DATABASE
// ============================================================
$totalTestimoni   = 0;
$avgRating        = 0;
$totalDestinasi   = 0;
$totalPelanggan   = 5000;
$ratingCounts     = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

try {

    // --------------------------------------------------------
    // Statistik Testimoni
    // --------------------------------------------------------
    $stat = db_get("
        SELECT
            COUNT(*) AS total,
            AVG(rating) AS avg_rating,
            SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS r5,
            SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS r4,
            SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS r3,
            SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS r2,
            SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS r1
        FROM testimoni
        WHERE status = 'approved'
    ");

    if ($stat) {
        $totalTestimoni   = (int) $stat['total'];
        $avgRating        = round((float) $stat['avg_rating'], 1);
        $ratingCounts[5]  = (int) $stat['r5'];
        $ratingCounts[4]  = (int) $stat['r4'];
        $ratingCounts[3]  = (int) $stat['r3'];
        $ratingCounts[2]  = (int) $stat['r2'];
        $ratingCounts[1]  = (int) $stat['r1'];
    }

    // --------------------------------------------------------
    // Statistik Destinasi Aktif
    // --------------------------------------------------------
    $statDest = db_get("SELECT COUNT(*) AS total FROM destinasi WHERE status = 'aktif'");
    if ($statDest) {
        $totalDestinasi = (int) $statDest['total'];
    }

    // --------------------------------------------------------
    // Estimasi Pelanggan Puas
    // (bisa diganti query ke tabel pelanggan kalau ada)
    // --------------------------------------------------------
    $totalPelanggan = $totalTestimoni > 0
        ? ($totalTestimoni * 50) + 5000
        : 5000;

} catch (PDOException $e) {
    // Biarkan default
}

// Hitung persentase per bintang
$ratingPercent = [];
foreach ($ratingCounts as $bintang => $jumlah) {
    $ratingPercent[$bintang] = $totalTestimoni > 0
        ? round(($jumlah / $totalTestimoni) * 100)
        : 0;
}

// ============================================================
// KONVERSI KE JSON UNTUK JAVASCRIPT
// ============================================================
$testimoniJson = [];

foreach ($testimoniList as $t) {
    $testimoniJson[] = [
        'id'              => (int) $t['id'],
        'name'            => $t['nama'],
        'location'        => $t['kota_asal'] ?: 'Indonesia',
        'destination'     => strtolower(preg_replace('/[^a-z0-9]/i', '', $t['destinasi'])),
        'destinationName' => $t['destinasi'],
        'rating'          => (int) $t['rating'],
        'date'            => date('Y-m-d', strtotime($t['created_at'])),
        'text'            => $t['testimoni'],
        'image'           => $t['foto'] ? $t['foto'] : null,
        'tripType'        => $t['tipe_perjalanan'] ?: 'Traveler',
        'verified'        => true,
        'isFeatured'      => (int) $t['is_featured'] === 1,
    ];
}

// Daftar destinasi unik untuk filter button
$uniqueDestinations = [];
foreach ($testimoniList as $t) {
    $key = strtolower(preg_replace('/[^a-z0-9]/i', '', $t['destinasi']));
    if (!isset($uniqueDestinations[$key])) {
        $uniqueDestinations[$key] = $t['destinasi'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Testimoni Pelanggan - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }

        /* Hero Section */
        .bg-testimoni-hero {
            background: linear-gradient(135deg, rgba(0,51,102,0.88) 0%, rgba(0,76,153,0.75) 100%), url('https://images.unsplash.com/photo-1556741533-6e6a3bd8e341?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }

        /* Testimoni Card */
        .testimoni-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .testimoni-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -12px rgba(0,0,0,0.15);
        }

        /* Star Rating */
        .star-rating i {
            color: #fbbf24;
            font-size: 12px;
        }

        /* Quote Icon */
        .quote-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            opacity: 0.1;
            font-size: 48px;
            transition: opacity 0.3s ease;
        }
        .testimoni-card:hover .quote-icon {
            opacity: 0.2;
        }

        /* Filter Button */
        .filter-btn {
            transition: all 0.2s ease;
        }
        .filter-btn.active {
            background-color: #003366;
            color: white;
            border-color: #003366;
        }

        /* Statistic Card */
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
        }

        /* Rating Summary */
        .rating-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        .rating-fill {
            height: 100%;
            background: #fbbf24;
            border-radius: 4px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.9);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .modal.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }
        .modal-content {
            max-width: 500px;
            width: 90%;
            background: white;
            border-radius: 1.5rem;
            cursor: default;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Scroll to top */
        .scroll-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #003366;
            color: white;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 99;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s;
        }
        .scroll-top:hover {
            background: #004c99;
            transform: translateY(-3px);
        }

        /* Swiper Container untuk Mobile */
        .swiper-container {
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }
        .swiper-slide {
            scroll-snap-align: start;
        }
        .swiper-container::-webkit-scrollbar {
            display: none;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Notifikasi */
        .notif {
            animation: slideDownNotif 0.4s ease;
        }
        @keyframes slideDownNotif {
            from { opacity: 0; transform: translate(-50%, -20px); }
            to { opacity: 1; transform: translate(-50%, 0); }
        }
    </style>
</head>

<?php include "layout/header.php"; ?>

<body class="bg-slate-50">

<!-- ============================================================
     NOTIFIKASI SUKSES / ERROR
============================================================ -->
<?php if (!empty($_SESSION['testimoni_success'])): ?>
    <div id="notifSuccess" class="notif fixed top-20 left-1/2 -translate-x-1/2 z-[9999] bg-green-500 text-white px-6 py-3 rounded-lg shadow-2xl flex items-center gap-2 text-sm font-semibold">
        <i class="fa-solid fa-circle-check"></i>
        <?= htmlspecialchars($_SESSION['testimoni_success']) ?>
    </div>
    <?php unset($_SESSION['testimoni_success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['testimoni_error'])): ?>
    <div id="notifError" class="notif fixed top-20 left-1/2 -translate-x-1/2 z-[9999] bg-red-500 text-white px-6 py-3 rounded-lg shadow-2xl flex items-center gap-2 text-sm font-semibold">
        <i class="fa-solid fa-circle-exclamation"></i>
        <?= htmlspecialchars($_SESSION['testimoni_error']) ?>
    </div>
    <?php unset($_SESSION['testimoni_error']); ?>
<?php endif; ?>

<script>
    setTimeout(() => {
        document.getElementById('notifSuccess')?.remove();
        document.getElementById('notifError')?.remove();
    }, 5000);
</script>


<!-- ============================================================
     HERO SECTION
============================================================ -->
<section class="bg-testimoni-hero py-20 md:py-28 text-white relative">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur rounded-full px-4 py-1.5 mb-5">
                <i class="fa-regular fa-star text-yellow-300 text-sm"></i>
                <span class="text-xs font-bold tracking-wide">TESTIMONI</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-4">
                Apa Kata<br>
                <span class="text-yellow-300">Pelanggan Kami</span>
            </h1>
            <p class="text-slate-200 text-base max-w-xl leading-relaxed">
                Lebih dari <?= number_format($totalPelanggan, 0, ',', '.') ?> pelanggan puas telah mempercayakan perjalanan mereka kepada BPW. Simak pengalaman mereka di sini.
            </p>
        </div>
    </div>
</section>


<!-- ============================================================
     STATISTIK SECTION (DINAMIS DARI DATABASE)
============================================================ -->
<section class="py-8 md:py-10 bg-white border-b">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-center">

            <!-- Pelanggan Puas -->
            <div class="stat-card p-3">
                <i class="fa-regular fa-face-smile text-blue-600 text-2xl md:text-3xl mb-2"></i>
                <div class="text-xl md:text-2xl font-bold text-gray-800">
                    <?= number_format($totalPelanggan, 0, ',', '.') ?>+
                </div>
                <div class="text-[10px] md:text-xs text-gray-400">Pelanggan Puas</div>
            </div>

            <!-- Rating Pelanggan -->
            <div class="stat-card p-3">
                <i class="fa-regular fa-star text-yellow-400 text-2xl md:text-3xl mb-2"></i>
                <div class="text-xl md:text-2xl font-bold text-gray-800">
                    <?= $avgRating > 0 ? number_format($avgRating, 1) : '0.0' ?>/5
                </div>
                <div class="text-[10px] md:text-xs text-gray-400">Rating Pelanggan</div>
            </div>

            <!-- Destinasi -->
            <div class="stat-card p-3">
                <i class="fa-regular fa-building text-blue-600 text-2xl md:text-3xl mb-2"></i>
                <div class="text-xl md:text-2xl font-bold text-gray-800">
                    <?= $totalDestinasi ?>+
                </div>
                <div class="text-[10px] md:text-xs text-gray-400">Destinasi</div>
            </div>

            <!-- Tahun Berpengalaman -->
            <div class="stat-card p-3">
                <i class="fa-regular fa-calendar text-blue-600 text-2xl md:text-3xl mb-2"></i>
                <div class="text-xl md:text-2xl font-bold text-gray-800">
                    <?= date('Y') - 2014 ?>+
                </div>
                <div class="text-[10px] md:text-xs text-gray-400">Tahun Berpengalaman</div>
            </div>

            <!-- Total Testimoni -->
            <div class="stat-card p-3">
                <i class="fa-regular fa-comment text-blue-600 text-2xl md:text-3xl mb-2"></i>
                <div class="text-xl md:text-2xl font-bold text-gray-800">
                    <?= number_format($totalTestimoni, 0, ',', '.') ?>
                </div>
                <div class="text-[10px] md:text-xs text-gray-400">Total Testimoni</div>
            </div>

        </div>
    </div>
</section>


<!-- ============================================================
     RATING SUMMARY SECTION
============================================================ -->
<section class="py-8 bg-slate-50">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Left: Overall Rating -->
                <div class="text-center md:text-left">
                    <div class="inline-flex items-center gap-2 bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold mb-3">
                        <i class="fa-regular fa-circle-check"></i> Terverifikasi
                    </div>
                    <div class="text-5xl md:text-6xl font-bold text-gray-800 mb-2">
                        <?= $avgRating > 0 ? number_format($avgRating, 1) : '0.0' ?>
                    </div>
                    <div class="star-rating mb-2">
                        <?php
                        $fullStars = floor($avgRating);
                        $halfStar  = ($avgRating - $fullStars) >= 0.5;
                        for ($i = 1; $i <= 5; $i++):
                            if ($i <= $fullStars):
                        ?>
                            <i class="fa-solid fa-star"></i>
                        <?php elseif ($i == $fullStars + 1 && $halfStar): ?>
                            <i class="fa-solid fa-star-half-alt"></i>
                        <?php else: ?>
                            <i class="fa-regular fa-star text-gray-300"></i>
                        <?php
                            endif;
                        endfor;
                        ?>
                    </div>
                    <p class="text-sm text-gray-500">Dari <?= number_format($totalTestimoni) ?> ulasan pelanggan</p>
                    <div class="flex flex-wrap gap-2 justify-center md:justify-start mt-4">
                        <span class="bg-gray-100 px-3 py-1 rounded-full text-xs">Google Maps</span>
                        <span class="bg-gray-100 px-3 py-1 rounded-full text-xs">TripAdvisor</span>
                        <span class="bg-gray-100 px-3 py-1 rounded-full text-xs">Facebook</span>
                        <span class="bg-gray-100 px-3 py-1 rounded-full text-xs">Website</span>
                    </div>
                </div>

                <!-- Right: Rating Breakdown -->
                <div class="space-y-2">
                    <?php for ($b = 5; $b >= 1; $b--): ?>
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span><?= $b ?> Bintang</span>
                                <span><?= $ratingPercent[$b] ?>%</span>
                            </div>
                            <div class="rating-bar">
                                <div class="rating-fill" style="width: <?= $ratingPercent[$b] ?>%"></div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>

            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     FILTER BUTTONS
============================================================ -->
<section class="py-6 bg-white border-b sticky top-16 z-40">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="flex flex-wrap justify-center gap-2">
            <button data-filter="all" class="filter-btn active px-5 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                Semua
            </button>

            <?php foreach ($uniqueDestinations as $key => $label): ?>
                <button data-filter="<?= htmlspecialchars($key) ?>" class="filter-btn px-5 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                    📍 <?= htmlspecialchars($label) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ============================================================
     TESTIMONI GRID SECTION
============================================================ -->
<section class="py-12 md:py-16 bg-slate-50">
    <div class="container mx-auto px-4 md:px-6 max-w-7xl">

        <!-- Featured Testimonial -->
        <div id="featuredTestimonial" class="mb-8"></div>

        <!-- Testimoni Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="testimoniGrid"></div>

        <!-- Load More Button -->
        <div class="text-center mt-10" id="loadMoreContainer">
            <button id="loadMoreBtn" class="bg-white border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 px-8 py-3 rounded-full text-sm font-semibold inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-down"></i> Muat Lebih Banyak
            </button>
        </div>

        <!-- No Results -->
        <div id="noResults" class="text-center py-12 hidden">
            <i class="fa-regular fa-face-frown text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-400">Belum ada testimoni untuk kategori ini</p>
        </div>
    </div>
</section>


<!-- ============================================================
     SECTION CTA TESTIMONI
============================================================ -->
<section class="py-16 bg-white" id="tulis">
    <div class="container mx-auto px-4 md:px-6 max-w-4xl">
        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-6 md:p-8 text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-regular fa-pen-to-square text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-3">Bagikan Pengalaman Anda</h3>
            <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">
                Sudah bepergian bersama BPW? Bagikan cerita Anda dan inspirasi traveler lainnya!
            </p>
            <button id="writeTestimoniBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full font-semibold transition shadow-lg inline-flex items-center gap-2">
                <i class="fa-regular fa-star"></i> Tulis Testimoni
            </button>
        </div>
    </div>
</section>


<!-- ============================================================
     MODAL FORM INPUT TESTIMONI
============================================================ -->
<div id="testimoniModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden px-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 md:p-8 relative shadow-xl max-h-[90vh] overflow-y-auto">
        <button id="closeModalBtn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>

        <h3 class="text-xl font-bold text-slate-800 mb-1">Kirim Testimoni Anda</h3>
        <p class="text-xs text-slate-500 mb-6">Ceritakan pengalaman perjalanan Anda bersama BPW.</p>

        <form action="proses-tambah-testimoni.php" method="POST" enctype="multipart/form-data" class="space-y-4">

            <!-- Nama & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" required value="<?= htmlspecialchars($_SESSION['testimoni_old']['nama'] ?? '') ?>" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600" placeholder="Cth: Budi Santoso">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($_SESSION['testimoni_old']['email'] ?? '') ?>" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600" placeholder="Cth: budi@email.com">
                </div>
            </div>

            <!-- Kota Asal & Destinasi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kota Asal</label>
                    <input type="text" name="kota_asal" value="<?= htmlspecialchars($_SESSION['testimoni_old']['kota_asal'] ?? '') ?>" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600" placeholder="Cth: Jakarta">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Destinasi yang Dikunjungi</label>
                    <input type="text" name="destinasi" required value="<?= htmlspecialchars($_SESSION['testimoni_old']['destinasi'] ?? '') ?>" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600" placeholder="Cth: Bali / Raja Ampat">
                </div>
            </div>

            <!-- Tipe Perjalanan & Rating -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Perjalanan</label>
                    <select name="tipe_perjalanan" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600">
                        <option value="Keluarga">Keluarga</option>
                        <option value="Couple">Couple</option>
                        <option value="Solo">Solo</option>
                        <option value="Teman">Teman</option>
                        <option value="Bisnis">Bisnis</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Rating</label>
                    <select name="rating" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600">
                        <option value="5">⭐⭐⭐⭐⭐ (5 - Luar Biasa)</option>
                        <option value="4">⭐⭐⭐⭐ (4 - Sangat Bagus)</option>
                        <option value="3">⭐⭐⭐ (3 - Cukup)</option>
                        <option value="2">⭐⭐ (2 - Kurang)</option>
                        <option value="1">⭐ (1 - Buruk)</option>
                    </select>
                </div>
            </div>

            <!-- Pesan Testimoni -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pesan / Ulasan Testimoni</label>
                <textarea name="testimoni" rows="4" required class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600" placeholder="Tuliskan pengalaman seru Anda..."><?= htmlspecialchars($_SESSION['testimoni_old']['testimoni'] ?? '') ?></textarea>
            </div>

            <!-- Upload Foto -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Foto Profil / Dokumentasi (Opsional)</label>
                <input type="file" name="foto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition text-sm">
                    Kirim Testimoni
                </button>
            </div>
        </form>
    </div>
</div>

<?php unset($_SESSION['testimoni_old']); ?>


<!-- ============================================================
     MODAL DETAIL TESTIMONI
============================================================ -->
<div id="detailTestimoniModal" class="modal">
    <div class="modal-content">
        <div class="relative p-6">
            <button class="absolute top-4 right-4 w-8 h-8 bg-gray-100 rounded-full hover:bg-gray-200 transition" id="closeDetailModal">
                <i class="fa-solid fa-xmark text-gray-600"></i>
            </button>
            <div id="modalBody"></div>
        </div>
    </div>
</div>


<!-- SCROLL TO TOP -->
<div class="scroll-top" id="scrollTopBtn">
    <i class="fa-solid fa-arrow-up"></i>
</div>


<script>
// ============================================================
// DATA TESTIMONI DARI DATABASE
// ============================================================
const testimonials = <?= json_encode($testimoniJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

let currentFilter = "all";
let visibleCount  = 6;

// ============================================================
// HELPER FUNCTIONS
// ============================================================
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

function getStarsHTML(rating) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            html += '<i class="fa-solid fa-star text-yellow-400 text-xs"></i>';
        } else if (i - 0.5 <= rating) {
            html += '<i class="fa-solid fa-star-half-alt text-yellow-400 text-xs"></i>';
        } else {
            html += '<i class="fa-regular fa-star text-gray-300 text-xs"></i>';
        }
    }
    return html;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getFilteredTestimonials() {
    let filtered = [...testimonials];
    if (currentFilter !== "all") {
        filtered = filtered.filter(t => t.destination === currentFilter);
    }
    return filtered;
}

// ============================================================
// RENDER TESTIMONI
// ============================================================
function renderTestimonials() {
    const filtered = getFilteredTestimonials();
    const grid = document.getElementById("testimoniGrid");
    const featuredContainer = document.getElementById("featuredTestimonial");
    const noResults = document.getElementById("noResults");
    const loadMoreContainer = document.getElementById("loadMoreContainer");

    // Featured testimonial (hanya untuk filter "all" pada desktop)
    if (filtered.length > 0 && window.innerWidth >= 768 && currentFilter === "all") {
        const featured = filtered[0];
        featuredContainer.innerHTML = `
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl p-6 md:p-8 text-white mb-6">
                <div class="flex flex-col md:flex-row gap-6 items-center">
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-full border-4 border-white/30 overflow-hidden flex-shrink-0 bg-white/20 flex items-center justify-center">
                        ${featured.image
                            ? `<img src="${escapeHtml(featured.image)}" class="w-full h-full object-cover" alt="${escapeHtml(featured.name)}">`
                            : `<span class="text-3xl font-bold">${escapeHtml(featured.name.charAt(0))}</span>`}
                    </div>
                    <div class="text-center md:text-left flex-1">
                        <div class="flex justify-center md:justify-start gap-1 mb-2">${getStarsHTML(featured.rating)}</div>
                        <p class="text-sm md:text-base italic mb-3">"${escapeHtml(featured.text.substring(0, 150))}..."</p>
                        <div>
                            <p class="font-semibold">${escapeHtml(featured.name)}</p>
                            <p class="text-xs text-blue-200">${escapeHtml(featured.location)} • ${escapeHtml(featured.tripType)} • ${formatDate(featured.date)}</p>
                        </div>
                    </div>
                    <button onclick="showTestimonialDetail(${featured.id})" class="bg-white/20 hover:bg-white/30 text-white px-5 py-2 rounded-full text-sm font-semibold transition flex items-center gap-2">
                        Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        `;
        const remaining = filtered.slice(1, visibleCount);
        if (remaining.length === 0) {
            grid.innerHTML = `<div class="col-span-full text-center py-8"><p class="text-gray-400">Belum ada testimoni lain</p></div>`;
        } else {
            grid.innerHTML = remaining.map(t => renderCard(t)).join("");
        }
    } else {
        featuredContainer.innerHTML = '';
        const displayPosts = filtered.slice(0, visibleCount);
        if (displayPosts.length === 0) {
            grid.innerHTML = '';
            noResults.classList.remove("hidden");
            loadMoreContainer.classList.add("hidden");
            return;
        }
        noResults.classList.add("hidden");
        grid.innerHTML = displayPosts.map(t => renderCard(t)).join("");
    }

    // Update load more button
    const totalFiltered = filtered.length;
    if (visibleCount >= totalFiltered || totalFiltered === 0) {
        loadMoreContainer.classList.add("hidden");
    } else {
        loadMoreContainer.classList.remove("hidden");
    }
}

function renderCard(t) {
    return `
        <div class="testimoni-card bg-white rounded-xl p-5 shadow-sm border border-gray-100 relative cursor-pointer" onclick="showTestimonialDetail(${t.id})">
            <i class="fa-solid fa-quote-right quote-icon"></i>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg overflow-hidden">
                    ${t.image
                        ? `<img src="${escapeHtml(t.image)}" class="w-full h-full object-cover">`
                        : `<span>${escapeHtml(t.name.charAt(0))}</span>`}
                </div>
                <div>
                    <h3 class="font-bold text-sm text-gray-800">${escapeHtml(t.name)}</h3>
                    <p class="text-[10px] text-gray-400">${escapeHtml(t.location)} • ${escapeHtml(t.tripType)}</p>
                </div>
                ${t.verified ? '<i class="fa-solid fa-circle-check text-green-500 text-sm ml-auto"></i>' : ''}
            </div>
            <div class="flex gap-0.5 mb-2">${getStarsHTML(t.rating)}</div>
            <p class="text-gray-500 text-xs leading-relaxed line-clamp-3">"${escapeHtml(t.text)}"</p>
            <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
                <span class="text-[10px] text-gray-400">${escapeHtml(t.destinationName)} • ${formatDate(t.date)}</span>
                <span class="text-blue-600 text-[10px] font-semibold">Baca →</span>
            </div>
        </div>
    `;
}

// ============================================================
// MODAL DETAIL TESTIMONI
// ============================================================
window.showTestimonialDetail = (id) => {
    const t = testimonials.find(t => t.id === id);
    if (!t) return;

    const modalBody = document.getElementById("modalBody");
    const modal = document.getElementById("detailTestimoniModal");

    modalBody.innerHTML = `
        <div class="text-center">
            <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4 overflow-hidden">
                ${t.image
                    ? `<img src="${escapeHtml(t.image)}" class="w-full h-full object-cover">`
                    : `<span class="text-2xl font-bold text-blue-600">${escapeHtml(t.name.charAt(0))}</span>`}
            </div>
            <h3 class="font-bold text-lg text-gray-800">${escapeHtml(t.name)}</h3>
            <p class="text-xs text-gray-400 mb-2">${escapeHtml(t.location)} • ${escapeHtml(t.tripType)}</p>
            <div class="flex justify-center gap-0.5 mb-3">${getStarsHTML(t.rating)}</div>
            <p class="text-gray-600 text-sm leading-relaxed italic mb-4">"${escapeHtml(t.text)}"</p>
            <div class="bg-blue-50 rounded-lg p-3">
                <p class="text-xs text-gray-500"><i class="fa-solid fa-location-dot text-blue-500"></i> Destinasi: ${escapeHtml(t.destinationName)}</p>
                <p class="text-xs text-gray-500 mt-1"><i class="fa-regular fa-calendar"></i> Tanggal: ${formatDate(t.date)}</p>
            </div>
            <button onclick="closeDetailModal()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full text-sm font-semibold transition">
                Tutup
            </button>
        </div>
    `;
    modal.classList.add("active");
    document.body.style.overflow = "hidden";
};

function closeDetailModal() {
    const modal = document.getElementById("detailTestimoniModal");
    modal.classList.remove("active");
    document.body.style.overflow = "";
}

document.getElementById("closeDetailModal")?.addEventListener("click", closeDetailModal);
document.getElementById("detailTestimoniModal")?.addEventListener("click", (e) => {
    if (e.target === document.getElementById("detailTestimoniModal")) closeDetailModal();
});

// ============================================================
// LOAD MORE
// ============================================================
document.getElementById("loadMoreBtn")?.addEventListener("click", () => {
    visibleCount += 4;
    renderTestimonials();
});

// ============================================================
// FILTER
// ============================================================
document.querySelectorAll(".filter-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
        this.classList.add("active");
        currentFilter = this.getAttribute("data-filter");
        visibleCount = 6;
        renderTestimonials();
        window.scrollTo({ top: 450, behavior: "smooth" });
    });
});

// ============================================================
// MODAL TULIS TESTIMONI
// ============================================================
const writeBtn = document.getElementById('writeTestimoniBtn');
const writeModal = document.getElementById('testimoniModal');
const closeWriteModalBtn = document.getElementById('closeModalBtn');

writeBtn?.addEventListener('click', () => writeModal.classList.remove('hidden'));
closeWriteModalBtn?.addEventListener('click', () => writeModal.classList.add('hidden'));
window.addEventListener('click', (e) => {
    if (e.target === writeModal) writeModal.classList.add('hidden');
});

// ============================================================
// SCROLL TO TOP
// ============================================================
const scrollBtn = document.getElementById("scrollTopBtn");
window.addEventListener("scroll", () => {
    if (window.scrollY > 400) {
        scrollBtn.style.display = "flex";
    } else {
        scrollBtn.style.display = "none";
    }
});
scrollBtn?.addEventListener("click", () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
});

// ============================================================
// HANDLE WINDOW RESIZE
// ============================================================
let resizeTimeout;
window.addEventListener("resize", () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => renderTestimonials(), 200);
});

// ============================================================
// INITIAL RENDER
// ============================================================
renderTestimonials();
</script>

<?php include "layout/footer.php"; ?>
</body>
</html>