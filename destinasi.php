<?php
/**
 * ============================================================
 * HALAMAN DESTINASI WISATA
 * Bayu Prima Wisata
 * ============================================================
 */

require_once __DIR__ . '/config/database.php';

// ============================================================
// FUNGSI UNTUK MENENTUKAN PATH GAMBAR
// ============================================================
function getDestinationImage($gambar)
{
    if (empty($gambar)) return null;

    $gambar = trim($gambar);
    $gambar = str_replace('\\', '/', $gambar);

    if (filter_var($gambar, FILTER_VALIDATE_URL)) {
        return $gambar;
    }

    if (strpos($gambar, 'images/destinasi/') === 0) {
        if (file_exists(__DIR__ . '/' . $gambar)) return $gambar;
        $filename = basename($gambar);
        if (file_exists(__DIR__ . '/images/destinasi/' . $filename)) {
            return 'images/destinasi/' . $filename;
        }
    }

    if (strpos($gambar, 'uploads/destinasi/') === 0) {
        $filename = basename($gambar);
        if (file_exists(__DIR__ . '/images/destinasi/' . $filename)) {
            return 'images/destinasi/' . $filename;
        }
    }

    $filename = basename($gambar);
    if (file_exists(__DIR__ . '/images/destinasi/' . $filename)) {
        return 'images/destinasi/' . $filename;
    }

    return null;
}

// ============================================================
// FILTER: kategori & search
// ============================================================
$filterKategori = trim($_GET['kategori'] ?? '');
$search         = trim($_GET['search'] ?? '');

// ============================================================
// QUERY: DESTINASI FAVORIT (dengan filter)
// ============================================================
$where  = ["status = 'aktif'"];
$params = [];

if (in_array($filterKategori, ['pantai', 'gunung', 'danau', 'budaya', 'kota'], true)) {
    $where[] = "kategori = :kategori";
    $params[':kategori'] = $filterKategori;
}

