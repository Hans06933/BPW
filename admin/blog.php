<?php
/**
 * ============================================================
 * ADMIN - LIST BLOG (READ)
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/../config/database.php';

// ============================================================
// PROTEKSI ADMIN (opsional)
// ============================================================
// if (empty($_SESSION['admin_id'])) {
//     header('Location: login.php');
//     exit;
// }

// ============================================================
// AMBIL NOTIFIKASI
// ============================================================
$msg     = $_SESSION['admin_msg'] ?? null;
$msgType = $_SESSION['admin_msg_type'] ?? 'success';
unset($_SESSION['admin_msg'], $_SESSION['admin_msg_type']);

// ============================================================
// FILTER & PAGINATION
// ============================================================
$filterStatus = $_GET['status'] ?? 'all';
$filterKat    = $_GET['kategori'] ?? 'all';
$search       = trim($_GET['q'] ?? '');
$page         = max(1, (int) ($_GET['page'] ?? 1));
$perPage      = 10;
$offset       = ($page - 1) * $perPage;

$where  = [];
$params = [];

if (in_array($filterStatus, ['draft', 'published', 'archived'], true)) {
    $where[] = "status = :status";
    $params[':status'] = $filterStatus;
}

if (in_array($filterKat, ['destinasi', 'tips', 'budaya', 'kuliner', 'event'], true)) {
    $where[] = "kategori = :kategori";
    $params[':kategori'] = $filterKat;
}

if ($search !== '') {
    $where[] = "(judul LIKE :search OR excerpt LIKE :search OR penulis LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// ============================================================
// TOTAL DATA
// ============================================================
$totalData = 0;
try {
    $stat = db_get("SELECT COUNT(*) AS total FROM blog $whereSql", $params);
    $totalData = (int) ($stat['total'] ?? 0);
} catch (PDOException $e) {
    $totalData = 0;
}
$totalPages = max(1, ceil($totalData / $perPage));

// ============================================================
// AMBIL DATA
// ============================================================
$blogList = [];
try {
    $sql = "SELECT * FROM blog $whereSql ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
    $blogList = db_get_all($sql, $params);
} catch (PDOException $e) {
    $blogList = [];
}

// ============================================================
// STATISTIK
// ============================================================
$totalAll       = db_count('blog');
$totalDraft     = db_count('blog', "status = 'draft'");
$totalPublished = db_count('blog', "status = 'published'");
$totalArchived  = db_count('blog', "status = 'archived'");

// ============================================================
// INCLUDE HEADER ADMIN
// ============================================================
include __DIR__ . '/../layout/admin_header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Blog - Admin BPW</title>
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


<!-- STATISTIK -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">

        <a href="blog.php?status=all" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'all' ? 'ring-2 ring-blue-500' : '' ?>">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-400">Semua</div>
                    <div class="text-xl font-bold"><?= $totalAll ?></div>
                </div>
            </div>
        </a>

        <a href="blog.php?status=draft" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'draft' ? 'ring-2 ring-yellow-500' : '' ?>">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">
                    <i class="fa-solid fa-pen"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-400">Draft</div>
                    <div class="text-xl font-bold"><?= $totalDraft ?></div>
                </div>
            </div>
        </a>

        <a href="blog.php?status=published" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'published' ? 'ring-2 ring-green-500' : '' ?>">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-400">Published</div>
                    <div class="text-xl font-bold"><?= $totalPublished ?></div>
                </div>
            </div>
        </a>

        <a href="blog.php?status=archived" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'archived' ? 'ring-2 ring-gray-500' : '' ?>">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center text-gray-600">
                    <i class="fa-solid fa-box-archive"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-400">Archived</div>
                    <div class="text-xl font-bold"><?= $totalArchived ?></div>
                </div>
            </div>
        </a>

    </div>
</section>


<!-- LIST BLOG -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6 pb-16">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Toolbar -->
        <div class="p-4 border-b space-y-4">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fa-solid fa-newspaper text-blue-600"></i>
                    <?= $filterStatus === 'all' ? 'Semua Artikel' : 'Artikel: <span class="capitalize">' . htmlspecialchars($filterStatus) . '</span>' ?>
                    <span class="text-xs font-normal text-gray-400">(<?= $totalData ?> data)</span>
                </h2>

                <a href="blog/tambah.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Tulis Artikel
                </a>
            </div>

            <form method="GET" action="blog.php" class="flex flex-wrap gap-2 items-center">
                <?php if ($filterStatus !== 'all'): ?>
                    <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
                <?php endif; ?>

                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                               placeholder="Cari judul, penulis..."
                               class="w-full pl-9 pr-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <select name="kategori" class="px-3 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="all" <?= $filterKat === 'all' ? 'selected' : '' ?>>Semua Kategori</option>
                    <option value="destinasi" <?= $filterKat === 'destinasi' ? 'selected' : '' ?>>Destinasi</option>
                    <option value="tips" <?= $filterKat === 'tips' ? 'selected' : '' ?>>Tips Travel</option>
                    <option value="budaya" <?= $filterKat === 'budaya' ? 'selected' : '' ?>>Budaya</option>
                    <option value="kuliner" <?= $filterKat === 'kuliner' ? 'selected' : '' ?>>Kuliner</option>
                    <option value="event" <?= $filterKat === 'event' ? 'selected' : '' ?>>Event</option>
                </select>

                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-xs font-semibold transition">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
            </form>
        </div>


        <?php if (empty($blogList)): ?>

            <div class="p-12 text-center">
                <i class="fa-regular fa-folder-open text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-400 text-sm">Belum ada artikel.</p>
                <a href="blog/tambah.php" class="inline-flex items-center gap-2 mt-4 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-xs font-semibold transition">
                    <i class="fa-solid fa-plus"></i> Tulis Artikel Pertama
                </a>
            </div>

        <?php else: ?>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-gray-100">
                <?php foreach ($blogList as $b): ?>
                    <div class="p-4 space-y-3">
                        <div class="flex items-start gap-3">
                            <img src="../<?= htmlspecialchars($b['gambar_utama'] ?: 'uploads/blog/placeholder.jpg') ?>"
                                 class="w-16 h-16 rounded-lg object-cover bg-gray-100 flex-shrink-0"
                                 onerror="this.src='https://via.placeholder.com/100x100?text=No+Image'" alt="">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-sm line-clamp-2"><?= htmlspecialchars($b['judul']) ?></h3>
                                <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-400 flex-wrap">
                                    <span class="bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded-full capitalize">
                                        <?= htmlspecialchars($b['kategori']) ?>
                                    </span>
                                    <span><i class="fa-regular fa-eye"></i> <?= number_format((int)$b['views']) ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-[10px]">
                            <span class="text-gray-400">
                                <i class="fa-regular fa-user"></i> <?= htmlspecialchars($b['penulis'] ?: 'Admin BPW') ?>
                            </span>
                            <span class="font-bold px-2 py-1 rounded-full
                                <?= $b['status'] === 'published' ? 'bg-green-100 text-green-700' :
                                    ($b['status'] === 'draft' ? 'bg-yellow-100 text-yellow-700' :
                                    'bg-gray-200 text-gray-700') ?>">
                                <?= ucfirst($b['status']) ?>
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-2">
                            <a href="blog/edit.php?id=<?= $b['id'] ?>"
                               class="text-xs bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-md font-semibold transition">
                                <i class="fa-solid fa-pen"></i> Edit
                            </a>

                            <?php if ($b['status'] !== 'published'): ?>
                                <a href="blog/simpan.php?aksi=publish&id=<?= $b['id'] ?>"
                                   onclick="return confirm('Publish artikel ini?')"
                                   class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md font-semibold transition">
                                    <i class="fa-solid fa-check"></i> Publish
                                </a>
                            <?php endif; ?>

                            <a href="blog/hapus.php?id=<?= $b['id'] ?>"
                               onclick="return confirm('Yakin hapus artikel ini?')"
                               class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md font-semibold transition">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="text-left py-3 px-4">Artikel</th>
                            <th class="text-left py-3 px-4">Kategori</th>
                            <th class="text-left py-3 px-4">Penulis</th>
                            <th class="text-center py-3 px-4">Views</th>
                            <th class="text-center py-3 px-4">Status</th>
                            <th class="text-left py-3 px-4">Tanggal</th>
                            <th class="text-right py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($blogList as $b): ?>
                            <tr class="hover:bg-slate-50 align-top">
                                <td class="py-3 px-4">
                                    <div class="flex items-start gap-3">
                                        <img src="../<?= htmlspecialchars($b['gambar_utama'] ?: 'uploads/blog/placeholder.jpg') ?>"
                                             class="w-12 h-12 rounded-lg object-cover bg-gray-100 flex-shrink-0"
                                             onerror="this.src='https://via.placeholder.com/100x100?text=No+Image'" alt="">
                                        <div class="max-w-xs">
                                            <div class="font-semibold text-gray-800 line-clamp-2">
                                                <?= htmlspecialchars($b['judul']) ?>
                                            </div>
                                            <div class="text-[11px] text-gray-400 mt-0.5">
                                                <?= htmlspecialchars(mb_substr($b['excerpt'] ?: strip_tags($b['konten']), 0, 60)) ?>...
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-[10px] bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-semibold capitalize">
                                        <?= htmlspecialchars($b['kategori']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-600 text-xs">
                                    <?= htmlspecialchars($b['penulis'] ?: 'Admin BPW') ?>
                                </td>
                                <td class="py-3 px-4 text-gray-600 text-xs text-center">
                                    <i class="fa-regular fa-eye text-gray-400"></i>
                                    <?= number_format((int)$b['views']) ?>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-full whitespace-nowrap
                                        <?= $b['status'] === 'published' ? 'bg-green-100 text-green-700' :
                                            ($b['status'] === 'draft' ? 'bg-yellow-100 text-yellow-700' :
                                            'bg-gray-200 text-gray-700') ?>">
                                        <?= ucfirst($b['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-500 text-xs whitespace-nowrap">
                                    <?= date('d M Y', strtotime($b['created_at'])) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex justify-end gap-1 flex-wrap">

                                        <!-- Edit -->
                                        <a href="blog/edit.php?id=<?= $b['id'] ?>" title="Edit"
                                           class="w-8 h-8 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition flex items-center justify-center">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>

                                        <!-- Lihat -->
                                        <a href="../detail-blog.php?slug=<?= urlencode($b['slug']) ?>" target="_blank"
                                           title="Lihat"
                                           class="w-8 h-8 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition flex items-center justify-center">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>

                                        <!-- Publish / Draft -->
                                        <?php if ($b['status'] !== 'published'): ?>
                                            <a href="blog/simpan.php?aksi=publish&id=<?= $b['id'] ?>"
                                               onclick="return confirm('Publish artikel ini?')"
                                               title="Publish"
                                               class="w-8 h-8 bg-green-500 hover:bg-green-600 text-white rounded-md transition flex items-center justify-center">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="blog/simpan.php?aksi=draft&id=<?= $b['id'] ?>"
                                               onclick="return confirm('Kembalikan ke draft?')"
                                               title="Kembalikan ke Draft"
                                               class="w-8 h-8 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition flex items-center justify-center">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Hapus -->
                                        <a href="blog/hapus.php?id=<?= $b['id'] ?>"
                                           onclick="return confirm('Yakin hapus artikel ini?\n\nTindakan ini tidak bisa dibatalkan.')"
                                           title="Hapus"
                                           class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition flex items-center justify-center">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </a>

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
                    if ($filterKat !== 'all')    $queryBase['kategori'] = $filterKat;
                    if ($search !== '')          $queryBase['q'] = $search;
                    ?>

                    <?php if ($page > 1): ?>
                        <a href="blog.php?<?= http_build_query(array_merge($queryBase, ['page' => $page - 1])) ?>" class="pagination-btn">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </a>
                    <?php else: ?>
                        <span class="pagination-btn disabled"><i class="fa-solid fa-chevron-left text-xs"></i></span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="blog.php?<?= http_build_query(array_merge($queryBase, ['page' => $i])) ?>"
                           class="pagination-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="blog.php?<?= http_build_query(array_merge($queryBase, ['page' => $page + 1])) ?>" class="pagination-btn">
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

</body>
</html>