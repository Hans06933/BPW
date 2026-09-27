<?php
/**
 * ============================================================
 * ADMIN - PESAN MASUK (READ)
 * Bayu Prima Wisata
 * ============================================================
 */

ob_start();
session_start();
require_once __DIR__ . '/../config/database.php';

// ============================================================
// PROTEKSI ADMIN
// ============================================================
// if (empty($_SESSION['admin_id'])) {
//     header('Location: login.php');
//     exit;
// }

// ============================================================
// NOTIFIKASI
// ============================================================
$msg     = $_SESSION['admin_msg'] ?? null;
$msgType = $_SESSION['admin_msg_type'] ?? 'success';
unset($_SESSION['admin_msg'], $_SESSION['admin_msg_type']);

// ============================================================
// FILTER & PAGINATION
// ============================================================
$filterTipe   = $_GET['tipe'] ?? 'all';
$filterStatus = $_GET['status'] ?? 'all';
$search       = trim($_GET['q'] ?? '');
$page         = max(1, (int) ($_GET['page'] ?? 1));
$perPage      = 15;
$offset       = ($page - 1) * $perPage;

$where  = [];
$params = [];

$allowedTipe = ['testimoni', 'blog', 'kontak', 'review', 'sistem'];
if (in_array($filterTipe, $allowedTipe, true)) {
    $where[] = "tipe = :tipe";
    $params[':tipe'] = $filterTipe;
}

if ($filterStatus === 'unread')       $where[] = "is_read = 0";
elseif ($filterStatus === 'read')     $where[] = "is_read = 1";
elseif ($filterStatus === 'starred')  $where[] = "is_starred = 1";

