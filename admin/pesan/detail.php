<?php
/**
 * ============================================================
 * ADMIN - DETAIL PESAN
 * Bayu Prima Wisata
 * ============================================================
 */

ob_start();
session_start();
require_once __DIR__ . '/../../config/database.php';

// ============================================================
// AMBIL ID
// ============================================================
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['admin_msg'] = 'ID pesan tidak valid.';
    $_SESSION['admin_msg_type'] = 'error';
    header('Location: ../pesan-masuk.php');
    exit;
}

// ============================================================
// AMBIL DATA
// ============================================================
$pesan = db_get("SELECT * FROM pesan_masuk WHERE id = :id", [':id' => $id]);

if (!$pesan) {
    $_SESSION['admin_msg'] = 'Pesan tidak ditemukan.';
    $_SESSION['admin_msg_type'] = 'error';
    header('Location: ../pesan-masuk.php');
    exit;
}

// ============================================================
// AUTO MARK AS READ
// ============================================================
if (empty($pesan['is_read'])) {
    db_update('pesan_masuk', ['is_read' => 1], 'id', $id);
    $pesan['is_read'] = 1;
}

// ============================================================
// ICON HELPER
// ============================================================
function iconTipe($tipe) {
    $icons = [
        'testimoni'  => ['fa-comment-dots', 'bg-yellow-100 text-yellow-600', 'Testimoni'],
        'blog'       => ['fa-newspaper',    'bg-blue-100 text-blue-600',    'Blog'],
        'kontak'     => ['fa-envelope',     'bg-purple-100 text-purple-600','Kontak'],
        'newsletter' => ['fa-bell',         'bg-pink-100 text-pink-600',    'Newsletter'],
        'review'     => ['fa-star',         'bg-orange-100 text-orange-600','Review'],
        'sistem'     => ['fa-info-circle',  'bg-gray-100 text-gray-600',    'Sistem'],
    ];
    return $icons[$tipe] ?? ['fa-bell', 'bg-gray-100 text-gray-600', 'Pesan'];
}

list($icon, $color, $labelTipe) = iconTipe($pesan['tipe']);

// ============================================================
// INCLUDE HEADER
// ============================================================
include __DIR__ . '/../../layout/admin_header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesan - Admin BPW</title>
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
            <a href="../pesan-masuk.php" class="hover:text-blue-600 transition">
                <i class="fa-solid fa-inbox"></i> Pesan Masuk
            </a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-gray-800 font-semibold">Detail Pesan</span>
        </nav>
    </div>
</div>

<!-- KONTEN -->
<section class="container mx-auto px-4 md:px-6 max-w-4xl mt-6 pb-16">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        <!-- Header Pesan -->
        <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-cyan-50">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-xl <?= $color ?> flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid <?= $icon ?> text-xl"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider <?= explode(' ', $color)[1] ?>">
                            <?= $labelTipe ?>
                        </span>
                        <?php if ($pesan['is_starred']): ?>
                            <span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-bold">
                                <i class="fa-solid fa-star text-[8px]"></i> Penting
                            </span>
                        <?php endif; ?>
                        <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">
                            <i class="fa-solid fa-check text-[8px]"></i> Dibaca
                        </span>
                    </div>
                    <h1 class="text-lg md:text-xl font-bold text-gray-800">
                        <?= htmlspecialchars($pesan['judul']) ?>
                    </h1>
                    <div class="text-[11px] text-gray-500 mt-1">
                        <i class="fa-regular fa-calendar"></i>
                        <?= date('d F Y, H:i', strtotime($pesan['created_at'])) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Pengirim -->
        <?php if (!empty($pesan['pengirim']) || !empty($pesan['email']) || !empty($pesan['no_hp'])): ?>
            <div class="p-6 border-b bg-slate-50">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Info Pengirim</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <?php if (!empty($pesan['pengirim'])): ?>
                        <div class="bg-white rounded-lg p-3 border border-gray-100">
                            <div class="text-[10px] text-gray-400 mb-0.5">
                                <i class="fa-regular fa-user"></i> Nama
                            </div>
                            <div class="text-sm font-semibold text-gray-800">
                                <?= htmlspecialchars($pesan['pengirim']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($pesan['email'])): ?>
                        <div class="bg-white rounded-lg p-3 border border-gray-100">
                            <div class="text-[10px] text-gray-400 mb-0.5">
                                <i class="fa-regular fa-envelope"></i> Email
                            </div>
                            <div class="text-sm font-semibold text-gray-800 break-all">
                                <?= htmlspecialchars($pesan['email']) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($pesan['no_hp'])): ?>
                        <div class="bg-white rounded-lg p-3 border border-gray-100">
                            <div class="text-[10px] text-gray-400 mb-0.5">
                                <i class="fa-solid fa-phone"></i> No. HP
                            </div>
                            <div class="text-sm font-semibold text-gray-800">
                                <?= htmlspecialchars($pesan['no_hp']) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Isi Pesan -->
        <div class="p-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Isi Pesan</h3>
            <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line bg-slate-50 rounded-lg p-4 border border-gray-100">
                <?= htmlspecialchars($pesan['pesan']) ?>
            </div>
        </div>

        <!-- Actions -->
        <div class="p-6 border-t bg-slate-50 flex flex-wrap items-center gap-2">

            <?php if (!empty($pesan['link'])): ?>
                <a href="../<?= htmlspecialchars($pesan['link']) ?>"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-xs font-semibold transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-arrow-right"></i> Buka Halaman Terkait
                </a>
            <?php endif; ?>

            <form method="POST" action="aksi.php" class="inline">
                <input type="hidden" name="aksi" value="star">
                <input type="hidden" name="id" value="<?= $pesan['id'] ?>">
                <input type="hidden" name="redirect" value="detail">
                <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2.5 rounded-lg text-xs font-semibold transition inline-flex items-center gap-2">
                    <i class="fa-<?= $pesan['is_starred'] ? 'solid' : 'regular' ?> fa-star"></i>
                    <?= $pesan['is_starred'] ? 'Hapus Tanda Penting' : 'Tandai Penting' ?>
                </button>
            </form>

            <form method="POST" action="aksi.php" class="inline">
                <input type="hidden" name="aksi" value="mark_unread">
                <input type="hidden" name="id" value="<?= $pesan['id'] ?>">
                <input type="hidden" name="redirect" value="detail">
                <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-xs font-semibold transition inline-flex items-center gap-2">
                    <i class="fa-solid fa-envelope"></i> Tandai Belum Dibaca
                </button>
            </form>

            <a href="hapus.php?id=<?= $pesan['id'] ?>"
               onclick="return confirm('Yakin hapus pesan ini?')"
               class="ml-auto bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-lg text-xs font-semibold transition inline-flex items-center gap-2">
                <i class="fa-solid fa-trash"></i> Hapus Pesan
            </a>

            <a href="../pesan-masuk.php"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg text-xs font-semibold transition inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>

        </div>

    </div>
</section>

<?php ob_end_flush(); ?>
</body>
</html>