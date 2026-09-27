<?php
/**
 * ============================================================
 * DETAIL PAKET WISATA
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/config/database.php';

/* =========================================================
   KONEKSI DATABASE
========================================================= */
$db = (new Database())->getConnection();

/* =========================================================
   AMBIL SLUG DARI URL
========================================================= */
$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    header('Location: paket_wisata.php');
    exit;
}

/* =========================================================
   AMBIL DATA PAKET
========================================================= */
try {
    $stmt = $db->prepare("
        SELECT *
        FROM paket_wisata
        WHERE slug = :slug
        AND status = 'aktif'
        LIMIT 1
    ");

    $stmt->execute([':slug' => $slug]);
    $paket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paket) {
        header('Location: paket_wisata.php');
        exit;
    }
} catch (PDOException $e) {
    die('Gagal mengambil data: ' . $e->getMessage());
}

/* =========================================================
   FUNGSI PATH GAMBAR
========================================================= */
function getImagePath($filename, $default = '')
{
    $filename = trim($filename);

    if ($filename === '') {
        return $default;
    }

    if (filter_var($filename, FILTER_VALIDATE_URL)) {
        return $filename;
    }

    return 'images/paket/' . basename($filename);
}

/* =========================================================
   GAMBAR UTAMA
========================================================= */
$defaultImage = 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80';
$gambarUtama  = getImagePath($paket['gambar_utama'] ?? '', $defaultImage);

/* =========================================================
   GAMBAR GALERI
========================================================= */
$gambarLain = [];

if (!empty($paket['gambar_lain'])) {
    $gambarLain = array_filter(array_map('trim', explode(',', $paket['gambar_lain'])));
}

/* =========================================================
   FORMAT HARGA
========================================================= */
$hargaNormal    = number_format((float) $paket['harga_normal'], 0, ',', '.');
$hargaNormalRaw = (float) $paket['harga_normal'];

$hargaDiskon = !empty($paket['harga_diskon'])
    ? number_format((float) $paket['harga_diskon'], 0, ',', '.')
    : null;
$hargaDiskonRaw = !empty($paket['harga_diskon']) ? (float) $paket['harga_diskon'] : 0;

/* =========================================================
   HITUNG DISKON
========================================================= */
$diskonPersen = (int) ($paket['diskon_persen'] ?? 0);

if ($diskonPersen <= 0 && !empty($paket['harga_diskon']) && (float) $paket['harga_normal'] > 0) {
    $diskonPersen = round((((float) $paket['harga_normal'] - (float) $paket['harga_diskon']) / (float) $paket['harga_normal']) * 100);
}

/* =========================================================
   HELPER TEXT
========================================================= */
function formatList($text)
{
    if (empty(trim($text))) {
        return [];
    }
    $lines = preg_split('/\r\n|\r|\n/', trim($text));
    return array_values(array_filter(array_map('trim', $lines)));
}

$fasilitas     = formatList($paket['fasilitas'] ?? '');
$termasuk      = formatList($paket['termasuk'] ?? '');
$tidakTermasuk = formatList($paket['tidak_termasuk'] ?? '');

/* =========================================================
   WHATSAPP URL
========================================================= */
$namaPaket = $paket['nama_paket'] ?? 'Paket Wisata';
$pesanWa   = "Halo Admin, saya ingin bertanya mengenai paket: " . $namaPaket;
$waNumber  = function_exists('setting') ? setting('kontak_whatsapp', '6285281441565') : '6285281441565';
$urlWa     = "https://wa.me/" . $waNumber . "?text=" . urlencode($pesanWa);

/* =========================================================
   INCLUDE HEADER
========================================================= */
include __DIR__ . '/layout/header.php';
?>

<!-- ============================================================
     DETAIL PAKET WISATA