if ($search !== '') {
    $where[] = "(judul LIKE :search OR pesan LIKE :search OR pengirim LIKE :search OR email LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ============================================================
// TOTAL
// ============================================================
$totalData = 0;
try {
    $stat = db_get("SELECT COUNT(*) AS total FROM pesan_masuk $whereSql", $params);
    $totalData = (int) ($stat['total'] ?? 0);
} catch (PDOException $e) {
    $totalData = 0;
}
$totalPages = max(1, ceil($totalData / $perPage));

// ============================================================
// AMBIL DATA
// ============================================================
$pesanList = [];
try {
    $sql = "SELECT * FROM pesan_masuk $whereSql ORDER BY is_starred DESC, is_read ASC, created_at DESC LIMIT $perPage OFFSET $offset";
    $pesanList = db_get_all($sql, $params);
} catch (PDOException $e) {
    $pesanList = [];
}

// ============================================================
// STATISTIK
// ============================================================
$statistik = [
    'total'      => 0,
    'unread'     => 0,
    'starred'    => 0,
    'testimoni'  => 0,
    'blog'       => 0,
    'kontak'     => 0,
    'review'     => 0,
];

try {
    $row = db_get("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) AS unread,
            SUM(CASE WHEN is_starred = 1 THEN 1 ELSE 0 END) AS starred,
            SUM(CASE WHEN tipe = 'testimoni' THEN 1 ELSE 0 END) AS testimoni,
            SUM(CASE WHEN tipe = 'blog' THEN 1 ELSE 0 END) AS blog,
            SUM(CASE WHEN tipe = 'kontak' THEN 1 ELSE 0 END) AS kontak,
            SUM(CASE WHEN tipe = 'review' THEN 1 ELSE 0 END) AS review
        FROM pesan_masuk
    ");
    if ($row) {
        foreach ($statistik as $k => $v) {
            $statistik[$k] = (int) ($row[$k] ?? 0);
        }
    }
} catch (PDOException $e) {
    // biarkan
}

// ============================================================
// HELPER
// ============================================================
function iconTipe($tipe) {
    $icons = [
        'testimoni'  => ['fa-comment-dots', 'bg-yellow-100 text-yellow-600'],
        'blog'       => ['fa-newspaper',    'bg-blue-100 text-blue-600'],
        'kontak'     => ['fa-envelope',     'bg-purple-100 text-purple-600'],
        'review'     => ['fa-star',         'bg-orange-100 text-orange-600'],
        'sistem'     => ['fa-info-circle',  'bg-gray-100 text-gray-600'],
    ];
    return $icons[$tipe] ?? ['fa-bell', 'bg-gray-100 text-gray-600'];
}

// ============================================================
// INCLUDE HEADER
// ============================================================
include __DIR__ . '/../layout/admin_header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk - Admin BPW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .pesan-item { transition: all 0.2s ease; }
        .pesan-item:hover { background-color: #f8fafc; }
        .pesan-item.unread {
            background-color: #eff6ff;
            border-left: 3px solid #2563eb;
        }
        .pesan-item.starred { background-color: #fef3c7; }
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
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-inbox text-blue-600"></i>
                Pesan Masuk
                <?php if ($statistik['unread'] > 0): ?>
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                        <?= $statistik['unread'] ?>
                    </span>
                <?php endif; ?>
            </h1>
            <p class="text-xs text-gray-400 mt-1">
                Notifikasi dari semua fitur yang berhubungan dengan admin.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <?php if ($statistik['unread'] > 0): ?>
                <form method="POST" action="pesan/aksi.php" class="inline">
                    <input type="hidden" name="aksi" value="mark_all_read">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs font-semibold transition inline-flex items-center gap-2">
                        <i class="fa-solid fa-check-double"></i> Tandai Semua Dibaca
                    </button>
                </form>
            <?php endif; ?>

            <form method="POST" action="pesan/hapus.php" class="inline" onsubmit="return confirm('Hapus semua pesan yang sudah dibaca?')">
                <input type="hidden" name="aksi" value="hapus_semua_read">
                <button class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg text-xs font-semibold transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-broom"></i> Bersihkan
                </button>
            </form>
        </div>
    </div>
</section>


<!-- STATISTIK -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6">
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">

        <a href="pesan-masuk.php" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterTipe === 'all' && $filterStatus === 'all' ? 'ring-2 ring-blue-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-inbox text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Semua</div>
                    <div class="text-sm font-bold"><?= $statistik['total'] ?></div>
                </div>
            </div>
        </a>

        <a href="pesan-masuk.php?status=unread" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'unread' ? 'ring-2 ring-red-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                    <i class="fa-solid fa-envelope text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Belum Dibaca</div>
                    <div class="text-sm font-bold"><?= $statistik['unread'] ?></div>
                </div>
            </div>
        </a>

        <a href="pesan-masuk.php?status=starred" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'starred' ? 'ring-2 ring-yellow-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">
                    <i class="fa-solid fa-star text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Penting</div>
                    <div class="text-sm font-bold"><?= $statistik['starred'] ?></div>
                </div>
            </div>
        </a>

        <a href="pesan-masuk.php?tipe=testimoni" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterTipe === 'testimoni' ? 'ring-2 ring-yellow-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">
                    <i class="fa-solid fa-comment-dots text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Testimoni</div>
                    <div class="text-sm font-bold"><?= $statistik['testimoni'] ?></div>
                </div>
            </div>
        </a>

        <a href="pesan-masuk.php?tipe=blog" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterTipe === 'blog' ? 'ring-2 ring-blue-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-newspaper text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Blog</div>
                    <div class="text-sm font-bold"><?= $statistik['blog'] ?></div>
                </div>
            </div>
        </a>

        <a href="pesan-masuk.php?tipe=kontak" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterTipe === 'kontak' ? 'ring-2 ring-purple-500' : '' ?>">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600">
                    <i class="fa-solid fa-envelope text-xs"></i>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400">Kontak</div>
                    <div class="text-sm font-bold"><?= $statistik['kontak'] ?></div>
                </div>
            </div>
        </a>

    </div>
</section>


<!-- SEARCH -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6">
    <form method="GET" action="pesan-masuk.php" class="bg-white rounded-xl p-3 shadow-sm border border-gray-100 flex flex-wrap gap-2">
        <?php if ($filterTipe !== 'all'): ?>
            <input type="hidden" name="tipe" value="<?= htmlspecialchars($filterTipe) ?>">
        <?php endif; ?>
        <?php if ($filterStatus !== 'all'): ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
        <?php endif; ?>

        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                       placeholder="Cari pesan, pengirim, atau email..."
                       class="w-full pl-9 pr-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
        </div>

        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-xs font-semibold transition">
            <i class="fa-solid fa-search"></i> Cari
        </button>

        <?php if ($filterTipe !== 'all' || $filterStatus !== 'all' || $search !== ''): ?>
            <a href="pesan-masuk.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-xs font-semibold transition">
                <i class="fa-solid fa-xmark"></i> Reset
            </a>
        <?php endif; ?>
    </form>
</section>


<!-- LIST PESAN -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6 pb-16">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="p-4 border-b flex items-center justify-between flex-wrap gap-3">
            <h2 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <?php if ($filterTipe !== 'all'): ?>
                    <i class="fa-solid fa-filter text-blue-600 text-xs"></i>
                    Filter: <span class="capitalize"><?= htmlspecialchars($filterTipe) ?></span>
                <?php endif; ?>
                <?php if ($filterStatus !== 'all'): ?>
                    <span class="capitalize text-xs bg-gray-100 px-2 py-0.5 rounded-full">
                        <?= htmlspecialchars($filterStatus) ?>
                    </span>
                <?php endif; ?>
                <span class="text-xs font-normal text-gray-400">(<?= $totalData ?> pesan)</span>
            </h2>
        </div>

        <?php if (empty($pesanList)): ?>

            <div class="p-16 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-regular fa-envelope-open text-3xl text-gray-300"></i>
                </div>
                <h3 class="font-bold text-gray-600 mb-1">Tidak Ada Pesan</h3>
                <p class="text-xs text-gray-400">
                    <?= $search !== '' ? 'Tidak ada pesan yang cocok dengan pencarian.' : 'Belum ada pesan masuk untuk filter ini.' ?>
                </p>
            </div>

        <?php else: ?>

            <div class="divide-y divide-gray-100">
                <?php foreach ($pesanList as $p): ?>
                    <?php
                    list($icon, $color) = iconTipe($p['tipe']);
                    $rowClass = '';
                    if (!$p['is_read'])   $rowClass .= ' unread';
                    if ($p['is_starred']) $rowClass .= ' starred';
                    ?>

                    <div class="pesan-item <?= $rowClass ?> p-4 flex items-start gap-3">

                        <!-- Icon -->
                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-lg <?= $color ?> flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid <?= $icon ?>"></i>
                        </div>

                        <!-- Konten -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider <?= explode(' ', $color)[1] ?>">
                                    <?= htmlspecialchars($p['tipe']) ?>
                                </span>
                                <?php if (!$p['is_read']): ?>
                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                <?php endif; ?>
                                <span class="text-[10px] text-gray-400 ml-auto">
                                    <i class="fa-regular fa-clock"></i>
                                    <?php
                                    $ts   = strtotime($p['created_at']);
                                    $diff = time() - $ts;
                                    if ($diff < 60)         echo 'Baru saja';
                                    elseif ($diff < 3600)   echo floor($diff / 60) . ' menit lalu';
                                    elseif ($diff < 86400)  echo floor($diff / 3600) . ' jam lalu';
                                    elseif ($diff < 604800) echo floor($diff / 86400) . ' hari lalu';
                                    else                    echo date('d M Y H:i', $ts);
                                    ?>
                                </span>
                            </div>

                            <h3 class="font-bold text-sm text-gray-800 line-clamp-2">
                                <?= htmlspecialchars($p['judul']) ?>
                            </h3>

                            <p class="text-xs text-gray-500 line-clamp-2 mt-1">
                                <?= htmlspecialchars($p['pesan']) ?>
                            </p>

                            <?php if (!empty($p['pengirim']) || !empty($p['email'])): ?>
                                <div class="flex items-center gap-3 mt-1.5 text-[10px] text-gray-400 flex-wrap">
                                    <?php if (!empty($p['pengirim'])): ?>
                                        <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars($p['pengirim']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($p['email'])): ?>
                                        <span><i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($p['email']) ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="mt-2 flex flex-wrap gap-2">
                                <a href="pesan/detail.php?id=<?= $p['id'] ?>"
                                   class="text-[10px] bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md font-semibold transition">
                                    <i class="fa-solid fa-eye"></i> Lihat Detail
                                </a>

                                <?php if (!empty($p['link'])): ?>
                                    <a href="<?= htmlspecialchars($p['link']) ?>"
                                       class="text-[10px] bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-md font-semibold transition">
                                        <i class="fa-solid fa-arrow-right"></i> Buka Terkait
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex flex-col gap-1 flex-shrink-0">

                            <!-- Star -->
                            <form method="POST" action="pesan/aksi.php" class="inline">
                                <input type="hidden" name="aksi" value="star">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button title="<?= $p['is_starred'] ? 'Hapus tanda penting' : 'Tandai penting' ?>"
                                        class="w-8 h-8 rounded-md flex items-center justify-center transition
                                            <?= $p['is_starred'] ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-gray-100 hover:bg-yellow-100 text-gray-400 hover:text-yellow-600' ?>">
                                    <i class="fa-<?= $p['is_starred'] ? 'solid' : 'regular' ?> fa-star text-xs"></i>
                                </button>
                            </form>

                            <!-- Mark Read/Unread -->
                            <form method="POST" action="pesan/aksi.php" class="inline">
                                <input type="hidden" name="aksi" value="<?= $p['is_read'] ? 'mark_unread' : 'mark_read' ?>">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button title="<?= $p['is_read'] ? 'Tandai belum dibaca' : 'Tandai sudah dibaca' ?>"
                                        class="w-8 h-8 rounded-md flex items-center justify-center transition
                                            <?= $p['is_read'] ? 'bg-gray-100 hover:bg-blue-100 text-gray-400 hover:text-blue-600' : 'bg-blue-500 hover:bg-blue-600 text-white' ?>">
                                    <i class="fa-solid <?= $p['is_read'] ? 'fa-envelope' : 'fa-envelope-open' ?> text-xs"></i>
                                </button>
                            </form>

                            <!-- Hapus -->
                            <a href="pesan/hapus.php?id=<?= $p['id'] ?>"
                               onclick="return confirm('Hapus pesan ini?')"
                               title="Hapus"
                               class="w-8 h-8 bg-gray-100 hover:bg-red-500 text-gray-400 hover:text-white rounded-md flex items-center justify-center transition">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </a>

                        </div>
                    </div>

                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="p-4 border-t flex flex-wrap justify-center gap-2">
                    <?php
                    $queryBase = [];
                    if ($filterTipe !== 'all')   $queryBase['tipe'] = $filterTipe;
                    if ($filterStatus !== 'all') $queryBase['status'] = $filterStatus;
                    if ($search !== '')          $queryBase['q'] = $search;
                    ?>

                    <?php if ($page > 1): ?>
                        <a href="pesan-masuk.php?<?= http_build_query(array_merge($queryBase, ['page' => $page - 1])) ?>" class="pagination-btn">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn disabled"><i class="fa-solid fa-chevron-left text-xs"></i></span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="pesan-masuk.php?<?= http_build_query(array_merge($queryBase, ['page' => $i])) ?>"
                           class="pagination-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="pesan-masuk.php?<?= http_build_query(array_merge($queryBase, ['page' => $page + 1])) ?>" class="pagination-btn">
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