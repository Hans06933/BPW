<?php
// Pastikan session dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek autentikasi login
$current_file = basename($_SERVER['PHP_SELF']);
if ($current_file !== 'login.php' && !isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Fungsi judul halaman otomatis
function getPageTitle($page) {
    $titles = [
        'index.php'      => 'Dashboard',
        'paket.php'      => 'Kelola Paket Wisata',
        'destinasi.php'  => 'Kelola Destinasi',
        'hotel.php'      => 'Kelola Hotel',
        'testimoni.php'  => 'Kelola Testimoni',
        'galeri.php'     => 'Kelola Galeri',
        'faq.php'        => 'Kelola FAQ',
        'promo.php'      => 'Kelola Promo',
        'blog.php'       => 'Kelola Blog',
        'pengaturan.php' => 'Pengaturan Website',
        'pemesanan.php'  => 'Data Pemesanan',
        'kontak.php'     => 'Pesan Masuk'
    ];
    return $titles[$page] ?? 'Admin Panel';
}

$page_title = getPageTitle($current_file);

// Array Menu Navigation
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
        'url'   => 'kontak.php',
        'pages' => ['kontak.php']
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
        
        /* Smooth Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen">

<!-- Overlay Mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden md:hidden transition-opacity duration-300"></div>

<!-- MAIN WRAPPER -->
<div class="flex min-h-screen w-full relative">

    <!-- SIDEBAR -->
    <aside id="sidebar" class="sidebar fixed md:static inset-y-0 left-0 z-50 w-72 text-white flex flex-col flex-shrink-0 -translate-x-full md:translate-x-0 shadow-xl">
        
        <!-- Logo Header -->
        <div class="p-5 border-b border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="text-cyan-400 text-2xl">
                    <i class="fa-solid fa-plane-departure"></i>
                </div>
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
                <p class="text-sm font-semibold truncate"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Administrator'); ?></p>
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
                    <?php if ($label === 'Pesan Masuk'): ?>
                        <span class="ml-auto bg-red-500 text-white text-[10px] px-2 py-0.5 rounded-full font-semibold">3</span>
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

    <!-- CONTENT WRAPPER -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen">
        
        <!-- TOP NAVBAR -->
        <header class="bg-white shadow-sm sticky top-0 z-30 px-4 md:px-6 py-3 flex items-center justify-between border-b border-slate-200">
            <div class="flex items-center gap-3">
                <!-- Mobile Menu Button -->
                <button id="openSidebar" class="md:hidden p-2 text-slate-600 hover:text-blue-600 rounded-lg hover:bg-slate-100 transition">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                
                <div>
                    <h1 class="text-base md:text-lg font-bold text-slate-800 leading-tight"><?= htmlspecialchars($page_title); ?></h1>
                    <p class="text-[11px] text-slate-400 hidden sm:block"><?= date('l, d F Y'); ?></p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Search Button -->
                <button class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </button>
                
                <!-- Notification Dropdown -->
                <div class="relative">
                    <button id="notifDropdownBtn" class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 transition relative">
                        <i class="fa-regular fa-bell text-base"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                </div>
                
                <div class="h-6 w-px bg-slate-200 my-auto"></div>
                
                <!-- User Profile Dropdown -->
                <div class="relative">
                    <button id="userDropdownBtn" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition">
                        <div class="w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                            <?= strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1)); ?>
                        </div>
                        <span class="text-xs font-semibold text-slate-700 hidden sm:block"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
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

        <!-- MAIN CONTENT CONTAINER -->
        <main class="p-4 md:p-6 flex-1 overflow-y-auto">