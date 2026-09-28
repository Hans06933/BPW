<?php
/**
 * ============================================================
 * ADMIN - EDIT LAYANAN
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once "../../config/database.php";

// ============================================================
// AMBIL ID
// ============================================================
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['admin_msg'] = 'ID layanan tidak valid.';
    $_SESSION['admin_msg_type'] = 'error';
    header("Location: ../layanan.php");
    exit;
}

// ============================================================
// AMBIL DATA
// ============================================================
$data = db_get("SELECT * FROM layanan WHERE id = :id LIMIT 1", ['id' => $id]);

if (!$data) {
    $_SESSION['admin_msg'] = 'Data layanan tidak ditemukan.';
    $_SESSION['admin_msg_type'] = 'error';
    header("Location: ../layanan.php");
    exit;
}

// ============================================================
// NOTIFIKASI DARI PROSES SEBELUMNYA
// ============================================================
$msg     = $_SESSION['admin_msg'] ?? null;
$msgType = $_SESSION['admin_msg_type'] ?? 'success';
unset($_SESSION['admin_msg'], $_SESSION['admin_msg_type']);

// ============================================================
// INCLUDE HEADER
// ============================================================
include "../../layout/admin_header.php";
?>

<!-- ============================================================
     NOTIFIKASI
============================================================ -->
<?php if ($msg): ?>
    <div id="adminNotif" class="container mx-auto px-4 md:px-6 max-w-3xl mt-4">
        <div class="<?= $msgType === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700' ?> border-l-4 p-3 rounded-md text-sm flex items-center gap-2">
            <i class="fa-solid <?= $msgType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
            <?= htmlspecialchars($msg) ?>
        </div>
    </div>
    <script>setTimeout(() => document.getElementById('adminNotif')?.remove(), 4000);</script>
<?php endif; ?>


<!-- ============================================================
     FORM EDIT
============================================================ -->
<section class="container mx-auto px-4 md:px-6 max-w-3xl my-6 pb-16">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                Edit Layanan
            </h1>
            <p class="text-xs text-gray-400 mt-1">Perbarui informasi layanan.</p>
        </div>
        <a href="../layanan.php"
           class="text-xs text-gray-600 hover:text-gray-800 bg-white border border-gray-200 px-4 py-2 rounded-lg font-semibold transition inline-flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form action="update.php" method="POST" enctype="multipart/form-data" class="space-y-6">

        <input type="hidden" name="id" value="<?= (int) $data['id'] ?>">

        <!-- INFO UTAMA -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b">
                <h2 class="font-bold text-gray-800 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-circle-info text-blue-600"></i>
                    Informasi Layanan
                </h2>
            </div>
            <div class="p-5 space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Nama Layanan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_layanan"
                           value="<?= htmlspecialchars($data['nama_layanan'] ?? '') ?>"
                           required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea name="deskripsi" rows="5" required
                              class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600"
                              placeholder="Deskripsi layanan..."><?= htmlspecialchars($data['deskripsi'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                    <select name="status"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                        <option value="Aktif"    <?= ($data['status'] ?? '') === 'Aktif'    ? 'selected' : '' ?>>Aktif</option>
                        <option value="Nonaktif" <?= ($data['status'] ?? '') === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>

            </div>
        </div>

        <!-- GAMBAR -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b">
                <h2 class="font-bold text-gray-800 flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-image text-blue-600"></i>
                    Gambar Layanan
                </h2>
            </div>
            <div class="p-5">

                <!-- Preview Gambar Saat Ini -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Gambar Saat Ini</label>

                    <?php if (!empty($data['gambar']) && file_exists(__DIR__ . '/../../' . $data['gambar'])): ?>
                        <div class="flex items-start gap-4">
                            <img src="../../<?= htmlspecialchars($data['gambar']) ?>"
                                 class="w-40 h-28 object-cover rounded-lg border border-gray-200"
                                 alt="Gambar layanan"
                                 onerror="this.src='https://via.placeholder.com/400x300?text=No+Image';">
                            <div class="text-xs text-gray-400">
                                <p class="font-semibold text-gray-600 mb-1">File saat ini:</p>
                                <p class="font-mono text-[10px] break-all"><?= htmlspecialchars($data['gambar']) ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="w-40 h-28 bg-slate-100 rounded-lg flex items-center justify-center text-gray-400 border border-dashed border-gray-300">
                            <div class="text-center">
                                <i class="fa-solid fa-image text-2xl mb-1"></i>
                                <p class="text-[10px]">Belum ada gambar</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Upload Gambar Baru -->
                <div class="pt-4 border-t border-gray-100">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">Ganti Gambar</label>
                    <input type="file" name="gambar"
                           accept=".jpg,.jpeg,.png,.webp"
                           class="w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2">
                    <p class="text-[10px] text-gray-400 mt-2">
                        Kosongkan jika tidak ingin mengganti gambar. Format: JPG, PNG, WEBP. Maks 5 MB.
                    </p>
                </div>

            </div>
        </div>

        <!-- TOMBOL AKSI -->
        <div class="sticky bottom-4 bg-white rounded-xl shadow-lg border border-gray-100 p-4 flex justify-end gap-3">
            <a href="../layanan.php"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold text-sm transition inline-flex items-center gap-2">
                <i class="fa-solid fa-xmark"></i> Batal
            </a>
            <button type="submit"
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition inline-flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>

    </form>
</section>
