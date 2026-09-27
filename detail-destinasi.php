<?php
/**
 * ============================================================
 * DETAIL DESTINASI WISATA
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/config/database.php';

// ============================================================
// AMBIL SLUG / ID DARI URL
// ============================================================
$slug = trim($_GET['slug'] ?? '');
$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($slug === '' && $id <= 0) {
    header('Location: destinasi.php');
    exit;
}

// ============================================================
// AMBIL DATA DESTINASI
// ============================================================
$destinasi = null;

try {
    if ($slug !== '') {
        $destinasi = db_get("
            SELECT * FROM destinasi
            WHERE slug = :slug AND status = 'aktif'
            LIMIT 1
        ", [':slug' => $slug]);
    } else {
        $destinasi = db_get("
            SELECT * FROM destinasi
            WHERE id = :id AND status = 'aktif'
            LIMIT 1
        ", [':id' => $id]);
    }
} catch (PDOException $e) {
    $destinasi = null;
}

if (!$destinasi) {
    header('Location: destinasi.php');
    exit;
}

// ============================================================
// INCREMENT VIEWS (sekali per session per destinasi)
// ============================================================
$viewKey = 'viewed_destinasi_' . $destinasi['id'];
if (empty($_SESSION[$viewKey])) {
    try {
        db_update('destinasi', ['views' => ((int)$destinasi['views'] + 1)], 'id', $destinasi['id']);
        $destinasi['views'] = (int)$destinasi['views'] + 1;
        $_SESSION[$viewKey] = true;
    } catch (PDOException $e) {
        // silent
    }
}

// ============================================================
// FUNGSI PATH GAMBAR
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

$gambarUtama = getDestinationImage($destinasi['gambar_utama'] ?? '');

// ============================================================
// DATA YANG DIPAKAI
// ============================================================
$nama          = $destinasi['nama_destinasi'] ?? 'Destinasi';
$wilayah       = $destinasi['wilayah'] ?? '-';
$kategori      = $destinasi['kategori'] ?? 'pantai';
$deskripsi     = $destinasi['deskripsi'] ?? '';
$alamat        = $destinasi['alamat'] ?? '';
$hargaTiket    = (float) ($destinasi['harga_tiket_masuk'] ?? 0);
$jamOperasional = $destinasi['jam_operasional'] ?? '';
$rating        = (float) ($destinasi['rating'] ?? 0);
$totalReview   = (int) ($destinasi['total_review'] ?? 0);
$views         = (int) ($destinasi['views'] ?? 0);

// ============================================================
// ICON & LABEL KATEGORI
// ============================================================
$kategoriMap = [
    'pantai' => ['icon' => 'fa-umbrella-beach', 'label' => 'Pantai', 'color' => 'cyan'],
    'gunung' => ['icon' => 'fa-mountain',        'label' => 'Gunung', 'color' => 'emerald'],
    'danau'  => ['icon' => 'fa-water',           'label' => 'Danau',  'color' => 'blue'],
    'budaya' => ['icon' => 'fa-gopuram',         'label' => 'Budaya', 'color' => 'amber'],
    'kota'   => ['icon' => 'fa-city',            'label' => 'Kota',   'color' => 'purple'],
];

$katData = $kategoriMap[$kategori] ?? ['icon' => 'fa-map-pin', 'label' => ucfirst($kategori), 'color' => 'gray'];

// ============================================================
// DESTINASI TERKAIT (kategori sama, exclude diri sendiri)
// ============================================================
$destinasiTerkait = [];
try {
    $destinasiTerkait = db_get_all("
        SELECT id, nama_destinasi, slug, wilayah, gambar_utama, rating, total_review
        FROM destinasi
        WHERE status = 'aktif'
          AND kategori = :kategori
          AND id != :id
        ORDER BY rating DESC, total_review DESC
        LIMIT 3
    ", [
        ':kategori' => $kategori,
        ':id'       => $destinasi['id'],
    ]);
} catch (PDOException $e) {
    $destinasiTerkait = [];
}

// ============================================================
// WHATSAPP
// ============================================================
$pesanWa = "Halo Admin, saya ingin bertanya tentang destinasi: " . $nama;
$waNumber = function_exists('setting') ? setting('kontak_whatsapp', '6285281441565') : '6285281441565';
$urlWa    = "https://wa.me/" . $waNumber . "?text=" . urlencode($pesanWa);

// ============================================================
// INCLUDE HEADER
// ============================================================
include __DIR__ . '/layout/header.php';
?>

<!-- ============================================================
     DETAIL DESTINASI
============================================================ -->
<div class="bg-slate-50 min-h-screen pb-12">

    <!-- HERO -->
    <section class="relative">
        <div class="h-64 md:h-96 bg-slate-900 relative overflow-hidden">
            <?php if ($gambarUtama): ?>
                <img src="<?= htmlspecialchars($gambarUtama) ?>"
                     alt="<?= htmlspecialchars($nama) ?>"
                     class="w-full h-full object-cover opacity-70">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-transparent"></div>
            <?php else: ?>
                <div class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-900"></div>
            <?php endif; ?>

            <div class="absolute inset-0 flex items-end">
                <div class="container mx-auto px-4 md:px-6 max-w-6xl pb-6 md:pb-10">

                    <!-- Breadcrumb -->
                    <nav class="flex items-center gap-2 text-xs text-blue-200 mb-3 flex-wrap">
                        <a href="index.php" class="hover:text-white transition">
                            <i class="fa-solid fa-house"></i> Home
                        </a>
                        <i class="fa-solid fa-chevron-right text-[8px]"></i>
                        <a href="destinasi.php" class="hover:text-white transition">Destinasi</a>
                        <i class="fa-solid fa-chevron-right text-[8px]"></i>
                        <span class="text-white line-clamp-1"><?= htmlspecialchars($nama) ?></span>
                    </nav>

                    <!-- Kategori Badge -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="bg-<?= $katData['color'] ?>-500 text-white px-3 py-1 rounded text-[10px] font-bold uppercase tracking-wider inline-flex items-center gap-1">
                            <i class="fa-solid <?= $katData['icon'] ?>"></i>
                            <?= $katData['label'] ?>
                        </span>

                        <span class="bg-white/20 backdrop-blur text-white px-3 py-1 rounded text-[10px] font-bold inline-flex items-center gap-1">
                            <i class="fa-solid fa-location-dot"></i>
                            <?= htmlspecialchars($wilayah) ?>
                        </span>
                    </div>

                    <!-- Judul -->
                    <h1 class="text-2xl md:text-4xl font-extrabold text-white leading-tight mb-3">
                        <?= htmlspecialchars($nama) ?>
                    </h1>

                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center gap-4 text-xs text-white/90">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="text-yellow-400">
                                <i class="fa-solid fa-star"></i>
                            </span>
                            <strong><?= number_format($rating, 1) ?></strong>
                            <span class="text-white/70">(<?= number_format($totalReview) ?> review)</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-regular fa-eye"></i>
                            <?= number_format($views) ?> views
                        </span>
                        <?php if ($alamat !== ''): ?>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-map-pin"></i>
                                <?= htmlspecialchars(mb_substr($alamat, 0, 60)) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </section>


    <!-- KONTEN -->
    <section class="container mx-auto px-4 md:px-6 max-w-6xl mt-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- KONTEN UTAMA (KIRI) -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Tentang Destinasi -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6">
                    <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 pb-3 border-b">
                        <i class="fa-solid fa-circle-info text-blue-600"></i>
                        Tentang Destinasi
                    </h2>

                    <?php if (!empty($deskripsi)): ?>
                        <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                            <?= nl2br(htmlspecialchars($deskripsi)) ?>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-gray-400 italic">Belum ada deskripsi untuk destinasi ini.</p>
                    <?php endif; ?>
                </div>

                <!-- Info Praktis -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6">
                    <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 pb-3 border-b">
                        <i class="fa-solid fa-list-check text-blue-600"></i>
                        Informasi Kunjungan
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        <!-- Jam Operasional -->
                        <?php if ($jamOperasional !== ''): ?>
                            <div class="flex items-start gap-3 bg-blue-50 rounded-xl p-4 border border-blue-100">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                                    <i class="fa-regular fa-clock text-blue-600"></i>
                                </div>
                                <div>
                                    <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-0.5">Jam Operasional</div>
                                    <div class="font-semibold text-gray-800 text-sm"><?= htmlspecialchars($jamOperasional) ?></div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Harga Tiket -->
                        <div class="flex items-start gap-3 bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-ticket text-emerald-600"></i>
                            </div>
                            <div>
                                <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-0.5">Harga Tiket Masuk</div>
                                <div class="font-semibold text-gray-800 text-sm">
                                    <?php if ($hargaTiket > 0): ?>
                                        Rp <?= number_format($hargaTiket, 0, ',', '.') ?>
                                    <?php else: ?>
                                        Gratis
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Wilayah -->
                        <div class="flex items-start gap-3 bg-cyan-50 rounded-xl p-4 border border-cyan-100">
                            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-location-dot text-cyan-600"></i>
                            </div>
                            <div>
                                <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-0.5">Wilayah</div>
                                <div class="font-semibold text-gray-800 text-sm"><?= htmlspecialchars($wilayah) ?></div>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="flex items-start gap-3 bg-amber-50 rounded-xl p-4 border border-amber-100">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-star text-amber-600"></i>
                            </div>
                            <div>
                                <div class="text-[10px] text-gray-500 uppercase tracking-wider mb-0.5">Rating</div>
                                <div class="font-semibold text-gray-800 text-sm">
                                    <?= number_format($rating, 1) ?>/5
                                    <span class="text-xs text-gray-400 font-normal">(<?= $totalReview ?> review)</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Alamat Lengkap -->
                <?php if ($alamat !== ''): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6">
                        <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 pb-3 border-b">
                            <i class="fa-solid fa-map-location-dot text-blue-600"></i>
                            Lokasi
                        </h2>
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-map-pin text-red-500 mt-1"></i>
                            <p class="text-sm text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($alamat)) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Destinasi Terkait -->
                <?php if (!empty($destinasiTerkait)): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6">
                        <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 pb-3 border-b">
                            <i class="fa-solid fa-map-location-dot text-blue-600"></i>
                            Destinasi Serupa
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <?php foreach ($destinasiTerkait as $d):
                                $img = getDestinationImage($d['gambar_utama'] ?? '');
                                $url = 'detail-destinasi.php?slug=' . urlencode($d['slug']);
                            ?>
                                <a href="<?= htmlspecialchars($url) ?>"
                                   class="group block rounded-xl overflow-hidden border border-gray-100 hover:shadow-md transition">
                                    <div class="h-32 bg-slate-200 overflow-hidden">
                                        <?php if ($img): ?>
                                            <img src="<?= htmlspecialchars($img) ?>"
                                                 alt="<?= htmlspecialchars($d['nama_destinasi']) ?>"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                                 loading="lazy"
                                                 onerror="this.style.display='none';">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                                                <i class="fa-regular fa-image text-2xl"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-3">
                                        <h3 class="font-bold text-xs text-gray-800 group-hover:text-blue-600 transition line-clamp-2 mb-1">
                                            <?= htmlspecialchars($d['nama_destinasi']) ?>
                                        </h3>
                                        <div class="flex items-center gap-2 text-[10px] text-gray-400">
                                            <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($d['wilayah']) ?></span>
                                            <span class="text-yellow-500">
                                                <i class="fa-solid fa-star"></i>
                                                <?= number_format((float) $d['rating'], 1) ?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>


            <!-- SIDEBAR (KANAN) -->
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-24 space-y-4">

                    <!-- Card Ringkasan -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

                        <?php if ($gambarUtama): ?>
                            <div class="h-40 bg-slate-100 overflow-hidden">
                                <img src="<?= htmlspecialchars($gambarUtama) ?>"
                                     alt="<?= htmlspecialchars($nama) ?>"
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none';">
                            </div>
                        <?php endif; ?>

                        <div class="p-5 space-y-3">

                            <div class="text-[10px] text-gray-400 uppercase tracking-widest">Ringkasan</div>
                            <h3 class="font-bold text-gray-800 leading-tight"><?= htmlspecialchars($nama) ?></h3>

                            <div class="space-y-2 text-sm pt-2 border-t">
                                <div class="flex justify-between py-1.5">
                                    <span class="text-gray-500 text-xs">
                                        <i class="fa-solid fa-tag text-blue-500 w-4"></i>
                                        Kategori
                                    </span>
                                    <span class="font-semibold text-gray-800 text-xs"><?= $katData['label'] ?></span>
                                </div>
                                <div class="flex justify-between py-1.5">
                                    <span class="text-gray-500 text-xs">
                                        <i class="fa-solid fa-location-dot text-blue-500 w-4"></i>
                                        Wilayah
                                    </span>
                                    <span class="font-semibold text-gray-800 text-xs"><?= htmlspecialchars($wilayah) ?></span>
                                </div>
                                <?php if ($jamOperasional !== ''): ?>
                                    <div class="flex justify-between py-1.5">
                                        <span class="text-gray-500 text-xs">
                                            <i class="fa-regular fa-clock text-blue-500 w-4"></i>
                                            Jam Buka
                                        </span>
                                        <span class="font-semibold text-gray-800 text-xs text-right"><?= htmlspecialchars($jamOperasional) ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="flex justify-between py-1.5">
                                    <span class="text-gray-500 text-xs">
                                        <i class="fa-solid fa-ticket text-blue-500 w-4"></i>
                                        Tiket
                                    </span>
                                    <span class="font-semibold text-emerald-600 text-xs">
                                        <?= $hargaTiket > 0 ? 'Rp ' . number_format($hargaTiket, 0, ',', '.') : 'Gratis' ?>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- CTA: Hubungi / Reservasi -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <div class="text-center mb-4">
                            <i class="fa-solid fa-headset text-3xl text-blue-600 mb-2"></i>
                            <h3 class="font-bold text-gray-800 text-sm">Tertarik Berkunjung?</h3>
                            <p class="text-xs text-gray-500 mt-1">Konsultasi gratis dengan tim kami.</p>
                        </div>

                        <a href="<?= htmlspecialchars($urlWa) ?>" target="_blank"
                           class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                            Chat WhatsApp
                        </a>

                        <a href="paket_wisata.php"
                           class="mt-2 w-full border border-blue-600 text-blue-600 hover:bg-blue-50 py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition">
                            <i class="fa-solid fa-suitcase"></i>
                            Lihat Paket Wisata
                        </a>
                    </div>

                    <!-- Back -->
                    <a href="destinasi.php"
                       class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center text-sm font-semibold text-gray-700 hover:text-blue-600 transition">
                        <i class="fa-solid fa-arrow-left mr-1"></i>
                        Kembali ke Semua Destinasi
                    </a>

                </div>
            </div>

        </div>
    </section>

</div>


<?php include __DIR__ . '/layout/footer.php'; ?>