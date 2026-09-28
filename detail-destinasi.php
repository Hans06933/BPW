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
// INCREMENT VIEWS
// ============================================================
$viewKey = 'viewed_destinasi_' . $destinasi['id'];
if (empty($_SESSION[$viewKey])) {
    try {
        db_update('destinasi', ['views' => ((int)$destinasi['views'] + 1)], 'id', $destinasi['id']);
        $destinasi['views'] = (int)$destinasi['views'] + 1;
        $_SESSION[$viewKey] = true;
    } catch (PDOException $e) {}
}

// ============================================================
// FUNGSI PATH GAMBAR
// ============================================================
function getDestinationImage($gambar)
{
    if (empty($gambar)) return null;
    $gambar = trim($gambar);
    $gambar = str_replace('\\', '/', $gambar);

    if (filter_var($gambar, FILTER_VALIDATE_URL)) return $gambar;

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
// DATA
// ============================================================
$nama           = $destinasi['nama_destinasi'] ?? 'Destinasi';
$wilayah        = $destinasi['wilayah'] ?? '-';
$kategori       = $destinasi['kategori'] ?? 'pantai';
$deskripsi      = $destinasi['deskripsi'] ?? '';
$alamat         = $destinasi['alamat'] ?? '';
$hargaTiket     = (float) ($destinasi['harga_tiket_masuk'] ?? 0);
$jamOperasional = $destinasi['jam_operasional'] ?? '';
$rating         = (float) ($destinasi['rating'] ?? 0);
$totalReview    = (int) ($destinasi['total_review'] ?? 0);
$views          = (int) ($destinasi['views'] ?? 0);

// ============================================================
// ICON & LABEL KATEGORI
// ============================================================
$kategoriMap = [
    'pantai' => ['icon' => 'fa-umbrella-beach', 'label' => 'Pantai',  'gradient' => 'from-cyan-500 to-blue-500',    'bg' => 'bg-cyan-500'],
    'gunung' => ['icon' => 'fa-mountain',        'label' => 'Gunung',  'gradient' => 'from-emerald-500 to-teal-500', 'bg' => 'bg-emerald-500'],
    'danau'  => ['icon' => 'fa-water',           'label' => 'Danau',   'gradient' => 'from-blue-500 to-indigo-500',  'bg' => 'bg-blue-500'],
    'budaya' => ['icon' => 'fa-gopuram',         'label' => 'Budaya',  'gradient' => 'from-amber-500 to-orange-500', 'bg' => 'bg-amber-500'],
    'kota'   => ['icon' => 'fa-city',            'label' => 'Kota',    'gradient' => 'from-purple-500 to-pink-500',  'bg' => 'bg-purple-500'],
];

$katData = $kategoriMap[$kategori] ?? ['icon' => 'fa-map-pin', 'label' => ucfirst($kategori), 'gradient' => 'from-gray-500 to-gray-700', 'bg' => 'bg-gray-500'];

// ============================================================
// DESTINASI TERKAIT
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
     HERO — Gambar Besar + Overlay
============================================================ -->
<section class="relative">

    <!-- Background Image -->
    <div class="h-[400px] md:h-[520px] bg-slate-900 relative overflow-hidden">
        <?php if ($gambarUtama): ?>
            <img src="<?= htmlspecialchars($gambarUtama) ?>"
                 alt="<?= htmlspecialchars($nama) ?>"
                 class="w-full h-full object-cover scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/60 to-slate-900/20"></div>
        <?php else: ?>
            <div class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-900"></div>
        <?php endif; ?>

        <!-- Konten Hero -->
        <div class="absolute inset-0 flex items-end pb-32 md:pb-40">
            <div class="container mx-auto px-4 md:px-6 max-w-6xl">

                <!-- Breadcrumb -->
                <nav class="flex items-center gap-2 text-xs text-white/70 mb-4 flex-wrap">
                    <a href="destinasi.php" class="hover:text-white transition">Destinasi</a>
                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                    <span class="text-white line-clamp-1"><?= htmlspecialchars($nama) ?></span>
                </nav>

                <!-- Kategori Badge -->
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="bg-gradient-to-r <?= $katData['gradient'] ?> text-white px-3.5 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider inline-flex items-center gap-1.5 shadow-lg">
                        <i class="fa-solid <?= $katData['icon'] ?>"></i>
                        <?= $katData['label'] ?>
                    </span>

                    <span class="bg-white/10 backdrop-blur border border-white/20 text-white px-3.5 py-1.5 rounded-full text-[10px] font-bold inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-cyan-300"></i>
                        <?= htmlspecialchars($wilayah) ?>
                    </span>

                    <?php if ($rating > 0): ?>
                        <span class="bg-yellow-400 text-yellow-900 px-3.5 py-1.5 rounded-full text-[10px] font-bold inline-flex items-center gap-1.5 shadow-lg">
                            <i class="fa-solid fa-star"></i>
                            <?= number_format($rating, 1) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Judul -->
                <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-4 drop-shadow-lg">
                    <?= htmlspecialchars($nama) ?>
                </h1>

                <!-- Meta Info -->
                <div class="flex flex-wrap items-center gap-4 text-xs md:text-sm text-white/90">
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fa-regular fa-eye text-cyan-300"></i>
                        <strong><?= number_format($views) ?></strong>
                        <span class="text-white/60">views</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <i class="fa-regular fa-comment text-cyan-300"></i>
                        <strong><?= number_format($totalReview) ?></strong>
                        <span class="text-white/60">review</span>
                    </span>
                    <?php if ($alamat !== ''): ?>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-map-pin text-cyan-300"></i>
                            <?= htmlspecialchars(mb_substr($alamat, 0, 60)) ?>
                        </span>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

</section>


<!-- ============================================================
     INFO CARDS (OVERLAP HERO)
============================================================ -->
<section class="container mx-auto px-4 md:px-6 max-w-6xl -mt-20 md:-mt-24 relative z-10">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">

        <!-- Tiket -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-4 md:p-5 hover:-translate-y-1 transition duration-300">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-ticket text-emerald-600 text-lg"></i>
            </div>
            <div class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Harga Tiket</div>
            <div class="font-extrabold text-gray-800 text-sm md:text-base">
                <?= $hargaTiket > 0 ? 'Rp ' . number_format($hargaTiket, 0, ',', '.') : 'Gratis' ?>
            </div>
        </div>

        <!-- Jam Operasional -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-4 md:p-5 hover:-translate-y-1 transition duration-300">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-regular fa-clock text-blue-600 text-lg"></i>
            </div>
            <div class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Jam Buka</div>
            <div class="font-extrabold text-gray-800 text-xs md:text-sm">
                <?= $jamOperasional !== '' ? htmlspecialchars($jamOperasional) : '—' ?>
            </div>
        </div>

        <!-- Wilayah -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-4 md:p-5 hover:-translate-y-1 transition duration-300">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-cyan-100 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-location-dot text-cyan-600 text-lg"></i>
            </div>
            <div class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Wilayah</div>
            <div class="font-extrabold text-gray-800 text-sm md:text-base">
                <?= htmlspecialchars($wilayah) ?>
            </div>
        </div>

        <!-- Rating -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-4 md:p-5 hover:-translate-y-1 transition duration-300">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-solid fa-star text-amber-600 text-lg"></i>
            </div>
            <div class="text-[10px] text-gray-400 uppercase tracking-wider mb-1">Rating</div>
            <div class="font-extrabold text-gray-800 text-sm md:text-base flex items-center gap-1.5">
                <?= number_format($rating, 1) ?><span class="text-xs text-gray-400 font-normal">/5</span>
            </div>
        </div>

    </div>
</section>


<!-- ============================================================
     KONTEN UTAMA
============================================================ -->
<section class="container mx-auto px-4 md:px-6 max-w-6xl mt-8 md:mt-12 pb-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">

        <!-- KOLOM KIRI — Konten -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Deskripsi -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 md:px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-blue-100">
                    <h2 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2.5">
                        <span class="w-9 h-9 bg-blue-600 text-white rounded-lg flex items-center justify-center text-sm shadow-md">
                            <i class="fa-solid fa-circle-info"></i>
                        </span>
                        Tentang Destinasi
                    </h2>
                </div>
                <div class="p-5 md:p-6">
                    <?php if (!empty($deskripsi)): ?>
                        <div class="text-sm md:text-base text-gray-600 leading-relaxed whitespace-pre-line">
                            <?= nl2br(htmlspecialchars($deskripsi)) ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-6">
                            <i class="fa-regular fa-file-lines text-3xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-400 italic">Belum ada deskripsi untuk destinasi ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Lokasi -->
            <?php if ($alamat !== ''): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 md:px-6 py-4 bg-gradient-to-r from-red-50 to-orange-50 border-b border-red-100">
                        <h2 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2.5">
                            <span class="w-9 h-9 bg-red-500 text-white rounded-lg flex items-center justify-center text-sm shadow-md">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </span>
                            Lokasi
                        </h2>
                    </div>
                    <div class="p-5 md:p-6">
                        <div class="flex items-start gap-3 bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shrink-0 shadow-sm">
                                <i class="fa-solid fa-map-pin text-red-500"></i>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed pt-1">
                                <?= nl2br(htmlspecialchars($alamat)) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Destinasi Serupa -->
            <?php if (!empty($destinasiTerkait)): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 md:px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-purple-100 flex items-center justify-between">
                        <h2 class="text-base md:text-lg font-bold text-gray-800 flex items-center gap-2.5">
                            <span class="w-9 h-9 bg-purple-600 text-white rounded-lg flex items-center justify-center text-sm shadow-md">
                                <i class="fa-solid fa-layer-group"></i>
                            </span>
                            Destinasi Serupa
                        </h2>
                        <a href="destinasi.php?kategori=<?= urlencode($kategori) ?>"
                           class="text-xs text-purple-600 hover:text-purple-700 font-semibold">
                            Lihat Semua <i class="fa-solid fa-arrow-right text-[9px]"></i>
                        </a>
                    </div>

                    <div class="p-5 md:p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <?php foreach ($destinasiTerkait as $d):
                                $img = getDestinationImage($d['gambar_utama'] ?? '');
                                $url = 'detail-destinasi.php?slug=' . urlencode($d['slug']);
                            ?>
                                <a href="<?= htmlspecialchars($url) ?>"
                                   class="group block rounded-xl overflow-hidden border border-gray-100 hover:border-blue-200 hover:shadow-lg transition duration-300">
                                    <div class="relative h-32 bg-slate-200 overflow-hidden">
                                        <?php if ($img): ?>
                                            <img src="<?= htmlspecialchars($img) ?>"
                                                 alt="<?= htmlspecialchars($d['nama_destinasi']) ?>"
                                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                                 loading="lazy">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <i class="fa-regular fa-image text-2xl"></i>
                                            </div>
                                        <?php endif; ?>

                                        <div class="absolute top-2 left-2 bg-white/95 backdrop-blur text-slate-800 text-[9px] font-bold px-2 py-0.5 rounded shadow">
                                            <i class="fa-solid fa-location-dot text-blue-600 text-[8px]"></i>
                                            <?= htmlspecialchars($d['wilayah']) ?>
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <h3 class="font-bold text-xs text-gray-800 group-hover:text-blue-600 transition line-clamp-2 mb-1.5">
                                            <?= htmlspecialchars($d['nama_destinasi']) ?>
                                        </h3>
                                        <div class="flex items-center gap-2 text-[10px] text-gray-400">
                                            <span class="text-yellow-500">
                                                <i class="fa-solid fa-star"></i>
                                                <?= number_format((float) $d['rating'], 1) ?>
                                            </span>
                                            <span>•</span>
                                            <span><?= (int) $d['total_review'] ?> review</span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>


        <!-- KOLOM KANAN — Sidebar -->
        <div class="lg:col-span-1">
            <div class="lg:sticky lg:top-24 space-y-4">

                <!-- Card CTA Utama -->
                <div class="bg-gradient-to-br <?= $katData['gradient'] ?> rounded-2xl p-5 md:p-6 text-white shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full -ml-12 -mb-12"></div>

                    <div class="relative">
                        <div class="text-[10px] uppercase tracking-widest opacity-90 mb-1">Tertarik Berkunjung?</div>
                        <h3 class="font-extrabold text-lg md:text-xl leading-tight mb-3">
                            Rencanakan Kunjungan Anda
                        </h3>
                        <p class="text-xs opacity-90 mb-5">
                            Konsultasi gratis dengan tim kami untuk pengalaman terbaik.
                        </p>

                        <a href="<?= htmlspecialchars($urlWa) ?>" target="_blank"
                           class="w-full bg-white text-gray-800 hover:bg-gray-50 py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition shadow-lg">
                            <i class="fa-brands fa-whatsapp text-lg text-green-600"></i>
                            Chat WhatsApp
                        </a>

                        <a href="paket_wisata.php"
                           class="mt-2 w-full border border-white/40 text-white hover:bg-white/10 py-3 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition">
                            <i class="fa-solid fa-suitcase"></i>
                            Lihat Paket Wisata
                        </a>
                    </div>
                </div>

                <!-- Ringkasan Info -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3 bg-slate-50 border-b">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-list-ul text-blue-600"></i>
                            Ringkasan Informasi
                        </h3>
                    </div>
                    <div class="p-5 space-y-1">

                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-xs text-gray-500 flex items-center gap-2">
                                <i class="fa-solid fa-tag text-blue-500 w-4"></i> Kategori
                            </span>
                            <span class="font-semibold text-gray-800 text-xs"><?= $katData['label'] ?></span>
                        </div>

                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-xs text-gray-500 flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-cyan-500 w-4"></i> Wilayah
                            </span>
                            <span class="font-semibold text-gray-800 text-xs"><?= htmlspecialchars($wilayah) ?></span>
                        </div>

                        <?php if ($jamOperasional !== ''): ?>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-xs text-gray-500 flex items-center gap-2">
                                    <i class="fa-regular fa-clock text-blue-500 w-4"></i> Jam Buka
                                </span>
                                <span class="font-semibold text-gray-800 text-xs text-right"><?= htmlspecialchars($jamOperasional) ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-xs text-gray-500 flex items-center gap-2">
                                <i class="fa-solid fa-ticket text-emerald-500 w-4"></i> Tiket
                            </span>
                            <span class="font-semibold text-emerald-600 text-xs">
                                <?= $hargaTiket > 0 ? 'Rp ' . number_format($hargaTiket, 0, ',', '.') : 'Gratis' ?>
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-2">
                            <span class="text-xs text-gray-500 flex items-center gap-2">
                                <i class="fa-solid fa-star text-amber-500 w-4"></i> Rating
                            </span>
                            <span class="font-semibold text-gray-800 text-xs">
                                <?= number_format($rating, 1) ?>/5
                                <span class="text-gray-400 font-normal">(<?= $totalReview ?>)</span>
                            </span>
                        </div>

                    </div>
                </div>

                <!-- Back Button -->
                <a href="destinasi.php"
                   class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-4 text-center text-sm font-semibold text-gray-700 hover:text-blue-600 hover:border-blue-200 transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i>
                    Kembali ke Semua Destinasi
                </a>

            </div>
        </div>

    </div>
</section>


<?php include __DIR__ . '/layout/footer.php'; ?>