============================================================ -->
<div class="bg-slate-50 min-h-screen pb-12">

    <!-- HERO -->
    <section class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-blue-900 py-10 md:py-16">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">

            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-xs text-blue-200 mb-5 flex-wrap">
                <a href="index.php" class="hover:text-white transition">
                    <i class="fa-solid fa-house"></i> Home
                </a>
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                <a href="paket_wisata.php" class="hover:text-white transition">Paket Wisata</a>
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                <span class="text-white line-clamp-1"><?= htmlspecialchars($namaPaket) ?></span>
            </nav>

            <!-- Title & Badge -->
            <div class="text-white">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="bg-blue-600 text-white px-3 py-1 rounded text-[10px] font-bold uppercase tracking-wider">
                        Paket Wisata
                    </span>
                    <?php if ($diskonPersen > 0): ?>
                        <span class="bg-red-500 text-white px-3 py-1 rounded text-[10px] font-bold uppercase tracking-wider animate-pulse">
                            <i class="fa-solid fa-bolt text-yellow-300"></i>
                            Diskon <?= $diskonPersen ?>%
                        </span>
                    <?php endif; ?>
                </div>

                <h1 class="text-2xl md:text-4xl font-extrabold leading-tight mb-3">
                    <?= htmlspecialchars($namaPaket) ?>
                </h1>

                <div class="flex flex-wrap items-center gap-4 text-xs md:text-sm text-blue-100">
                    <span>
                        <i class="fa-solid fa-location-dot text-cyan-300"></i>
                        <?= htmlspecialchars($paket['destinasi'] ?? '-') ?>
                    </span>
                    <span>
                        <i class="fa-regular fa-clock text-cyan-300"></i>
                        <?= htmlspecialchars($paket['durasi'] ?? '-') ?>
                    </span>
                    <?php if (!empty($paket['minimal_peserta'])): ?>
                        <span>
                            <i class="fa-solid fa-users text-cyan-300"></i>
                            Min. <?= (int) $paket['minimal_peserta'] ?> peserta
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>


    <!-- KONTEN -->
    <section class="container mx-auto px-4 md:px-6 max-w-6xl -mt-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- KOLOM KIRI (KONTEN UTAMA) -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Gambar Utama -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <div class="relative">
                        <img src="<?= htmlspecialchars($gambarUtama) ?>"
                             alt="<?= htmlspecialchars($namaPaket) ?>"
                             class="w-full h-64 md:h-96 object-cover"
                             onerror="this.onerror=null; this.src='<?= htmlspecialchars($defaultImage) ?>';">

                        <?php if (!empty($gambarLain) && count($gambarLain) > 1): ?>
                            <div class="absolute bottom-3 right-3 bg-black/60 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur">
                                <i class="fa-solid fa-images"></i>
                                <?= count($gambarLain) ?> foto
                            </div>
                        <?php endif; ?>
                    </div>
                </div>


                <!-- Deskripsi -->
                <?php if (!empty($paket['deskripsi'])): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6">
                        <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 pb-3 border-b">
                            <i class="fa-solid fa-circle-info text-blue-600"></i>
                            Tentang Paket
                        </h2>
                        <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                            <?= nl2br(htmlspecialchars($paket['deskripsi'])) ?>
                        </div>
                    </div>
                <?php endif; ?>


                <!-- Galeri -->
                <?php if (!empty($gambarLain)): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6">
                        <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 pb-3 border-b">
                            <i class="fa-solid fa-images text-blue-600"></i>
                            Galeri Paket
                        </h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <?php foreach ($gambarLain as $gambar):
                                $srcGambar = getImagePath($gambar, 'https://via.placeholder.com/400x300?text=Gambar+Tidak+Ada');
                            ?>
                                <div class="group relative overflow-hidden rounded-xl cursor-pointer aspect-video"
                                     onclick="openImageModal('<?= htmlspecialchars($srcGambar) ?>')">
                                    <img src="<?= htmlspecialchars($srcGambar) ?>"
                                         alt="Galeri"
                                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=No+Image';">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                        <div class="w-10 h-10 bg-white/90 rounded-full flex items-center justify-center transform scale-75 group-hover:scale-100 transition">
                                            <i class="fa-solid fa-magnifying-glass-plus text-slate-700"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>


                <!-- Itinerary -->
                <?php if (!empty($paket['itinerary'])): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6">
                        <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 pb-3 border-b">
                            <i class="fa-solid fa-route text-blue-600"></i>
                            Itinerary
                        </h2>
                        <div class="text-sm text-gray-600 leading-relaxed whitespace-pre-line bg-blue-50/50 rounded-xl p-4 border border-blue-100">
                            <?= nl2br(htmlspecialchars($paket['itinerary'])) ?>
                        </div>
                    </div>
                <?php endif; ?>


                <!-- Fasilitas -->
                <?php if (!empty($fasilitas)): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6">
                        <h2 class="text-base md:text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 pb-3 border-b">
                            <i class="fa-solid fa-list-check text-blue-600"></i>
                            Fasilitas
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <?php foreach ($fasilitas as $item): ?>
                                <div class="flex items-center gap-3 bg-slate-50 hover:bg-blue-50 rounded-xl p-3 border border-slate-100 hover:border-blue-200 transition">
                                    <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-check text-blue-600 text-xs"></i>
                                    </div>
                                    <span class="text-sm text-gray-700 font-medium">
                                        <?= htmlspecialchars($item) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>


                <!-- Termasuk & Tidak Termasuk -->
                <?php if (!empty($termasuk) || !empty($tidakTermasuk)): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        <?php if (!empty($termasuk)): ?>
                            <div class="bg-white rounded-2xl shadow-sm border border-emerald-100 p-5">
                                <h3 class="text-base font-bold text-emerald-700 mb-3 flex items-center gap-2 pb-3 border-b border-emerald-100">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                    Termasuk
                                </h3>
                                <ul class="space-y-2">
                                    <?php foreach ($termasuk as $item): ?>
                                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                                            <i class="fa-solid fa-check text-emerald-500 mt-1 text-xs"></i>
                                            <span><?= htmlspecialchars($item) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($tidakTermasuk)): ?>
                            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5">
                                <h3 class="text-base font-bold text-red-700 mb-3 flex items-center gap-2 pb-3 border-b border-red-100">
                                    <i class="fa-solid fa-circle-xmark text-red-500"></i>
                                    Tidak Termasuk
                                </h3>
                                <ul class="space-y-2">
                                    <?php foreach ($tidakTermasuk as $item): ?>
                                        <li class="flex items-start gap-2.5 text-sm text-gray-700">
                                            <i class="fa-solid fa-xmark text-red-500 mt-1 text-xs"></i>
                                            <span><?= htmlspecialchars($item) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>

            </div>


            <!-- SIDEBAR HARGA -->
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-24 space-y-4">

                    <!-- Card Harga -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-5 text-white">
                            <div class="text-xs uppercase tracking-widest opacity-80 mb-1">Harga mulai dari</div>

                            <?php if ($hargaDiskon): ?>
                                <div class="text-sm line-through opacity-70 mb-0.5">
                                    Rp <?= $hargaNormal ?>
                                </div>
                                <div class="text-3xl font-extrabold">
                                    Rp <?= $hargaDiskon ?>
                                </div>
                            <?php else: ?>
                                <div class="text-3xl font-extrabold">
                                    Rp <?= $hargaNormal ?>
                                </div>
                            <?php endif; ?>

                            <div class="text-xs opacity-80 mt-1">/ orang</div>

                            <?php if ($diskonPersen > 0): ?>
                                <div class="mt-3 inline-flex items-center gap-1.5 bg-yellow-400 text-yellow-900 px-3 py-1.5 rounded-full text-xs font-bold">
                                    <i class="fa-solid fa-tags"></i>
                                    Hemat <?= $diskonPersen ?>%
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="p-5 space-y-3">

                            <!-- Info -->
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-500">
                                        <i class="fa-solid fa-location-dot text-blue-500 w-4"></i>
                                        Destinasi
                                    </span>
                                    <span class="font-semibold text-gray-800 text-right">
                                        <?= htmlspecialchars($paket['destinasi'] ?? '-') ?>
                                    </span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-100">
                                    <span class="text-gray-500">
                                        <i class="fa-regular fa-clock text-blue-500 w-4"></i>
                                        Durasi
                                    </span>
                                    <span class="font-semibold text-gray-800">
                                        <?= htmlspecialchars($paket['durasi'] ?? '-') ?>
                                    </span>
                                </div>
                                <?php if (!empty($paket['minimal_peserta'])): ?>
                                    <div class="flex justify-between py-2">
                                        <span class="text-gray-500">
                                            <i class="fa-solid fa-users text-blue-500 w-4"></i>
                                            Min. Peserta
                                        </span>
                                        <span class="font-semibold text-gray-800">
                                            <?= (int) $paket['minimal_peserta'] ?> orang
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Kuota -->
                            <?php if ((int) ($paket['kuota'] ?? 0) > 0 && !empty($paket['tersisa'])): ?>
                                <?php
                                $persentaseKuota = min(100, max(0, ((int) $paket['tersisa'] / (int) $paket['kuota']) * 100));
                                $progressColor   = $persentaseKuota > 50 ? 'bg-emerald-500' : ($persentaseKuota > 25 ? 'bg-amber-500' : 'bg-red-500');
                                ?>
                                <div class="pt-2">
                                    <div class="flex justify-between text-xs font-medium text-gray-600 mb-1.5">
                                        <span>Kuota Tersisa</span>
                                        <span><?= (int) $paket['tersisa'] ?> / <?= (int) $paket['kuota'] ?></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                        <div class="<?= $progressColor ?> h-2 rounded-full transition-all duration-1000"
                                             style="width: <?= $persentaseKuota ?>%"></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Tombol Reservasi -->
                            <a href="reservasi.php?paket=<?= urlencode($paket['slug']) ?>"
                               class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-3 rounded-xl font-bold flex items-center justify-center gap-2 transition shadow-lg shadow-blue-600/25">
                                <i class="fa-solid fa-calendar-check"></i>
                                Reservasi Sekarang
                            </a>

                            <!-- WhatsApp -->
                            <a href="<?= $urlWa ?>" target="_blank"
                               class="w-full border-2 border-green-500 text-green-600 hover:bg-green-50 py-3 rounded-xl font-bold flex items-center justify-center gap-2 transition">
                                <i class="fa-brands fa-whatsapp text-lg"></i>
                                Tanya via WhatsApp
                            </a>

                        </div>
                    </div>

                    <!-- Trust Badge -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-4">
                        <div class="flex items-center justify-around text-[10px] text-gray-500">
                            <span class="flex items-center gap-1 flex-col text-center">
                                <i class="fa-solid fa-lock text-blue-500 text-base mb-1"></i>
                                Transaksi<br>Aman
                            </span>
                            <span class="w-px h-8 bg-gray-200"></span>
                            <span class="flex items-center gap-1 flex-col text-center">
                                <i class="fa-solid fa-bolt text-yellow-500 text-base mb-1"></i>
                                Respon<br>Cepat
                            </span>
                            <span class="w-px h-8 bg-gray-200"></span>
                            <span class="flex items-center gap-1 flex-col text-center">
                                <i class="fa-solid fa-star text-orange-500 text-base mb-1"></i>
                                Terpercaya
                            </span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

</div>


<!-- ============================================================
     MODAL LIGHTBOX
============================================================ -->
<div id="imageModal"
     class="fixed inset-0 z-[9999] hidden items-center justify-center p-4"
     style="background: rgba(0,0,0,0.9); backdrop-filter: blur(8px);"
     onclick="closeImageModal()">

    <button type="button" onclick="closeImageModal(event)"
            class="absolute top-5 right-5 w-11 h-11 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition z-10 border border-white/20">
        <i class="fa-solid fa-xmark text-xl"></i>
    </button>

    <div class="relative max-w-5xl max-h-[90vh] flex items-center justify-center"
         onclick="event.stopPropagation()">
        <img id="modalImage" src="" alt="Gambar Paket"
             class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl">
    </div>
</div>


<script>
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');

    modalImage.src = imageSrc;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImageModal(event) {
    if (event) event.stopPropagation();

    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('modalImage').src = '';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closeImageModal();
});
</script>


<?php
/* =========================================================
   INCLUDE FOOTER
========================================================= */
include __DIR__ . '/layout/footer.php';
?>