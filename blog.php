<?php
/**
 * ============================================================
 * HALAMAN BLOG & ARTIKEL WISATA
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/config/database.php';

// ============================================================
// FILTER & PAGINATION
// ============================================================
$kategori     = $_GET['kategori'] ?? 'all';
$search       = trim($_GET['q'] ?? '');
$page         = max(1, (int) ($_GET['page'] ?? 1));
$perPage      = 6;
$offset       = ($page - 1) * $perPage;

$where  = ["status = 'published'"];
$params = [];

if (in_array($kategori, ['destinasi', 'tips', 'budaya', 'kuliner', 'event'], true)) {
    $where[] = "kategori = :kategori";
    $params[':kategori'] = $kategori;
}

if ($search !== '') {
    $where[] = "(judul LIKE :search OR excerpt LIKE :search OR konten LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$whereSql = 'WHERE ' . implode(' AND ', $where);

// ============================================================
// TOTAL DATA (untuk pagination)
// ============================================================
$totalBlog = 0;
try {
    $statTotal = db_get("SELECT COUNT(*) AS total FROM blog $whereSql", $params);
    $totalBlog = (int) ($statTotal['total'] ?? 0);
} catch (PDOException $e) {
    $totalBlog = 0;
}

$totalPages = max(1, ceil($totalBlog / $perPage));

// ============================================================
// AMBIL DATA BLOG
// ============================================================
$blogPosts = [];
try {
    $sql = "
        SELECT
            id, judul, slug, konten, excerpt, kategori,
            gambar_utama, penulis, views, status,
            published_at, created_at
        FROM blog
        $whereSql
        ORDER BY published_at DESC, created_at DESC
        LIMIT $perPage OFFSET $offset
    ";
    $blogPosts = db_get_all($sql, $params);
} catch (PDOException $e) {
    $blogPosts = [];
}

// ============================================================
// POPULAR POSTS (untuk sidebar)
// ============================================================
$popularPosts = [];
try {
    $popularPosts = db_get_all("
        SELECT id, judul, gambar_utama, views, created_at
        FROM blog
        WHERE status = 'published'
        ORDER BY views DESC
        LIMIT 4
    ");
} catch (PDOException $e) {
    $popularPosts = [];
}

// ============================================================
// KATEGORI COUNT (untuk widget)
// ============================================================
$kategoriCount = [
    'destinasi' => 0,
    'tips'      => 0,
    'budaya'    => 0,
    'kuliner'   => 0,
    'event'     => 0,
];

try {
    $rows = db_get_all("
        SELECT kategori, COUNT(*) AS total
        FROM blog
        WHERE status = 'published'
        GROUP BY kategori
    ");
    foreach ($rows as $row) {
        if (isset($kategoriCount[$row['kategori']])) {
            $kategoriCount[$row['kategori']] = (int) $row['total'];
        }
    }
} catch (PDOException $e) {
    // biarkan default 0
}

// ============================================================
// CONVERT KE JSON UNTUK JS (kalau perlu)
// ============================================================
$blogJson = [];
foreach ($blogPosts as $p) {
    $blogJson[] = [
        'id'         => (int) $p['id'],
        'title'      => $p['judul'],
        'slug'       => $p['slug'],
        'category'   => $p['kategori'],
        'image'      => $p['gambar_utama'] ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=800&q=80',
        'excerpt'    => $p['excerpt'] ?: mb_substr(strip_tags($p['konten']), 0, 150) . '...',
        'date'       => $p['published_at'] ? date('Y-m-d', strtotime($p['published_at'])) : date('Y-m-d', strtotime($p['created_at'])),
        'readTime'   => max(1, ceil(str_word_count(strip_tags($p['konten'])) / 200)),
        'views'      => (int) $p['views'],
        'author'     => $p['penulis'] ?: 'Admin BPW',
    ];
}

$popularJson = [];
foreach ($popularPosts as $p) {
    $popularJson[] = [
        'id'       => (int) $p['id'],
        'title'    => $p['judul'],
        'image'    => $p['gambar_utama'] ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=800&q=80',
        'views'    => (int) $p['views'],
        'readTime' => 5,
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Blog & Artikel Wisata - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }

        .bg-blog-hero {
            background: linear-gradient(135deg, rgba(0,51,102,0.88) 0%, rgba(0,76,153,0.75) 100%), url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }

        .blog-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .blog-card:hover { transform: translateY(-6px); box-shadow: 0 20px 25px -12px rgba(0,0,0,0.15); }

        .category-badge { transition: all 0.2s ease; }
        .category-badge.active { background-color: #003366; color: white; border-color: #003366; }

        .widget {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #f0f0f0;
        }

        .popular-item { transition: all 0.2s ease; }
        .popular-item:hover { background-color: #f8fafc; transform: translateX(4px); }

        .scroll-top {
            position: fixed; bottom: 2rem; right: 2rem;
            background: #003366; color: white;
            width: 48px; height: 48px; border-radius: 50%;
            display: none; align-items: center; justify-content: center;
            cursor: pointer; z-index: 99;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s;
        }
        .scroll-top:hover { background: #004c99; transform: translateY(-3px); }

        .search-input:focus { box-shadow: 0 0 0 3px rgba(0,51,102,0.1); }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .pagination-btn {
            min-width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            background: white;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .pagination-btn:hover { background: #f1f5f9; }
        .pagination-btn.active { background: #003366; color: white; border-color: #003366; }
        .pagination-btn.disabled { opacity: 0.4; pointer-events: none; }
    </style>
</head>

<?php include "layout/header.php"; ?>

<body class="bg-slate-50">

<!-- ============================================================
     HERO SECTION
============================================================ -->
<section class="bg-blog-hero py-20 md:py-28 text-white relative">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur rounded-full px-4 py-1.5 mb-5">
                <i class="fa-regular fa-newspaper text-yellow-300 text-sm"></i>
                <span class="text-xs font-bold tracking-wide">BLOG & ARTIKEL</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-4">
                Inspirasi<br>
                <span class="text-yellow-300">Perjalanan Anda</span>
            </h1>
            <p class="text-slate-200 text-base max-w-xl leading-relaxed">
                Temukan tips perjalanan, rekomendasi destinasi, dan cerita seru dari para traveler bersama Bayu Prima Wisata.
            </p>

            <!-- Search Bar -->
            <form method="GET" action="" class="mt-8">
                <div class="bg-white rounded-full p-1.5 flex items-center shadow-lg max-w-md">
                    <div class="flex-1 px-4">
                        <input
                            type="text"
                            name="q"
                            value="<?= htmlspecialchars($search) ?>"
                            placeholder="Cari artikel..."
                            class="w-full py-2 text-sm text-gray-700 focus:outline-none search-input bg-transparent"
                        >
                    </div>
                    <?php if ($kategori !== 'all'): ?>
                        <input type="hidden" name="kategori" value="<?= htmlspecialchars($kategori) ?>">
                    <?php endif; ?>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-full text-sm font-semibold transition flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>


<!-- ============================================================
     MAIN CONTENT
============================================================ -->
<section class="py-10 md:py-12 bg-slate-50">
    <div class="container mx-auto px-4 md:px-6 max-w-7xl">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- BLOG POSTS -->
            <div class="flex-1">

                <!-- Categories -->
                <div class="flex flex-wrap gap-2 mb-8">
                    <a href="?<?= $search ? 'q=' . urlencode($search) . '&' : '' ?>kategori=all"
                       class="category-badge <?= $kategori === 'all' ? 'active' : '' ?> px-4 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                        Semua
                    </a>
                    <a href="?<?= $search ? 'q=' . urlencode($search) . '&' : '' ?>kategori=destinasi"
                       class="category-badge <?= $kategori === 'destinasi' ? 'active' : '' ?> px-4 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                        <i class="fa-solid fa-map-location-dot mr-1"></i> Destinasi
                    </a>
                    <a href="?<?= $search ? 'q=' . urlencode($search) . '&' : '' ?>kategori=tips"
                       class="category-badge <?= $kategori === 'tips' ? 'active' : '' ?> px-4 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                        <i class="fa-solid fa-lightbulb mr-1"></i> Tips Travel
                    </a>
                    <a href="?<?= $search ? 'q=' . urlencode($search) . '&' : '' ?>kategori=budaya"
                       class="category-badge <?= $kategori === 'budaya' ? 'active' : '' ?> px-4 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                        <i class="fa-solid fa-gopuram mr-1"></i> Budaya
                    </a>
                    <a href="?<?= $search ? 'q=' . urlencode($search) . '&' : '' ?>kategori=kuliner"
                       class="category-badge <?= $kategori === 'kuliner' ? 'active' : '' ?> px-4 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                        <i class="fa-solid fa-utensils mr-1"></i> Kuliner
                    </a>
                    <a href="?<?= $search ? 'q=' . urlencode($search) . '&' : '' ?>kategori=event"
                       class="category-badge <?= $kategori === 'event' ? 'active' : '' ?> px-4 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                        <i class="fa-regular fa-calendar mr-1"></i> Event
                    </a>
                </div>

                <?php if (empty($blogPosts)): ?>

                    <!-- No Results -->
                    <div class="text-center py-12 bg-white rounded-2xl border border-gray-100">
                        <i class="fa-regular fa-face-frown text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-400">Belum ada artikel yang ditemukan.</p>
                    </div>

                <?php else: ?>

                    <!-- Featured Post (pertama) -->
                    <?php $featured = $blogPosts[0]; ?>
                    <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition blog-card mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                            <div class="h-64 md:h-auto overflow-hidden">
                                <img src="<?= htmlspecialchars($featured['gambar_utama'] ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=800&q=80') ?>"
                                     class="w-full h-full object-cover"
                                     alt="<?= htmlspecialchars($featured['judul']) ?>">
                            </div>
                            <div class="p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-400 mb-2">
                                        <span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">
                                            <?= strtoupper(htmlspecialchars($featured['kategori'])) ?>
                                        </span>
                                        <span>
                                            <i class="fa-regular fa-calendar"></i>
                                            <?= date('d F Y', strtotime($featured['published_at'] ?? $featured['created_at'])) ?>
                                        </span>
                                    </div>
                                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-3 hover:text-blue-600 transition cursor-pointer">
                                        <a href="detail-blog.php?slug=<?= urlencode($featured['slug']) ?>">
                                            <?= htmlspecialchars($featured['judul']) ?>
                                        </a>
                                    </h2>
                                    <p class="text-gray-500 text-sm leading-relaxed mb-4">
                                        <?= htmlspecialchars($featured['excerpt'] ?: mb_substr(strip_tags($featured['konten']), 0, 120) . '...') ?>
                                    </p>
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t">
                                    <div class="flex items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($featured['penulis'] ?? 'BPW') ?>&background=003366&color=fff"
                                             class="w-8 h-8 rounded-full" alt="">
                                        <span class="text-xs font-medium text-gray-700">
                                            <?= htmlspecialchars($featured['penulis'] ?: 'Admin BPW') ?>
                                        </span>
                                    </div>
                                    <a href="detail-blog.php?slug=<?= urlencode($featured['slug']) ?>"
                                       class="text-blue-600 hover:text-blue-700 text-sm font-semibold flex items-center gap-1">
                                        Baca Selengkapnya <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grid Post Selanjutnya -->
                    <?php $remaining = array_slice($blogPosts, 1); ?>
                    <?php if (!empty($remaining)): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php foreach ($remaining as $post): ?>
                                <div class="blog-card bg-white rounded-xl overflow-hidden shadow-md border border-gray-100">
                                    <div class="relative h-48 overflow-hidden">
                                        <img src="<?= htmlspecialchars($post['gambar_utama'] ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=800&q=80') ?>"
                                             class="w-full h-full object-cover transition duration-300 hover:scale-105"
                                             alt="<?= htmlspecialchars($post['judul']) ?>"
                                             loading="lazy">
                                        <span class="absolute top-3 left-3 bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded-full">
                                            <?= strtoupper(htmlspecialchars($post['kategori'])) ?>
                                        </span>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-center gap-2 text-[10px] text-gray-400 mb-2">
                                            <span>
                                                <i class="fa-regular fa-calendar"></i>
                                                <?= date('d M Y', strtotime($post['published_at'] ?? $post['created_at'])) ?>
                                            </span>
                                            <span>
                                                <i class="fa-regular fa-eye"></i> <?= number_format((int)$post['views']) ?>
                                            </span>
                                        </div>
                                        <h3 class="font-bold text-sm text-gray-800 mb-2 hover:text-blue-600 transition">
                                            <a href="detail-blog.php?slug=<?= urlencode($post['slug']) ?>">
                                                <?= htmlspecialchars($post['judul']) ?>
                                            </a>
                                        </h3>
                                        <p class="text-gray-500 text-[11px] leading-relaxed mb-3">
                                            <?= htmlspecialchars($post['excerpt'] ?: mb_substr(strip_tags($post['konten']), 0, 80) . '...') ?>
                                        </p>
                                        <div class="flex items-center justify-between pt-3 border-t">
                                            <div class="flex items-center gap-1.5">
                                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($post['penulis'] ?? 'BPW') ?>&background=003366&color=fff"
                                                     class="w-5 h-5 rounded-full" alt="">
                                                <span class="text-[10px] text-gray-500">
                                                    <?= htmlspecialchars($post['penulis'] ?: 'Admin BPW') ?>
                                                </span>
                                            </div>
                                            <a href="detail-blog.php?slug=<?= urlencode($post['slug']) ?>"
                                               class="text-blue-600 text-[10px] font-semibold flex items-center gap-1">
                                                Baca <i class="fa-solid fa-arrow-right text-[8px]"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="flex flex-wrap justify-center gap-2 mt-10">
                            <?php
                            $queryBase = [];
                            if ($kategori !== 'all') $queryBase['kategori'] = $kategori;
                            if ($search !== '')      $queryBase['q'] = $search;
                            ?>

                            <!-- Prev -->
                            <?php if ($page > 1): ?>
                                <a href="?<?= http_build_query(array_merge($queryBase, ['page' => $page - 1])) ?>"
                                   class="pagination-btn">
                                    <i class="fa-solid fa-chevron-left"></i>
                                </a>
                            <?php else: ?>
                                <span class="pagination-btn disabled"><i class="fa-solid fa-chevron-left"></i></span>
                            <?php endif; ?>

                            <!-- Page Numbers -->
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?<?= http_build_query(array_merge($queryBase, ['page' => $i])) ?>"
                                   class="pagination-btn <?= $i === $page ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <!-- Next -->
                            <?php if ($page < $totalPages): ?>
                                <a href="?<?= http_build_query(array_merge($queryBase, ['page' => $page + 1])) ?>"
                                   class="pagination-btn">
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <span class="pagination-btn disabled"><i class="fa-solid fa-chevron-right"></i></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>


            <!-- SIDEBAR -->
            <aside class="lg:w-80 flex-shrink-0">
                <div class="space-y-6">

                    <!-- Popular Posts Widget -->
                    <div class="widget">
                        <h3 class="font-bold text-base text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                            <i class="fa-solid fa-fire text-orange-500"></i> Populer
                        </h3>
                        <div class="space-y-4">
                            <?php if (!empty($popularPosts)): ?>
                                <?php foreach ($popularPosts as $pp): ?>
                                    <a href="detail-blog.php?slug=<?= urlencode($pp['slug'] ?? '') ?>"
                                       class="popular-item flex gap-3 cursor-pointer p-2 rounded-lg">
                                        <img src="<?= htmlspecialchars($pp['gambar_utama'] ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=200&q=80') ?>"
                                             class="w-16 h-16 rounded-lg object-cover"
                                             alt="">
                                        <div class="flex-1">
                                            <h4 class="text-xs font-semibold text-gray-800 hover:text-blue-600 transition line-clamp-2">
                                                <?= htmlspecialchars($pp['judul']) ?>
                                            </h4>
                                            <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-400">
                                                <span><i class="fa-regular fa-eye"></i> <?= number_format((int)$pp['views']) ?></span>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-xs text-gray-400 text-center py-4">Belum ada artikel</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Categories Widget -->
                    <div class="widget">
                        <h3 class="font-bold text-base text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                            <i class="fa-solid fa-tag"></i> Kategori
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            $kategoriList = [
                                'destinasi' => ['label' => 'Destinasi',   'icon' => 'fa-map-location-dot'],
                                'tips'      => ['label' => 'Tips Travel', 'icon' => 'fa-lightbulb'],
                                'budaya'    => ['label' => 'Budaya',      'icon' => 'fa-gopuram'],
                                'kuliner'   => ['label' => 'Kuliner',     'icon' => 'fa-utensils'],
                                'event'     => ['label' => 'Event',       'icon' => 'fa-calendar'],
                            ];
                            foreach ($kategoriList as $key => $item):
                                $count = $kategoriCount[$key] ?? 0;
                            ?>
                                <a href="?kategori=<?= $key ?>"
                                   class="category-badge <?= $kategori === $key ? 'active' : '' ?> px-3 py-1.5 rounded-full text-[11px] font-semibold transition border border-gray-200 bg-white text-gray-600 hover:bg-gray-100">
                                    <i class="fa-solid <?= $item['icon'] ?> mr-1"></i>
                                    <?= $item['label'] ?> (<?= $count ?>)
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Follow Us Widget -->
                    <div class="widget">
                        <h3 class="font-bold text-base text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                            <i class="fa-regular fa-share-from-square"></i> Ikuti Kami
                        </h3>
                        <div class="flex justify-center gap-4">
                            <a href="#" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="w-10 h-10 bg-pink-600 text-white rounded-full flex items-center justify-center hover:bg-pink-700 transition"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="w-10 h-10 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700 transition"><i class="fab fa-youtube"></i></a>
                            <a href="#" class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>

                </div>
            </aside>

        </div>
    </div>
</section>


<!-- ============================================================
     CTA SECTION
============================================================ -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="bg-gradient-to-r from-blue-700 to-cyan-600 rounded-2xl p-8 md:p-10 text-white text-center">
            <i class="fa-regular fa-pen-to-square text-4xl mb-4"></i>
            <h3 class="text-2xl md:text-3xl font-bold mb-3">Ingin Menjadi Contributor?</h3>
            <p class="text-blue-100 mb-6 max-w-lg mx-auto">
                Bagikan pengalaman perjalanan Anda dan dapatkan kesempatan untuk dipublikasikan di blog kami.
            </p>
            <a href="kontak.php" class="inline-flex items-center gap-2 bg-white text-blue-700 hover:bg-gray-100 px-8 py-3 rounded-full font-semibold transition shadow-lg">
                <i class="fa-regular fa-pen-to-square"></i> Hubungi Kami
            </a>
        </div>
    </div>
</section>


<!-- SCROLL TO TOP -->
<div class="scroll-top" id="scrollTopBtn">
    <i class="fa-solid fa-arrow-up"></i>
</div>


<script>
    // Scroll to top
    const scrollBtn = document.getElementById("scrollTopBtn");
    window.addEventListener("scroll", () => {
        if (window.scrollY > 400) {
            scrollBtn.style.display = "flex";
        } else {
            scrollBtn.style.display = "none";
        }
    });
    scrollBtn?.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
</script>

<?php include "layout/footer.php"; ?>
</body>
</html>