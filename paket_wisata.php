<?php
require_once "config/database.php";

/* =========================================================
   FILTER PENCARIAN
========================================================= */
$destinasi = isset($_GET['destinasi']) ? trim($_GET['destinasi']) : '';
$tanggal   = isset($_GET['tanggal'])   ? trim($_GET['tanggal'])   : '';
$peserta   = isset($_GET['peserta'])   ? (int)$_GET['peserta']    : 0;
$durasi    = isset($_GET['durasi'])    ? trim($_GET['durasi'])    : '';

/* =========================================================
   QUERY DATABASE
========================================================= */
$where  = [];
$params = [];

// Hanya paket aktif
$where[] = "status = 'aktif'";

// Filter destinasi
if ($destinasi !== '') {
    $where[] = "destinasi LIKE :destinasi";
    $params['destinasi'] = '%' . $destinasi . '%';
}

// Filter peserta
if ($peserta > 0) {
    $where[] = "tersisa >= :peserta";
    $params['peserta'] = $peserta;
}

// Filter durasi
if ($durasi === '1-2') {
    $where[] = "(durasi LIKE '%1%' OR durasi LIKE '%2%')";
} elseif ($durasi === '3-4') {
    $where[] = "(durasi LIKE '%3%' OR durasi LIKE '%4%')";
} elseif ($durasi === '5+') {
    $where[] = "(durasi LIKE '%5%' OR durasi LIKE '%6%' OR durasi LIKE '%7%' OR durasi LIKE '%8%' OR durasi LIKE '%9%' OR durasi LIKE '%10%')";
}

// Query final
$sql = "SELECT * FROM paket_wisata WHERE " . implode(" AND ", $where) . " ORDER BY is_featured DESC, is_flash_sale DESC, id DESC";
$rows = db_get_all($sql, $params);

/* =========================================================
   OLAH DATA PAKET
========================================================= */
$packages = [];

if (!empty($rows)) {
    foreach ($rows as $row) {
        // Fasilitas
        $facilities = [];
        if (!empty($row['fasilitas'])) {
            $facilities = preg_split('/[,;\r\n]+/', $row['fasilitas']);
            $facilities = array_filter(array_map('trim', $facilities));
        }

        // Harga (prioritas diskon)
        $harga = (!empty($row['harga_diskon']) && $row['harga_diskon'] > 0) 
            ? $row['harga_diskon'] 
            : $row['harga_normal'];

        // Badge
        if (!empty($row['is_flash_sale'])) {
            $tag = 'FLASH SALE';
            $tagColor = 'bg-red-600';
        } elseif (!empty($row['is_featured'])) {
            $tag = 'UNGGULAN';
            $tagColor = 'bg-blue-600';
        } else {
            $tag = 'POPULER';
            $tagColor = 'bg-blue-600';
        }

        // Gambar
        $gambar = !empty($row['gambar_utama']) 
            ? 'images/paket/' . $row['gambar_utama']
            : 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80';

        // Data card
        $packages[] = [
            'id'              => $row['id'],
            'title'           => $row['nama_paket'] ?? 'Tanpa Nama',
            'slug'            => $row['slug'] ?? '',
            'loc'             => $row['destinasi'] ?? '-',
            'tag'             => $tag,
            'tag_color'       => $tagColor,
            'price'           => $harga,
            'harga_normal'    => $row['harga_normal'] ?? 0,
            'harga_diskon'    => $row['harga_diskon'] ?? 0,
            'diskon_persen'   => $row['diskon_persen'] ?? 0,
            'img'             => $gambar,
            'dur'             => $row['durasi'] ?? '-',
            'facilities'      => $facilities,
            'kuota'           => $row['kuota'] ?? 0,
            'tersisa'         => $row['tersisa'] ?? 0,
            'minimal_peserta' => $row['minimal_peserta'] ?? 2,
            'status'          => $row['status'] ?? 'aktif',
            'is_featured'     => $row['is_featured'] ?? 0,
            'is_flash_sale'   => $row['is_flash_sale'] ?? 0
        ];
    }
}

