<?php
/**
 * ============================================================
 * DETAIL ARTIKEL BLOG
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/config/database.php';

// ============================================================
// AMBIL SLUG DARI URL
// ============================================================
$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    header('Location: blog.php');
    exit;
}

// ============================================================
// AMBIL DATA ARTIKEL
// ============================================================
$post = null;

try {
    $post = db_get("
        SELECT
            id, judul, slug, konten, excerpt, kategori,
            gambar_utama, penulis, views, status,
            published_at, created_at, updated_at
        FROM blog
        WHERE slug = :slug
          AND status = 'published'
        LIMIT 1
    ", [':slug' => $slug]);
} catch (PDOException $e) {
    $post = null;
}

if (!$post) {
    // Artikel tidak ditemukan → tampilkan 404 sederhana
    include __DIR__ . '/layout/header.php';
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Artikel Tidak Ditemukan - BPW</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>* { font-family: 'Poppins', sans-serif; }</style>
    </head>
    <body class="bg-slate-50 min-h-screen flex items-center justify-center">
        <div class="text-center px-4">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fa-regular fa-face-frown text-red-500 text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Artikel Tidak Ditemukan</h1>
            <p class="text-sm text-gray-500 mb-6">Artikel yang Anda cari tidak tersedia atau sudah dihapus.</p>
            <a href="blog.php" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-full font-semibold transition text-sm">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Blog
            </a>
        </div>
    </body>
    </html>
    <?php
    include __DIR__ . '/layout/footer.php';
    exit;
}

// ============================================================
// INCREMENT VIEWS (sekali per kunjungan)
// ============================================================
$viewedKey = 'viewed_blog_' . $post['id'];

if (empty($_SESSION[$viewedKey])) {
    try {
        db_update('blog', ['views' => ((int)$post['views'] + 1)], 'id', $post['id']);
        $post['views'] = (int)$post['views'] + 1;
        $_SESSION[$viewedKey] = true;
    } catch (PDOException $e) {
        // Biarkan
    }
}

// ============================================================
// ARTIKEL TERKAIT (sama kategori, selain artikel ini)
// ============================================================
$relatedPosts = [];
try {
    $relatedPosts = db_get_all("
        SELECT id, judul, slug, gambar_utama, kategori, created_at, published_at
        FROM blog
        WHERE status = 'published'
          AND kategori = :kategori
          AND id != :id
        ORDER BY published_at DESC, created_at DESC
        LIMIT 3
    ", [
        ':kategori' => $post['kategori'],
        ':id'       => $post['id'],
    ]);
} catch (PDOException $e) {
    $relatedPosts = [];
}

// ============================================================
// ARTIKEL POPULER (untuk sidebar)
// ============================================================
$popularPosts = [];
try {
    $popularPosts = db_get_all("
        SELECT id, judul, slug, gambar_utama, views, created_at, published_at
        FROM blog
        WHERE status = 'published'
          AND id != :id
        ORDER BY views DESC
        LIMIT 4
    ", [':id' => $post['id']]);
} catch (PDOException $e) {
    $popularPosts = [];
}

// ============================================================
// ESTIMASI WAKTU BACA
// ============================================================
$wordCount  = str_word_count(strip_tags($post['konten']));
$readTime   = max(1, ceil($wordCount / 200));

// ============================================================
// TANGGAL PUBLISH
// ============================================================
$publishDate = $post['published_at'] ?: $post['created_at'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title><?= htmlspecialchars($post['judul']) ?> - Blog Bayu Prima Wisata</title>
    <meta name="description" content="<?= htmlspecialchars($post['excerpt'] ?: mb_substr(strip_tags($post['konten']), 0, 155)) ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }

        /* Hero */
        .bg-detail-hero {
            background: linear-gradient(135deg, rgba(0,51,102,0.85) 0%, rgba(0,76,153,0.7) 100%);
            background-size: cover;
            background-position: center;
        }

        /* Konten artikel */
        .article-content {
            font-size: 1rem;
            line-height: 1.8;
            color: #374151;
        }
        .article-content h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .article-content h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .article-content p {
            margin-bottom: 1.25rem;
        }
        .article-content ul, .article-content ol {
            margin-bottom: 1.25rem;
            padding-left: 1.5rem;
        }
        .article-content ul { list-style: disc; }
        .article-content ol { list-style: decimal; }
        .article-content li {
            margin-bottom: 0.5rem;
        }
        .article-content a {
            color: #2563eb;
            text-decoration: underline;
        }
        .article-content img {
            border-radius: 0.75rem;
            margin: 1.5rem 0;
            max-width: 100%;
            height: auto;
        }
        .article-content blockquote {
            border-left: 4px solid #2563eb;
            padding-left: 1rem;
            margin: 1.5rem 0;
            font-style: italic;
            color: #4b5563;
            background: #eff6ff;
            padding: 1rem 1.25rem;
            border-radius: 0.5rem;
        }
        .article-content strong {
            font-weight: 700;
            color: #111827;
        }

        /* Share button */
        .share-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.2s;
        }
        .share-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px -4px rgba(0,0,0,0.2);
        }

        /* Sidebar widget */
        .widget {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #f0f0f0;
        }
        .popular-item {
            transition: all 0.2s ease;
        }
        .popular-item:hover {
            background-color: #f8fafc;
            transform: translateX(4px);
        }

        /* Scroll top */
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

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Reading progress bar */
        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #003366, #00b4d8);
            z-index: 999;
            width: 0%;
            transition: width 0.1s ease;
        }
    </style>
