<?php
/**
 * ============================================================
 * LAYOUT - ADMIN HEADER
 * Bayu Prima Wisata
 * ============================================================
 */

// Pastikan session dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// LOAD DATABASE (untuk notifikasi)
// ============================================================
if (!function_exists('db_get')) {
    require_once __DIR__ . '/../config/database.php';
}

// ============================================================
// CEK AUTENTIKASI LOGIN
// ============================================================
$current_file = basename($_SERVER['PHP_SELF']);
if ($current_file !== 'login.php' && !isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// ============================================================
// FUNGSI JUDUL HALAMAN OTOMATIS
// ============================================================
function getPageTitle($page) {
    $titles = [
        'index.php'       => 'Dashboard',
        'paket.php'       => 'Kelola Paket Wisata',
        'destinasi.php'   => 'Kelola Destinasi',
        'layanan.php'     => 'Kelola Layanan',
        'hotel.php'       => 'Kelola Hotel',
        'testimoni.php'   => 'Kelola Testimoni',
        'galeri.php'      => 'Kelola Galeri',
        'blog.php'        => 'Kelola Blog',
        'promo.php'       => 'Kelola Promo',
        'pemesanan.php'   => 'Data Pemesanan',
        'pesan-masuk.php' => 'Pesan Masuk',
        'pengaturan.php'  => 'Pengaturan Website',
        'profile.php'     => 'Profil Saya',
        'kelola-admin.php'=> 'Kelola Admin',
    ];
    return $titles[$page] ?? 'Admin Panel';
}

$page_title = getPageTitle($current_file);

// ============================================================
// AMBIL DATA ADMIN YANG LOGIN
// ============================================================
$adminId       = (int) ($_SESSION['admin_id'] ?? 0);
$adminUsername = $_SESSION['admin_username']  ?? 'Administrator';
$adminEmail    = $_SESSION['admin_email']     ?? 'admin@bpw.com';
$adminRole     = $_SESSION['admin_role']      ?? 'admin';
$adminFullName = $_SESSION['admin_full_name'] ?? $adminUsername;
$adminInitial  = strtoupper(substr($adminFullName, 0, 1));

// ============================================================
// AMBIL AVATAR DARI SESSION + VERIFIKASI FILE
// ============================================================
$avatarSession = $_SESSION['admin_avatar'] ?? null;
$avatarUrl = null;

if (!empty($avatarSession)) {
    $avatarFile = __DIR__ . '/../' . $avatarSession;
    if (file_exists($avatarFile)) {
        // Cache buster pakai timestamp file
        $avatarUrl = '../' . $avatarSession . '?v=' . filemtime($avatarFile);
    }
}

// Fallback: kalau session kosong, cek DB langsung
if (!$avatarUrl && $adminId > 0) {
    try {
        $dbUser = db_get("SELECT avatar FROM users WHERE id = :id", ['id' => $adminId]);
        if (!empty($dbUser['avatar'])) {
            $avatarFile = __DIR__ . '/../' . $dbUser['avatar'];
            if (file_exists($avatarFile)) {
                $_SESSION['admin_avatar'] = $dbUser['avatar'];
                $avatarUrl = '../' . $dbUser['avatar'] . '?v=' . filemtime($avatarFile);
            }
        }
    } catch (PDOException $e) {
        // silent
    }
}

// Label role (buat tampilan)
$roleLabel = [
    'super_admin' => 'Super Admin',
    'admin'       => 'Admin',
    'editor'      => 'Editor',
][$adminRole] ?? ucfirst($adminRole);

// ============================================================
// AMBIL JUMLAH PESAN BELUM DIBACA (untuk badge & dropdown)
// ============================================================
$jumlahPesanBelumDibaca = 0;
$pesanTerbaru = [];

try {
    if (function_exists('hitung_pesan_belum_dibaca')) {
        $jumlahPesanBelumDibaca = hitung_pesan_belum_dibaca();
    } else {
        $row = db_get("SELECT COUNT(*) AS total FROM pesan_masuk WHERE is_read = 0");
        $jumlahPesanBelumDibaca = (int) ($row['total'] ?? 0);
    }

    $pesanTerbaru = db_get_all("
        SELECT id, tipe, judul, pengirim, created_at, is_read
        FROM pesan_masuk
        ORDER BY is_read ASC, created_at DESC
        LIMIT 5
    ");
} catch (PDOException $e) {
    $jumlahPesanBelumDibaca = 0;
    $pesanTerbaru = [];
}

// ============================================================
// ARRAY MENU NAVIGATION
// ============================================================
$menu_items = [
    'Dashboard' => [
        'icon'  => 'fa-chart-line',
        'url'   => 'index.php',
        'pages' => ['index.php']
    ],
    'Paket Wisata' => [
        'icon'  => 'fa-suitcase',
        'url'   => 'paket.php',
        'pages' => ['paket.php']
    ],
    'Layanan' => [
        'icon'  => 'fa-bell-concierge',
        'url'   => 'layanan.php',
        'pages' => ['layanan.php']
    ],
    'Destinasi' => [
        'icon'  => 'fa-map-location-dot',
        'url'   => 'destinasi.php',
        'pages' => ['destinasi.php']
    ],
    'Hotel' => [
        'icon'  => 'fa-hotel',
        'url'   => 'hotel.php',
        'pages' => ['hotel.php']
    ],
    'Testimoni' => [
        'icon'  => 'fa-star',
        'url'   => 'testimoni.php',
        'pages' => ['testimoni.php']
    ],
    'Galeri' => [
        'icon'  => 'fa-images',
        'url'   => 'galeri.php',
        'pages' => ['galeri.php']
    ],
    'Blog' => [
        'icon'  => 'fa-newspaper',
        'url'   => 'blog.php',
        'pages' => ['blog.php']
    ],
    'Promo' => [
        'icon'  => 'fa-tag',
        'url'   => 'promo.php',
        'pages' => ['promo.php']
    ],
    'Pemesanan' => [
        'icon'  => 'fa-cart-shopping',
        'url'   => 'pemesanan.php',
        'pages' => ['pemesanan.php']
    ],
    'Pesan Masuk' => [
        'icon'  => 'fa-envelope',
        'url'   => 'pesan-masuk.php',
        'pages' => ['pesan-masuk.php']
    ],
    'Pengaturan' => [
        'icon'  => 'fa-gear',
        'url'   => 'pengaturan.php',
        'pages' => ['pengaturan.php']
    ],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($page_title); ?> - Admin BPW</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Poppins', sans-serif; }

        .sidebar {
            background: linear-gradient(180deg, #003366 0%, #001a33 100%);
            transition: transform 0.3s ease-in-out;
        }

        .nav-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(255, 255, 255, 0.12);
            border-left-color: #06b6d4;
        }

        .nav-item.active i { color: #06b6d4; }
        .nav-item.active span { color: #ffffff; font-weight: 500; }

        .sidebar-logo {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 8px;
            background: #ffffff;
            padding: 2px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .dropdown-menu {
            animation: slideDown 0.2s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        #searchModal.show {
            display: flex;
            animation: fadeIn 0.15s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-slate-100 text-slate-800 antialiased min-h-screen">

<!-- Overlay Mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden transition-opacity duration-300"></div>

<!-- MAIN WRAPPER -->
<div class="flex min-h-screen w-full relative">

    <!-- ============================================================
         SIDEBAR
    ============================================================ -->
    <aside id="sidebar" class="sidebar fixed md:static inset-y-0 left-0 z-50 w-72 text-white flex flex-col flex-shrink-0 -translate-x-full md:translate-x-0 shadow-xl">

        <!-- Logo Header -->
        <div class="p-5 border-b border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">

                <img src="../images/logo.jpg"
                     onerror="this.src='images/logo.jpg'"
                     alt="Logo BPW"
                     class="sidebar-logo">

                <div>
                    <h1 class="font-bold text-lg tracking-wide leading-tight">BPW Admin</h1>
                    <p class="text-[11px] text-blue-300">Bayu Prima Wisata</p>
                </div>
            </div>

            <button id="closeSidebar" class="md:hidden text-slate-300 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- User Profile Info (SIDEBAR) — DENGAN AVATAR DINAMIS -->
        <div class="p-4 border-b border-white/10 bg-white/5 flex items-center gap-3">

            <?php if ($avatarUrl): ?>
                <img src="<?= htmlspecialchars($avatarUrl) ?>"
                     class="w-10 h-10 rounded-full object-cover shadow-lg shrink-0 border-2 border-white/20"
                     alt="Avatar">
            <?php else: ?>
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center shadow-lg shrink-0 font-bold text-white">
                    <?= $adminInitial; ?>
                </div>
            <?php endif; ?>

            <div class="min-w-0">
                <p class="text-sm font-semibold truncate"><?= htmlspecialchars($adminFullName); ?></p>
                <p class="text-[11px] text-blue-300"><?= htmlspecialchars($roleLabel); ?></p>
            </div>
        </div>

        <!-- Nav Menu List -->
        <nav class="p-3 flex-1 overflow-y-auto space-y-1">
            <div class="text-[10px] text-blue-300 font-semibold uppercase tracking-wider px-3 py-2">
                MAIN MENU
            </div>

            <?php foreach ($menu_items as $label => $item): ?>
                <?php $isActive = in_array($current_file, $item['pages']); ?>
                <a href="<?= $item['url']; ?>"
                   class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition text-slate-200 <?= $isActive ? 'active' : ''; ?>">
                    <i class="fa-solid <?= $item['icon']; ?> w-5 text-center <?= $isActive ? 'text-cyan-400' : 'text-blue-300'; ?>"></i>
                    <span><?= $label; ?></span>

                    <?php if ($label === 'Pesan Masuk' && $jumlahPesanBelumDibaca > 0): ?>
                        <span class="ml-auto bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-semibold">
                            <?= $jumlahPesanBelumDibaca; ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>

            <div class="text-[10px] text-blue-300 font-semibold uppercase tracking-wider px-3 py-2 pt-4">
                SYSTEM
            </div>

            <a href="logout.php"
               onclick="return confirm('Yakin ingin logout?')"
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-300 hover:text-red-100 hover:bg-red-500/10 transition">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                <span>Logout</span>
            </a>
        </nav>

        <div class="p-4 border-t border-white/10 text-center text-[10px] text-blue-300/70">
            <p>Bayu Prima Wisata &copy; <?= date('Y'); ?></p>
        </div>
    </aside>

    <!-- ============================================================
         CONTENT WRAPPER
    ============================================================ -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen">

        <!-- TOP NAVBAR -->
        <header class="bg-white shadow-sm sticky top-0 z-30 px-4 md:px-6 py-3 flex items-center justify-between border-b border-slate-200">
            <div class="flex items-center gap-3">
                <button id="openSidebar" class="md:hidden p-2 text-slate-600 hover:text-blue-600 rounded-lg hover:bg-slate-100 transition">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <div>
                    <h1 class="text-base md:text-lg font-bold text-slate-800 leading-tight">
                        <?= htmlspecialchars($page_title); ?>
                    </h1>
                    <p class="text-[11px] text-slate-400 hidden sm:block">
                        <?= date('l, d F Y'); ?>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 md:gap-3">

                <!-- SEARCH BUTTON -->
                <button id="searchBtn"
                        class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-blue-600 transition"
                        title="Cari (Ctrl+K)">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </button>

                <!-- NOTIFICATION DROPDOWN -->
                <div class="relative">
                    <button id="notifDropdownBtn"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition relative"
                            title="Notifikasi">
                        <i class="fa-regular fa-bell text-base"></i>
                        <?php if ($jumlahPesanBelumDibaca > 0): ?>
                            <span class="absolute top-1 right-1 min-w-[16px] h-[16px] bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center px-1">
                                <?= $jumlahPesanBelumDibaca > 9 ? '9+' : $jumlahPesanBelumDibaca; ?>
                            </span>
                        <?php endif; ?>
                    </button>

                    <!-- Dropdown Notifikasi -->
                    <div id="notifDropdownMenu"
                         class="dropdown-menu hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-slate-100 py-2 z-50">

                        <div class="flex items-center justify-between px-4 py-2 border-b border-slate-100">
                            <h4 class="font-bold text-sm text-slate-800">Notifikasi</h4>
                            <?php if ($jumlahPesanBelumDibaca > 0): ?>
                                <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold">
                                    <?= $jumlahPesanBelumDibaca ?> baru
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="max-h-80 overflow-y-auto">
                            <?php if (!empty($pesanTerbaru)): ?>
                                <?php
                                $iconMap = [
                                    'testimoni'  => ['fa-comment-dots', 'bg-yellow-100 text-yellow-600'],
                                    'blog'       => ['fa-newspaper',    'bg-blue-100 text-blue-600'],
                                    'kontak'     => ['fa-envelope',     'bg-purple-100 text-purple-600'],
                                    'newsletter' => ['fa-bell',         'bg-pink-100 text-pink-600'],
                                    'review'     => ['fa-star',         'bg-orange-100 text-orange-600'],
                                    'reservasi'  => ['fa-cart-shopping','bg-emerald-100 text-emerald-600'],
                                    'sistem'     => ['fa-info-circle',  'bg-gray-100 text-gray-600'],
                                ];
                                foreach ($pesanTerbaru as $p):
                                    $ico = $iconMap[$p['tipe']] ?? ['fa-bell', 'bg-gray-100 text-gray-600'];
                                ?>
                                    <a href="pesan/detail.php?id=<?= $p['id'] ?>"
                                       class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition border-b border-slate-50 last:border-0 <?= !$p['is_read'] ? 'bg-blue-50/40' : '' ?>">

                                        <div class="w-8 h-8 rounded-lg <?= $ico[1] ?> flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid <?= $ico[0] ?> text-xs"></i>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-slate-800 line-clamp-2">
                                                <?= htmlspecialchars($p['judul']) ?>
                                            </p>
                                            <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-400">
                                                <?php if (!empty($p['pengirim'])): ?>
                                                    <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars($p['pengirim']) ?></span>
                                                <?php endif; ?>
                                                <span>
                                                    <?php
                                                    $ts   = strtotime($p['created_at']);
                                                    $diff = time() - $ts;
                                                    if ($diff < 60)         echo 'Baru saja';
                                                    elseif ($diff < 3600)   echo floor($diff / 60) . ' mnt lalu';
                                                    elseif ($diff < 86400)  echo floor($diff / 3600) . ' jam lalu';
                                                    elseif ($diff < 604800) echo floor($diff / 86400) . ' hari lalu';
                                                    else                    echo date('d M Y', $ts);
                                                    ?>
                                                </span>
                                            </div>
                                        </div>

                                        <?php if (!$p['is_read']): ?>
                                            <span class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 flex-shrink-0"></span>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="px-4 py-8 text-center">
                                    <i class="fa-regular fa-bell-slash text-2xl text-slate-300 mb-2"></i>
                                    <p class="text-xs text-slate-400">Belum ada notifikasi</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="px-4 py-2 border-t border-slate-100 text-center">
                            <a href="pesan-masuk.php" class="text-[11px] text-blue-600 hover:text-blue-700 font-semibold">
                                Lihat Semua Pesan <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </div>

                    </div>
                </div>

                <div class="h-6 w-px bg-slate-200 my-auto hidden sm:block"></div>

                <!-- USER PROFILE DROPDOWN — DENGAN AVATAR DINAMIS -->
                <div class="relative">
                    <button id="userDropdownBtn" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition">

                        <?php if ($avatarUrl): ?>
                            <img src="<?= htmlspecialchars($avatarUrl) ?>"
                                 class="w-7 h-7 rounded-full object-cover border border-slate-200"
                                 alt="Avatar">
                        <?php else: ?>
                            <div class="w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                                <?= $adminInitial; ?>
                            </div>
                        <?php endif; ?>

                        <span class="text-xs font-semibold text-slate-700 hidden sm:block">
                            <?= htmlspecialchars($adminUsername); ?>
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 hidden sm:block"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="userDropdownMenu" class="dropdown-menu hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl border border-slate-100 py-1 z-50">

                        <!-- User Info — DENGAN AVATAR DINAMIS -->
                        <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-3">
                            <?php if ($avatarUrl): ?>
                                <img src="<?= htmlspecialchars($avatarUrl) ?>"
                                     class="w-10 h-10 rounded-full object-cover border border-slate-200"
                                     alt="Avatar">
                            <?php else: ?>
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center text-white font-bold">
                                    <?= $adminInitial; ?>
                                </div>
                            <?php endif; ?>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-800 truncate"><?= htmlspecialchars($adminFullName); ?></p>
                                <p class="text-[11px] text-slate-400 truncate"><?= htmlspecialchars($adminEmail); ?></p>
                            </div>
                        </div>

                        <a href="profile.php" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 transition">
                            <i class="fa-regular fa-user w-4 text-slate-400"></i> Profil Saya
                        </a>
                        <a href="pengaturan.php" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 transition">
                            <i class="fa-solid fa-gear w-4 text-slate-400"></i> Pengaturan
                        </a>
                        <a href="pesan-masuk.php" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 transition">
                            <i class="fa-solid fa-inbox w-4 text-slate-400"></i> Pesan Masuk
                            <?php if ($jumlahPesanBelumDibaca > 0): ?>
                                <span class="ml-auto bg-red-500 text-white text-[9px] px-1.5 py-0.5 rounded-full font-bold">
                                    <?= $jumlahPesanBelumDibaca ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <hr class="my-1 border-slate-100">
                        <a href="logout.php" onclick="return confirm('Yakin ingin logout?')"
                           class="flex items-center gap-2.5 px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition">
                            <i class="fa-solid fa-right-from-bracket w-4"></i> Logout
                        </a>
                    </div>
                </div>

            </div>
        </header>

        <!-- ============================================================
             MAIN CONTENT CONTAINER (DIBUKA — DITUTUP DI admin_footer.php)
        ============================================================ -->
        <main class="p-4 md:p-6 flex-1 overflow-y-auto">


<!-- ============================================================
     SEARCH MODAL (GLOBAL)
============================================================ -->
<div id="searchModal"
     class="hidden fixed inset-0 z-[9999] items-center justify-center p-4"
     style="background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">

        <div class="flex items-center gap-3 p-4 border-b border-slate-100">
            <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
            <input type="text" id="globalSearchInput"
                   placeholder="Cari halaman, paket, destinasi, pesanan..."
                   class="flex-1 text-sm focus:outline-none placeholder-slate-400">
            <button onclick="closeSearchModal()"
                    class="text-slate-400 hover:text-slate-600 text-xs px-2 py-1 rounded border border-slate-200">
                ESC
            </button>
        </div>

        <div class="p-4">
            <p class="text-[10px] text-slate-400 uppercase tracking-wider mb-2 font-semibold">
                Menu Cepat
            </p>
            <div class="grid grid-cols-2 gap-2" id="quickLinks">
                <a href="index.php" class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 transition text-xs text-slate-700">
                    <i class="fa-solid fa-chart-line text-blue-500 w-5 text-center"></i> Dashboard
                </a>
                <a href="paket.php" class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 transition text-xs text-slate-700">
                    <i class="fa-solid fa-suitcase text-blue-500 w-5 text-center"></i> Paket Wisata
                </a>
                <a href="destinasi.php" class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 transition text-xs text-slate-700">
                    <i class="fa-solid fa-map-location-dot text-blue-500 w-5 text-center"></i> Destinasi
                </a>
                <a href="layanan.php" class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 transition text-xs text-slate-700">
                    <i class="fa-solid fa-bell-concierge text-blue-500 w-5 text-center"></i> Layanan
                </a>
                <a href="promo.php" class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 transition text-xs text-slate-700">
                    <i class="fa-solid fa-tag text-blue-500 w-5 text-center"></i> Promo
                </a>
                <a href="blog.php" class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 transition text-xs text-slate-700">
                    <i class="fa-solid fa-newspaper text-blue-500 w-5 text-center"></i> Blog
                </a>
                <a href="pemesanan.php" class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 transition text-xs text-slate-700">
                    <i class="fa-solid fa-cart-shopping text-blue-500 w-5 text-center"></i> Pemesanan
                </a>
                <a href="pesan-masuk.php" class="flex items-center gap-2 p-2 rounded-lg hover:bg-slate-50 transition text-xs text-slate-700">
                    <i class="fa-solid fa-inbox text-blue-500 w-5 text-center"></i> Pesan Masuk
                </a>
            </div>
        </div>

        <div class="px-4 py-2 border-t border-slate-100 text-[10px] text-slate-400 text-center">
            <kbd class="px-1.5 py-0.5 bg-slate-100 rounded">Ctrl</kbd> + <kbd class="px-1.5 py-0.5 bg-slate-100 rounded">K</kbd> untuk membuka
        </div>

    </div>
</div>


<!-- ============================================================
     JAVASCRIPT GLOBAL (Sidebar, Dropdowns, Search, Shortcuts)
============================================================ -->
<script>
(function() {
    'use strict';

    // SIDEBAR TOGGLE
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const openBtn = document.getElementById('openSidebar');
    const closeBtn = document.getElementById('closeSidebar');

    function openSidebar() {
        sidebar?.classList.remove('-translate-x-full');
        overlay?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar?.classList.add('-translate-x-full');
        overlay?.classList.add('hidden');
        document.body.style.overflow = '';
    }

    openBtn?.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    // NOTIFICATION DROPDOWN
    const notifBtn = document.getElementById('notifDropdownBtn');
    const notifMenu = document.getElementById('notifDropdownMenu');

    notifBtn?.addEventListener('click', function(e) {
        e.stopPropagation();
        notifMenu?.classList.toggle('hidden');
        userMenu?.classList.add('hidden');
    });

    // USER DROPDOWN
    const userBtn = document.getElementById('userDropdownBtn');
    const userMenu = document.getElementById('userDropdownMenu');

    userBtn?.addEventListener('click', function(e) {
        e.stopPropagation();
        userMenu?.classList.toggle('hidden');
        notifMenu?.classList.add('hidden');
    });

    // TUTUP SEMUA DROPDOWN SAAT KLIK DI LUAR
    document.addEventListener('click', function(e) {
        if (notifMenu && !notifMenu.contains(e.target) && e.target !== notifBtn) {
            notifMenu.classList.add('hidden');
        }
        if (userMenu && !userMenu.contains(e.target) && e.target !== userBtn) {
            userMenu.classList.add('hidden');
        }
    });

    // SEARCH MODAL
    const searchBtn   = document.getElementById('searchBtn');
    const searchModal = document.getElementById('searchModal');
    const searchInput = document.getElementById('globalSearchInput');

    window.openSearchModal = function() {
        searchModal?.classList.remove('hidden');
        searchModal?.classList.add('show');
        setTimeout(() => searchInput?.focus(), 50);
        document.body.style.overflow = 'hidden';
    };

    window.closeSearchModal = function() {
        searchModal?.classList.add('hidden');
        searchModal?.classList.remove('show');
        if (searchInput) searchInput.value = '';
        document.body.style.overflow = '';
    };

    searchBtn?.addEventListener('click', openSearchModal);

    searchModal?.addEventListener('click', function(e) {
        if (e.target === this) closeSearchModal();
    });

    searchInput?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const q = this.value.trim();
            if (q.length > 0) {
                window.location.href = 'pencarian.php?q=' + encodeURIComponent(q);
            }
        }
    });

    // KEYBOARD SHORTCUTS
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openSearchModal();
        }
        if (e.key === 'Escape') {
            closeSearchModal();
            notifMenu?.classList.add('hidden');
            userMenu?.classList.add('hidden');
            closeSidebar();
        }
    });

    // AUTO HIGHLIGHT ACTIVE MENU
    const currentPath = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.nav-item').forEach(item => {
        const href = item.getAttribute('href');
        if (href && href.split('?')[0] === currentPath) {
            item.classList.add('active');
        }
    });

})();
</script>