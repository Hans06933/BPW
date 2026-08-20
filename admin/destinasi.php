<?php

require_once __DIR__ . '/../config/database.php';

try {

    // =========================================================
    // KONEKSI DATABASE
    // =========================================================
    $db = (new Database())->getConnection();


    // =========================================================
    // MATIKAN CACHE BROWSER
    // Supaya setelah tambah/edit/hapus data langsung diperbarui
    // =========================================================
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: 0");


    // =========================================================
    // AMBIL FILTER
    // =========================================================
    $keyword = isset($_GET['keyword'])
        ? trim($_GET['keyword'])
        : '';

    $status = isset($_GET['status'])
        ? trim($_GET['status'])
        : '';


    // =========================================================
    // HITUNG SEMUA DATA DESTINASI
    // =========================================================
    $stmtTotal = $db->query("
        SELECT COUNT(*) 
        FROM destinasi
    ");

    $totalSemuaDestinasi = (int) $stmtTotal->fetchColumn();


    // =========================================================
    // AMBIL DATA DESTINASI
    // =========================================================
    $sql = "
        SELECT
            id,
            nama_destinasi,
            slug,
            wilayah,
            kategori,
            deskripsi,
            alamat,
            harga_tiket_masuk,
            jam_operasional,
            gambar_utama,
            rating,
            total_review,
            status,
            views,
            created_at,
            updated_at
        FROM destinasi
        WHERE 1 = 1
    ";

    $params = [];


    // =========================================================
    // SEARCH
    // =========================================================
    if ($keyword !== '') {

        $sql .= "
            AND (
                nama_destinasi LIKE :keyword1
                OR wilayah LIKE :keyword2
                OR slug LIKE :keyword3
                OR kategori LIKE :keyword4
            )
        ";

        $search = '%' . $keyword . '%';

        $params[':keyword1'] = $search;
        $params[':keyword2'] = $search;
        $params[':keyword3'] = $search;
        $params[':keyword4'] = $search;
    }


    // =========================================================
    // FILTER STATUS
    // =========================================================
    if ($status === 'aktif' || $status === 'nonaktif') {

        $sql .= "
            AND status = :status
        ";

        $params[':status'] = $status;
    }


    // =========================================================
    // URUTKAN DATA
    // =========================================================
    $sql .= "
        ORDER BY id DESC
    ";


    // =========================================================
    // EXECUTE
    // =========================================================
    $stmt = $db->prepare($sql);
    $stmt->execute($params);


    // =========================================================
    // AMBIL HASIL
    // =========================================================
    $destinasi = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // =========================================================
    // JUMLAH DATA HASIL FILTER
    // =========================================================
    $totalDestinasi = count($destinasi);

} catch (PDOException $e) {

    die("
        <div style='
            font-family:Arial;
            padding:30px;
            background:#fee2e2;
            color:#991b1b;
            margin:20px;
            border-radius:10px;
        '>

            <h2>Database Error</h2>

            <p>
                " . htmlspecialchars($e->getMessage()) . "
            </p>

        </div>
    ");
}
require_once '../layout/admin_header.php';

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Destinasi - Admin BPW</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body class="bg-slate-100">


<div class="max-w-7xl mx-auto p-4 md:p-6">


    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>

            <h1 class="text-2xl font-bold text-slate-800">
                Kelola Destinasi
            </h1>

            <p class="text-sm text-slate-500">
                Kelola data destinasi wisata yang tampil di website.
            </p>

        </div>


        <a
            href="destinasi/tambah.php"
            class="inline-flex items-center justify-center gap-2
                   bg-blue-600 hover:bg-blue-700
                   text-white px-5 py-2.5
                   rounded-lg font-semibold"
        >

            <i class="fa-solid fa-plus"></i>

            Tambah Destinasi

        </a>

    </div>



    <!-- =====================================================
         NOTIFIKASI
    ====================================================== -->

    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>

        <div class="mb-5 bg-green-50 border border-green-200
                    text-green-700 px-4 py-3 rounded-lg">

            <i class="fa-solid fa-circle-check mr-1"></i>

            Data destinasi berhasil disimpan.

        </div>

    <?php elseif (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>

        <div class="mb-5 bg-green-50 border border-green-200
                    text-green-700 px-4 py-3 rounded-lg">

            <i class="fa-solid fa-circle-check mr-1"></i>

            Data destinasi berhasil dihapus.

        </div>

    <?php elseif (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>

        <div class="mb-5 bg-green-50 border border-green-200
                    text-green-700 px-4 py-3 rounded-lg">

            <i class="fa-solid fa-circle-check mr-1"></i>

            Data destinasi berhasil diperbarui.

        </div>

    <?php elseif (isset($_GET['status']) && $_GET['status'] === 'error'): ?>

        <div class="mb-5 bg-red-50 border border-red-200
                    text-red-700 px-4 py-3 rounded-lg">

            <i class="fa-solid fa-circle-xmark mr-1"></i>

            Terjadi kesalahan saat memproses data destinasi.

        </div>

    <?php endif; ?>



    <!-- =====================================================
         SEARCH & FILTER
    ====================================================== -->

    <form
        method="GET"
        action="destinasi.php"
        class="bg-white p-4 rounded-xl
               border border-slate-200
               shadow-sm mb-6"
    >

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">


            <!-- KEYWORD -->

            <input
                type="text"
                name="keyword"
                value="<?= htmlspecialchars($keyword) ?>"
                placeholder="Cari nama, wilayah, kategori, atau slug..."
                class="w-full border border-slate-300
                       rounded-lg px-4 py-2.5
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-500"
            >


            <!-- STATUS -->

            <select
                name="status"
                class="w-full border border-slate-300
                       rounded-lg px-4 py-2.5
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-500"
            >

                <option value="">
                    Semua Status
                </option>

                <option
                    value="aktif"
                    <?= $status === 'aktif' ? 'selected' : '' ?>
                >
                    Aktif
                </option>

                <option
                    value="nonaktif"
                    <?= $status === 'nonaktif' ? 'selected' : '' ?>
                >
                    Nonaktif
                </option>

            </select>


            <!-- BUTTON -->

            <button
                type="submit"
                class="bg-slate-800
                       hover:bg-slate-900
                       text-white
                       rounded-lg
                       px-4 py-2.5
                       font-semibold"
            >

                <i class="fa-solid fa-magnifying-glass mr-1"></i>

                Cari

            </button>

        </div>

    </form>



    <!-- =====================================================
         INFO DATA
    ====================================================== -->

    <div class="mb-4">

        <p class="text-sm text-slate-500">

            Menampilkan

            <span class="font-bold text-slate-800">
                <?= $totalDestinasi ?>
            </span>

            dari

            <span class="font-bold text-blue-600">
                <?= $totalSemuaDestinasi ?>
            </span>

            destinasi wisata

        </p>

    </div>



    <!-- =====================================================
         TABLE
    ====================================================== -->

    <div
        class="bg-white rounded-xl
               border border-slate-200
               shadow-sm overflow-hidden"
    >

        <div class="overflow-x-auto">

            <table class="w-full text-sm">


                <!-- TABLE HEADER -->

                <thead
                    class="bg-slate-50
                           border-b
                           border-slate-200"
                >

                    <tr>

                        <th class="text-left px-5 py-4">
                            Gambar
                        </th>

                        <th class="text-left px-5 py-4">
                            Destinasi
                        </th>

                        <th class="text-left px-5 py-4">
                            Wilayah
                        </th>

                        <th class="text-left px-5 py-4">
                            Kategori
                        </th>

                        <th class="text-left px-5 py-4">
                            Harga Tiket
                        </th>

                        <th class="text-left px-5 py-4">
                            Rating
                        </th>

                        <th class="text-left px-5 py-4">
                            Status
                        </th>

                        <th class="text-right px-5 py-4">
                            Aksi
                        </th>

                    </tr>

                </thead>



                <!-- TABLE BODY -->

                <tbody class="divide-y divide-slate-100">


                <?php if (empty($destinasi)): ?>

                    <tr>

                        <td
                            colspan="8"
                            class="px-5 py-12 text-center"
                        >

                            <div class="flex flex-col items-center">

                                <div
                                    class="w-14 h-14
                                           bg-slate-100
                                           rounded-full
                                           flex items-center
                                           justify-center
                                           mb-3"
                                >

                                    <i
                                        class="fa-solid
                                               fa-location-dot
                                               text-slate-400
                                               text-xl"
                                    ></i>

                                </div>

                                <p
                                    class="text-slate-500
                                           font-medium"
                                >
                                    Belum ada data destinasi.
                                </p>

                                <p
                                    class="text-slate-400
                                           text-xs mt-1"
                                >
                                    Data destinasi dari database
                                    akan muncul di sini.
                                </p>

                            </div>

                        </td>

                    </tr>


                <?php else: ?>


                    <?php foreach ($destinasi as $row): ?>


                        <tr class="hover:bg-slate-50">


                            <!-- =================================================
                                 GAMBAR
                            ================================================== -->

                            <td class="px-5 py-4">

                                <?php if (!empty($row['gambar_utama'])): ?>

                                    <img
                                        src="../images/destinasi/<?= htmlspecialchars($row['gambar_utama']) ?>"
                                        alt="<?= htmlspecialchars($row['nama_destinasi']) ?>"
                                        class="w-24 h-16
                                               object-cover
                                               rounded-lg
                                               border
                                               border-slate-200"
                                        onerror="
                                            this.style.display='none';
                                            this.nextElementSibling.style.display='flex';
                                        "
                                    >

                                    <div
                                        class="w-24 h-16
                                               rounded-lg
                                               bg-red-50
                                               border
                                               border-red-100
                                               items-center
                                               justify-center
                                               text-red-400"
                                        style="display:none;"
                                    >

                                        <i class="fa-solid fa-image"></i>

                                    </div>

                                <?php else: ?>

                                    <div
                                        class="w-24 h-16
                                               rounded-lg
                                               bg-slate-100
                                               flex items-center
                                               justify-center
                                               text-slate-400"
                                    >

                                        <i class="fa-regular fa-image"></i>

                                    </div>

                                <?php endif; ?>

                            </td>



                            <!-- =================================================
                                 NAMA DESTINASI
                            ================================================== -->

                            <td class="px-5 py-4">

                                <div
                                    class="font-semibold
                                           text-slate-800"
                                >

                                    <?= htmlspecialchars(
                                        $row['nama_destinasi'] ?? '-'
                                    ) ?>

                                </div>

                                <div
                                    class="text-xs
                                           text-slate-400"
                                >

                                    <?= htmlspecialchars(
                                        $row['slug'] ?? '-'
                                    ) ?>

                                </div>

                            </td>



                            <!-- =================================================
                                 WILAYAH
                            ================================================== -->

                            <td class="px-5 py-4">

                                <?= htmlspecialchars(
                                    $row['wilayah'] ?? '-'
                                ) ?>

                            </td>



                            <!-- =================================================
                                 KATEGORI
                            ================================================== -->

                            <td class="px-5 py-4 capitalize">

                                <?= htmlspecialchars(
                                    $row['kategori'] ?? '-'
                                ) ?>

                            </td>



                            <!-- =================================================
                                 HARGA TIKET
                            ================================================== -->

                            <td class="px-5 py-4">

                                <?php if (
                                    isset($row['harga_tiket_masuk']) &&
                                    $row['harga_tiket_masuk'] !== null &&
                                    $row['harga_tiket_masuk'] !== ''
                                ): ?>

                                    Rp
                                    <?= number_format(
                                        (float)$row['harga_tiket_masuk'],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                <?php else: ?>

                                    <span class="text-slate-400">
                                        Gratis / -
                                    </span>

                                <?php endif; ?>

                            </td>



                            <!-- =================================================
                                 RATING
                            ================================================== -->

                            <td class="px-5 py-4">

                                <div>

                                    <?= number_format(
                                        (float)($row['rating'] ?? 0),
                                        1
                                    ) ?>

                                    <span class="text-amber-500">
                                        ★
                                    </span>

                                </div>

                                <div
                                    class="text-xs
                                           text-slate-400"
                                >

                                    <?= (int)(
                                        $row['total_review'] ?? 0
                                    ) ?>

                                    review

                                </div>

                            </td>



                            <!-- =================================================
                                 STATUS
                            ================================================== -->

                            <td class="px-5 py-4">

                                <?php if (
                                    ($row['status'] ?? '') === 'aktif'
                                ): ?>

                                    <span
                                        class="px-2.5 py-1
                                               rounded-full
                                               text-xs
                                               font-semibold
                                               bg-green-100
                                               text-green-700"
                                    >

                                        Aktif

                                    </span>

                                <?php else: ?>

                                    <span
                                        class="px-2.5 py-1
                                               rounded-full
                                               text-xs
                                               font-semibold
                                               bg-red-100
                                               text-red-700"
                                    >

                                        Nonaktif

                                    </span>

                                <?php endif; ?>

                            </td>



                            <!-- =================================================
                                 AKSI
                            ================================================== -->

                            <td
                                class="px-5 py-4
                                       text-right
                                       whitespace-nowrap"
                            >


                                <!-- EDIT -->

                                <a
                                    href="destinasi/edit.php?id=<?= (int)$row['id'] ?>"
                                    class="inline-flex
                                           items-center
                                           justify-center
                                           w-9 h-9
                                           rounded-lg
                                           bg-blue-50
                                           text-blue-600
                                           hover:bg-blue-100"
                                    title="Edit Destinasi"
                                >

                                    <i class="fa-solid fa-pen"></i>

                                </a>



                                <!-- HAPUS -->

                                <a
                                    href="destinasi/hapus.php?id=<?= (int)$row['id'] ?>"
                                    onclick="
                                        return confirm(
                                            'Yakin ingin menghapus destinasi ini?'
                                        );
                                    "
                                    class="inline-flex
                                           items-center
                                           justify-center
                                           w-9 h-9
                                           rounded-lg
                                           bg-red-50
                                           text-red-600
                                           hover:bg-red-100
                                           ml-1"
                                    title="Hapus Destinasi"
                                >

                                    <i class="fa-solid fa-trash"></i>

                                </a>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                <?php endif; ?>


                </tbody>

            </table>

        </div>

    </div>


</div>


</body>

</html>