</head>

<?php include "layout/header.php"; ?>

<body class="bg-slate-50">

<!-- PROGRESS BAR -->
<div class="progress-bar" id="progressBar"></div>


<!-- ============================================================
     HERO SECTION
============================================================ -->
<section class="bg-detail-hero py-16 md:py-24 text-white relative">
    <div class="container mx-auto px-4 md:px-6 max-w-4xl">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-blue-100 mb-5 flex-wrap">
            <a href="index.php" class="hover:text-white transition">
                <i class="fa-solid fa-house"></i> Home
            </a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <a href="blog.php" class="hover:text-white transition">Blog</a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <a href="blog.php?kategori=<?= urlencode($post['kategori']) ?>" class="hover:text-white transition capitalize">
                <?= htmlspecialchars($post['kategori']) ?>
            </a>
        </nav>

        <!-- Category Badge -->
        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur rounded-full px-3 py-1 mb-4">
            <i class="fa-solid fa-tag text-yellow-300 text-xs"></i>
            <span class="text-[10px] font-bold tracking-wide uppercase">
                <?= htmlspecialchars($post['kategori']) ?>
            </span>
        </div>

        <!-- Judul -->
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight mb-5">
            <?= htmlspecialchars($post['judul']) ?>
        </h1>

        <!-- Meta -->
        <div class="flex flex-wrap items-center gap-4 text-xs text-blue-100">
            <div class="flex items-center gap-2">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($post['penulis'] ?: 'BPW') ?>&background=ffffff&color=003366"
                     class="w-8 h-8 rounded-full" alt="">
                <span class="font-semibold text-white"><?= htmlspecialchars($post['penulis'] ?: 'Admin BPW') ?></span>
            </div>
            <span><i class="fa-regular fa-calendar"></i> <?= date('d F Y', strtotime($publishDate)) ?></span>
            <span><i class="fa-regular fa-clock"></i> <?= $readTime ?> menit baca</span>
            <span><i class="fa-regular fa-eye"></i> <?= number_format((int)$post['views']) ?> views</span>
        </div>

    </div>
</section>


<!-- ============================================================
     MAIN CONTENT
