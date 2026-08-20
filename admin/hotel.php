<?php
include "../layout/admin_header.php";
require_once __DIR__ . '/../config/database.php';

try {
    $hotel = db_get_all("
        SELECT
            id,
            nama_hotel,
            slug,
            destinasi,
            bintang,
            harga_per_malam,
            deskripsi,
            alamat,
            fasilitas,
            gambar_utama,
            rating,
            total_review,
            status,
            created_at
        FROM hotel
        ORDER BY id DESC
    ");
} catch (Exception $e) {
    $hotel = [];
    $error = $e->getMessage();
}

function gambarHotel($gambar)
{
    if (empty($gambar)) {
        return null;
    }

    $gambar = trim($gambar);

    if (
        strpos($gambar, 'http://') === 0 ||
        strpos($gambar, 'https://') === 0
    ) {
        return $gambar;
    }

    if (strpos($gambar, 'images/') === 0) {
        return '../' . $gambar;
    }

    if (strpos($gambar, 'uploads/') === 0) {
        return '../' . $gambar;
    }

    return '../images/hotel/' . ltrim($gambar, '/');
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Hotel - Admin BPW</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-100 text-slate-800">

<div class="min-h-screen">

    <!-- HEADER -->
    <header class="bg-[#002244] text-white shadow-lg">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="h-16 flex items-center justify-between">

                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-hotel text-blue-300"></i>
                    </div>

                    <div>
                        <h1 class="font-bold text-lg">
                            Kelola Hotel
                        </h1>

                        <p class="text-[10px] text-slate-300">
                            Admin Bayu Prima Wisata
                        </p>
                    </div>

                </div>

                <a
                    href="../index.php"
                    class="text-xs bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg transition"
                >
                    <i class="fa-solid fa-house mr-1"></i>
                    Website
                </a>

            </div>

        </div>

    </header>


    <!-- CONTENT -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- TITLE -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

            <div>
                <h2 class="text-2xl font-bold text-[#002244]">
                    Data Hotel
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Kelola data hotel yang tersedia di website.
                </p>
            </div>

            <a
                href="hotel/tambah.php"
                class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg text-sm font-semibold transition shadow-sm"
            >
                <i class="fa-solid fa-plus"></i>
                Tambah Hotel
            </a>

        </div>


        <!-- PESAN -->
        <?php if (isset($_GET['success'])): ?>

            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                <i class="fa-solid fa-circle-check mr-2"></i>

                <?= htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>


        <?php if (isset($_GET['error'])): ?>

            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <i class="fa-solid fa-circle-exclamation mr-2"></i>

                <?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>


        <?php if (isset($error)): ?>

            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                <strong>Error database:</strong>
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>


        <!-- TABLE -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-[#002244] text-white">

                        <tr>

                            <th class="px-4 py-4 text-left">
                                #
                            </th>

                            <th class="px-4 py-4 text-left">
                                Hotel
                            </th>

                            <th class="px-4 py-4 text-left">
                                Destinasi
                            </th>

                            <th class="px-4 py-4 text-left">
                                Bintang
                            </th>

                            <th class="px-4 py-4 text-left">
                                Harga / Malam
                            </th>

                            <th class="px-4 py-4 text-left">
                                Rating
                            </th>

                            <th class="px-4 py-4 text-left">
                                Status
                            </th>

                            <th class="px-4 py-4 text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-slate-100">

                    <?php if (!empty($hotel)): ?>

                        <?php foreach ($hotel as $index => $item): ?>

                            <?php
                            $gambar = gambarHotel($item['gambar_utama'] ?? '');
                            ?>

                            <tr class="hover:bg-slate-50 transition">

                                <!-- NOMOR -->
                                <td class="px-4 py-4 text-slate-500">
                                    <?= $index + 1 ?>
                                </td>


                                <!-- HOTEL -->
                                <td class="px-4 py-4">

                                    <div class="flex items-center gap-3">

                                        <?php if ($gambar): ?>

                                            <img
                                                src="<?= htmlspecialchars($gambar, ENT_QUOTES, 'UTF-8') ?>"
                                                alt="<?= htmlspecialchars($item['nama_hotel'], ENT_QUOTES, 'UTF-8') ?>"
                                                class="w-16 h-12 object-cover rounded-lg border"
                                                onerror="this.style.display='none';"
                                            >

                                        <?php else: ?>

                                            <div class="w-16 h-12 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                                                <i class="fa-solid fa-image"></i>
                                            </div>

                                        <?php endif; ?>


                                        <div>

                                            <div class="font-semibold text-[#002244]">
                                                <?= htmlspecialchars($item['nama_hotel'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>

                                            <div class="text-[11px] text-slate-400">
                                                <?= htmlspecialchars($item['slug'], ENT_QUOTES, 'UTF-8') ?>
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <!-- DESTINASI -->
                                <td class="px-4 py-4 text-slate-600">
                                    <?= htmlspecialchars($item['destinasi'], ENT_QUOTES, 'UTF-8') ?>
                                </td>


                                <!-- BINTANG -->
                                <td class="px-4 py-4">

                                    <div class="flex items-center gap-1 text-yellow-500">

                                        <?php
                                        $bintang = (int)$item['bintang'];

                                        for ($i = 1; $i <= 5; $i++):
                                        ?>

                                            <i
                                                class="fa-<?= $i <= $bintang ? 'solid' : 'regular' ?> fa-star text-xs"
                                            ></i>

                                        <?php endfor; ?>

                                    </div>

                                </td>


                                <!-- HARGA -->
                                <td class="px-4 py-4">

                                    <span class="font-semibold text-[#002244]">
                                        Rp <?= number_format((float)$item['harga_per_malam'], 0, ',', '.') ?>
                                    </span>

                                </td>


                                <!-- RATING -->
                                <td class="px-4 py-4">

                                    <div class="text-yellow-500 font-semibold">
                                        <i class="fa-solid fa-star text-xs"></i>

                                        <?= number_format((float)$item['rating'], 1) ?>
                                    </div>

                                    <div class="text-[10px] text-slate-400">
                                        <?= (int)$item['total_review'] ?> review
                                    </div>

                                </td>


                                <!-- STATUS -->
                                <td class="px-4 py-4">

                                    <?php if (strtolower($item['status']) === 'aktif'): ?>

                                        <span class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-3 py-1 rounded-full text-[11px] font-semibold">
                                            <i class="fa-solid fa-circle text-[6px]"></i>
                                            Aktif
                                        </span>

                                    <?php else: ?>

                                        <span class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-3 py-1 rounded-full text-[11px] font-semibold">
                                            <i class="fa-solid fa-circle text-[6px]"></i>
                                            Nonaktif
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- AKSI -->
                                <td class="px-4 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        <a
                                            href="hotel/edit.php?id=<?= (int)$item['id'] ?>"
                                            class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition"
                                            title="Edit"
                                        >
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>


                                        <a
                                            href="hotel/hapus.php?id=<?= (int)$item['id'] ?>"
                                            onclick="return confirm('Yakin ingin menghapus hotel ini?')"
                                            class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition"
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

                            <td
                                colspan="8"
                                class="px-4 py-16 text-center"
                            >

                                <div class="text-slate-400">

                                    <i class="fa-solid fa-hotel text-5xl mb-4"></i>

                                    <p class="font-semibold text-slate-600">
                                        Belum ada data hotel
                                    </p>

                                    <p class="text-xs mt-1">
                                        Silakan tambahkan hotel baru.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>

</body>
</html>