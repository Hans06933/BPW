<?php
require_once '../layout/admin_header.php';
require_once __DIR__ . '/../config/database.php';

// Ambil semua paket wisata
$query = "SELECT * FROM paket_wisata ORDER BY id DESC";
$paket_list = db_get_all($query);

// Statistik
$total_paket    = count($paket_list);
$total_aktif    = 0;
$total_nonaktif = 0;
$total_habis    = 0;

foreach ($paket_list as $paket) {
    $status = $paket['status'] ?? '';
    if ($status === 'aktif') $total_aktif++;
    elseif ($status === 'nonaktif') $total_nonaktif++;
    elseif ($status === 'habis') $total_habis++;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <!-- Mencegah zoom out / pinch zoom -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kelola Paket Wisata</title>
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
                    <h1 class="text-2xl font-bold text-slate-800">Paket Wisata</h1>
                    <p class="text-sm text-slate-500 mt-1">Kelola paket wisata yang ditampilkan pada website.</p>
                </div>
                <a href="paket/tambah.php" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold transition">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Paket
                </a>
            </div>
            
            <!-- STATISTIK -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Total Paket</p>
                            <h2 class="text-2xl font-bold text-slate-800 mt-1"><?= $total_paket; ?></h2>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-suitcase text-lg"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Aktif -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Paket Aktif</p>
                            <h2 class="text-2xl font-bold text-green-600 mt-1"><?= $total_aktif; ?></h2>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                            <i class="fa-solid fa-circle-check text-lg"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Nonaktif -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Paket Nonaktif</p>
                            <h2 class="text-2xl font-bold text-slate-600 mt-1"><?= $total_nonaktif; ?></h2>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                            <i class="fa-solid fa-circle-pause text-lg"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Habis -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Paket Habis</p>
                            <h2 class="text-2xl font-bold text-red-600 mt-1"><?= $total_habis; ?></h2>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                            <i class="fa-solid fa-circle-xmark text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- TABLE CARD -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Header -->
                <div class="px-5 md:px-6 py-4 border-b border-slate-200">
                    <h2 class="font-bold text-slate-800">Daftar Paket Wisata</h2>
                    <p class="text-xs text-slate-500 mt-1">Data diambil langsung dari database paket_wisata.</p>
                </div>
                
                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left whitespace-nowrap">#</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Paket</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Destinasi</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Durasi</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Harga</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Kuota</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Status</th>
                                <th class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (!empty($paket_list)): ?>
                                <?php $no = 1; ?>
                                <?php foreach ($paket_list as $paket): ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <!-- Nomor -->
                                        <td class="px-4 py-4"><?= $no++; ?></td>
                                        
                                        <!-- Paket -->
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">
                                                <?php if (!empty($paket['gambar_utama'])): ?>
                                                    <!-- Perbaikan tag image & onerror -->
                                                    <img src="../images/paket/<?= htmlspecialchars($paket['gambar_utama']); ?>"
                                                         class="w-16 h-12 object-cover rounded-lg shrink-0"
                                                         alt="<?= htmlspecialchars($paket['nama_paket'] ?? ''); ?>"
                                                         onerror="this.style.display='none';">
                                                <?php else: ?>
                                                    <div class="w-16 h-12 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                                                        <i class="fa-solid fa-image text-slate-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-slate-800"><?= htmlspecialchars($paket['nama_paket'] ?? 'Tanpa Nama'); ?></p>
                                                    <p class="text-xs text-slate-400 truncate max-w-[220px]"><?= htmlspecialchars($paket['slug'] ?? ''); ?></p>
                                                    <div class="flex gap-1 mt-1">
                                                        <?php if (!empty($paket['is_featured'])): ?>
                                                            <span class="text-[9px] bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded">Unggulan</span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($paket['is_flash_sale'])): ?>
                                                            <span class="text-[9px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded">Flash Sale</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <!-- Destinasi -->
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-2 text-slate-700">
                                                <i class="fa-solid fa-location-dot text-blue-500"></i>
                                                <?= htmlspecialchars($paket['destinasi'] ?? '-'); ?>
                                            </div>
                                        </td>
                                        
                                        <!-- Durasi -->
                                        <td class="px-4 py-4"><?= htmlspecialchars($paket['durasi'] ?? '-'); ?></td>
                                        
                                        <!-- Harga -->
                                        <td class="px-4 py-4">
                                            <?php if (!empty($paket['harga_diskon']) && $paket['harga_diskon'] > 0): ?>
                                                <p class="font-bold text-blue-600">Rp <?= number_format($paket['harga_diskon'], 0, ',', '.'); ?></p>
                                                <p class="text-xs text-slate-400 line-through">Rp <?= number_format($paket['harga_normal'] ?? 0, 0, ',', '.'); ?></p>
                                                <?php if (!empty($paket['diskon_persen'])): ?>
                                                    <span class="text-[9px] text-red-500 font-semibold">-<?= (int)$paket['diskon_persen']; ?>%</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <p class="font-bold text-blue-600">Rp <?= number_format($paket['harga_normal'] ?? 0, 0, ',', '.'); ?></p>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Kuota -->
                                        <td class="px-4 py-4">
                                            <p class="font-medium text-slate-700"><?= (int)($paket['tersisa'] ?? 0); ?> / <?= (int)($paket['kuota'] ?? 0); ?></p>
                                            <p class="text-xs text-slate-400">Min. <?= (int)($paket['minimal_peserta'] ?? 2); ?> peserta</p>
                                        </td>
                                        
                                        <!-- Status -->
                                        <td class="px-4 py-4">
                                            <?php
                                            $status = $paket['status'] ?? 'nonaktif';
                                            if ($status === 'aktif'):
                                            ?>
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Aktif</span>
                                            <?php elseif ($status === 'habis'): ?>
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Habis</span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <!-- Aksi -->
                                        <td class="px-4 py-4">
                                            <div class="flex justify-center gap-2">
                                                <a href="../paket/detail.php?slug=<?= urlencode($paket['slug'] ?? ''); ?>" 
                                                   target="_blank" 
                                                   title="Lihat" 
                                                   class="w-9 h-9 flex items-center justify-center rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="paket/edit.php?id=<?= (int)$paket['id']; ?>" 
                                                   title="Edit" 
                                                   class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <a href="paket/hapus.php?id=<?= (int)$paket['id']; ?>" 
                                                   title="Hapus" 
                                                   onclick="return confirm('Yakin ingin menghapus paket ini?')" 
                                                   class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                        <i class="fa-solid fa-suitcase-rolling text-4xl mb-3"></i>
                                        <p>Belum ada paket wisata.</p>
                                        <a href="paket/tambah.php" class="inline-block mt-3 text-blue-600 font-semibold hover:underline">
                                            Tambah paket pertama
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