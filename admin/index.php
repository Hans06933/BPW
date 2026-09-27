<?php
// admin/index.php
ob_start();
session_start();
require_once __DIR__ . '/../config/database.php';

// ============================================================
// PROTEKSI ADMIN
// ============================================================
// if (empty($_SESSION['admin_logged_in'])) {
//     header('Location: login.php');
//     exit;
// }

// ============================================================
// STATISTIK UTAMA
// ============================================================
$statPaket     = 0;
$statDestinasi = 0;
$statTestimoni = 0;
$statPengunjung = 0;
$avgRating     = 0;

try {
    // Paket Wisata aktif
    $row = db_get("SELECT COUNT(*) AS total FROM paket_wisata WHERE status = 'aktif'");
    $statPaket = (int) ($row['total'] ?? 0);

    // Destinasi aktif
    $row = db_get("SELECT COUNT(*) AS total FROM destinasi WHERE status = 'aktif'");
    $statDestinasi = (int) ($row['total'] ?? 0);

    // Testimoni approved
    $row = db_get("SELECT COUNT(*) AS total FROM testimoni WHERE status = 'approved'");
    $statTestimoni = (int) ($row['total'] ?? 0);

    // Total views semua paket
    $row = db_get("SELECT SUM(views) AS total FROM paket_wisata");
    $statPengunjung = (int) ($row['total'] ?? 0);

    // Rata-rata rating testimoni approved
    $row = db_get("SELECT AVG(rating) AS avg_rating FROM testimoni WHERE status = 'approved'");
    $avgRating = round((float) ($row['avg_rating'] ?? 0), 1);

} catch (PDOException $e) {
    // Biarkan default 0
}

