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
        'hotel.php'       => 'Kelola Hotel',
        'testimoni.php'   => 'Kelola Testimoni',
        'galeri.php'      => 'Kelola Galeri',
        'faq.php'         => 'Kelola FAQ',
        'promo.php'       => 'Kelola Promo',
        'blog.php'        => 'Kelola Blog',
        'pengaturan.php'  => 'Pengaturan Website',
        'pemesanan.php'   => 'Data Pemesanan',
        'pesan-masuk.php' => 'Pesan Masuk',
    ];
    return $titles[$page] ?? 'Admin Panel';
}

$page_title = getPageTitle($current_file);

// ============================================================
// AMBIL JUMLAH PESAN BELUM DIBACA (untuk badge & dropdown)
// ============================================================
$jumlahPesanBelumDibaca = 0;
$pesanTerbaru = [];

try {
    $jumlahPesanBelumDibaca = hitung_pesan_belum_dibaca();

    // Ambil 5 pesan terbaru untuk dropdown
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
        'pages' => ['paket.php', 'paket_tambah.php', 'paket_edit.php']
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
        'url'   => '/bpw/admin/hotel.php',
        'pages' => ['hotel.php', 'edit.php']
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
    'FAQ' => [
        'icon'  => 'fa-circle-question',
        'url'   => 'faq.php',
        'pages' => ['faq.php']
    ],
    'Promo' => [
        'icon'  => 'fa-tag',
        'url'   => 'promo.php',
        'pages' => ['promo.php']
    ],
    'Blog' => [
        'icon'  => 'fa-newspaper',
        'url'   => 'blog.php',
        'pages' => ['blog.php']
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
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($page_title); ?> - Admin BPW</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Google Font: Poppins -->
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

        /* Logo Sidebar */
        .sidebar-logo {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 8px;
            background: #ffffff;
            padding: 2px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        /* Smooth Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Notifikasi Dropdown Animation */
        #notifDropdownMenu {
            animation: slideDown 0.2s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
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

                <!-- LOGO DARI images/logo.jpg -->
                <img src="../images/logo.jpg"
                     onerror="this.src='images/logo.jpg'"
                     alt="Logo BPW"
                     class="sidebar-logo">

                <div>
                    <h1 class="font-bold text-lg tracking-wide leading-tight">BPW Admin</h1>
                    <p class="text-[11px] text-blue-300">Bayu Prima Wisata</p>
                </div>
            </div>

            <!-- Close Mobile Button -->
            <button id="closeSidebar" class="md:hidden text-slate-300 hover:text-white p-1">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- User Profile Info -->
        <div class="p-4 border-b border-white/10 bg-white/5 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center shadow-lg shrink-0">
                <i class="fa-solid fa-user text-white text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold truncate">
                    <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Administrator'); ?>
                </p>
                <p class="text-[11px] text-blue-300">Administrator</p>
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
               class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-300 hover:text-red-100 hover:bg-red-500/10 transition">
                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                <span>Logout</span>
            </a>
        </nav>

        <!-- Footer Sidebar -->
        <div class="p-4 border-t border-white/10 text-center text-[10px] text-blue-300/70">
            <p>Bayu Prima Wisata &copy; <?= date('Y'); ?></p>
        </div>
    </aside>

    <!-- ============================================================
         CONTENT WRAPPER
    ============================================================ -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen">

        <!-- ============================================================
             TOP NAVBAR
        ============================================================ -->
        <header class="bg-white shadow-sm sticky top-0 z-30 px-4 md:px-6 py-3 flex items-center justify-between border-b border-slate-200">
            <div class="flex items-center gap-3">
                <!-- Mobile Menu Button -->
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

            <div class="flex items-center gap-3">

                <!-- Search Button -->
                <button class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </button>

                <!-- ============================================================
                     NOTIFICATION DROPDOWN (DINAMIS)
                ============================================================ -->
                <div class="relative">
                    <button id="notifDropdownBtn"
                            class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition relative">
                        <i class="fa-regular fa-bell text-base"></i>
                        <?php if ($jumlahPesanBelumDibaca > 0): ?>
                            <span class="absolute top-1 right-1 min-w-[16px] h-[16px] bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center px-1">
                                <?= $jumlahPesanBelumDibaca > 9 ? '9+' : $jumlahPesanBelumDibaca; ?>
                            </span>
                        <?php endif; ?>
                    </button>

                    <!-- Dropdown Notifikasi -->
                    <div id="notifDropdownMenu"
                         class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50">

                        <!-- Header -->
                        <div class="flex items-center justify-between px-4 py-2 border-b border-slate-100">
                            <h4 class="font-bold text-sm text-slate-800">Notifikasi</h4>
                            <?php if ($jumlahPesanBelumDibaca > 0): ?>
                                <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold">
                                    <?= $jumlahPesanBelumDibaca ?> baru
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- List Pesan -->
                        <div class="max-h-80 overflow-y-auto">
                            <?php if (!empty($pesanTerbaru)): ?>
                                <?php
                                $iconMap = [
                                    'testimoni'  => ['fa-comment-dots', 'bg-yellow-100 text-yellow-600'],
                                    'blog'       => ['fa-newspaper',    'bg-blue-100 text-blue-600'],
                                    'kontak'     => ['fa-envelope',     'bg-purple-100 text-purple-600'],
                                    'newsletter' => ['fa-bell',         'bg-pink-100 text-pink-600'],
                                    'review'     => ['fa-star',         'bg-orange-100 text-orange-600'],
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

                        <!-- Footer -->
                        <div class="px-4 py-2 border-t border-slate-100 text-center">
                            <a href="pesan-masuk.php" class="text-[11px] text-blue-600 hover:text-blue-700 font-semibold">
                                Lihat Semua Pesan <i class="fa-solid fa-arrow-right text-[9px]"></i>
                            </a>
                        </div>

                    </div>
                </div>

                <div class="h-6 w-px bg-slate-200 my-auto"></div>

                <!-- User Profile Dropdown -->
                <div class="relative">
                    <button id="userDropdownBtn" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition">
                        <div class="w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            <?= strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)); ?>
                        </div>
                        <span class="text-xs font-semibold text-slate-700 hidden sm:block">
                            <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
                        </span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 hidden sm:block"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50">
                        <a href="profile.php" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 transition">
                            <i class="fa-regular fa-user w-4 text-slate-400"></i> Profil Saya
                        </a>
                        <a href="pengaturan.php" class="flex items-center gap-2.5 px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 transition">
                            <i class="fa-solid fa-gear w-4 text-slate-400"></i> Pengaturan
                        </a>
                        <hr class="my-1 border-slate-100">
                        <a href="logout.php" class="flex items-center gap-2.5 px-4 py-2 text-xs text-red-600 hover:bg-red-50 transition">
                            <i class="fa-solid fa-right-from-bracket w-4"></i> Logout
                        </a>
                    </div>
                </div>

            </div>
        </header>

        <!-- ============================================================
             MAIN CONTENT CONTAINER
        ============================================================ -->
        <main class="p-4 md:p-6 flex-1 overflow-y-auto">