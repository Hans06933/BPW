<?php
/**
 * ============================================================
 * ADMIN - KELOLA PEMESANAN
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
// FILTER & PAGINATION
// ============================================================
$filterStatus = $_GET['status'] ?? 'all';
$search       = trim($_GET['q'] ?? '');
$page         = max(1, (int) ($_GET['page'] ?? 1));
$perPage      = 10;
$offset       = ($page - 1) * $perPage;

$where  = [];
$params = [];

if (in_array($filterStatus, ['pending', 'dikonfirmasi', 'dp', 'lunas', 'batal', 'selesai'], true)) {
    $where[] = "status = :status";
    $params[':status'] = $filterStatus;
}

if ($search !== '') {
    $where[] = "(kode_booking LIKE :search OR nama_lengkap LIKE :search OR email LIKE :search OR no_hp LIKE :search OR nama_paket LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ============================================================
// TOTAL
// ============================================================
$totalData = 0;
try {
    $stat = db_get("SELECT COUNT(*) AS total FROM pemesanan $whereSql", $params);
    $totalData = (int) ($stat['total'] ?? 0);
} catch (PDOException $e) {
    $totalData = 0;
}
$totalPages = max(1, ceil($totalData / $perPage));

// ============================================================
// AMBIL DATA
// ============================================================
$pemesananList = [];
try {
    $sql = "SELECT * FROM pemesanan $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
    $pemesananList = db_get_all($sql, $params);
} catch (PDOException $e) {
    $pemesananList = [];
}

// ============================================================
// STATISTIK
// ============================================================
$stat = [
    'total'        => 0,
    'pending'      => 0,
    'dikonfirmasi' => 0,
    'dp'           => 0,
    'lunas'        => 0,
    'batal'        => 0,
    'selesai'      => 0,
    'pendapatan'   => 0,
];

try {
    $row = db_get("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status = 'dikonfirmasi' THEN 1 ELSE 0 END) AS dikonfirmasi,
            SUM(CASE WHEN status = 'dp' THEN 1 ELSE 0 END) AS dp,
            SUM(CASE WHEN status = 'lunas' THEN 1 ELSE 0 END) AS lunas,
            SUM(CASE WHEN status = 'batal' THEN 1 ELSE 0 END) AS batal,
            SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) AS selesai,
            SUM(CASE WHEN status IN ('dp','lunas','selesai') THEN total_harga ELSE 0 END) AS pendapatan
        FROM pemesanan
    ");
    if ($row) {
        foreach ($stat as $k => $v) {
            $stat[$k] = $k === 'pendapatan' ? (float) ($row[$k] ?? 0) : (int) ($row[$k] ?? 0);
        }
    }
} catch (PDOException $e) {
    // default
}

// ============================================================
// HELPER
// ============================================================
function safe($arr, $key, $default = '') {
    return isset($arr[$key]) && $arr[$key] !== '' ? $arr[$key] : $default;
}

function badgeStatus($status) {
    $map = [
        'pending'      => ['bg-yellow-100 text-yellow-700', 'fa-clock',          'Pending'],
        'dikonfirmasi' => ['bg-blue-100 text-blue-700',    'fa-check',           'Dikonfirmasi'],
        'dp'           => ['bg-purple-100 text-purple-700','fa-money-bill-wave', 'DP'],
        'lunas'        => ['bg-green-100 text-green-700',  'fa-circle-check',    'Lunas'],
        'batal'        => ['bg-red-100 text-red-700',      'fa-circle-xmark',    'Batal'],
        'selesai'      => ['bg-slate-200 text-slate-700',  'fa-flag-checkered',  'Selesai'],
    ];
    return $map[$status] ?? ['bg-gray-100 text-gray-700', 'fa-circle', ucfirst($status ?: '-')];
}

include __DIR__ . '/../layout/admin_header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemesanan - Admin BPW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .pagination-btn {
            min-width: 36px; height: 36px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 0.5rem; border: 1px solid #e2e8f0;
            background: white; color: #475569;
            font-size: 0.8rem; font-weight: 600;
            transition: all 0.2s;
        }
        .pagination-btn:hover { background: #f1f5f9; }
        .pagination-btn.active { background: #003366; color: white; border-color: #003366; }
        .pagination-btn.disabled { opacity: 0.4; pointer-events: none; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-slate-50 text-gray-800">

<!-- NOTIFIKASI -->
<?php if ($msg): ?>
    <?php
    $colors = [
        'success' => 'bg-green-100 border-green-500 text-green-700',
        'info'    => 'bg-blue-100 border-blue-500 text-blue-700',
        'error'   => 'bg-red-100 border-red-500 text-red-700',
    ];
    $icons = [
        'success' => 'fa-circle-check',
        'info'    => 'fa-circle-info',
        'error'   => 'fa-circle-exclamation',
    ];
    ?>
    <div id="adminNotif" class="container mx-auto px-4 md:px-6 max-w-7xl mt-4">
        <div class="<?= $colors[$msgType] ?> border-l-4 p-3 rounded-md text-sm flex items-center gap-2">
            <i class="fa-solid <?= $icons[$msgType] ?>"></i>
            <?= htmlspecialchars($msg) ?>
        </div>
    </div>
    <script>setTimeout(() => document.getElementById('adminNotif')?.remove(), 4000);</script>
<?php endif; ?>


<!-- HEADER -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-cart-shopping text-blue-600"></i>
            Kelola Pemesanan
        </h1>
        <p class="text-xs text-gray-400 mt-1">
            Daftar semua reservasi paket wisata yang masuk.
        </p>
    </div>
</section>


<!-- STATISTIK -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6">
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">

        <a href="pemesanan.php" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'all' ? 'ring-2 ring-blue-500' : '' ?>">
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

        <a href="pemesanan.php?status=pending" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'pending' ? 'ring-2 ring-yellow-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">
                    <i class="fa-solid fa-clock text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Pending</div>
                    <div class="text-sm font-bold"><?= $stat['pending'] ?></div>
                </div>
            </div>
        </a>

        <a href="pemesanan.php?status=dikonfirmasi" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'dikonfirmasi' ? 'ring-2 ring-blue-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-check text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Konfirmasi</div>
                    <div class="text-sm font-bold"><?= $stat['dikonfirmasi'] ?></div>
                </div>
            </div>
        </a>

        <a href="pemesanan.php?status=dp" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'dp' ? 'ring-2 ring-purple-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">
                    <i class="fa-solid fa-money-bill-wave text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">DP</div>
                    <div class="text-sm font-bold"><?= $stat['dp'] ?></div>
                </div>
            </div>
        </a>

        <a href="pemesanan.php?status=lunas" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'lunas' ? 'ring-2 ring-green-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                    <i class="fa-solid fa-circle-check text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Lunas</div>
                    <div class="text-sm font-bold"><?= $stat['lunas'] ?></div>
                </div>
            </div>
        </a>

        <a href="pemesanan.php?status=batal" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'batal' ? 'ring-2 ring-red-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                    <i class="fa-solid fa-circle-xmark text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Batal</div>
                    <div class="text-sm font-bold"><?= $stat['batal'] ?></div>
                </div>
            </div>
        </a>

        <div class="bg-gradient-to-br from-emerald-500 to-green-600 text-white rounded-xl p-3 shadow-sm col-span-2 md:col-span-1">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-trend-up text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] opacity-80">Pendapatan</div>
                    <div class="text-xs font-bold">
                        Rp <?= number_format($stat['pendapatan'], 0, ',', '.') ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>


<!-- SEARCH -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6">
    <form method="GET" action="pemesanan.php" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 flex flex-wrap gap-2">
        <?php if ($filterStatus !== 'all'): ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
        <?php endif; ?>

        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                       placeholder="Cari kode booking, nama, email, paket..."
                       class="w-full pl-9 pr-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
        </div>

        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-xs font-semibold transition">
            <i class="fa-solid fa-search"></i> Cari
        </button>

        <?php if ($filterStatus !== 'all' || $search !== ''): ?>
            <a href="pemesanan.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-xs font-semibold transition">
                <i class="fa-solid fa-xmark"></i> Reset
            </a>
        <?php endif; ?>
    </form>
</section>


<!-- LIST PEMESANAN -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6 pb-16">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="p-4 border-b flex items-center justify-between flex-wrap gap-3">
            <h2 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-list text-blue-600 text-xs"></i>
                Daftar Pemesanan
                <span class="text-xs font-normal text-gray-400">(<?= $totalData ?> data)</span>
            </h2>
        </div>

        <?php if (empty($pemesananList)): ?>

            <div class="p-16 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-regular fa-folder-open text-3xl text-gray-300"></i>
                </div>
                <h3 class="font-bold text-gray-600 mb-1">Tidak Ada Pemesanan</h3>
                <p class="text-xs text-gray-400">
                    <?= $search !== '' ? 'Tidak ada pemesanan yang cocok.' : 'Belum ada pemesanan masuk.' ?>
                </p>
            </div>

        <?php else: ?>

            <!-- Mobile Card -->
            <div class="md:hidden divide-y divide-gray-100">
                <?php foreach ($pemesananList as $p):
                    $status = safe($p, 'status', 'pending');
                    list($badgeClass, $badgeIcon, $badgeLabel) = badgeStatus($status);
                    $waNumber = preg_replace('/^0/', '62', preg_replace('/\D/', '', safe($p, 'no_hp', '')));
                ?>
                    <div class="p-4 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars(safe($p, 'kode_booking', '-')) ?></div>
                                <h3 class="font-bold text-sm text-gray-800"><?= htmlspecialchars(safe($p, 'nama_lengkap', '-')) ?></h3>
                                <div class="text-[10px] text-gray-500">
                                    <i class="fa-solid fa-suitcase"></i> <?= htmlspecialchars(safe($p, 'nama_paket', '-')) ?>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-1 rounded-full <?= $badgeClass ?> whitespace-nowrap">
                                <i class="fa-solid <?= $badgeIcon ?> text-[8px]"></i>
                                <?= $badgeLabel ?>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-[10px]">
                            <div class="bg-slate-50 rounded p-2">
                                <div class="text-gray-400">Berangkat</div>
                                <div class="font-semibold text-gray-700">
                                    <?= safe($p, 'tanggal_berangkat', '') ? date('d M Y', strtotime($p['tanggal_berangkat'])) : '-' ?>
                                </div>
                            </div>
                            <div class="bg-slate-50 rounded p-2">
                                <div class="text-gray-400">Peserta</div>
                                <div class="font-semibold text-gray-700">
                                    <?= (int) safe($p, 'total_peserta', 0) ?> orang
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center bg-blue-50 rounded p-2">
                            <span class="text-[10px] text-gray-600">Total</span>
                            <span class="text-sm font-bold text-blue-700">
                                Rp <?= number_format((float) safe($p, 'total_harga', 0), 0, ',', '.') ?>
                            </span>
                        </div>

                        <div class="flex gap-2">
                            <a href="pemesanan/detail.php?id=<?= (int) safe($p, 'id', 0) ?>"
                               class="flex-1 text-center text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md font-semibold transition">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                            <?php if ($waNumber): ?>
                                <a href="https://wa.me/<?= $waNumber ?>"
                                   target="_blank"
                                   class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-md font-semibold transition">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="text-left py-3 px-4">Kode & Pemesan</th>
                            <th class="text-left py-3 px-4">Paket</th>
                            <th class="text-left py-3 px-4">Berangkat</th>
                            <th class="text-center py-3 px-4">Peserta</th>
                            <th class="text-right py-3 px-4">Total</th>
                            <th class="text-center py-3 px-4">Status</th>
                            <th class="text-right py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($pemesananList as $p):
                            $status = safe($p, 'status', 'pending');
                            list($badgeClass, $badgeIcon, $badgeLabel) = badgeStatus($status);
                            $noHp = safe($p, 'no_hp', '');
                            $waNumber = $noHp !== '' ? preg_replace('/^0/', '62', preg_replace('/\D/', '', $noHp)) : '';
                            $waText = urlencode('Halo ' . safe($p, 'nama_lengkap', 'Customer') . ', terkait reservasi ' . safe($p, 'kode_booking', '-'));
                        ?>
                            <tr class="hover:bg-slate-50 align-top">
                                <td class="py-3 px-4">
                                    <div class="font-mono text-[10px] text-blue-600 font-bold">
                                        <?= htmlspecialchars(safe($p, 'kode_booking', '-')) ?>
                                    </div>
                                    <div class="font-semibold text-gray-800 mt-0.5">
                                        <?= htmlspecialchars(safe($p, 'nama_lengkap', '-')) ?>
                                    </div>
                                    <div class="text-[10px] text-gray-400">
                                        <i class="fa-regular fa-envelope"></i> <?= htmlspecialchars(safe($p, 'email', '-')) ?>
                                    </div>
                                    <div class="text-[10px] text-gray-400">
                                        <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($noHp ?: '-') ?>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-700 max-w-[200px]">
                                    <div class="line-clamp-2 font-semibold"><?= htmlspecialchars(safe($p, 'nama_paket', '-')) ?></div>
                                    <?php if (safe($p, 'kota', '')): ?>
                                        <div class="text-[10px] text-gray-400 mt-0.5">
                                            <i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($p['kota']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-600 whitespace-nowrap">
                                    <?= safe($p, 'tanggal_berangkat', '') ? date('d M Y', strtotime($p['tanggal_berangkat'])) : '-' ?>
                                </td>
                                <td class="py-3 px-4 text-center text-xs text-gray-600">
                                    <div class="font-bold"><?= (int) safe($p, 'total_peserta', 0) ?></div>
                                    <div class="text-[10px] text-gray-400">
                                        <?= (int) safe($p, 'jumlah_dewasa', 0) ?>D + <?= (int) safe($p, 'jumlah_anak', 0) ?>A
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="font-bold text-gray-800 text-xs">
                                        Rp <?= number_format((float) safe($p, 'total_harga', 0), 0, ',', '.') ?>
                                    </div>
                                    <div class="text-[10px] text-gray-400"><?= htmlspecialchars(safe($p, 'metode_bayar', '-')) ?></div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-full <?= $badgeClass ?> whitespace-nowrap">
                                        <i class="fa-solid <?= $badgeIcon ?> text-[8px]"></i>
                                        <?= $badgeLabel ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex justify-end gap-1">
                                        <a href="pemesanan/detail.php?id=<?= (int) safe($p, 'id', 0) ?>"
                                           title="Detail"
                                           class="w-8 h-8 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition flex items-center justify-center">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                        <?php if ($waNumber): ?>
                                            <a href="https://wa.me/<?= $waNumber ?>?text=<?= $waText ?>"
                                               target="_blank" title="WhatsApp"
                                               class="w-8 h-8 bg-green-500 hover:bg-green-600 text-white rounded-md transition flex items-center justify-center">
                                                <i class="fa-brands fa-whatsapp text-xs"></i>
                                            </a>
                                        <?php endif; ?>
                                        <form method="POST" action="pemesanan/aksi.php" class="inline"
                                              onsubmit="return confirm('Yakin hapus pemesanan ini?')">
                                            <input type="hidden" name="aksi" value="hapus">
                                            <input type="hidden" name="id" value="<?= (int) safe($p, 'id', 0) ?>">
                                            <button title="Hapus"
                                                    class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition flex items-center justify-center">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="p-4 border-t flex flex-wrap justify-center gap-2">
                    <?php
                    $queryBase = [];
                    if ($filterStatus !== 'all') $queryBase['status'] = $filterStatus;
                    if ($search !== '')          $queryBase['q'] = $search;
                    ?>

                    <?php if ($page > 1): ?>
                        <a href="pemesanan.php?<?= http_build_query(array_merge($queryBase, ['page' => $page - 1])) ?>" class="pagination-btn">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn disabled"><i class="fa-solid fa-chevron-left text-xs"></i></span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="pemesanan.php?<?= http_build_query(array_merge($queryBase, ['page' => $i])) ?>"
                           class="pagination-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="pemesanan.php?<?= http_build_query(array_merge($queryBase, ['page' => $page + 1])) ?>" class="pagination-btn">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn disabled"><i class="fa-solid fa-chevron-right text-xs"></i></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</section>

<?php ob_end_flush(); ?>
</body>
</html>