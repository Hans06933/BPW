<?php
require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| AMBIL DATA GALERI
|--------------------------------------------------------------------------
*/
try {
    $stmt = db_query("
        SELECT *
        FROM galeri
        ORDER BY id DESC
    ");

    $galeriList = $stmt->fetchAll();

    $totalGaleri = count($galeriList);

} catch (PDOException $e) {
    $galeriList = [];
    $totalGaleri = 0;
    $errorDatabase = $e->getMessage();
}

/*
|--------------------------------------------------------------------------
| PESAN
|--------------------------------------------------------------------------
*/
$status = $_GET['status'] ?? '';
$message = '';

if ($status === 'saved') {
    $message = 'Data galeri berhasil ditambahkan.';
} elseif ($status === 'updated') {
    $message = 'Data galeri berhasil diperbarui.';
} elseif ($status === 'deleted') {
    $message = 'Data galeri berhasil dihapus.';
}

include "../layout/admin_header.php";
?>

<div class="p-4 md:p-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                Kelola Galeri
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Kelola gambar dan informasi galeri yang tampil di website.
            </p>
        </div>

        <a
            href="galeri/tambah.php"
            class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-3 rounded-lg transition"
        >
            <i class="fa-solid fa-plus"></i>
            Tambah Galeri
        </a>

    </div>


    <!-- PESAN BERHASIL -->
    <?php if ($message): ?>

        <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">

            <i class="fa-solid fa-circle-check"></i>

            <span>
                <?= htmlspecialchars($message) ?>
            </span>

        </div>

    <?php endif; ?>


    <!-- ERROR DATABASE -->
    <?php if (isset($errorDatabase)): ?>

        <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">

            <div class="font-bold mb-1">
                Database Error
            </div>

            <div class="text-sm">
                <?= htmlspecialchars($errorDatabase) ?>
            </div>

        </div>

    <?php endif; ?>


    <!-- TOTAL DATA -->
    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-6">

        <div class="flex items-center gap-3">

            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-images"></i>
            </div>

            <div>
                <p class="text-sm text-slate-500">
                    Total Galeri
                </p>

                <p class="text-xl font-bold text-blue-700">
                    <?= $totalGaleri ?>
                </p>
            </div>

        </div>

    </div>


    <!-- TABEL -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 border-b border-slate-200">

                    <tr>

                        <th class="px-5 py-4 text-left font-bold text-slate-700">
                            #
                        </th>

                        <th class="px-5 py-4 text-left font-bold text-slate-700">
                            Gambar
                        </th>

                        <th class="px-5 py-4 text-left font-bold text-slate-700">
                            Judul
                        </th>

                        <th class="px-5 py-4 text-left font-bold text-slate-700">
                            Kategori
                        </th>

                        <th class="px-5 py-4 text-left font-bold text-slate-700">
                            Status
                        </th>

                        <th class="px-5 py-4 text-center font-bold text-slate-700">
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-100">

                    <?php if (empty($galeriList)): ?>

                        <tr>

                            <td colspan="6" class="px-5 py-16 text-center">

                                <div class="flex flex-col items-center">

                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">

                                        <i class="fa-solid fa-images text-2xl text-slate-400"></i>

                                    </div>

                                    <h3 class="font-bold text-slate-700">
                                        Belum Ada Data Galeri
                                    </h3>

                                    <p class="text-sm text-slate-400 mt-1">
                                        Silakan tambahkan data galeri terlebih dahulu.
                                    </p>

                                    <a
                                        href="galeri/tambah.php"
                                        class="mt-4 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold"
                                    >
                                        <i class="fa-solid fa-plus"></i>
                                        Tambah Galeri
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach ($galeriList as $index => $galeri): ?>

                            <?php

                            $gambar = trim($galeri['gambar'] ?? '');

                            /*
                             * Jika database menyimpan:
                             * images/galeri/nama.jpg
                             *
                             * maka URL:
                             * ../images/galeri/nama.jpg
                             */

                            if ($gambar !== '') {

                                if (
                                    filter_var($gambar, FILTER_VALIDATE_URL)
                                ) {
                                    $gambarUrl = $gambar;
                                } else {

                                    $gambar = ltrim($gambar, '/');

                                    $gambarUrl = "../" . $gambar;
                                }

                            } else {

                                $gambarUrl = '';

                            }

                            ?>

                            <tr class="hover:bg-slate-50 transition">

                                <!-- NOMOR -->
                                <td class="px-5 py-4 text-slate-500">
                                    <?= $index + 1 ?>
                                </td>


                                <!-- GAMBAR -->
                                <td class="px-5 py-4">

                                    <?php if ($gambarUrl): ?>

                                        <img
                                            src="<?= htmlspecialchars($gambarUrl) ?>"
                                            alt="<?= htmlspecialchars($galeri['judul']) ?>"
                                            class="w-20 h-14 object-cover rounded-lg border border-slate-200"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        >

                                        <div
                                            style="display:none"
                                            class="w-20 h-14 bg-slate-100 rounded-lg items-center justify-center text-slate-400"
                                        >
                                            <i class="fa-solid fa-image"></i>
                                        </div>

                                    <?php else: ?>

                                        <div class="w-20 h-14 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">

                                            <i class="fa-solid fa-image"></i>

                                        </div>

                                    <?php endif; ?>

                                </td>


                                <!-- JUDUL -->
                                <td class="px-5 py-4">

                                    <div class="font-semibold text-slate-800">
                                        <?= htmlspecialchars($galeri['judul']) ?>
                                    </div>

                                    <?php if (!empty($galeri['deskripsi'])): ?>

                                        <div class="text-xs text-slate-400 mt-1 max-w-xs truncate">
                                            <?= htmlspecialchars($galeri['deskripsi']) ?>
                                        </div>

                                    <?php endif; ?>

                                </td>


                                <!-- KATEGORI -->
                                <td class="px-5 py-4">

                                    <?php if (!empty($galeri['kategori'])): ?>

                                        <span class="inline-flex px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold">

                                            <?= htmlspecialchars($galeri['kategori']) ?>

                                        </span>

                                    <?php else: ?>

                                        <span class="text-slate-400">
                                            -
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- STATUS -->
                                <td class="px-5 py-4">

                                    <?php if (($galeri['status'] ?? 'aktif') === 'aktif'): ?>

                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-50 text-green-600 text-xs font-semibold">

                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>

                                            Aktif

                                        </span>

                                    <?php else: ?>

                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-600 text-xs font-semibold">

                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>

                                            Nonaktif

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- AKSI -->
                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        <!-- EDIT -->
                                        <a
                                            href="galeri/edit.php?id=<?= (int)$galeri['id'] ?>"
                                            class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition"
                                            title="Edit"
                                        >
                                            <i class="fa-solid fa-pen"></i>
                                        </a>


                                        <!-- HAPUS -->
                                        <a
                                            href="galeri/hapus.php?id=<?= (int)$galeri['id'] ?>"
                                            onclick="return confirm('Yakin ingin menghapus galeri ini? Gambar juga akan dihapus dari server.')"
                                            class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition"
                                            title="Hapus"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<?php
if (file_exists("../layout/admin_footer.php")) {
    include "../layout/admin_footer.php";
}
?>