if ($search !== '') {
    $where[] = "(nama_destinasi LIKE :search OR deskripsi LIKE :search OR wilayah LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$whereSql = 'WHERE ' . implode(' AND ', $where);

$destinations = [];
try {
    $destinations = db_get_all("
        SELECT
            id, nama_destinasi, slug, wilayah, kategori, deskripsi, alamat,
            harga_tiket_masuk, jam_operasional, gambar_utama,
            rating, total_review, status, views
        FROM destinasi
        $whereSql
        ORDER BY rating DESC, total_review DESC, id DESC
        LIMIT 20
    ", $params);
} catch (PDOException $e) {
    $destinations = [];
}

// ============================================================
// QUERY: SEMUA DESTINASI (untuk wilayah)
// ============================================================
$allDestinations = [];
try {
    $allDestinations = db_get_all("
        SELECT id, nama_destinasi, slug, wilayah, kategori, deskripsi,
               rating, total_review, gambar_utama
        FROM destinasi
        WHERE status = 'aktif'
        ORDER BY wilayah ASC, nama_destinasi ASC
    ");
} catch (PDOException $e) {
    $allDestinations = [];
}

// ============================================================
// KELOMPOKKAN DESTINASI PER WILAYAH
// ============================================================
$regions = [];
foreach ($allDestinations as $destination) {
    $wilayah = trim($destination['wilayah'] ?? '');
    if ($wilayah === '') $wilayah = 'Lainnya';

    if (!isset($regions[$wilayah])) $regions[$wilayah] = [];
    $regions[$wilayah][] = $destination;
}

// ============================================================
// KATEGORI UNTUK FILTER (sesuai ENUM DB)
// ============================================================
$categories = [
    ['key' => '',        'nama' => 'Semua',  'icon' => 'fa-border-all'],
    ['key' => 'pantai',  'nama' => 'Pantai', 'icon' => 'fa-umbrella-beach'],
    ['key' => 'gunung',  'nama' => 'Gunung', 'icon' => 'fa-mountain'],
    ['key' => 'danau',   'nama' => 'Danau',  'icon' => 'fa-water'],
    ['key' => 'budaya',  'nama' => 'Budaya', 'icon' => 'fa-gopuram'],
    ['key' => 'kota',    'nama' => 'Kota',   'icon' => 'fa-city'],
];

include __DIR__ . '/layout/header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Destinasi Wisata - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }

        .bg-destinasi-hero {
            background:
                linear-gradient(to bottom, rgba(0, 51, 102, 0.85), rgba(0, 76, 153, 0.6)),
                url('https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }

        .tab-shadow { box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1); }

        .cat-btn {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .cat-btn:hover { background: #f1f5f9; }
        .cat-btn.active {
            background: #2563eb;
            color: white;
        }
        .cat-btn.active i { color: white !important; }

        .destination-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .destination-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.15);
        }

        @media (max-width: 640px) {
            .hero-mobile-padding {
                padding-top: 2rem;
                padding-bottom: 2.5rem;
            }
        }

        .region-destination { transition: all 0.3s ease; }
        .region-destination.show {
            display: block;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .region-header.active .region-icon { transform: rotate(180deg); }
        .region-header.active { background-color: #eff6ff; }
        .destination-item { transition: all 0.2s ease; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-slate-50 text-gray-800">

<!-- ============================================================
     HERO
============================================================ -->
<section class="bg-destinasi-hero hero-mobile-padding pt-16 md:pt-24 pb-16 md:pb-28 text-white relative">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl space-y-3 md:space-y-6">

        <span class="bg-blue-600 text-white px-3 md:px-4 py-1 md:py-1.5 rounded text-[10px] md:text-[11px] font-bold uppercase tracking-wider inline-block">
            Destinasi
        </span>

        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight">
            Jelajahi Keindahan<br>
            <span class="text-cyan-300">Indonesia Bersama BPW</span>
        </h1>

        <p class="text-slate-200 text-xs sm:text-sm max-w-xl leading-relaxed">
            Temukan berbagai destinasi terbaik di seluruh penjuru Indonesia.
            Dari pantai indah, pegunungan, hingga kota budaya.
            Semua siap untuk petualangan Anda.
        </p>

        <!-- SEARCH -->
        <form method="GET" action="" class="max-w-xl bg-white rounded-lg p-1.5 md:p-2 flex items-center shadow-2xl text-gray-700 mt-2">
            <?php if ($filterKategori !== ''): ?>
                <input type="hidden" name="kategori" value="<?= htmlspecialchars($filterKategori) ?>">
            <?php endif; ?>

            <div class="flex-1 px-2 md:px-3 flex items-center space-x-2">
                <i class="fa-solid fa-location-dot text-gray-400 text-xs md:text-sm"></i>
                <input type="text" name="search"
                       value="<?= htmlspecialchars($search) ?>"
                       placeholder="Cari destinasi..."
                       class="w-full text-xs md:text-sm focus:outline-none bg-transparent">
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] md:text-xs font-bold px-3 md:px-6 py-2 md:py-3 rounded-md transition flex items-center space-x-1 md:space-x-2">
                <span>Cari</span>
                <i class="fa-solid fa-magnifying-glass text-[8px] md:text-[10px]"></i>
            </button>
        </form>

    </div>
</section>


<!-- ============================================================
     KATEGORI (KLIK UNTUK FILTER)
============================================================ -->
<section class="relative z-20 -mt-6 md:-mt-10">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="bg-white rounded-xl tab-shadow p-2 md:p-3 overflow-x-auto">
            <div class="grid grid-cols-6 md:grid-cols-6 gap-1.5 md:gap-2 text-center text-[10px] md:text-xs font-semibold text-gray-600 min-w-[500px] md:min-w-0">

                <?php foreach ($categories as $cat):
                    $isActive = ($cat['key'] === '' && $filterKategori === '')
                             || ($cat['key'] !== '' && $filterKategori === $cat['key']);

                    $urlParams = [];
                    if ($cat['key'] !== '') $urlParams['kategori'] = $cat['key'];
                    if ($search !== '')     $urlParams['search'] = $search;
                    $url = 'destinasi.php' . ($urlParams ? '?' . http_build_query($urlParams) : '');
                ?>
                    <a href="<?= htmlspecialchars($url) ?>"
                       class="cat-btn <?= $isActive ? 'active' : '' ?> rounded-lg p-2 md:p-3 flex flex-col items-center justify-center space-y-1">
                        <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?> text-sm md:text-base text-blue-500"></i>
                        <span class="text-[9px] md:text-[11px]"><?= htmlspecialchars($cat['nama']) ?></span>
                    </a>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     DESTINASI FAVORIT
============================================================ -->
<section class="py-10 md:py-16 bg-white mt-2 md:mt-4">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">

        <!-- JUDUL -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-5 md:mb-8 gap-2">
            <div>
                <span class="text-blue-600 font-extrabold text-[10px] md:text-xs uppercase tracking-wider block mb-1">
                    <?= $filterKategori !== '' ? 'Filter: ' . ucfirst($filterKategori) : 'Destinasi Populer' ?>
                </span>
                <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-custom-blue">
                    <?php if ($search !== ''): ?>
                        Hasil Pencarian "<?= htmlspecialchars($search) ?>"
                    <?php else: ?>
                        Destinasi Favorit Wisatawan
                    <?php endif; ?>
                </h2>
                <p class="text-[10px] md:text-[11px] text-gray-400 mt-0.5 md:mt-1">
                    <?= count($destinations) ?> destinasi ditemukan
                </p>
            </div>

            <?php if ($filterKategori !== '' || $search !== ''): ?>
                <a href="destinasi.php" class="text-red-500 hover:text-red-600 font-bold text-[10px] md:text-xs flex items-center space-x-1 border border-red-200 px-2.5 md:px-4 py-1.5 md:py-2 rounded-md hover:bg-red-50 transition shrink-0">
                    <i class="fa-solid fa-xmark text-[8px] md:text-[10px]"></i>
                    <span>Reset Filter</span>
                </a>
            <?php endif; ?>
        </div>

        <!-- CARD DATABASE -->
        <?php if (!empty($destinations)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">

                <?php foreach ($destinations as $destination):
                    $nama        = $destination['nama_destinasi'] ?? 'Tanpa Nama';
                    $slug        = $destination['slug'] ?? '';
                    $wilayah     = $destination['wilayah'] ?? '-';
                    $kategori    = $destination['kategori'] ?? '-';
                    $deskripsi   = $destination['deskripsi'] ?? '';
                    $rating      = $destination['rating'] ?? 0;
                    $totalReview = $destination['total_review'] ?? 0;
                    $gambar      = $destination['gambar_utama'] ?? '';
                    $hargaTiket  = (float) ($destination['harga_tiket_masuk'] ?? 0);

                    $imagePath = getDestinationImage($gambar);

                    // URL ke detail
                    $detailUrl = 'detail-destinasi.php';
                    if (!empty($slug)) {
                        $detailUrl .= '?slug=' . urlencode($slug);
                    } else {
                        $detailUrl .= '?id=' . (int) $destination['id'];
                    }

                    // Icon kategori
                    $katIcon = [
                        'pantai' => 'fa-umbrella-beach',
                        'gunung' => 'fa-mountain',
                        'danau'  => 'fa-water',
                        'budaya' => 'fa-gopuram',
                        'kota'   => 'fa-city',
                    ][$kategori] ?? 'fa-map-pin';
                ?>

                    <!-- KARTU (seluruhnya bisa diklik) -->
                    <a href="<?= htmlspecialchars($detailUrl) ?>"
                       class="destination-card bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between group">

                        <div>
                            <!-- GAMBAR -->
                            <div class="relative h-40 md:h-44 overflow-hidden bg-slate-200">
                                <?php if ($imagePath): ?>
                                    <img src="<?= htmlspecialchars($imagePath) ?>"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                         alt="<?= htmlspecialchars($nama) ?>"
                                         loading="lazy"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display:none;" class="absolute inset-0 bg-slate-200 items-center justify-center text-slate-500 text-xs font-semibold">
                                        Gambar Tidak Tersedia
                                    </div>
                                <?php else: ?>
                                    <div class="w-full h-full bg-slate-200 flex items-center justify-center text-slate-500 text-xs font-semibold">
                                        <div class="text-center">
                                            <i class="fa-regular fa-image text-2xl mb-2"></i>
                                            <div>Gambar Tidak Tersedia</div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- KATEGORI BADGE -->
                                <span class="absolute top-2 left-2 md:top-3 md:left-3 bg-blue-600 text-white text-[9px] md:text-[10px] font-bold px-2 md:px-2.5 py-0.5 md:py-1 rounded shadow flex items-center space-x-1 uppercase">
                                    <i class="fa-solid <?= $katIcon ?> text-[8px]"></i>
                                    <span><?= htmlspecialchars($kategori) ?></span>
                                </span>

                                <!-- WILAYAH -->
                                <span class="absolute bottom-2 left-2 md:bottom-3 md:left-3 bg-white/95 backdrop-blur text-slate-800 text-[8px] md:text-[10px] font-bold px-2 md:px-2.5 py-0.5 md:py-1 rounded shadow flex items-center space-x-1">
                                    <i class="fa-solid fa-location-dot text-blue-600 text-[8px]"></i>
                                    <span><?= htmlspecialchars($wilayah) ?></span>
                                </span>

                                <!-- RATING -->
                                <?php if ($rating > 0): ?>
                                    <span class="absolute top-2 right-2 md:top-3 md:right-3 bg-yellow-400 text-yellow-900 text-[9px] md:text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center space-x-1 shadow">
                                        <i class="fa-solid fa-star text-[8px]"></i>
                                        <span><?= number_format((float) $rating, 1) ?></span>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- ISI CARD -->
                            <div class="p-3 md:p-4 space-y-2">
                                <h3 class="font-bold text-sm md:text-base text-slate-800 line-clamp-2 group-hover:text-blue-600 transition">
                                    <?= htmlspecialchars($nama) ?>
                                </h3>

                                <?php if (!empty($deskripsi)): ?>
                                    <p class="text-[10px] md:text-xs text-gray-400 leading-relaxed line-clamp-2">
                                        <?= htmlspecialchars($deskripsi) ?>
                                    </p>
                                <?php endif; ?>

                                <!-- Harga + Review -->
                                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                    <div class="text-[10px] text-gray-500">
                                        <i class="fa-solid fa-ticket text-emerald-500"></i>
                                        <?= $hargaTiket > 0 ? 'Rp ' . number_format($hargaTiket, 0, ',', '.') : 'Gratis' ?>
                                    </div>
                                    <div class="text-[10px] text-gray-400">
                                        <i class="fa-regular fa-comment"></i> <?= (int) $totalReview ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- BUTTON -->
                        <div class="p-3 md:p-4 pt-0">
                            <div class="text-[10px] md:text-[11px] font-bold text-blue-600 group-hover:text-blue-700 flex items-center justify-between">
                                <span>Lihat Detail</span>
                                <i class="fa-solid fa-arrow-right text-[8px] md:text-[9px] group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>
                    </a>

                <?php endforeach; ?>

            </div>
        <?php else: ?>
            <!-- JIKA TIDAK ADA DATA -->
            <div class="bg-slate-50 border border-gray-100 rounded-xl p-10 text-center">
                <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-location-dot text-blue-500 text-xl"></i>
                </div>
                <h3 class="font-bold text-gray-700 mb-1">
                    <?= $search !== '' ? 'Destinasi Tidak Ditemukan' : 'Belum Ada Destinasi' ?>
                </h3>
                <p class="text-xs text-gray-400">
                    <?= $search !== '' ? 'Coba kata kunci lain atau reset filter.' : 'Belum ada destinasi aktif yang tersedia.' ?>
                </p>
                <?php if ($filterKategori !== '' || $search !== ''): ?>
                    <a href="destinasi.php" class="inline-flex items-center gap-2 mt-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition">
                        <i class="fa-solid fa-rotate-left"></i> Lihat Semua Destinasi
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</section>


<!-- ============================================================
     PETA DAN WILAYAH
============================================================ -->
<section class="py-10 md:py-16 bg-slate-50 border-t border-b">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 items-start">

            <!-- PETA -->
            <div class="lg:col-span-7 space-y-3 md:space-y-4">
                <div>
                    <span class="text-blue-600 font-extrabold text-[10px] md:text-xs uppercase tracking-wider block mb-1">
                        Peta Destinasi
                    </span>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-custom-blue">
                        Jelajahi Destinasi di Seluruh Indonesia
                    </h2>
                    <p class="text-[10px] md:text-[11px] text-gray-400 mt-0.5 md:mt-1">
                        Klik pada wilayah untuk melihat destinasi menarik.
                    </p>
                </div>

                <div class="bg-white border rounded-xl p-3 md:p-6 shadow-sm flex items-center justify-center min-h-[200px] md:min-h-[300px]">
                    <img src="https://img.freepik.com/free-vector/indonesia-map-with-regions_23-2148281143.jpg?w=800"
                         class="w-full h-auto object-contain max-h-40 md:max-h-60 opacity-80"
                         alt="Peta Indonesia"
                         loading="lazy">
                </div>
            </div>

            <!-- WILAYAH -->
            <div class="lg:col-span-5 bg-white border border-gray-100 rounded-xl p-4 md:p-5 shadow-sm space-y-2 md:space-y-3">
                <h3 class="font-bold text-[11px] md:text-xs text-slate-800 pb-2 border-b flex items-center justify-between">
                    <span>Pilih Wilayah</span>
                    <span class="text-[10px] text-gray-400 font-normal">klik untuk lihat destinasi</span>
                </h3>

                <?php if (!empty($regions)): ?>
                    <?php foreach ($regions as $regionName => $regionDestinations): ?>
                        <?php $regionId = 'region_' . md5($regionName); ?>

                        <div class="region-item border border-gray-100 rounded-lg overflow-hidden">
                            <div class="region-header flex justify-between items-center p-2 md:p-3 bg-white hover:bg-blue-50/30 transition cursor-pointer group"
                                 data-region="<?= htmlspecialchars($regionId) ?>">

                                <span class="font-semibold text-[11px] md:text-xs text-gray-700 group-hover:text-blue-600 transition">
                                    <?= htmlspecialchars($regionName) ?>
                                </span>

                                <div class="flex items-center space-x-1.5 md:space-x-2 text-gray-400 group-hover:text-blue-600 transition">
                                    <span class="text-[8px] md:text-[10px] bg-slate-100 px-1.5 md:px-2 py-0.5 rounded-full group-hover:bg-blue-100 font-medium">
                                        <?= count($regionDestinations) ?> Destinasi
                                    </span>
                                    <i class="region-icon fa-solid fa-chevron-down text-[8px] md:text-[10px] transition-transform duration-300"></i>
                                </div>
                            </div>

                            <div class="region-destination hidden bg-blue-50/10 border-t border-blue-100/30 p-2 md:p-3 space-y-2"
                                 id="<?= htmlspecialchars($regionId) ?>">

                                <div class="grid grid-cols-1 gap-2">
                                    <?php foreach ($regionDestinations as $dest):
                                        $destUrl = 'detail-destinasi.php';
                                        if (!empty($dest['slug'])) {
                                            $destUrl .= '?slug=' . urlencode($dest['slug']);
                                        } else {
                                            $destUrl .= '?id=' . (int) $dest['id'];
                                        }
                                    ?>
                                        <a href="<?= htmlspecialchars($destUrl) ?>"
                                           class="destination-item flex items-start gap-2 p-2 rounded-lg hover:bg-white transition group/item">
                                            <i class="fa-solid fa-location-dot text-blue-500 text-[10px] mt-0.5 group-hover/item:text-blue-600"></i>

                                            <div class="flex-1">
                                                <div class="flex items-center justify-between flex-wrap gap-1">
                                                    <span class="font-medium text-[11px] md:text-xs text-gray-700 group-hover/item:text-blue-600 transition">
                                                        <?= htmlspecialchars($dest['nama_destinasi']) ?>
                                                    </span>
                                                    <span class="text-[9px] text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded-full">
                                                        ⭐ <?= number_format((float) ($dest['rating'] ?? 0), 1) ?>
                                                    </span>
                                                </div>

                                                <?php if (!empty($dest['deskripsi'])): ?>
                                                    <p class="text-[9px] md:text-[10px] text-gray-500 mt-0.5 line-clamp-2">
                                                        <?= htmlspecialchars($dest['deskripsi']) ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-6">
                        <i class="fa-solid fa-location-dot text-gray-300 text-2xl mb-2"></i>
                        <p class="text-xs text-gray-400">Belum ada data wilayah.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     CTA
============================================================ -->
<section class="pb-10 md:pb-16 pt-10 bg-white">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="bg-gradient-to-r from-blue-700 to-cyan-500 rounded-2xl p-5 md:p-8 text-white flex flex-col md:flex-row justify-between items-center shadow-lg space-y-4 md:space-y-0">

            <div class="flex items-center space-x-3 md:space-x-4 text-center md:text-left flex-col md:flex-row space-y-2 md:space-y-0">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-white/10 rounded-full flex items-center justify-center text-lg md:text-xl shrink-0">
                    <i class="fa-solid fa-comments text-cyan-300"></i>
                </div>

                <div>
                    <h3 class="font-extrabold text-sm md:text-base">
                        Belum Menemukan Destinasi yang Tepat?
                    </h3>
                    <p class="text-[10px] md:text-[11px] text-slate-100 opacity-90">
                        Tim BPW siap membantu Anda menemukan destinasi terbaik.
                    </p>
                </div>
            </div>

            <a href="kontak.php" class="bg-white hover:bg-slate-50 text-blue-700 font-bold text-[10px] md:text-xs px-4 md:px-5 py-2.5 md:py-3 rounded-md flex items-center space-x-2 shadow transition shrink-0">
                <span>Konsultasi Sekarang</span>
                <i class="fa-solid fa-arrow-right text-[9px] md:text-[10px]"></i>
            </a>

        </div>
    </div>
</section>


<?php include __DIR__ . '/layout/footer.php'; ?>


<!-- ============================================================
     JAVASCRIPT DROPDOWN WILAYAH
============================================================ -->
<script>
document.querySelectorAll('.region-header').forEach(function (header) {
    header.addEventListener('click', function () {
        const regionId = this.getAttribute('data-region');
        const destinationDiv = document.getElementById(regionId);
        const icon = this.querySelector('.region-icon');

        if (!destinationDiv) return;

        this.classList.toggle('active');

        if (destinationDiv.classList.contains('hidden')) {
            destinationDiv.classList.remove('hidden');
            destinationDiv.classList.add('show');
            if (icon) icon.style.transform = 'rotate(180deg)';
        } else {
            destinationDiv.classList.add('hidden');
            destinationDiv.classList.remove('show');
            if (icon) icon.style.transform = 'rotate(0deg)';
        }
    });
});
</script>

</body>
</html>