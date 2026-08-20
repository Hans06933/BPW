<?php
require_once 'config/database.php';

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
$gambarUtama = getImagePath($paket['gambar_utama'] ?? '', $defaultImage);

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
$hargaNormal = number_format((float) $paket['harga_normal'], 0, ',', '.');
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
    $diskonPersen = round((( (float) $paket['harga_normal'] - (float) $paket['harga_diskon']) / (float) $paket['harga_normal']) * 100);
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

$fasilitas      = formatList($paket['fasilitas'] ?? '');
$termasuk       = formatList($paket['termasuk'] ?? '');
$tidakTermasuk  = formatList($paket['tidak_termasuk'] ?? '');

/* =========================================================
   WHATSAPP URL
========================================================= */
$namaPaket = $paket['nama_paket'] ?? 'Paket Wisata';
$pesanWa = "Halo Admin, saya ingin bertanya mengenai paket: " . $namaPaket;
$urlWa = "https://wa.me/6285281441565?text=" . urlencode($pesanWa);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($paket['nama_paket']) ?> - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
        .gradient-hero {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .price-card {
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            border: 1px solid #e2e8f0;
        }
        
        .badge-gradient {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        }
        
        .discount-badge {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
        }
        
        .feature-icon {
            background: linear-gradient(135deg, #dbeafe, #eff6ff);
        }
        
        .image-overlay {
            background: linear-gradient(0deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);
        }
        
        .shimmer {
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.1) 50%, transparent 100%);
            background-size: 200% 100%;
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        
        .modal-backdrop {
            backdrop-filter: blur(12px);
            background: rgba(0, 0, 0, 0.85);
        }
        
        .floating-wa {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }
        
        .progress-bar {
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            border-radius: 2px;
            transition: width 0.5s ease;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-slate-50 to-white text-slate-800">

<!-- NAVBAR -->
<header class="bg-white/80 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="h-16 flex items-center justify-between">
            <a href="paket_wisata.php" class="flex items-center gap-2 font-extrabold text-xl bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                <i class="fa-solid fa-plane-departure text-blue-600"></i>
                Bayu Prima Wisata
            </a>
            <a href="paket_wisata.php" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition group">
                <i class="fa-solid fa-arrow-left mr-1 group-hover:-translate-x-1 transition"></i>
                Kembali ke Paket Wisata
            </a>
        </div>
    </div>
</header>

<!-- HERO GAMBAR -->
<section class="relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="relative overflow-hidden rounded-2xl shadow-2xl">
            <img src="<?= htmlspecialchars($gambarUtama) ?>" 
                 alt="<?= htmlspecialchars($paket['nama_paket']) ?>" 
                 class="w-full h-[280px] md:h-[480px] object-cover" 
                 onerror="this.onerror=null; this.src='<?= htmlspecialchars($defaultImage) ?>';">
            
            <!-- Overlay Gradient -->
            <div class="absolute inset-0 image-overlay rounded-2xl"></div>
            
            <!-- Badge Diskon -->
            <?php if ($diskonPersen > 0): ?>
                <div class="absolute top-5 left-5 discount-badge text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-yellow-300"></i>
                    DISKON <?= $diskonPersen ?>%
                </div>
            <?php endif; ?>
            
            <!-- Info di atas gambar -->
            <div class="absolute bottom-6 left-6 right-6 text-white">
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-blue-300"></i>
                        <?= htmlspecialchars($paket['destinasi']) ?>
                    </span>
                    <span class="px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-blue-300"></i>
                        <?= htmlspecialchars($paket['durasi']) ?>
                    </span>
                    <?php if (!empty($paket['minimal_peserta'])): ?>
                        <span class="px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-full text-xs font-semibold flex items-center gap-1.5">
                            <i class="fa-solid fa-users text-blue-300"></i>
                            Min. <?= (int) $paket['minimal_peserta'] ?> peserta
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KONTEN DETAIL -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- KONTEN UTAMA -->
        <div class="lg:col-span-2 space-y-6">

            <!-- INFORMASI PAKET -->
            <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition p-6 md:p-8 hover-lift">
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">
                    <?= htmlspecialchars($paket['nama_paket']) ?>
                </h1>

                <?php if (!empty($paket['deskripsi'])): ?>
                    <div class="mt-6">
                        <h2 class="text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">
                            <span class="w-1 h-6 bg-gradient-to-b from-blue-600 to-purple-600 rounded-full"></span>
                            Tentang Paket
                        </h2>
                        <div class="text-slate-600 leading-7 whitespace-pre-line bg-slate-50 rounded-xl p-5 border border-slate-100">
                            <?= nl2br(htmlspecialchars($paket['deskripsi'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- GALERI -->
            <?php if (!empty($gambarLain)): ?>
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition p-6 md:p-8 hover-lift">
                    <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="w-1 h-6 bg-gradient-to-b from-blue-600 to-purple-600 rounded-full"></span>
                        <i class="fa-solid fa-images text-blue-600 mr-1"></i>
                        Galeri Paket
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php foreach ($gambarLain as $gambar): 
                            $srcGambar = getImagePath($gambar, 'https://via.placeholder.com/400x300?text=Gambar+Tidak+Ada');
                        ?>
                            <div class="group relative overflow-hidden rounded-xl cursor-pointer" onclick="openImageModal('<?= htmlspecialchars($srcGambar) ?>')">
                                <img src="<?= htmlspecialchars($srcGambar) ?>" 
                                     alt="Galeri <?= htmlspecialchars($paket['nama_paket']) ?>" 
                                     class="w-full h-44 object-cover group-hover:scale-110 transition duration-500" 
                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/400x300?text=Gambar+Tidak+Ada';">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-black/0 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center transform scale-75 group-hover:scale-100 transition duration-300 shadow-xl">
                                        <i class="fa-solid fa-magnifying-glass-plus text-slate-700 text-lg"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ITINERARY -->
            <?php if (!empty($paket['itinerary'])): ?>
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition p-6 md:p-8 hover-lift">
                    <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="w-1 h-6 bg-gradient-to-b from-blue-600 to-purple-600 rounded-full"></span>
                        <i class="fa-solid fa-route text-blue-600 mr-1"></i>
                        Itinerary
                    </h2>
                    <div class="text-slate-600 leading-7 whitespace-pre-line bg-gradient-to-br from-blue-50/50 to-purple-50/50 rounded-xl p-5 border border-blue-100/50">
                        <?= nl2br(htmlspecialchars($paket['itinerary'])) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- FASILITAS -->
            <?php if (!empty($fasilitas)): ?>
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition p-6 md:p-8 hover-lift">
                    <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="w-1 h-6 bg-gradient-to-b from-blue-600 to-purple-600 rounded-full"></span>
                        <i class="fa-solid fa-list-check text-blue-600 mr-1"></i>
                        Fasilitas
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach ($fasilitas as $item): ?>
                            <div class="flex items-start gap-3 bg-slate-50 rounded-xl p-3 border border-slate-100 hover:border-blue-200 transition">
                                <div class="w-8 h-8 rounded-full feature-icon flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-circle-check text-blue-600 text-sm"></i>
                                </div>
                                <span class="text-slate-700 font-medium text-sm"><?= htmlspecialchars($item) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- TERMASUK / TIDAK TERMASUK -->
            <?php if (!empty($termasuk) || !empty($tidakTermasuk)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php if (!empty($termasuk)): ?>
                        <div class="bg-white rounded-2xl border border-emerald-200/60 shadow-sm hover:shadow-md transition p-6 hover-lift">
                            <h2 class="text-lg font-bold text-emerald-700 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                Termasuk
                            </h2>
                            <div class="space-y-2.5">
                                <?php foreach ($termasuk as $item): ?>
                                    <div class="flex gap-3 items-start bg-emerald-50/50 rounded-xl p-3 border border-emerald-100/50">
                                        <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 mt-0.5">
                                            <i class="fa-solid fa-check text-emerald-600 text-xs"></i>
                                        </div>
                                        <span class="text-slate-700 text-sm"><?= htmlspecialchars($item) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($tidakTermasuk)): ?>
                        <div class="bg-white rounded-2xl border border-red-200/60 shadow-sm hover:shadow-md transition p-6 hover-lift">
                            <h2 class="text-lg font-bold text-red-700 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-circle-xmark text-red-500"></i>
                                Tidak Termasuk
                            </h2>
                            <div class="space-y-2.5">
                                <?php foreach ($tidakTermasuk as $item): ?>
                                    <div class="flex gap-3 items-start bg-red-50/50 rounded-xl p-3 border border-red-100/50">
                                        <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center shrink-0 mt-0.5">
                                            <i class="fa-solid fa-xmark text-red-600 text-xs"></i>
                                        </div>
                                        <span class="text-slate-700 text-sm"><?= htmlspecialchars($item) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>

        <!-- SIDEBAR HARGA -->
        <div class="lg:col-span-1">
            <div class="price-card rounded-2xl shadow-xl p-6 lg:sticky lg:top-24 relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-purple-500/5 rounded-full blur-3xl"></div>
                
                <div class="relative z-10">
                    <p class="text-sm text-slate-500 mb-1 font-medium">Harga mulai dari</p>

                    <?php if ($hargaDiskon): ?>
                        <div class="text-sm text-slate-400 line-through mb-0.5">Rp <?= $hargaNormal ?></div>
                        <div class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                            Rp <?= $hargaDiskon ?>
                        </div>
                    <?php else: ?>
                        <div class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                            Rp <?= $hargaNormal ?>
                        </div>
                    <?php endif; ?>

                    <span class="text-xs text-slate-400 font-medium">/ orang</span>

                    <?php if ($diskonPersen > 0): ?>
                        <div class="mt-3 inline-flex items-center gap-1.5 bg-gradient-to-r from-red-50 to-red-100 text-red-600 px-3.5 py-1.5 rounded-full text-sm font-bold border border-red-200">
                            <i class="fa-solid fa-tags"></i>
                            Hemat <?= $diskonPersen ?>%
                        </div>
                    <?php endif; ?>

                    <div class="border-t border-slate-200/60 my-5"></div>

                    <!-- Kuota Progress -->
                    <?php if ((int) $paket['kuota'] > 0): ?>
                        <div class="mb-4">
                            <div class="flex justify-between text-xs font-medium text-slate-600 mb-1.5">
                                <span>Kuota Tersisa</span>
                                <span><?= (int) $paket['tersisa'] ?> / <?= (int) $paket['kuota'] ?></span>
                            </div>
                            <?php 
                            $persentaseKuota = ((int) $paket['tersisa'] / (int) $paket['kuota']) * 100;
                            $progressColor = $persentaseKuota > 50 ? 'bg-emerald-500' : ($persentaseKuota > 25 ? 'bg-amber-500' : 'bg-red-500');
                            ?>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="<?= $progressColor ?> h-2 rounded-full transition-all duration-1000" style="width: <?= $persentaseKuota ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between p-2.5 rounded-xl bg-slate-50">
                            <span class="text-slate-500 font-medium">Destinasi</span>
                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($paket['destinasi']) ?></span>
                        </div>
                        <div class="flex justify-between p-2.5 rounded-xl bg-slate-50">
                            <span class="text-slate-500 font-medium">Durasi</span>
                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($paket['durasi']) ?></span>
                        </div>
                        <div class="flex justify-between p-2.5 rounded-xl bg-slate-50">
                            <span class="text-slate-500 font-medium">Minimal Peserta</span>
                            <span class="font-semibold text-slate-800"><?= (int) $paket['minimal_peserta'] ?> orang</span>
                        </div>
                    </div>

                    <a href="reservasi.php?paket=<?= urlencode($paket['slug']) ?>" class="mt-6 w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-3.5 rounded-xl font-bold flex items-center justify-center gap-2 transition-all duration-300 shadow-lg shadow-blue-600/25 hover:shadow-blue-600/40">
                        <i class="fa-solid fa-calendar-check"></i>
                        Reservasi Sekarang
                    </a>

                    <a href="<?= $urlWa; ?>" target="_blank" class="mt-3 w-full border-2 border-green-500/30 text-green-600 hover:bg-green-50 py-3.5 rounded-xl font-bold flex items-center justify-center gap-2 transition-all duration-300 floating-wa">
                        <i class="fa-brands fa-whatsapp text-xl"></i>
                        <span>Tanya via WhatsApp</span>
                    </a>

                    <!-- Trust Badge -->
                    <div class="mt-4 flex items-center justify-center gap-4 text-[10px] text-slate-400">
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-lock text-blue-400"></i>
                            Transaksi Aman
                        </span>
                        <span class="w-px h-3 bg-slate-300"></span>
                        <span class="flex items-center gap-1">
                            <i class="fa-regular fa-clock text-blue-400"></i>
                            Respon Cepat
                        </span>
                        <span class="w-px h-3 bg-slate-300"></span>
                        <span class="flex items-center gap-1">
                            <i class="fa-regular fa-star text-yellow-400"></i>
                            Terpercaya
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<!-- FOOTER -->
<footer class="bg-gradient-to-br from-slate-900 to-slate-800 text-white py-10 mt-8">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <div class="flex items-center justify-center gap-2 font-extrabold text-xl mb-2">
            <i class="fa-solid fa-plane-departure text-blue-400"></i>
            <span>Bayu Prima Wisata</span>
        </div>
        <p class="text-slate-400 text-sm max-w-md mx-auto">Temukan pengalaman wisata terbaik bersama kami.</p>
        <div class="flex items-center justify-center gap-4 mt-4 text-xs text-slate-500">
            <span>© <?= date('Y') ?> Bayu Prima Wisata</span>
            <span class="w-px h-3 bg-slate-700"></span>
            <span>All Rights Reserved</span>
        </div>
    </div>
</footer>

<!-- MODAL LIGHTBOX -->
<div id="imageModal" 
     class="fixed inset-0 z-[9999] hidden modal-backdrop items-center justify-center p-4"
     onclick="closeImageModal()">
    
    <button type="button" onclick="closeImageModal(event)" class="absolute top-5 right-5 w-12 h-12 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition z-10 border border-white/10">
        <i class="fa-solid fa-xmark text-2xl"></i>
    </button>
    
    <div class="relative max-w-5xl max-h-[90vh] flex items-center justify-center" onclick="event.stopPropagation()">
        <img id="modalImage" src="" alt="Gambar Paket Wisata" class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl">
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
    if (event) {
        event.stopPropagation();
    }
    
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('modalImage').src = '';
    document.body.style.overflow = '';
}

// Tutup dengan tombol ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});
</script>

</body>
</html>