// ============================================================
// PAKET WISATA TERBARU (5 terakhir)
// ============================================================
$paketTerbaru = [];
try {
    $paketTerbaru = db_get_all("
        SELECT id, nama_paket, destinasi, harga_normal, harga_diskon, status
        FROM paket_wisata
        ORDER BY created_at DESC
        LIMIT 5
    ");
} catch (PDOException $e) {
    $paketTerbaru = [];
}

// ============================================================
// TESTIMONI TERBARU (3 terakhir yang approved)
// ============================================================
$testimoniTerbaru = [];
try {
    $testimoniTerbaru = db_get_all("
        SELECT id, nama, kota_asal, destinasi, testimoni, rating, foto
        FROM testimoni
        WHERE status = 'approved'
        ORDER BY created_at DESC
        LIMIT 3
    ");
} catch (PDOException $e) {
    $testimoniTerbaru = [];
}

// ============================================================
// PEMESANAN PENDING (untuk alert)
// ============================================================
$pemesananPending = 0;
try {
    $row = db_get("SELECT COUNT(*) AS total FROM pemesanan WHERE status = 'pending'");
    $pemesananPending = (int) ($row['total'] ?? 0);
} catch (PDOException $e) {
    $pemesananPending = 0;
}

// ============================================================
// PESAN MASUK BELUM DIBACA
// ============================================================
$pesanUnread = function_exists('hitung_pesan_belum_dibaca') ? hitung_pesan_belum_dibaca() : 0;

// ============================================================
// HELPER
// ============================================================
function safe($arr, $key, $default = '') {
    return isset($arr[$key]) && $arr[$key] !== '' ? $arr[$key] : $default;
}

function badgePaketStatus($status) {
    $map = [
        'aktif'    => 'bg-green-100 text-green-600',
        'draft'    => 'bg-yellow-100 text-yellow-600',
        'nonaktif' => 'bg-red-100 text-red-600',
    ];
    return $map[$status] ?? 'bg-gray-100 text-gray-600';
}

require_once __DIR__ . '/../layout/admin_header.php';
?>

<!-- ============================================================
     WELCOME BANNER
============================================================ -->
<div class="mb-6 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-2xl p-5 md:p-6 shadow-lg">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl md:text-2xl font-bold mb-1">
                Selamat Datang, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Administrator') ?> 👋
            </h2>
            <p class="text-xs md:text-sm text-blue-100">
                <?= date('l, d F Y') ?> • Ringkasan data website Bayu Prima Wisata
            </p>
        </div>
        <?php if ($pesanUnread > 0): ?>
            <a href="pesan-masuk.php"
               class="bg-white/20 hover:bg-white/30 backdrop-blur px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition">
                <i class="fa-solid fa-bell"></i>
                <?= $pesanUnread ?> Pesan Baru
            </a>
        <?php endif; ?>
    </div>
</div>


<!-- ============================================================
     STATISTIK CARDS
============================================================ -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4 mb-8">

    <!-- Paket Wisata -->
    <a href="paket.php" class="card-stats bg-white rounded-xl p-4 md:p-5 shadow-sm border-l-4 border-blue-600 hover:shadow-md transition">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">Paket Wisata</p>
                <p class="text-xl md:text-2xl font-bold text-gray-800"><?= number_format($statPaket) ?></p>
                <p class="text-[10px] text-gray-400 mt-0.5">Aktif</p>
            </div>
            <div class="w-9 h-9 md:w-10 md:h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-suitcase text-blue-600 text-sm md:text-base"></i>
            </div>
        </div>
    </a>

    <!-- Destinasi -->
    <a href="destinasi.php" class="card-stats bg-white rounded-xl p-4 md:p-5 shadow-sm border-l-4 border-cyan-600 hover:shadow-md transition">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">Destinasi</p>
                <p class="text-xl md:text-2xl font-bold text-gray-800"><?= number_format($statDestinasi) ?></p>
                <p class="text-[10px] text-gray-400 mt-0.5">Aktif</p>
            </div>
            <div class="w-9 h-9 md:w-10 md:h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-map-location-dot text-cyan-600 text-sm md:text-base"></i>
            </div>
        </div>
    </a>

    <!-- Testimoni -->
    <a href="testimoni.php" class="card-stats bg-white rounded-xl p-4 md:p-5 shadow-sm border-l-4 border-green-600 hover:shadow-md transition">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">Testimoni</p>
                <p class="text-xl md:text-2xl font-bold text-gray-800"><?= number_format($statTestimoni) ?></p>
                <p class="text-[10px] text-gray-400 mt-0.5">Approved</p>
            </div>
            <div class="w-9 h-9 md:w-10 md:h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-star text-green-600 text-sm md:text-base"></i>
            </div>
        </div>
    </a>

    <!-- Pengunjung -->
    <div class="card-stats bg-white rounded-xl p-4 md:p-5 shadow-sm border-l-4 border-yellow-600">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">Pengunjung</p>
                <p class="text-xl md:text-2xl font-bold text-gray-800"><?= number_format($statPengunjung, 0, ',', '.') ?></p>
                <p class="text-[10px] text-gray-400 mt-0.5">Total views</p>
            </div>
            <div class="w-9 h-9 md:w-10 md:h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-eye text-yellow-600 text-sm md:text-base"></i>
            </div>
        </div>
    </div>

    <!-- Rating -->
    <div class="card-stats bg-white rounded-xl p-4 md:p-5 shadow-sm border-l-4 border-purple-600">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-400 text-xs md:text-sm">Rating</p>
                <p class="text-xl md:text-2xl font-bold text-gray-800">
                    <?= $avgRating > 0 ? number_format($avgRating, 1) : '0.0' ?>/5
                </p>
                <p class="text-[10px] text-gray-400 mt-0.5">Rata-rata</p>
            </div>
            <div class="w-9 h-9 md:w-10 md:h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-ranking-star text-purple-600 text-sm md:text-base"></i>
            </div>
        </div>
    </div>
</div>


<!-- ============================================================
     ALERT: PEMESANAN PENDING
============================================================ -->
<?php if ($pemesananPending > 0): ?>
    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-clock text-yellow-600"></i>
        </div>
        <div class="flex-1">
            <p class="font-semibold text-yellow-800 text-sm">
                Ada <?= $pemesananPending ?> pemesanan baru yang menunggu konfirmasi!
            </p>
            <p class="text-xs text-yellow-700 mt-0.5">
                Segera tindak lanjuti agar pelanggan tidak menunggu lama.
            </p>
        </div>
        <a href="pemesanan.php?status=pending"
           class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap">
            Lihat <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>
<?php endif; ?>


<!-- ============================================================
     RECENT DATA + QUICK ACTIONS
============================================================ -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Paket Wisata Terbaru -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-sm md:text-base">Paket Wisata Terbaru</h3>
            <a href="paket.php" class="text-blue-600 text-xs hover:underline">Lihat Semua →</a>
        </div>

        <?php if (empty($paketTerbaru)): ?>
            <div class="p-10 text-center">
                <i class="fa-regular fa-folder-open text-3xl text-gray-300 mb-2"></i>
                <p class="text-xs text-gray-400">Belum ada paket wisata.</p>
                <a href="paket.php?action=tambah"
                   class="inline-flex items-center gap-2 mt-3 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition">
                    <i class="fa-solid fa-plus"></i> Tambah Paket
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama Paket</th>
                            <th class="px-4 py-3 text-left">Destinasi</th>
                            <th class="px-4 py-3 text-right">Harga</th>
                            <th class="px-4 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($paketTerbaru as $p):
                            $harga = safe($p, 'harga_diskon', 0) > 0
                                ? (float) $p['harga_diskon']
                                : (float) safe($p, 'harga_normal', 0);
                            $status = safe($p, 'status', 'aktif');
                        ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <a href="paket.php?action=edit&id=<?= (int) safe($p, 'id', 0) ?>"
                                       class="font-medium text-gray-800 hover:text-blue-600 transition line-clamp-1">
                                        <?= htmlspecialchars(safe($p, 'nama_paket', '-')) ?>
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    <?= htmlspecialchars(safe($p, 'destinasi', '-')) ?>
                                </td>
                                <td class="px-4 py-3 text-xs text-right font-semibold text-gray-800 whitespace-nowrap">
                                    Rp <?= number_format($harga, 0, ',', '.') ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="<?= badgePaketStatus($status) ?> px-2 py-1 rounded-full text-[10px] font-bold">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b bg-gray-50">
            <h3 class="font-semibold text-gray-800 text-sm md:text-base">Aksi Cepat</h3>
        </div>
        <div class="p-5 grid grid-cols-2 sm:grid-cols-3 gap-3">

            <a href="paket.php?action=tambah" class="bg-blue-50 hover:bg-blue-100 p-3 md:p-4 rounded-xl text-center transition">
                <i class="fa-solid fa-plus text-blue-600 text-lg md:text-xl mb-1"></i>
                <p class="text-[11px] md:text-xs font-medium text-gray-700">Tambah Paket</p>
            </a>

            <a href="destinasi.php?action=tambah" class="bg-cyan-50 hover:bg-cyan-100 p-3 md:p-4 rounded-xl text-center transition">
                <i class="fa-solid fa-location-dot text-cyan-600 text-lg md:text-xl mb-1"></i>
                <p class="text-[11px] md:text-xs font-medium text-gray-700">Tambah Destinasi</p>
            </a>

            <a href="blog/tambah.php" class="bg-orange-50 hover:bg-orange-100 p-3 md:p-4 rounded-xl text-center transition">
                <i class="fa-regular fa-newspaper text-orange-600 text-lg md:text-xl mb-1"></i>
                <p class="text-[11px] md:text-xs font-medium text-gray-700">Tulis Artikel</p>
            </a>

            <a href="pesan-masuk.php" class="bg-purple-50 hover:bg-purple-100 p-3 md:p-4 rounded-xl text-center transition relative">
                <i class="fa-solid fa-envelope text-purple-600 text-lg md:text-xl mb-1"></i>
                <p class="text-[11px] md:text-xs font-medium text-gray-700">Pesan Masuk</p>
                <?php if ($pesanUnread > 0): ?>
                    <span class="absolute top-1 right-1 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">
                        <?= $pesanUnread ?>
                    </span>
                <?php endif; ?>
            </a>

            <a href="pemesanan.php" class="bg-yellow-50 hover:bg-yellow-100 p-3 md:p-4 rounded-xl text-center transition relative">
                <i class="fa-solid fa-cart-shopping text-yellow-600 text-lg md:text-xl mb-1"></i>
                <p class="text-[11px] md:text-xs font-medium text-gray-700">Pemesanan</p>
                <?php if ($pemesananPending > 0): ?>
                    <span class="absolute top-1 right-1 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full">
                        <?= $pemesananPending ?>
                    </span>
                <?php endif; ?>
            </a>

            <a href="pengaturan.php" class="bg-gray-50 hover:bg-gray-100 p-3 md:p-4 rounded-xl text-center transition">
                <i class="fa-solid fa-gear text-gray-600 text-lg md:text-xl mb-1"></i>
                <p class="text-[11px] md:text-xs font-medium text-gray-700">Pengaturan</p>
            </a>
        </div>
    </div>
</div>


<!-- ============================================================
     TESTIMONI TERBARU
============================================================ -->
<div class="mt-6 bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-sm md:text-base">Testimoni Terbaru</h3>
        <a href="testimoni.php" class="text-blue-600 text-xs hover:underline">Kelola →</a>
    </div>

    <?php if (empty($testimoniTerbaru)): ?>
        <div class="p-10 text-center">
            <i class="fa-regular fa-comment-dots text-3xl text-gray-300 mb-2"></i>
            <p class="text-xs text-gray-400">Belum ada testimoni.</p>
        </div>
    <?php else: ?>
        <div class="divide-y divide-gray-100">
            <?php foreach ($testimoniTerbaru as $t):
                $nama   = safe($t, 'nama', 'Anonim');
                $rating = max(0, min(5, (int) safe($t, 'rating', 5)));
                $isi    = safe($t, 'testimoni', '');
                $foto   = safe($t, 'foto', '');
                $kota   = safe($t, 'kota_asal', 'Indonesia');

                // Cek foto ada
                $fotoPath = null;
                if ($foto !== '' && file_exists(__DIR__ . '/../' . $foto)) {
                    $fotoPath = '../' . $foto;
                }
            ?>
                <div class="p-4 flex items-start gap-4">
                    <?php if ($fotoPath): ?>
                        <img src="<?= htmlspecialchars($fotoPath) ?>"
                             class="w-10 h-10 rounded-full object-cover flex-shrink-0"
                             alt="">
                    <?php else: ?>
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="font-bold text-blue-600 text-sm">
                                <?= htmlspecialchars(strtoupper(substr($nama, 0, 1))) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-medium text-gray-800 text-sm"><?= htmlspecialchars($nama) ?></p>
                            <span class="text-[10px] text-gray-400">• <?= htmlspecialchars($kota) ?></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">
                            "<?= htmlspecialchars(mb_substr($isi, 0, 120)) ?>..."
                        </p>
                    </div>

                    <div class="text-yellow-400 text-xs whitespace-nowrap flex-shrink-0">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa-<?= $i <= $rating ? 'solid' : 'regular' ?> fa-star"></i>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>


<?php ob_end_flush(); ?>