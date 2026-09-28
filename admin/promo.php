<?php
/**
 * ============================================================
 * ADMIN - KELOLA PROMO
 * Bayu Prima Wisata
 * ============================================================
 */

ob_start();
session_start();
require_once __DIR__ . '/../config/database.php';

// ============================================================
// NOTIFIKASI
// ============================================================
$msg     = $_SESSION['admin_msg'] ?? null;
$msgType = $_SESSION['admin_msg_type'] ?? 'success';
unset($_SESSION['admin_msg'], $_SESSION['admin_msg_type']);

// ============================================================
// FILTER
// ============================================================
$filterKategori = $_GET['kategori'] ?? 'all';
$filterStatus   = $_GET['status'] ?? 'all';
$search         = trim($_GET['q'] ?? '');

$where  = [];
$params = [];

$allowedKategori = ['flash_sale', 'early_bird', 'group', 'member', 'umum'];
if (in_array($filterKategori, $allowedKategori, true)) {
    $where[] = "kategori = :kategori";
    $params[':kategori'] = $filterKategori;
}

if (in_array($filterStatus, ['aktif', 'nonaktif', 'habis'], true)) {
    $where[] = "status = :status";
    $params[':status'] = $filterStatus;
}

if ($search !== '') {
    $where[] = "(judul LIKE :search OR kode_promo LIKE :search OR deskripsi LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ============================================================
// AMBIL DATA
// ============================================================
$promoList = [];
try {
    $promoList = db_get_all("
        SELECT * FROM promo
        $whereSql
        ORDER BY
            FIELD(status, 'aktif', 'habis', 'nonaktif'),
            berlaku_sampai ASC,
            id DESC
    ", $params);
} catch (PDOException $e) {
    $promoList = [];
}

// ============================================================
// STATISTIK
// ============================================================
$stat = [
    'total'      => 0,
    'aktif'      => 0,
    'nonaktif'   => 0,
    'habis'      => 0,
    'flash_sale' => 0,
    'early_bird' => 0,
    'member'     => 0,
];

try {
    $row = db_get("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'aktif' THEN 1 ELSE 0 END) AS aktif,
            SUM(CASE WHEN status = 'nonaktif' THEN 1 ELSE 0 END) AS nonaktif,
            SUM(CASE WHEN status = 'habis' THEN 1 ELSE 0 END) AS habis,
            SUM(CASE WHEN kategori = 'flash_sale' THEN 1 ELSE 0 END) AS flash_sale,
            SUM(CASE WHEN kategori = 'early_bird' THEN 1 ELSE 0 END) AS early_bird,
            SUM(CASE WHEN kategori = 'member' THEN 1 ELSE 0 END) AS member
        FROM promo
    ");
    if ($row) {
        foreach ($stat as $k => $v) {
            $stat[$k] = (int) ($row[$k] ?? 0);
        }
    }
} catch (PDOException $e) {
    // biarkan
}

// ============================================================
// HELPER
// ============================================================
function badgeKategori($kategori) {
    $map = [
        'flash_sale' => ['bg-red-100 text-red-700',    'fa-bolt',       'Flash Sale'],
        'early_bird' => ['bg-blue-100 text-blue-700',  'fa-calendar',   'Early Bird'],
        'group'      => ['bg-purple-100 text-purple-700','fa-users',    'Group'],
        'member'     => ['bg-amber-100 text-amber-700','fa-gem',        'Member'],
        'umum'       => ['bg-gray-100 text-gray-700',  'fa-tag',        'Umum'],
    ];
    return $map[$kategori] ?? ['bg-gray-100 text-gray-700', 'fa-tag', ucfirst($kategori)];
}

function badgeStatusPromo($status) {
    $map = [
        'aktif'    => ['bg-green-100 text-green-700',  'fa-circle-check', 'Aktif'],
        'nonaktif' => ['bg-gray-100 text-gray-600',    'fa-pause',        'Nonaktif'],
        'habis'    => ['bg-red-100 text-red-700',      'fa-circle-xmark', 'Habis'],
    ];
    return $map[$status] ?? ['bg-gray-100 text-gray-700', 'fa-circle', ucfirst($status)];
}

include __DIR__ . '/../layout/admin_header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Promo - Admin BPW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>* { font-family: 'Poppins', sans-serif; }</style>
</head>

<body class="bg-slate-50 text-gray-800">

<!-- NOTIFIKASI -->
<?php if ($msg): ?>
    <div id="adminNotif" class="container mx-auto px-4 md:px-6 max-w-7xl mt-4">
        <div class="<?= $msgType === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700' ?> border-l-4 p-3 rounded-md text-sm flex items-center gap-2">
            <i class="fa-solid <?= $msgType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
            <?= htmlspecialchars($msg) ?>
        </div>
    </div>
    <script>setTimeout(() => document.getElementById('adminNotif')?.remove(), 4000);</script>
<?php endif; ?>


<!-- HEADER -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-tags text-blue-600"></i>
                Kelola Promo
            </h1>
            <p class="text-xs text-gray-400 mt-1">Buat dan kelola promo untuk pelanggan.</p>
        </div>
        <a href="promo/tambah.php"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition inline-flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Promo
        </a>
    </div>
</section>


<!-- STATISTIK -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6">
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">

        <a href="promo.php" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'all' && $filterKategori === 'all' ? 'ring-2 ring-blue-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-layer-group text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Semua</div>
                    <div class="text-sm font-bold"><?= $stat['total'] ?></div>
                </div>
            </div>
        </a>

        <a href="promo.php?status=aktif" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'aktif' ? 'ring-2 ring-green-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                    <i class="fa-solid fa-circle-check text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Aktif</div>
                    <div class="text-sm font-bold"><?= $stat['aktif'] ?></div>
                </div>
            </div>
        </a>

        <a href="promo.php?status=nonaktif" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'nonaktif' ? 'ring-2 ring-gray-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-gray-600">
                    <i class="fa-solid fa-pause text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Nonaktif</div>
                    <div class="text-sm font-bold"><?= $stat['nonaktif'] ?></div>
                </div>
            </div>
        </a>

        <a href="promo.php?status=habis" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'habis' ? 'ring-2 ring-red-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                    <i class="fa-solid fa-circle-xmark text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Habis</div>
                    <div class="text-sm font-bold"><?= $stat['habis'] ?></div>
                </div>
            </div>
        </a>

        <a href="promo.php?kategori=flash_sale" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterKategori === 'flash_sale' ? 'ring-2 ring-red-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                    <i class="fa-solid fa-bolt text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Flash</div>
                    <div class="text-sm font-bold"><?= $stat['flash_sale'] ?></div>
                </div>
            </div>
        </a>

        <a href="promo.php?kategori=early_bird" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterKategori === 'early_bird' ? 'ring-2 ring-blue-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-calendar text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Early</div>
                    <div class="text-sm font-bold"><?= $stat['early_bird'] ?></div>
                </div>
            </div>
        </a>

        <a href="promo.php?kategori=member" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterKategori === 'member' ? 'ring-2 ring-amber-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600">
                    <i class="fa-solid fa-gem text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Member</div>
                    <div class="text-sm font-bold"><?= $stat['member'] ?></div>
                </div>
            </div>
        </a>

    </div>
</section>


<!-- SEARCH -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6">
    <form method="GET" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 flex flex-wrap gap-2">
        <?php if ($filterStatus !== 'all'): ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
        <?php endif; ?>
        <?php if ($filterKategori !== 'all'): ?>
            <input type="hidden" name="kategori" value="<?= htmlspecialchars($filterKategori) ?>">
        <?php endif; ?>

        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                       placeholder="Cari judul, kode promo..."
                       class="w-full pl-9 pr-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
        </div>

        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-xs font-semibold transition">
            <i class="fa-solid fa-search"></i> Cari
        </button>

        <?php if ($filterStatus !== 'all' || $filterKategori !== 'all' || $search !== ''): ?>
            <a href="promo.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-xs font-semibold transition">
                <i class="fa-solid fa-xmark"></i> Reset
            </a>
        <?php endif; ?>
    </form>
</section>


<!-- LIST PROMO -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6 pb-16">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <?php if (empty($promoList)): ?>
            <div class="p-16 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-regular fa-folder-open text-3xl text-gray-300"></i>
                </div>
                <h3 class="font-bold text-gray-600 mb-1">Belum Ada Promo</h3>
                <p class="text-xs text-gray-400 mb-4">Mulai buat promo pertama Anda.</p>
                <a href="promo/tambah.php" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition">
                    <i class="fa-solid fa-plus"></i> Tambah Promo
                </a>
            </div>
        <?php else: ?>

            <div class="divide-y divide-gray-100">
                <?php foreach ($promoList as $promo):
                    list($katClass, $katIcon, $katLabel) = badgeKategori($promo['kategori']);
                    list($statClass, $statIcon, $statLabel) = badgeStatusPromo($promo['status']);

                    // Progress kuota
                    $kuota      = (int) ($promo['kuota'] ?? 0);
                    $sisaKuota  = (int) ($promo['sisa_kuota'] ?? 0);
                    $persentase = $kuota > 0 ? (($kuota - $sisaKuota) / $kuota) * 100 : 0;

                    // Cek expired
                    $isExpired = strtotime($promo['berlaku_sampai']) < strtotime('today');
                ?>
                    <div class="p-4 md:p-5 hover:bg-slate-50 transition flex items-start gap-4">

                        <!-- Gambar -->
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-lg overflow-hidden bg-slate-100 flex-shrink-0">
                            <?php if (!empty($promo['gambar']) && file_exists(__DIR__ . '/../' . $promo['gambar'])): ?>
                                <img src="../<?= htmlspecialchars($promo['gambar']) ?>"
                                     class="w-full h-full object-cover"
                                     onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i class="fa-solid fa-tag text-2xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Konten -->
                        <div class="flex-1 min-w-0">

                            <!-- Badges -->
                            <div class="flex flex-wrap items-center gap-1.5 mb-2">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $katClass ?>">
                                    <i class="fa-solid <?= $katIcon ?> text-[8px]"></i>
                                    <?= $katLabel ?>
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $statClass ?>">
                                    <i class="fa-solid <?= $statIcon ?> text-[8px]"></i>
                                    <?= $statLabel ?>
                                </span>
                                <?php if ($isExpired && $promo['status'] === 'aktif'): ?>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-700">
                                        <i class="fa-solid fa-triangle-exclamation text-[8px]"></i> Kadaluarsa
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($promo['kode_promo'])): ?>
                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700">
                                        <?= htmlspecialchars($promo['kode_promo']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Judul -->
                            <h3 class="font-bold text-sm text-gray-800 mb-1">
                                <?= htmlspecialchars($promo['judul']) ?>
                            </h3>

                            <!-- Deskripsi -->
                            <?php if (!empty($promo['deskripsi'])): ?>
                                <p class="text-xs text-gray-500 mb-2 line-clamp-2">
                                    <?= htmlspecialchars($promo['deskripsi']) ?>
                                </p>
                            <?php endif; ?>

                            <!-- Detail -->
                            <div class="flex flex-wrap gap-3 text-[11px] text-gray-500 mb-2">
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-percent text-blue-500"></i>
                                    <strong>Diskon <?= (int) $promo['diskon_persen'] ?>%</strong>
                                </span>
                                <?php if ((float) $promo['minimal_pembelian'] > 0): ?>
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-cart-shopping text-purple-500"></i>
                                        Min. Rp <?= number_format((float) $promo['minimal_pembelian'], 0, ',', '.') ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($promo['maksimal_diskon'])): ?>
                                    <span class="flex items-center gap-1">
                                        <i class="fa-solid fa-hand-holding-dollar text-emerald-500"></i>
                                        Maks. Rp <?= number_format((float) $promo['maksimal_diskon'], 0, ',', '.') ?>
                                    </span>
                                <?php endif; ?>
                                <span class="flex items-center gap-1">
                                    <i class="fa-regular fa-calendar text-gray-400"></i>
                                    <?= date('d M Y', strtotime($promo['berlaku_mulai'])) ?> — <?= date('d M Y', strtotime($promo['berlaku_sampai'])) ?>
                                </span>
                            </div>

                            <!-- Progress Kuota -->
                            <?php if ($kuota > 0): ?>
                                <div class="mb-2 max-w-xs">
                                    <div class="flex justify-between text-[10px] text-gray-400 mb-1">
                                        <span>Kuota terpakai</span>
                                        <span><strong><?= $kuota - $sisaKuota ?></strong> / <?= $kuota ?></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="<?= $persentase > 80 ? 'bg-red-500' : ($persentase > 50 ? 'bg-amber-500' : 'bg-green-500') ?> h-1.5 rounded-full"
                                             style="width: <?= $persentase ?>%"></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Actions -->
                            <div class="flex flex-wrap gap-2 mt-3">
                                <a href="promo/edit.php?id=<?= $promo['id'] ?>"
                                   class="text-[10px] bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md font-semibold transition inline-flex items-center gap-1">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>

                                <?php if ($promo['status'] === 'aktif'): ?>
                                    <a href="promo/simpan.php?aksi=nonaktif&id=<?= $promo['id'] ?>"
                                       onclick="return confirm('Nonaktifkan promo ini?')"
                                       class="text-[10px] bg-gray-500 hover:bg-gray-600 text-white px-3 py-1.5 rounded-md font-semibold transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-pause"></i> Nonaktifkan
                                    </a>
                                <?php else: ?>
                                    <a href="promo/simpan.php?aksi=aktifkan&id=<?= $promo['id'] ?>"
                                       onclick="return confirm('Aktifkan promo ini?')"
                                       class="text-[10px] bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md font-semibold transition inline-flex items-center gap-1">
                                        <i class="fa-solid fa-play"></i> Aktifkan
                                    </a>
                                <?php endif; ?>

                                <a href="promo/hapus.php?id=<?= $promo['id'] ?>"
                                   onclick="return confirm('Yakin hapus promo ini?\n\nTindakan ini tidak bisa dibatalkan.')"
                                   class="text-[10px] bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md font-semibold transition inline-flex items-center gap-1">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </a>
                            </div>

                        </div>

                        <!-- Diskon Besar (kanan) -->
                        <div class="hidden md:flex flex-col items-center justify-center flex-shrink-0 px-3">
                            <div class="text-2xl font-extrabold text-red-600"><?= (int) $promo['diskon_persen'] ?>%</div>
                            <div class="text-[9px] text-gray-400 uppercase tracking-wider">Diskon</div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

    </div>
</section>

</body>
</html>