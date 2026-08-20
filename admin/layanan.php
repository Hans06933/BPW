<?php
require_once "../config/database.php";

$layanan = db_get_all("SELECT * FROM layanan ORDER BY id DESC");

$totalLayanan = db_count("layanan");
$totalAktif = db_count("layanan", "status = ?", ["Aktif"]);
$totalNonaktif = db_count("layanan", "status = ?", ["Nonaktif"]);

include "../layout/admin_header.php";
?>

<div class="p-4 md:p-6">

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Kelola Layanan
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Kelola layanan yang tersedia pada website Bayu Prima Wisata.
            </p>
        </div>

        <a href="layanan/tambah.php"
           class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition">

            <i class="fa-solid fa-plus"></i>
            Tambah Layanan

        </a>

    </div>


    <!-- NOTIFIKASI -->

    <?php if (isset($_GET['status'])): ?>

        <?php if ($_GET['status'] == 'success'): ?>

            <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>Data layanan berhasil disimpan.</span>
            </div>

        <?php elseif ($_GET['status'] == 'updated'): ?>

            <div class="mb-5 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>Data layanan berhasil diperbarui.</span>
            </div>

        <?php elseif ($_GET['status'] == 'deleted'): ?>

            <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2">
                <i class="fa-solid fa-trash"></i>
                <span>Data layanan berhasil dihapus.</span>
            </div>

        <?php endif; ?>

    <?php endif; ?>


    <!-- STATISTIK -->

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        <div class="bg-white border rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs text-gray-500">
                        Total Layanan
                    </p>

                    <h3 class="text-2xl font-bold text-slate-800 mt-1">
                        <?= $totalLayanan ?>
                    </h3>
                </div>

                <div class="w-11 h-11 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-concierge-bell"></i>
                </div>

            </div>
        </div>


        <div class="bg-white border rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs text-gray-500">
                        Layanan Aktif
                    </p>

                    <h3 class="text-2xl font-bold text-green-600 mt-1">
                        <?= $totalAktif ?>
                    </h3>
                </div>

                <div class="w-11 h-11 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

            </div>
        </div>


        <div class="bg-white border rounded-xl p-5 shadow-sm">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs text-gray-500">
                        Layanan Nonaktif
                    </p>

                    <h3 class="text-2xl font-bold text-red-600 mt-1">
                        <?= $totalNonaktif ?>
                    </h3>
                </div>

                <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>

            </div>
        </div>

    </div>


    <!-- TABLE -->

    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">

        <div class="p-5 border-b">

            <h2 class="font-bold text-slate-800">
                Daftar Layanan
            </h2>

            <p class="text-xs text-gray-500 mt-1">
                Data layanan yang dikelola oleh administrator.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 border-b">

                    <tr>

                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500">
                            No
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500">
                            Gambar
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500">
                            Nama Layanan
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-bold text-gray-500">
                            Deskripsi
                        </th>

                        <th class="px-5 py-3 text-center text-xs font-bold text-gray-500">
                            Status
                        </th>

                        <th class="px-5 py-3 text-center text-xs font-bold text-gray-500">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y">

                    <?php if (!empty($layanan)): ?>

                        <?php $no = 1; ?>

                        <?php foreach ($layanan as $row): ?>

                            <tr class="hover:bg-slate-50 transition">

                                <td class="px-5 py-4 text-gray-600">
                                    <?= $no++ ?>
                                </td>


                                <td class="px-5 py-4">

                                    <?php if (!empty($row['gambar'])): ?>

                                        <img
                                            src="../<?= htmlspecialchars($row['gambar']) ?>"
                                            class="w-20 h-14 object-cover rounded-lg border"
                                            alt="<?= htmlspecialchars($row['nama_layanan']) ?>"
                                        >

                                    <?php else: ?>

                                        <div class="w-20 h-14 rounded-lg bg-slate-100 flex items-center justify-center text-gray-400">
                                            <i class="fa-solid fa-image"></i>
                                        </div>

                                    <?php endif; ?>

                                </td>


                                <td class="px-5 py-4">

                                    <div class="font-semibold text-slate-800">
                                        <?= htmlspecialchars($row['nama_layanan']) ?>
                                    </div>

                                </td>


                                <td class="px-5 py-4 max-w-md">

                                    <p class="text-xs text-gray-500 line-clamp-2">
                                        <?= htmlspecialchars($row['deskripsi']) ?>
                                    </p>

                                </td>


                                <td class="px-5 py-4 text-center">

                                    <?php if ($row['status'] === 'Aktif'): ?>

                                        <span class="inline-flex items-center gap-1 bg-green-50 text-green-600 border border-green-200 px-2.5 py-1 rounded-full text-xs font-semibold">

                                            <i class="fa-solid fa-circle text-[6px]"></i>
                                            Aktif

                                        </span>

                                    <?php else: ?>

                                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-600 border border-red-200 px-2.5 py-1 rounded-full text-xs font-semibold">

                                            <i class="fa-solid fa-circle text-[6px]"></i>
                                            Nonaktif

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        <a
                                            href="layanan/edit.php?id=<?= $row['id'] ?>"
                                            class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition"
                                            title="Edit"
                                        >

                                            <i class="fa-solid fa-pen"></i>

                                        </a>


                                        <a
                                            href="layanan/hapus.php?id=<?= $row['id'] ?>"
                                            onclick="return confirm('Yakin ingin menghapus layanan ini?')"
                                            class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white flex items-center justify-center transition"
                                            title="Hapus"
                                        >

                                            <i class="fa-solid fa-trash"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="6" class="px-5 py-12 text-center">

                                <div class="text-gray-400">

                                    <i class="fa-solid fa-inbox text-4xl mb-3"></i>

                                    <p class="font-semibold">
                                        Belum ada layanan
                                    </p>

                                    <p class="text-xs mt-1">
                                        Silakan tambahkan layanan baru.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

