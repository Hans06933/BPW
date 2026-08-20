<?php
require_once __DIR__ . '/config/database.php';

/*
|--------------------------------------------------------------------------
| Ambil ID layanan
|--------------------------------------------------------------------------
*/
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: layanan.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Ambil data layanan dari database
|--------------------------------------------------------------------------
*/
try {
    $layanan = db_get(
        "SELECT id, nama_layanan, deskripsi, gambar, status, created_at, updated_at
         FROM layanan
         WHERE id = :id
         LIMIT 1",
        ['id' => $id]
    );
} catch (PDOException $e) {
    die("Terjadi kesalahan database.");
}

/*
|--------------------------------------------------------------------------
| Jika data tidak ditemukan
|--------------------------------------------------------------------------
*/
if (!$layanan) {
    header("Location: layanan.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Fungsi escape HTML
|--------------------------------------------------------------------------
*/
function e($text)
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

/*
|--------------------------------------------------------------------------
| Tentukan path gambar
|--------------------------------------------------------------------------
*/
$gambar = trim((string) ($layanan['gambar'] ?? ''));

if ($gambar === '') {

    $gambarWeb = 'https://placehold.co/1200x600/e2e8f0/64748b?text=Gambar+Tidak+Tersedia';

} else {

    /*
    |--------------------------------------------------------------------------
    | Jika database hanya menyimpan nama file
    | contoh:
    | bus.jpg
    |--------------------------------------------------------------------------
    */
    if (
        strpos($gambar, '/') === false &&
        strpos($gambar, '\\') === false
    ) {
        $gambarWeb = 'images/layanan/' . rawurlencode($gambar);
    } else {

        /*
        |--------------------------------------------------------------------------
        | Jika database sudah menyimpan:
        | images/layanan/nama-file.jpg
        | atau
        | uploads/layanan/nama-file.jpg
        |--------------------------------------------------------------------------
        */
        $gambar = str_replace('\\', '/', $gambar);

        /*
        | Hilangkan ../ jika ada agar URL tetap berada di project
        */
        $gambar = ltrim($gambar, './');

        $gambarWeb = $gambar;
    }
}

/*
|--------------------------------------------------------------------------
| Status layanan
|--------------------------------------------------------------------------
*/
$status = strtolower(trim((string) ($layanan['status'] ?? 'Aktif')));

if ($status === 'aktif') {
    $statusText = 'Layanan Aktif';
    $statusClass = 'bg-green-100 text-green-700';
} else {
    $statusText = 'Layanan Tidak Aktif';
    $statusClass = 'bg-red-100 text-red-700';
}

include __DIR__ . '/layout/header.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= e($layanan['nama_layanan']) ?> - Bayu Prima Wisata
    </title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800 antialiased">

    <!-- ============================================================
         DETAIL LAYANAN
    ============================================================ -->

    <section class="w-full py-12 md:py-20 bg-[#f8fafc]">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-6">

                <a
                    href="layanan.php"
                    class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 transition"
                >
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Layanan</span>
                </a>

            </div>


            <!-- Card Detail -->
            <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">

                <div class="grid grid-cols-1 lg:grid-cols-2">

                    <!-- ====================================================
                         GAMBAR
                    ==================================================== -->

                    <div class="relative bg-slate-100 min-h-[300px] lg:min-h-[500px]">

                        <img
                            src="<?= e($gambarWeb) ?>"
                            alt="<?= e($layanan['nama_layanan']) ?>"
                            class="absolute inset-0 w-full h-full object-cover"
                            onerror="this.onerror=null;this.src='https://placehold.co/1200x600/e2e8f0/64748b?text=Gambar+Tidak+Tersedia';"
                        >

                        <!-- Status -->
                        <div class="absolute top-5 left-5">

                            <span
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold shadow-md <?= e($statusClass) ?>"
                            >

                                <span class="w-2 h-2 rounded-full bg-current"></span>

                                <?= e($statusText) ?>

                            </span>

                        </div>

                    </div>


                    <!-- ====================================================
                         INFORMASI LAYANAN
                    ==================================================== -->

                    <div class="p-6 md:p-10 lg:p-12 flex flex-col justify-center">

                        <p
                            class="text-blue-600 font-semibold text-xs uppercase tracking-wider mb-3"
                        >
                            Layanan Bayu Prima Wisata
                        </p>


                        <h1
                            class="text-2xl md:text-3xl lg:text-4xl font-bold text-[#002244] leading-tight"
                        >
                            <?= e($layanan['nama_layanan']) ?>
                        </h1>


                        <div class="w-16 h-1 bg-blue-600 rounded-full mt-5 mb-6"></div>


                        <!-- Deskripsi -->
                        <div>

                            <h2
                                class="text-lg font-semibold text-[#002244] mb-3"
                            >
                                Tentang Layanan
                            </h2>

                            <div
                                class="text-sm md:text-base text-slate-600 leading-7 whitespace-pre-line"
                            >
                                <?= e($layanan['deskripsi']) ?>
                            </div>

                        </div>


                        <!-- Info -->
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8"
                        >

                            <div
                                class="bg-slate-50 rounded-xl p-4 border border-slate-100"
                            >

                                <div
                                    class="flex items-center gap-3"
                                >

                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center"
                                    >
                                        <i class="fa-solid fa-circle-info"></i>
                                    </div>

                                    <div>

                                        <p class="text-[11px] text-slate-400">
                                            Status
                                        </p>

                                        <p class="text-sm font-semibold text-[#002244]">
                                            <?= e($statusText) ?>
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <div
                                class="bg-slate-50 rounded-xl p-4 border border-slate-100"
                            >

                                <div
                                    class="flex items-center gap-3"
                                >

                                    <div
                                        class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center"
                                    >
                                        <i class="fa-solid fa-headset"></i>
                                    </div>

                                    <div>

                                        <p class="text-[11px] text-slate-400">
                                            Informasi
                                        </p>

                                        <p class="text-sm font-semibold text-[#002244]">
                                            Konsultasi
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- Tombol -->
                        <div
                            class="flex flex-col sm:flex-row gap-3 mt-8"
                        >

                            <a
                                href="layanan.php"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
                            >
                                <i class="fa-solid fa-arrow-left"></i>
                                Kembali
                            </a>


                            <a
                                href="https://wa.me/"
                                target="_blank"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition shadow-md"
                            >
                                <i class="fa-brands fa-whatsapp text-base"></i>
                                Konsultasi Sekarang
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- ============================================================
         FOOTER
    ============================================================ -->

    <?php include __DIR__ . '/layout/footer.php'; ?>

</body>

</html>
