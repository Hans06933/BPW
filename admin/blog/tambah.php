<?php
/**
 * ============================================================
 * ADMIN - TAMBAH BLOG (Form CREATE)
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/../../config/database.php';

// Ambil error & old input kalau ada
$errors = $_SESSION['form_errors'] ?? [];
$old    = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_old']);

// Default values
$judul    = $old['judul']    ?? '';
$slug     = $old['slug']     ?? '';
$konten   = $old['konten']   ?? '';
$excerpt  = $old['excerpt']  ?? '';
$kategori = $old['kategori'] ?? 'destinasi';
$penulis  = $old['penulis']  ?? ($_SESSION['admin_nama'] ?? 'Admin BPW');
$status   = $old['status']   ?? 'draft';

include __DIR__ . '/../../layout/admin_header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Artikel - Admin BPW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>* { font-family: 'Poppins', sans-serif; }</style>
</head>

<body class="bg-slate-50 text-gray-800">

<!-- BREADCRUMB -->
<div class="bg-white border-b">
    <div class="container mx-auto px-4 md:px-6 max-w-4xl py-3">
        <nav class="flex items-center gap-2 text-xs text-gray-500 flex-wrap">
            <a href="../blog.php" class="hover:text-blue-600 transition">
                <i class="fa-solid fa-newspaper"></i> Blog
            </a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-gray-800 font-semibold">Tulis Artikel Baru</span>
        </nav>
    </div>
</div>

<!-- ERROR BOX -->
<?php if (!empty($errors)): ?>
    <div class="container mx-auto px-4 md:px-6 max-w-4xl mt-4">
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-md text-sm">
            <div class="flex items-start gap-2">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <ul class="list-disc pl-4 space-y-0.5">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- FORM -->
<section class="container mx-auto px-4 md:px-6 max-w-4xl mt-6 pb-16">
    <form action="simpan.php" method="POST" enctype="multipart/form-data" class="space-y-6">

        <input type="hidden" name="aksi" value="tambah">

        <!-- INFO UTAMA -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
            <h3 class="font-bold text-gray-800 flex items-center gap-2 pb-2 border-b">
                <i class="fa-solid fa-file-lines text-blue-600"></i> Informasi Utama
            </h3>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Judul Artikel <span class="text-red-500">*</span>
                </label>
                <input type="text" name="judul" required value="<?= htmlspecialchars($judul) ?>"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600"
                       placeholder="Cth: 10 Destinasi Wisata Terbaik di Bali">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Slug URL <span class="text-gray-400 font-normal">(kosongkan untuk auto)</span>
                </label>
                <div class="flex items-center">
                    <span class="text-xs text-gray-400 px-3 py-2.5 bg-gray-50 border border-r-0 border-slate-200 rounded-l-lg">
                        detail-blog.php?slug=
                    </span>
                    <input type="text" name="slug" id="slugInput" value="<?= htmlspecialchars($slug) ?>"
                           class="flex-1 px-4 py-2.5 text-sm border border-slate-200 rounded-r-lg focus:outline-none focus:border-blue-600"
                           placeholder="otomatis-dari-judul">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                    <select name="kategori" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600">
                        <option value="destinasi" <?= $kategori === 'destinasi' ? 'selected' : '' ?>>Destinasi</option>
                        <option value="tips"      <?= $kategori === 'tips'      ? 'selected' : '' ?>>Tips Travel</option>
                        <option value="budaya"    <?= $kategori === 'budaya'    ? 'selected' : '' ?>>Budaya</option>
                        <option value="kuliner"   <?= $kategori === 'kuliner'   ? 'selected' : '' ?>>Kuliner</option>
                        <option value="event"     <?= $kategori === 'event'     ? 'selected' : '' ?>>Event</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600">
                        <option value="draft"     <?= $status === 'draft'     ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= $status === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="archived"  <?= $status === 'archived'  ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Penulis</label>
                <input type="text" name="penulis" value="<?= htmlspecialchars($penulis) ?>"
                       class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600">
            </div>
        </div>

        <!-- GAMBAR -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
            <h3 class="font-bold text-gray-800 flex items-center gap-2 pb-2 border-b">
                <i class="fa-solid fa-image text-blue-600"></i> Gambar Utama
            </h3>
            <input type="file" name="gambar_utama" accept="image/*"
                   class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <p class="text-[10px] text-gray-400">Format: JPG, PNG, WEBP, GIF. Maks 2MB.</p>
        </div>

        <!-- KONTEN -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-4">
            <h3 class="font-bold text-gray-800 flex items-center gap-2 pb-2 border-b">
                <i class="fa-solid fa-pen-fancy text-blue-600"></i> Konten Artikel
            </h3>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Excerpt / Ringkasan</label>
                <textarea name="excerpt" rows="2" maxlength="200"
                          class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600"
                          placeholder="Ringkasan singkat artikel..."><?= htmlspecialchars($excerpt) ?></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Konten Lengkap <span class="text-red-500">*</span>
                </label>
                <textarea name="konten" rows="14" required
                          class="w-full px-4 py-2.5 text-sm border border-slate-200 rounded-lg focus:outline-none focus:border-blue-600 font-mono"
                          placeholder="Tulis konten artikel..."><?= htmlspecialchars($konten) ?></textarea>
                <p class="text-[10px] text-gray-400 mt-1">Bisa pakai HTML: &lt;p&gt;, &lt;h2&gt;, &lt;ul&gt;, &lt;strong&gt;, &lt;a&gt;</p>
            </div>
        </div>

        <!-- ACTION -->
        <div class="flex flex-wrap items-center gap-3 sticky bottom-4 bg-white p-3 rounded-xl shadow-lg border">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition text-sm inline-flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Artikel
            </button>
            <a href="../blog.php" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold transition text-sm inline-flex items-center gap-2">
                <i class="fa-solid fa-xmark"></i> Batal
            </a>
        </div>

    </form>
</section>

<script>
    // Auto-slug
    const judulInput = document.querySelector('input[name="judul"]');
    const slugInput  = document.getElementById('slugInput');

    judulInput?.addEventListener('input', function () {
        if (slugInput.dataset.manual === 'true') return;
        slugInput.value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    });

    slugInput?.addEventListener('input', function () {
        this.dataset.manual = 'true';
    });
</script>

</body>
</html>