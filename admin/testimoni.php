<?php
require_once '../layout/admin_header.php';
require_once __DIR__ . '/../config/database.php';

// Ambil semua data testimoni
$query = "SELECT * FROM testimoni ORDER BY id DESC";
$testimoni_list = db_get_all($query);

// Statistik Testimoni
$total_testimoni = count($testimoni_list);
$total_aktif     = 0;
$total_pending   = 0;
$total_nonaktif  = 0;

foreach ($testimoni_list as $testi) {
    $status = $testi['status'] ?? '';
    if ($status === 'aktif') $total_aktif++;
    elseif ($status === 'pending') $total_pending++;
    elseif ($status === 'nonaktif') $total_nonaktif++;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <!-- Mencegah zoom out / pinch zoom -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kelola Testimoni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        /* Mencegah scroll horisontal liar di body */
        html, body {
            overflow-x: hidden;
            touch-action: pan-x pan-y;
        }
    </style>
</head>
<body class="bg-slate-100 antialiased">
    <div class="flex min-h-screen w-full">
        
        <!-- SIDEBAR -->
        <?php
        if (file_exists(__DIR__ . '/sidebar.php')) {
            include __DIR__ . '/sidebar.php';
        } elseif (file_exists(__DIR__ . '/../sidebar.php')) {
            include __DIR__ . '/../sidebar.php';
        }
        ?>
        
        <!-- MAIN CONTENT (flex-1 & min-w-0 agar rapat dan tidak overflow) -->
        <main class="flex-1 min-w-0 p-4 md:p-6 overflow-y-auto">
            
            <!-- HEADER -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Testimoni Pelanggan</h1>
                    <p class="text-sm text-slate-500 mt-1">Kelola ulasan dan testimoni yang ditampilkan pada website.</p>
                </div>
                <a href="testimoni/tambah.php" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold transition">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Testimoni
                </a>
            </div>
            
            <!-- STATISTIK -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Total Testimoni</p>
                            <h2 class="text-2xl font-bold text-slate-800 mt-1"><?= $total_testimoni; ?></h2>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-comments text-lg"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Aktif -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Testimoni Aktif</p>
                            <h2 class="text-2xl font-bold text-green-600 mt-1"><?= $total_aktif; ?></h2>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                            <i class="fa-solid fa-circle-check text-lg"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Pending -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Menunggu Moderasi</p>
                            <h2 class="text-2xl font-bold text-amber-600 mt-1"><?= $total_pending; ?></h2>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                            <i class="fa-solid fa-clock text-lg"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Nonaktif -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Testimoni Nonaktif</p>
                            <h2 class="text-2xl font-bold text-slate-600 mt-1"><?= $total_nonaktif; ?></h2>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                            <i class="fa-solid fa-circle-pause text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- TABLE CARD -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Header -->
                <div class="px-5 md:px-6 py-4 border-b border-slate-200">
                    <h2 class="font-bold text-slate-800">Daftar Testimoni Pelanggan</h2>
                    <p class="text-xs text-slate-500 mt-1">Data diambil langsung dari database testimoni.</p>
                </div>
                
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left whitespace-nowrap">#</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Pelanggan</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Rating</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Pesan / Ulasan</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Status</th>
                                <th class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (!empty($testimoni_list)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($testimoni_list as $testi): ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <!-- Nomor -->
                                        <td class="px-4 py-4"><?= $no++; ?></td>
                                        
                                        <!-- Pelanggan -->
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">
                                                <?php if (!empty($testi['foto'])): ?>
                                                    <img src="../images/testimoni/<?= htmlspecialchars($testi['foto']); ?>"
                                                         class="w-12 h-12 object-cover rounded-full shrink-0"
                                                         alt="<?= htmlspecialchars($testi['nama_pelanggan'] ?? ''); ?>"
                                                         onerror="this.style.display='none';">
                                                <?php else: ?>
                                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                                                        <i class="fa-solid fa-user text-slate-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-slate-800"><?= htmlspecialchars($testi['nama_pelanggan'] ?? 'Tanpa Nama'); ?></p>
                                                    <p class="text-xs text-slate-400 truncate max-w-[200px]"><?= htmlspecialchars($testi['pekerjaan'] ?? 'Pelanggan'); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Rating -->
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-1 text-amber-400 text-xs">
                                                <?php 
                                                $rating = (int)($testi['rating'] ?? 5);
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $rating) {
                                                        echo '<i class="fa-solid fa-star"></i>';
                                                    } else {
                                                        echo '<i class="fa-regular fa-star text-slate-300"></i>';
                                                    }
                                                }
                                                ?>
                                                <span class="ml-1 text-slate-600 font-semibold">(<?= $rating; ?>)</span>
                                            </div>
                                        </td>
                                        
                                        <!-- Pesan -->
                                        <td class="px-4 py-4">
                                            <p class="text-slate-700 italic text-xs max-w-xs truncate">
                                                "<?= htmlspecialchars($testi['pesan'] ?? '-'); ?>"
                                            </p>
                                        </td>
                                        
                                        <!-- Status -->
                                        <td class="px-4 py-4">
                                            <?php
                                            $status = $testi['status'] ?? 'nonaktif';
                                            if ($status === 'aktif'):
                                            ?>
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                            <?php elseif ($status === 'pending'): ?>
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Pending</span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Aksi -->
                                        <td class="px-4 py-4">
                                            <div class="flex justify-center gap-2">
                                                <a href="testimoni/edit.php?id=<?= (int)$testi['id']; ?>" 
                                                   title="Edit" 
                                                   class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <a href="testimoni/hapus.php?id=<?= (int)$testi['id']; ?>" 
                                                   title="Hapus" 
                                                   onclick="return confirm('Yakin ingin menghapus testimoni ini?')" 
                                                   class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                        <i class="fa-solid fa-comment-slash text-4xl mb-3"></i>
                                        <p>Belum ada testimoni pelanggan.</p>
                                        <a href="testimoni/tambah.php" class="inline-block mt-3 text-blue-600 font-semibold hover:underline">
                                            Tambah testimoni pertama
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
 
        </main>
    </div>
</body>
</html>