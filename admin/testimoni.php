<?php
/**
 * ============================================================
 * ADMIN - KELOLA TESTIMONI
 * Bayu Prima Wisata
 * ============================================================
 */

// ------------------------------------------------------------
// 1. BUFFER OUTPUT (anti "headers already sent")
// ------------------------------------------------------------
ob_start();

session_start();

require_once __DIR__ . '/../config/database.php';

// ------------------------------------------------------------
// 2. PROTEKSI ADMIN (opsional — sesuaikan)
// ------------------------------------------------------------
// if (empty($_SESSION['admin_id'])) {
//     header('Location: login.php');
//     exit;
// }

// ============================================================
// 3. HANDLE AKSI (approve / reject / feature / delete)
//    WAJIB di atas SEBELUM ada output HTML apapun
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'], $_POST['id'])) {

    $id   = (int) $_POST['id'];
    $aksi = $_POST['aksi'];

    try {

        if ($aksi === 'approve') {
            db_update('testimoni', ['status' => 'approved'], 'id', $id);
            $_SESSION['admin_msg'] = 'Testimoni berhasil di-approve.';

        } elseif ($aksi === 'reject') {
            db_update('testimoni', ['status' => 'rejected'], 'id', $id);
            $_SESSION['admin_msg'] = 'Testimoni berhasil di-reject.';

        } elseif ($aksi === 'pending') {
            db_update('testimoni', ['status' => 'pending'], 'id', $id);
            $_SESSION['admin_msg'] = 'Testimoni dikembalikan ke pending.';

        } elseif ($aksi === 'feature') {
            db_update('testimoni', ['is_featured' => 1], 'id', $id);
            $_SESSION['admin_msg'] = 'Testimoni ditandai sebagai unggulan.';

        } elseif ($aksi === 'unfeature') {
            db_update('testimoni', ['is_featured' => 0], 'id', $id);
            $_SESSION['admin_msg'] = 'Status unggulan dihapus.';

        } elseif ($aksi === 'delete') {
            // Hapus file foto jika ada
            $row = db_get("SELECT foto FROM testimoni WHERE id = :id", [':id' => $id]);
            if (!empty($row['foto'])) {
                $filePath = __DIR__ . '/../' . $row['foto'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            db_delete('testimoni', 'id', $id);
            $_SESSION['admin_msg'] = 'Testimoni berhasil dihapus.';
        }

    } catch (PDOException $e) {
        $_SESSION['admin_msg'] = 'Gagal: ' . $e->getMessage();
    }

    header('Location: testimoni.php');
    exit;
}

// ============================================================
// 4. FILTER STATUS
// ============================================================
$filterStatus = $_GET['status'] ?? 'all';

$where  = '';
$params = [];

if (in_array($filterStatus, ['pending', 'approved', 'rejected'], true)) {
    $where = "WHERE status = :status";
    $params[':status'] = $filterStatus;
}

// ============================================================
// 5. AMBIL DATA
// ============================================================
$testimoniList = [];
try {
    $sql = "SELECT * FROM testimoni $where ORDER BY is_featured DESC, created_at DESC";
    $testimoniList = db_get_all($sql, $params);
} catch (PDOException $e) {
    $testimoniList = [];
}

// ============================================================
// 6. STATISTIK
// ============================================================
$totalAll      = db_count('testimoni');
$totalPending  = db_count('testimoni', "status = 'pending'");
$totalApproved = db_count('testimoni', "status = 'approved'");
$totalRejected = db_count('testimoni', "status = 'rejected'");

// ============================================================
// 7. BARU INCLUDE HEADER ADMIN — setelah semua logika selesai
// ============================================================
include __DIR__ . '/../layout/admin_header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Testimoni - Admin BPW</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="bg-slate-50 text-gray-800">

<!-- ============================================================
     NOTIFIKASI
============================================================ -->
<?php if (!empty($_SESSION['admin_msg'])): ?>
    <div id="adminNotif" class="container mx-auto px-4 md:px-6 max-w-7xl mt-4">
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded-md text-sm flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars($_SESSION['admin_msg']) ?>
        </div>
    </div>
    <script>
        setTimeout(() => document.getElementById('adminNotif')?.remove(), 4000);
    </script>
    <?php unset($_SESSION['admin_msg']); ?>
<?php endif; ?>


<!-- ============================================================
     STATISTIK
============================================================ -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">

        <a href="?status=all" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'all' ? 'ring-2 ring-blue-500' : '' ?>">
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

        <a href="?status=pending" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'pending' ? 'ring-2 ring-yellow-500' : '' ?>">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-400">Pending</div>
                    <div class="text-xl font-bold"><?= $totalPending ?></div>
                </div>
            </div>
        </a>

        <a href="?status=approved" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'approved' ? 'ring-2 ring-green-500' : '' ?>">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-green-600">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-400">Approved</div>
                    <div class="text-xl font-bold"><?= $totalApproved ?></div>
                </div>
            </div>
        </a>

        <a href="?status=rejected" class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 hover:shadow-md transition <?= $filterStatus === 'rejected' ? 'ring-2 ring-red-500' : '' ?>">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div>
                    <div class="text-xs text-gray-400">Rejected</div>
                    <div class="text-xl font-bold"><?= $totalRejected ?></div>
                </div>
            </div>
        </a>

    </div>
</section>


<!-- ============================================================
     DAFTAR TESTIMONI
============================================================ -->
<section class="container mx-auto px-4 md:px-6 max-w-7xl mt-6 pb-16">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        <div class="p-4 border-b flex items-center justify-between flex-wrap gap-3">
            <h2 class="font-bold text-gray-800">
                <?php if ($filterStatus === 'all'): ?>
                    Semua Testimoni
                <?php else: ?>
                    Testimoni Status: <span class="capitalize"><?= htmlspecialchars($filterStatus) ?></span>
                <?php endif; ?>
            </h2>
            <span class="text-xs text-gray-400"><?= count($testimoniList) ?> data</span>
        </div>

        <?php if (empty($testimoniList)): ?>
            <div class="p-12 text-center">
                <i class="fa-regular fa-folder-open text-4xl text-gray-300 mb-3"></i>
                <p class="text-gray-400 text-sm">Belum ada testimoni untuk filter ini.</p>
            </div>
        <?php else: ?>

            <!-- Mobile: Card View -->
            <div class="md:hidden divide-y divide-gray-100">
                <?php foreach ($testimoniList as $t): ?>
                    <div class="p-4 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                                <?php if (!empty($t['foto'])): ?>
                                    <img src="../<?= htmlspecialchars($t['foto']) ?>" class="w-full h-full object-cover" alt="">
                                <?php else: ?>
                                    <span class="font-bold text-blue-600"><?= htmlspecialchars(substr($t['nama'], 0, 1)) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-sm"><?= htmlspecialchars($t['nama']) ?></h3>
                                    <?php if ($t['is_featured']): ?>
                                        <i class="fa-solid fa-star text-yellow-500 text-xs" title="Featured"></i>
                                    <?php endif; ?>
                                </div>
                                <p class="text-[10px] text-gray-400">
                                    <?= htmlspecialchars($t['email']) ?>
                                </p>
                                <p class="text-[10px] text-gray-500 mt-0.5">
                                    <?= htmlspecialchars($t['kota_asal'] ?: '-') ?> → <?= htmlspecialchars($t['destinasi']) ?>
                                </p>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-1 rounded-full
                                <?= $t['status'] === 'approved' ? 'bg-green-100 text-green-700' :
                                    ($t['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                                    'bg-red-100 text-red-700') ?>">
                                <?= ucfirst($t['status']) ?>
                            </span>
                        </div>

                        <p class="text-xs text-gray-600 leading-relaxed">
                            <?= htmlspecialchars(substr($t['testimoni'], 0, 120)) ?>...
                        </p>

                        <div class="flex items-center gap-2 text-xs text-gray-400">
                            <span><?= str_repeat('⭐', (int)$t['rating']) ?></span>
                            <span>• <?= htmlspecialchars($t['tipe_perjalanan'] ?? '-') ?></span>
                            <span>• <?= date('d M Y', strtotime($t['created_at'])) ?></span>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-2">
                            <?php if ($t['status'] !== 'approved'): ?>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <input type="hidden" name="aksi" value="approve">
                                    <button class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-md font-semibold transition">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </button>
                                </form>
                            <?php endif; ?>
                            <?php if ($t['status'] !== 'rejected'): ?>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <input type="hidden" name="aksi" value="reject">
                                    <button class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md font-semibold transition">
                                        <i class="fa-solid fa-xmark"></i> Reject
                                    </button>
                                </form>
                            <?php endif; ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <input type="hidden" name="aksi" value="<?= $t['is_featured'] ? 'unfeature' : 'feature' ?>">
                                <button class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-md font-semibold transition">
                                    <i class="fa-solid fa-star"></i> <?= $t['is_featured'] ? 'Unfeature' : 'Feature' ?>
                                </button>
                            </form>
                            <form method="POST" class="inline" onsubmit="return confirm('Hapus testimoni ini?')">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <input type="hidden" name="aksi" value="delete">
                                <button class="text-xs bg-gray-600 hover:bg-gray-700 text-white px-3 py-1.5 rounded-md font-semibold transition">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop: Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="text-left py-3 px-4">Pengirim</th>
                            <th class="text-left py-3 px-4">Destinasi</th>
                            <th class="text-left py-3 px-4">Rating</th>
                            <th class="text-left py-3 px-4">Testimoni</th>
                            <th class="text-left py-3 px-4">Status</th>
                            <th class="text-left py-3 px-4">Tanggal</th>
                            <th class="text-right py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($testimoniList as $t): ?>
                            <tr class="hover:bg-slate-50 align-top">
                                <td class="py-3 px-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center overflow-hidden flex-shrink-0">
                                            <?php if (!empty($t['foto'])): ?>
                                                <img src="../<?= htmlspecialchars($t['foto']) ?>" class="w-full h-full object-cover" alt="">
                                            <?php else: ?>
                                                <span class="font-bold text-blue-600 text-sm"><?= htmlspecialchars(substr($t['nama'], 0, 1)) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800 flex items-center gap-1">
                                                <?= htmlspecialchars($t['nama']) ?>
                                                <?php if ($t['is_featured']): ?>
                                                    <i class="fa-solid fa-star text-yellow-500 text-xs" title="Featured"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-[11px] text-gray-400"><?= htmlspecialchars($t['email']) ?></div>
                                            <div class="text-[11px] text-gray-400"><?= htmlspecialchars($t['kota_asal'] ?: '-') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-600 text-xs">
                                    <?= htmlspecialchars($t['destinasi']) ?><br>
                                    <span class="text-[10px] text-gray-400"><?= htmlspecialchars($t['tipe_perjalanan'] ?? '-') ?></span>
                                </td>
                                <td class="py-3 px-4 text-xs whitespace-nowrap">
                                    <?= str_repeat('⭐', (int)$t['rating']) ?>
                                </td>
                                <td class="py-3 px-4 text-gray-500 text-xs max-w-xs">
                                    <?= htmlspecialchars(substr($t['testimoni'], 0, 100)) ?>...
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-full whitespace-nowrap
                                        <?= $t['status'] === 'approved' ? 'bg-green-100 text-green-700' :
                                            ($t['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                                            'bg-red-100 text-red-700') ?>">
                                        <?= ucfirst($t['status']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-500 text-xs whitespace-nowrap">
                                    <?= date('d M Y', strtotime($t['created_at'])) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex justify-end gap-1 flex-wrap">

                                        <?php if ($t['status'] !== 'approved'): ?>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                                <input type="hidden" name="aksi" value="approve">
                                                <button title="Approve" class="w-8 h-8 bg-green-500 hover:bg-green-600 text-white rounded-md transition flex items-center justify-center">
                                                    <i class="fa-solid fa-check text-xs"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($t['status'] !== 'rejected'): ?>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                                <input type="hidden" name="aksi" value="reject">
                                                <button title="Reject" class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition flex items-center justify-center">
                                                    <i class="fa-solid fa-xmark text-xs"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <form method="POST" class="inline">
                                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                            <input type="hidden" name="aksi" value="<?= $t['is_featured'] ? 'unfeature' : 'feature' ?>">
                                            <button title="<?= $t['is_featured'] ? 'Unfeature' : 'Feature' ?>" class="w-8 h-8 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition flex items-center justify-center">
                                                <i class="fa-solid fa-star text-xs"></i>
                                            </button>
                                        </form>

                                        <form method="POST" class="inline" onsubmit="return confirm('Hapus testimoni ini?')">
                                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                            <input type="hidden" name="aksi" value="delete">
                                            <button title="Hapus" class="w-8 h-8 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition flex items-center justify-center">
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

        <?php endif; ?>

    </div>
</section>

<?php ob_end_flush(); ?>
</body>
</html>