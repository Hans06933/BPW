<?php
/**
 * ============================================================
 * HALAMAN PROMO (PUBLIK)
 * Bayu Prima Wisata
 * ============================================================
 */

require_once __DIR__ . '/config/database.php';

// ============================================================
// FILTER KATEGORI
// ============================================================
$filterKategori = trim($_GET['kategori'] ?? 'all');
$allowedKategori = ['flash_sale', 'early_bird', 'group', 'member', 'umum'];

// ============================================================
// QUERY: AMBIL PROMO AKTIF & BELUM EXPIRED
// ============================================================
$where  = ["status = 'aktif'", "berlaku_sampai >= CURDATE()"];
$params = [];

if (in_array($filterKategori, $allowedKategori, true)) {
    $where[] = "kategori = :kategori";
    $params[':kategori'] = $filterKategori;
}

$whereSql = 'WHERE ' . implode(' AND ', $where);

$promoList = [];
try {
    $promoList = db_get_all("
        SELECT * FROM promo
        $whereSql
        ORDER BY
            FIELD(kategori, 'flash_sale', 'early_bird', 'member', 'group', 'umum'),
            berlaku_sampai ASC,
            id DESC
    ", $params);
} catch (PDOException $e) {
    $promoList = [];
}

// ============================================================
// HELPER PATH GAMBAR
// ============================================================
function getPromoImage($gambar) {
    if (empty($gambar)) return null;

    $gambar = trim($gambar);
    if (filter_var($gambar, FILTER_VALIDATE_URL)) return $gambar;

    if (file_exists(__DIR__ . '/' . $gambar)) return $gambar;

    $filename = basename($gambar);
    if (file_exists(__DIR__ . '/images/promo/' . $filename)) {
        return 'images/promo/' . $filename;
    }

    return null;
}

// ============================================================
// KATEGORI UNTUK TAB
// ============================================================
$kategoriTabs = [
    'all'        => ['label' => 'Semua Promo',    'icon' => 'fa-border-all'],
    'flash_sale' => ['label' => 'Flash Sale',     'icon' => 'fa-bolt'],
    'early_bird' => ['label' => 'Early Bird',     'icon' => 'fa-calendar-week'],
    'group'      => ['label' => 'Group Discount', 'icon' => 'fa-users'],
    'member'     => ['label' => 'Member Exclusive','icon' => 'fa-gem'],
    'umum'       => ['label' => 'Umum',           'icon' => 'fa-tag'],
];

// ============================================================
// CARI PROMO YANG BERAKHIR PALING CEPAT (untuk countdown)
// ============================================================
$promoTerdekat = null;
foreach ($promoList as $p) {
    if ($promoTerdekat === null || strtotime($p['berlaku_sampai']) < strtotime($promoTerdekat['berlaku_sampai'])) {
        $promoTerdekat = $p;
    }
}

include __DIR__ . '/layout/header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Promo & Diskon - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }

        .bg-promo-hero {
            background: linear-gradient(135deg, rgba(0,51,102,0.92) 0%, rgba(0,76,153,0.75) 100%),
                        url('https://images.unsplash.com/photo-1506929562872-bb421503ef21?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }

        .countdown-box {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(4px);
            border-radius: 0.75rem;
            padding: 0.5rem;
            min-width: 60px;
            text-align: center;
        }

        @media (min-width: 640px) {
            .countdown-box { min-width: 80px; padding: 0.75rem; }
        }

        .promo-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .promo-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -12px rgba(0,0,0,0.15);
        }

        .discount-ribbon {
            position: absolute;
            top: 16px;
            right: -34px;
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
            font-weight: bold;
            font-size: 12px;
            padding: 6px 40px;
            transform: rotate(45deg);
            text-align: center;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .promo-tab { transition: all 0.2s ease; }
        .promo-tab.active {
            background-color: #003366;
            color: white;
            border-color: #003366;
        }

        @keyframes pulse-red {
            0%, 100% { background-color: #e53e3e; }
            50% { background-color: #c53030; }
        }
        .flash-badge { animation: pulse-red 1.5s infinite; }

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
        .scroll-top:hover { background: #004c99; transform: translateY(-3px); }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-slate-50">

<!-- HERO -->
<section class="bg-promo-hero py-16 md:py-24 text-white relative">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 bg-red-500/80 backdrop-blur rounded-full px-4 py-1.5 mb-5">
                <i class="fa-solid fa-tag text-yellow-300 text-sm"></i>
                <span class="text-xs font-bold tracking-wide">PROMO TERBATAS</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-4">
                Penawaran Spesial<br>
                <span class="text-yellow-300">Untuk Perjalanan Anda</span>
            </h1>
            <p class="text-slate-200 text-base max-w-xl leading-relaxed mb-6">
                Dapatkan diskon menarik dan bonus eksklusif untuk berbagai paket wisata pilihan. Periode terbatas, jangan sampai kelewatan!
            </p>

            <!-- COUNTDOWN -->
            <?php if ($promoTerdekat): ?>
                <div class="bg-black/30 backdrop-blur-md rounded-2xl p-4 md:p-5 inline-block w-full md:w-auto"
                     data-end="<?= date('Y-m-d\TH:i:s', strtotime($promoTerdekat['berlaku_sampai'] . ' 23:59:59')) ?>"
                     id="countdownWrapper">
                    <p class="text-xs text-slate-200 mb-2 flex items-center gap-2">
                        <i class="fa-regular fa-clock"></i>
                        <?= htmlspecialchars($promoTerdekat['judul']) ?> berakhir dalam:
                    </p>
                    <div class="flex gap-2 md:gap-4">
                        <div class="countdown-box">
                            <span class="days text-xl md:text-2xl font-bold block">00</span>
                            <span class="text-[10px] md:text-xs">Hari</span>
                        </div>
                        <div class="countdown-box">
                            <span class="hours text-xl md:text-2xl font-bold block">00</span>
                            <span class="text-[10px] md:text-xs">Jam</span>
                        </div>
                        <div class="countdown-box">
                            <span class="minutes text-xl md:text-2xl font-bold block">00</span>
                            <span class="text-[10px] md:text-xs">Menit</span>
                        </div>
                        <div class="countdown-box">
                            <span class="seconds text-xl md:text-2xl font-bold block">00</span>
                            <span class="text-[10px] md:text-xs">Detik</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>


<!-- TABS KATEGORI -->
<section class="py-8 bg-white border-b shadow-sm sticky top-16 z-40">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="flex flex-wrap justify-center gap-2 md:gap-3">

            <?php foreach ($kategoriTabs as $key => $tab):
                $isActive = ($filterKategori === $key);
                $url = $key === 'all' ? 'promo.php' : 'promo.php?kategori=' . $key;
            ?>
                <a href="<?= $url ?>"
                   class="promo-tab <?= $isActive ? 'active' : '' ?> px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                    <i class="fa-solid <?= $tab['icon'] ?> <?= $key === 'flash_sale' ? 'text-yellow-500' : '' ?> mr-1"></i>
                    <?= $tab['label'] ?>
                </a>
            <?php endforeach; ?>

        </div>
    </div>
</section>


<!-- PROMO GRID -->
<section class="py-12 md:py-16 bg-slate-50">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">

        <!-- Flash Sale Banner -->
        <?php if ($filterKategori === 'all' || $filterKategori === 'flash_sale'): ?>
            <?php
            $hasFlashSale = false;
            foreach ($promoList as $p) {
                if ($p['kategori'] === 'flash_sale') { $hasFlashSale = true; break; }
            }
            ?>
            <?php if ($hasFlashSale): ?>
                <div class="bg-gradient-to-r from-red-600 to-red-500 rounded-2xl p-4 md:p-6 mb-8 text-white flex flex-col md:flex-row justify-between items-center gap-4 flash-badge">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 rounded-full w-12 h-12 flex items-center justify-center">
                            <i class="fa-solid fa-bolt text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg">FLASH SALE!</h3>
                            <p class="text-sm text-red-100">Diskon hingga 50% untuk pemesanan terbatas setiap harinya</p>
                        </div>
                    </div>
                    <a href="paket_wisata.php" class="bg-white text-red-600 px-6 py-2 rounded-full font-semibold text-sm hover:bg-gray-100 transition">
                        Pesan Sekarang
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Grid -->
        <?php if (!empty($promoList)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <?php foreach ($promoList as $promo):
                    $img = getPromoImage($promo['gambar']);

                    // Hitung sisa hari
                    $sisaHari = max(0, (int) ceil((strtotime($promo['berlaku_sampai']) - time()) / 86400));

                    // Cek kuota
                    $kuota     = (int) ($promo['kuota'] ?? 0);
                    $sisaKuota = (int) ($promo['sisa_kuota'] ?? 0);

                    // Label kategori
                    $katLabel = [
                        'flash_sale' => ['FLASH SALE', 'fa-bolt'],
                        'early_bird' => ['EARLY BIRD', 'fa-calendar'],
                        'group'      => ['GROUP', 'fa-users'],
                        'member'     => ['MEMBER', 'fa-gem'],
                        'umum'       => ['PROMO', 'fa-tag'],
                    ][$promo['kategori']] ?? ['PROMO', 'fa-tag'];
                ?>
                    <div class="promo-card bg-white rounded-2xl overflow-hidden shadow-md border border-gray-100 relative group">

                        <?php if ($promo['kategori'] === 'flash_sale'): ?>
                            <div class="discount-ribbon">FLASH SALE</div>
                        <?php endif; ?>

                        <!-- Gambar -->
                        <div class="relative h-48 overflow-hidden bg-gray-200">
                            <?php if ($img): ?>
                                <img src="<?= htmlspecialchars($img) ?>"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                     alt="<?= htmlspecialchars($promo['judul']) ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white">
                                    <i class="fa-solid fa-tag text-5xl opacity-50"></i>
                                </div>
                            <?php endif; ?>

                            <!-- Diskon Badge -->
                            <div class="absolute bottom-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                -<?= (int) $promo['diskon_persen'] ?>%
                            </div>

                            <!-- Sisa Kuota -->
                            <?php if ($kuota > 0 && $sisaKuota <= 5 && $sisaKuota > 0): ?>
                                <div class="absolute top-3 right-3 bg-yellow-500 text-white px-2 py-1 rounded-full text-[10px] font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-fire"></i> Sisa <?= $sisaKuota ?>
                                </div>
                            <?php endif; ?>

                            <!-- Kategori -->
                            <div class="absolute top-3 left-3 bg-black/60 backdrop-blur text-white px-2.5 py-1 rounded-lg text-[9px] font-bold flex items-center gap-1 uppercase">
                                <i class="fa-solid <?= $katLabel[1] ?> text-yellow-300"></i>
                                <?= $katLabel[0] ?>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <h3 class="font-bold text-base text-gray-800 group-hover:text-blue-600 transition line-clamp-2 mb-2">
                                <?= htmlspecialchars($promo['judul']) ?>
                            </h3>

                            <?php if (!empty($promo['deskripsi'])): ?>
                                <p class="text-xs text-gray-500 line-clamp-2 mb-3">
                                    <?= htmlspecialchars($promo['deskripsi']) ?>
                                </p>
                            <?php endif; ?>

                            <!-- Kode Promo -->
                            <?php if (!empty($promo['kode_promo'])): ?>
                                <div class="mb-3 flex items-center gap-2">
                                    <span class="text-[10px] text-gray-400">Kode:</span>
                                    <button onclick="copyKode('<?= htmlspecialchars($promo['kode_promo']) ?>', this)"
                                            class="bg-blue-50 hover:bg-blue-100 text-blue-700 font-mono font-bold text-xs px-2.5 py-1 rounded border border-dashed border-blue-300 transition inline-flex items-center gap-1">
                                        <?= htmlspecialchars($promo['kode_promo']) ?>
                                        <i class="fa-regular fa-copy text-[10px]"></i>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <!-- Info Detail -->
                            <div class="space-y-1.5 text-[10px] text-gray-500 mb-3">
                                <?php if ((float) $promo['minimal_pembelian'] > 0): ?>
                                    <div>
                                        <i class="fa-solid fa-cart-shopping text-purple-500"></i>
                                        Min. transaksi Rp <?= number_format((float) $promo['minimal_pembelian'], 0, ',', '.') ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($promo['maksimal_diskon'])): ?>
                                    <div>
                                        <i class="fa-solid fa-hand-holding-dollar text-emerald-500"></i>
                                        Maks. diskon Rp <?= number_format((float) $promo['maksimal_diskon'], 0, ',', '.') ?>
                                    </div>
                                <?php endif; ?>
                                <div class="<?= $sisaHari <= 3 ? 'text-red-500 font-bold' : '' ?>">
                                    <i class="fa-regular fa-clock"></i>
                                    <?php if ($sisaHari <= 0): ?>
                                        Berakhir hari ini!
                                    <?php else: ?>
                                        Berakhir dalam <?= $sisaHari ?> hari
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="border-t pt-3 flex justify-between items-center">
                                <div>
                                    <span class="text-[10px] text-gray-400 block">Diskon</span>
                                    <span class="text-xl font-extrabold text-red-600"><?= (int) $promo['diskon_persen'] ?>%</span>
                                </div>
                                <a href="paket_wisata.php"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1">
                                    Pesan <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

        <?php else: ?>
            <div class="text-center py-12">
                <i class="fa-regular fa-face-frown text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-400">Belum ada promo untuk kategori ini. Cek kategori lainnya ya!</p>
                <a href="promo.php" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition">
                    <i class="fa-solid fa-rotate-left"></i> Lihat Semua Promo
                </a>
            </div>
        <?php endif; ?>


        <!-- Newsletter -->
        <div class="mt-16 bg-gradient-to-r from-blue-700 to-cyan-600 rounded-2xl p-6 md:p-8 text-white">
            <div class="text-center max-w-2xl mx-auto">
                <i class="fa-regular fa-bell text-3xl mb-3"></i>
                <h3 class="text-xl md:text-2xl font-bold mb-2">Dapatkan Info Promo Terbaru</h3>
                <p class="text-blue-100 mb-5 text-sm">
                    Bergabunglah dengan newsletter kami dan jadilah yang pertama tahu tentang promo spesial!
                </p>
                <form action="proses-newsletter.php" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="hidden" name="redirect" value="promo.php">
                    <input type="email" name="email" required
                           placeholder="Masukkan email Anda"
                           class="flex-1 px-4 py-3 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-yellow-400 text-sm">
                    <button type="submit"
                            class="bg-yellow-400 hover:bg-yellow-300 text-gray-800 font-semibold px-6 py-3 rounded-lg transition text-sm">
                        Berlangganan
                    </button>
                </form>
                <p class="text-blue-200 text-xs mt-3">*Kamu akan menerima promo menarik setiap minggu</p>
            </div>
        </div>

    </div>
</section>


<!-- SCROLL TO TOP -->
<div class="scroll-top" id="scrollTopBtn">
    <i class="fa-solid fa-arrow-up"></i>
</div>


<script>
    // ============================================================
    // COUNTDOWN TIMER
    // ============================================================
    const countdownWrapper = document.getElementById('countdownWrapper');

    if (countdownWrapper) {
        const endTime = new Date(countdownWrapper.dataset.end).getTime();

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                countdownWrapper.querySelector('.days').textContent = '00';
                countdownWrapper.querySelector('.hours').textContent = '00';
                countdownWrapper.querySelector('.minutes').textContent = '00';
                countdownWrapper.querySelector('.seconds').textContent = '00';
                return;
            }

            const days    = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours   = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownWrapper.querySelector('.days').textContent    = String(days).padStart(2, '0');
            countdownWrapper.querySelector('.hours').textContent   = String(hours).padStart(2, '0');
            countdownWrapper.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
            countdownWrapper.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    // ============================================================
    // COPY KODE PROMO
    // ============================================================
    function copyKode(kode, btn) {
        navigator.clipboard.writeText(kode).then(() => {
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin!';
            btn.classList.add('bg-green-100', 'text-green-700', 'border-green-300');

            setTimeout(() => {
                btn.innerHTML = original;
                btn.classList.remove('bg-green-100', 'text-green-700', 'border-green-300');
            }, 1500);
        });
    }

    // ============================================================
    // SCROLL TO TOP
    // ============================================================
    const scrollBtn = document.getElementById('scrollTopBtn');
    window.addEventListener('scroll', () => {
        scrollBtn.style.display = window.scrollY > 400 ? 'flex' : 'none';
    });
    scrollBtn?.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>

<?php include 'layout/footer.php'; ?>
</body>
</html>