$totalPackages = count($packages);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Paket Wisata - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }
        .bg-paket-hero {
            background: linear-gradient(to bottom, rgba(0, 51, 102, 0.85), rgba(0, 76, 153, 0.5)),
                        url('https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
        html { scroll-behavior: smooth; }
        @media (max-width: 640px) {
            button, a, select, input {
                cursor: pointer;
                touch-action: manipulation;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-gray-800 flex flex-col min-h-screen">

<?php include "layout/header.php"; ?>

<!-- HERO -->
<section class="bg-paket-hero pt-12 md:pt-20 pb-16 md:pb-24 text-white relative">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl space-y-4 md:space-y-6">
        <span class="bg-blue-600 text-white px-3 py-1 rounded text-[10px] font-bold uppercase tracking-wider inline-block">
            Paket Wisata
        </span>
        <h1 class="text-2xl md:text-4xl font-extrabold leading-tight">
            Paket Wisata Terbaik
            <br>
            <span class="text-cyan-300">untuk Perjalanan Tak Terlupakan</span>
        </h1>
        <p class="text-slate-200 text-xs max-w-xl leading-relaxed">
            Temukan berbagai pilihan paket wisata dalam negeri yang dirancang
            untuk pengalaman perjalanan terbaik Anda bersama Bayu Prima Wisata.
        </p>

        <!-- SEARCH -->
        <form action="" method="GET" class="bg-custom-blue/90 border border-slate-700/60 rounded-xl p-3 md:p-4 shadow-xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2 md:gap-3 text-gray-800 text-[11px] font-medium">
            <!-- Destinasi -->
            <div>
                <label class="block text-white mb-1 text-[10px] md:text-xs">Tujuan / Destinasi</label>
                <select name="destinasi" class="w-full bg-white px-2 md:px-3 py-1.5 md:py-2 rounded focus:outline-none text-gray-600 h-8 md:h-9 text-xs">
                    <option value="">Semua Destinasi</option>
                    <option value="Bali" <?= $destinasi === 'Bali' ? 'selected' : ''; ?>>Bali</option>
                    <option value="Yogyakarta" <?= $destinasi === 'Yogyakarta' ? 'selected' : ''; ?>>Yogyakarta</option>
                    <option value="Labuan Bajo" <?= $destinasi === 'Labuan Bajo' ? 'selected' : ''; ?>>Labuan Bajo</option>
                    <option value="Bandung" <?= $destinasi === 'Bandung' ? 'selected' : ''; ?>>Bandung</option>
                    <option value="Bromo" <?= $destinasi === 'Bromo' ? 'selected' : ''; ?>>Bromo</option>
                </select>
            </div>

            <!-- Tanggal -->
            <div>
                <label class="block text-white mb-1 text-[10px] md:text-xs">Tanggal Berangkat</label>
                <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal); ?>" class="w-full bg-white px-2 md:px-3 py-1.5 md:py-2 rounded focus:outline-none text-gray-500 h-8 md:h-9 text-xs">
            </div>

            <!-- Durasi -->
            <div>
                <label class="block text-white mb-1 text-[10px] md:text-xs">Durasi</label>
                <select name="durasi" class="w-full bg-white px-2 md:px-3 py-1.5 md:py-2 rounded focus:outline-none text-gray-600 h-8 md:h-9 text-xs">
                    <option value="">Semua Durasi</option>
                    <option value="1-2" <?= $durasi === '1-2' ? 'selected' : ''; ?>>1-2 Hari</option>
                    <option value="3-4" <?= $durasi === '3-4' ? 'selected' : ''; ?>>3-4 Hari</option>
                    <option value="5+" <?= $durasi === '5+' ? 'selected' : ''; ?>>5+ Hari</option>
                </select>
            </div>

            <!-- Peserta -->
            <div>
                <label class="block text-white mb-1 text-[10px] md:text-xs">Jumlah Peserta</label>
                <input type="number" name="peserta" min="1" placeholder="Jumlah Orang" value="<?= $peserta > 0 ? $peserta : ''; ?>" class="w-full bg-white px-2 md:px-3 py-1.5 md:py-2 rounded focus:outline-none text-gray-600 h-8 md:h-9 text-xs">
            </div>

            <!-- Button -->
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold h-8 md:h-9 rounded transition flex items-center justify-center space-x-1.5 shadow text-xs">
                    <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                    <span>Cari Paket</span>
                </button>
            </div>
        </form>
    </div>
</section>

<!-- MAIN -->
<main class="container mx-auto px-4 md:px-6 max-w-6xl py-6 md:py-10 grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8 flex-1">
    
    <!-- SIDEBAR -->
    <aside class="lg:col-span-3 space-y-4">
        <!-- Kategori Destinasi -->
        <div class="bg-white border rounded-xl p-3 md:p-4 shadow-sm">
            <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider pb-2 border-b mb-2">Destinasi</h3>
            
            <?php
            $destinations = [];
            foreach ($rows as $r) {
                if (!empty($r['destinasi'])) {
                    $destinations[] = trim($r['destinasi']);
                }
            }
            $destinations = array_unique($destinations);
            sort($destinations);
            ?>
            
            <div class="space-y-1">
                <a href="paket_wisata.php" class="flex justify-between items-center p-2 rounded-md transition <?= empty($destinasi) ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-600 hover:bg-slate-50'; ?>">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-border-all"></i>
                        <span>Semua Destinasi</span>
                    </div>
                    <span class="bg-blue-600 text-white text-[9px] px-1.5 py-0.5 rounded"><?= $totalPackages; ?></span>
                </a>
                
                <?php foreach ($destinations as $destination): ?>
                    <a href="?destinasi=<?= urlencode($destination); ?>" class="flex items-center p-2 rounded-md text-gray-600 hover:bg-slate-50 transition">
                        <i class="fa-solid fa-location-dot text-gray-400 mr-2"></i>
                        <span class="text-xs"><?= htmlspecialchars($destination); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Informasi Paket -->
        <div class="bg-white border rounded-xl p-3 md:p-4 shadow-sm">
            <h3 class="font-bold text-xs text-slate-800 uppercase tracking-wider pb-2 border-b mb-3">Informasi Paket</h3>
            <div class="space-y-3 text-xs text-gray-600">
                <div class="flex justify-between">
                    <span>Total Paket</span>
                    <strong><?= $totalPackages; ?></strong>
                </div>
                <div class="flex justify-between">
                    <span>Paket Unggulan</span>
                    <strong>
                        <?php
                        $featuredCount = 0;
                        foreach ($packages as $p) {
                            if (!empty($p['is_featured'])) $featuredCount++;
                        }
                        echo $featuredCount;
                        ?>
                    </strong>
                </div>
                <div class="flex justify-between">
                    <span>Flash Sale</span>
                    <strong>
                        <?php
                        $flashCount = 0;
                        foreach ($packages as $p) {
                            if (!empty($p['is_flash_sale'])) $flashCount++;
                        }
                        echo $flashCount;
                        ?>
                    </strong>
                </div>
            </div>
            <a href="paket_wisata.php" class="mt-4 w-full border border-blue-200 text-blue-600 hover:bg-blue-50 font-bold py-2 rounded transition flex items-center justify-center gap-1 text-xs">
                <i class="fa-solid fa-rotate-left text-[10px]"></i>
                Reset Filter
            </a>
        </div>
    </aside>

    <!-- PAKET LIST -->
    <section class="lg:col-span-9 space-y-5 md:space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center text-xs font-medium text-gray-500 border-b pb-3 gap-2">
            <span>
                Menampilkan <strong class="text-slate-800"><?= $totalPackages; ?></strong> paket wisata
            </span>
        </div>

        <!-- Card Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">
            <?php if (!empty($packages)): ?>
                <?php foreach ($packages as $pkg): ?>
                    <div class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col group relative h-full">
                        <!-- Gambar -->
                        <div class="relative h-44 md:h-48 overflow-hidden bg-slate-100 shrink-0">
                            <img src="<?= htmlspecialchars($pkg['img']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="<?= htmlspecialchars($pkg['title']); ?>" onerror="this.src='https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80';">
                            <!-- Badge -->
                            <span class="absolute top-3 left-3 <?= htmlspecialchars($pkg['tag_color']); ?> text-white text-[8px] font-extrabold px-2 py-0.5 rounded tracking-wide uppercase shadow-sm">
                                <?= htmlspecialchars($pkg['tag']); ?>
                            </span>
                            <!-- Location -->
                            <span class="absolute bottom-3 left-3 text-[10px] text-white font-bold flex items-center space-x-1 bg-black/30 px-2 py-0.5 rounded-full backdrop-blur-sm">
                                <i class="fa-solid fa-location-dot text-cyan-400"></i>
                                <span><?= htmlspecialchars($pkg['loc']); ?></span>
                            </span>
                        </div>

                        <!-- Body -->
                        <div class="p-3 md:p-4 flex-1 flex flex-col justify-between space-y-3 md:space-y-4">
                            <div class="space-y-2">
                                <!-- Nama -->
                                <h3 class="font-bold text-xs md:text-sm text-slate-800 leading-snug group-hover:text-blue-600 transition">
                                    <?= htmlspecialchars($pkg['title']); ?>
                                </h3>
                                
                                <!-- Info -->
                                <div class="flex items-center space-x-3 text-[9px] text-gray-500 font-semibold">
                                    <span class="flex items-center space-x-1">
                                        <i class="fa-regular fa-clock text-blue-500"></i>
                                        <span><?= htmlspecialchars($pkg['dur']); ?></span>
                                    </span>
                                    <span class="flex items-center space-x-1">
                                        <i class="fa-solid fa-user-group text-blue-500"></i>
                                        <span>Min. <?= (int)$pkg['minimal_peserta']; ?> orang</span>
                                    </span>
                                </div>

                                <!-- Fasilitas -->
                                <?php if (!empty($pkg['facilities'])): ?>
                                    <div class="grid grid-cols-4 gap-1 pt-2 border-t text-[8px] text-gray-500 text-center font-medium">
                                        <?php
                                        $icons = ['fa-hotel', 'fa-utensils', 'fa-car', 'fa-ticket'];
                                        $facilities = array_slice($pkg['facilities'], 0, 4);
                                        foreach ($facilities as $idx => $fac):
                                            $icon = $icons[$idx] ?? 'fa-check';
                                        ?>
                                            <div class="p-1 bg-slate-50 border border-gray-100 rounded flex flex-col items-center justify-center" title="<?= htmlspecialchars($fac); ?>">
                                                <i class="fa-solid <?= $icon; ?> text-[10px] text-blue-500 mb-0.5"></i>
                                                <span class="truncate w-full"><?= htmlspecialchars($fac); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Footer -->
                            <div class="flex justify-between items-center pt-3 border-t">
                                <!-- Harga -->
                                <div>
                                    <span class="text-[8px] text-gray-400 block font-medium uppercase tracking-wider">Mulai dari</span>
                                    <?php if (!empty($pkg['harga_diskon']) && $pkg['harga_diskon'] > 0): ?>
                                        <span class="text-xs md:text-sm font-bold text-blue-600">Rp <?= number_format($pkg['harga_diskon'], 0, ',', '.'); ?></span>
                                        <span class="text-[9px] text-gray-400 line-through block">Rp <?= number_format($pkg['harga_normal'], 0, ',', '.'); ?></span>
                                    <?php else: ?>
                                        <span class="text-xs md:text-sm font-bold text-blue-600">Rp <?= number_format($pkg['harga_normal'], 0, ',', '.'); ?></span>
                                    <?php endif; ?>
                                    <span class="text-[9px] text-gray-400">/pax</span>
                                </div>

                                <!-- Detail -->
                                <a href="detail_paket_wisata.php?slug=<?= urlencode($pkg['slug']); ?>"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-[10px] md:text-xs px-2.5 md:px-3 py-1.5 rounded flex items-center space-x-1 shadow-sm transition shrink-0">

                                <span>Lihat Detail</span>
                                <i class="fa-solid fa-chevron-right text-[8px]"></i>

                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Tidak Ada Data -->
                <div class="col-span-full bg-white p-10 rounded-xl text-center border text-gray-500">
                    <i class="fa-solid fa-box-open text-4xl mb-3 text-gray-300"></i>
                    <p class="text-sm font-semibold">Tidak ada paket wisata.</p>
                    <?php if ($destinasi || $peserta || $durasi): ?>
                        <p class="text-xs mt-1 text-gray-400">Tidak ditemukan paket yang sesuai dengan filter.</p>
                        <a href="paket_wisata.php" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold">Reset Filter</a>
                    <?php else: ?>
                        <p class="text-xs mt-1 text-gray-400">Belum ada paket wisata aktif yang tersedia.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<!-- CTA -->
<section class="py-8 md:py-10 bg-white">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="bg-gradient-to-r from-blue-700 to-cyan-500 rounded-2xl p-5 md:p-6 lg:p-8 text-white flex flex-col md:flex-row justify-between items-center shadow-lg gap-4">
            <div class="flex items-center space-x-3 md:space-x-4 text-center md:text-left flex-col md:flex-row">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-white/10 rounded-full flex items-center justify-center text-lg md:text-xl shrink-0">
                    <i class="fa-solid fa-headset text-cyan-300"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-sm md:text-base">Butuh Paket Wisata Custom?</h3>
                    <p class="text-[10px] md:text-[11px] text-slate-100 opacity-90">Konsultasikan kebutuhan perjalanan Anda dengan tim kami dan dapatkan penawaran terbaik.</p>
                </div>
            </div>
            <a href="https://wa.me/6285281441565?text=Halo%20Admin%20Bayu%20Prima%20Wisata%2C%20saya%20ingin%20konsultasi%20mengenai%20paket%20wisata.%0A%0ANama%3A%20%0ARencana%20Tanggal%3A%20%0AJumlah%20Peserta%3A%20" 
            target="_blank" 
            class="bg-white hover:bg-slate-50 text-blue-700 font-bold text-xs px-4 md:px-5 py-2.5 md:py-3 rounded-md flex items-center space-x-2 shadow transition shrink-0">
                <span>Konsultasi Gratis</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>
</section>

<?php include "layout/footer.php"; ?>

<script>
    const menuBtn = document.getElementById('mobileMenuBtn');
    const mobileNav = document.getElementById('mobileNav');
    if (menuBtn && mobileNav) {
        menuBtn.addEventListener('click', () => {
            mobileNav.classList.toggle('hidden');
        });
    }
</script>

</body>
</html>