============================================================ -->
<section class="py-10 md:py-12">
    <div class="container mx-auto px-4 md:px-6 max-w-7xl">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- ARTIKEL -->
            <article class="flex-1 max-w-4xl">

                <!-- Gambar Utama -->
                <?php if (!empty($post['gambar_utama'])): ?>
                    <div class="rounded-2xl overflow-hidden shadow-lg mb-8 -mt-16 md:-mt-20 relative z-10 bg-white p-2">
                        <img src="<?= htmlspecialchars($post['gambar_utama']) ?>"
                             class="w-full h-64 md:h-96 object-cover rounded-xl"
                             alt="<?= htmlspecialchars($post['judul']) ?>">
                    </div>
                <?php endif; ?>

                <!-- Share Buttons (atas) -->
                <div class="flex items-center justify-between flex-wrap gap-3 mb-6 pb-6 border-b border-gray-200">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400 font-semibold">Bagikan:</span>
                        <?php
                        $shareUrl = urlencode('http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                        $shareTitle = urlencode($post['judul']);
                        ?>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>"
                           target="_blank" class="share-btn bg-blue-600" title="Facebook">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>"
                           target="_blank" class="share-btn bg-black" title="X / Twitter">
                            <i class="fab fa-x-twitter text-sm"></i>
                        </a>
                        <a href="https://wa.me/?text=<?= $shareTitle ?>%20<?= $shareUrl ?>"
                           target="_blank" class="share-btn bg-green-500" title="WhatsApp">
                            <i class="fab fa-whatsapp text-sm"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $shareUrl ?>"
                           target="_blank" class="share-btn bg-blue-700" title="LinkedIn">
                            <i class="fab fa-linkedin-in text-sm"></i>
                        </a>
                        <button onclick="copyLink()" class="share-btn bg-gray-600" title="Copy Link">
                            <i class="fa-solid fa-link text-sm"></i>
                        </button>
                    </div>

                    <a href="blog.php?kategori=<?= urlencode($post['kategori']) ?>"
                       class="text-xs text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-arrow-left"></i> Artikel <?= htmlspecialchars($post['kategori']) ?> lainnya
                    </a>
                </div>

                <!-- Konten Artikel -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 mb-8">
                    <div class="article-content">
                        <?= $post['konten'] ?>
                    </div>

                    <!-- Tags / Kategori -->
                    <div class="mt-10 pt-6 border-t border-gray-200">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-xs text-gray-500 font-semibold">Kategori:</span>
                            <a href="blog.php?kategori=<?= urlencode($post['kategori']) ?>"
                               class="bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-semibold px-3 py-1 rounded-full transition">
                                <i class="fa-solid fa-tag text-[10px]"></i>
                                <?= htmlspecialchars($post['kategori']) ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Author Box -->
                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-5 md:p-6 mb-8 flex items-center gap-4">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($post['penulis'] ?: 'BPW') ?>&background=003366&color=fff&size=80"
                         class="w-16 h-16 rounded-full border-2 border-white shadow" alt="">
                    <div class="flex-1">
                        <div class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">Ditulis oleh</div>
                        <div class="font-bold text-gray-800"><?= htmlspecialchars($post['penulis'] ?: 'Admin BPW') ?></div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            Tim editorial Bayu Prima Wisata yang berbagi inspirasi perjalanan.
                        </div>
                    </div>
                </div>

                <!-- Share Buttons (bawah) -->
                <div class="text-center mb-10">
                    <p class="text-sm text-gray-500 mb-3">Suka artikel ini? Bagikan ke teman Anda!</p>
                    <div class="flex justify-center gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>"
                           target="_blank" class="share-btn bg-blue-600">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>"
                           target="_blank" class="share-btn bg-black">
                            <i class="fab fa-x-twitter"></i>
                        </a>
                        <a href="https://wa.me/?text=<?= $shareTitle ?>%20<?= $shareUrl ?>"
                           target="_blank" class="share-btn bg-green-500">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <!-- Related Posts -->
                <?php if (!empty($relatedPosts)): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                        <h3 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
                            <i class="fa-solid fa-newspaper text-blue-600"></i>
                            Artikel Terkait
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <?php foreach ($relatedPosts as $rp): ?>
                                <a href="detail-blog.php?slug=<?= urlencode($rp['slug']) ?>"
                                   class="group block">
                                    <div class="h-32 rounded-lg overflow-hidden bg-gray-100 mb-3">
                                        <img src="<?= htmlspecialchars($rp['gambar_utama'] ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=400&q=80') ?>"
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                             alt="<?= htmlspecialchars($rp['judul']) ?>"
                                             loading="lazy">
                                    </div>
                                    <div class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-1">
                                        <?= htmlspecialchars($rp['kategori']) ?>
                                    </div>
                                    <h4 class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition line-clamp-2">
                                        <?= htmlspecialchars($rp['judul']) ?>
                                    </h4>
                                    <div class="text-[10px] text-gray-400 mt-1">
                                        <i class="fa-regular fa-calendar"></i>
                                        <?= date('d M Y', strtotime($rp['published_at'] ?: $rp['created_at'])) ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </article>


            <!-- SIDEBAR -->
            <aside class="lg:w-80 flex-shrink-0">
                <div class="space-y-6 lg:sticky lg:top-24">

                    <!-- Popular Posts -->
                    <div class="widget">
                        <h3 class="font-bold text-base text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                            <i class="fa-solid fa-fire text-orange-500"></i> Populer
                        </h3>
                        <div class="space-y-4">
                            <?php if (!empty($popularPosts)): ?>
                                <?php foreach ($popularPosts as $pp): ?>
                                    <a href="detail-blog.php?slug=<?= urlencode($pp['slug']) ?>"
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

                    <!-- CTA Konsultasi -->
                    <div class="widget bg-gradient-to-br from-blue-700 to-cyan-600 border-0 text-white">
                        <div class="text-center">
                            <i class="fa-solid fa-headset text-3xl mb-3"></i>
                            <h3 class="font-bold text-base mb-2">Butuh Bantuan?</h3>
                            <p class="text-xs text-blue-100 mb-4">
                                Tim BPW siap membantu merencanakan perjalanan impian Anda.
                            </p>
                            <a href="kontak.php"
                               class="inline-flex items-center gap-2 bg-white text-blue-700 hover:bg-gray-100 px-5 py-2.5 rounded-full font-semibold transition text-xs">
                                <i class="fa-solid fa-comments"></i> Hubungi Kami
                            </a>
                        </div>
                    </div>

                    <!-- Back to Blog -->
                    <div class="widget text-center">
                        <i class="fa-regular fa-newspaper text-3xl text-blue-600 mb-3"></i>
                        <h3 class="font-bold text-sm text-gray-800 mb-2">Jelajahi Artikel Lainnya</h3>
                        <p class="text-xs text-gray-500 mb-3">
                            Temukan lebih banyak inspirasi perjalanan.
                        </p>
                        <a href="blog.php"
                           class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-full font-semibold transition text-xs">
                            <i class="fa-solid fa-list"></i> Semua Artikel
                        </a>
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
            <i class="fa-solid fa-plane-departure text-4xl mb-4"></i>
            <h3 class="text-2xl md:text-3xl font-bold mb-3">Siap Berpetualang?</h3>
            <p class="text-blue-100 mb-6 max-w-lg mx-auto">
                Wujudkan perjalanan impian Anda bersama Bayu Prima Wisata.
            </p>
            <a href="destinasi.php"
               class="inline-flex items-center gap-2 bg-white text-blue-700 hover:bg-gray-100 px-8 py-3 rounded-full font-semibold transition shadow-lg">
                <i class="fa-solid fa-map-location-dot"></i> Lihat Destinasi
            </a>
        </div>
    </div>
</section>


<!-- SCROLL TO TOP -->
<div class="scroll-top" id="scrollTopBtn">
    <i class="fa-solid fa-arrow-up"></i>
</div>


<script>
    // ============================================================
    // SCROLL TO TOP
    // ============================================================
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

    // ============================================================
    // READING PROGRESS BAR
    // ============================================================
    const progressBar = document.getElementById("progressBar");
    window.addEventListener("scroll", () => {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
        if (progressBar) progressBar.style.width = progress + "%";
    });

    // ============================================================
    // COPY LINK
    // ============================================================
    function copyLink() {
        const url = window.location.href;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                showToast("Link berhasil disalin!");
            }).catch(() => {
                fallbackCopy(url);
            });
        } else {
            fallbackCopy(url);
        }
    }

    function fallbackCopy(text) {
        const input = document.createElement("input");
        input.value = text;
        document.body.appendChild(input);
        input.select();
        try {
            document.execCommand("copy");
            showToast("Link berhasil disalin!");
        } catch (e) {
            alert("Link: " + text);
        }
        document.body.removeChild(input);
    }

    // ============================================================
    // TOAST NOTIFICATION
    // ============================================================
    function showToast(message) {
        const toast = document.createElement("div");
        toast.className = "fixed bottom-24 right-8 bg-gray-900 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-semibold z-[9999]";
        toast.style.animation = "slideUp 0.3s ease";
        toast.innerHTML = '<i class="fa-solid fa-check-circle text-green-400 mr-2"></i>' + message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transition = "opacity 0.3s";
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }
</script>

<style>
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
</style>

<?php include "layout/footer.php"; ?>
</body>
</html>