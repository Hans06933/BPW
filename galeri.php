<?php
/**
 * ============================================================
 * HALAMAN GALERI - BAYU PRIMA WISATA
 * ============================================================
 */

// Koneksi database
require_once __DIR__ . '/config/database.php';

// ============================================================
// AMBIL DATA GALERI DARI DATABASE
// ============================================================

try {
    $galeriData = db_get_all("
        SELECT 
            id,
            judul,
            slug,
            gambar,
            kategori,
            deskripsi,
            status,
            created_at
        FROM galeri
        WHERE status = 'aktif'
        ORDER BY id DESC
    ");
} catch (Exception $e) {
    $galeriData = [];
}

// ============================================================
// FUNGSI UNTUK MENENTUKAN PATH GAMBAR
// ============================================================

function getGaleriImage($gambar)
{
    if (empty($gambar)) {
        return null;
    }

    $gambar = trim($gambar);

    /*
     * Jika database sudah menyimpan:
     * images/galeri/nama.jpg
     */
    if (
        strpos($gambar, 'images/galeri/') === 0 ||
        strpos($gambar, '/images/galeri/') === 0
    ) {
        return ltrim($gambar, '/');
    }

    /*
     * Jika database hanya menyimpan:
     * nama.jpg
     */
    return 'images/galeri/' . basename($gambar);
}

// ============================================================
// FUNGSI UNTUK ESCAPE OUTPUT
// ============================================================

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// ============================================================
// AMBIL KATEGORI UNIK
// ============================================================

$kategoriList = [];

foreach ($galeriData as $item) {
    if (!empty($item['kategori'])) {
        $kategori = trim($item['kategori']);

        if (!in_array($kategori, $kategoriList)) {
            $kategoriList[] = $kategori;
        }
    }
}

sort($kategoriList);

// ============================================================
// HEADER
// ============================================================

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

    <title>Galeri Wisata - Bayu Prima Wisata</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>

        body {
            font-family: 'Poppins', sans-serif;
        }

        .bg-custom-blue {
            background-color: #003366;
        }

        .text-custom-blue {
            color: #003366;
        }

        .gallery-card {
            transition: all 0.3s ease;
        }

        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.10);
        }

        .gallery-image {
            transition: transform 0.5s ease;
        }

        .gallery-card:hover .gallery-image {
            transform: scale(1.06);
        }

        .category-button.active {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .modal-image {
            max-height: 80vh;
            object-fit: contain;
        }

        @media (max-width: 640px) {

            .hero-gallery {
                padding-top: 3rem;
                padding-bottom: 4rem;
            }

        }

    </style>

</head>

<body class="bg-slate-50 text-gray-800">

<!-- ============================================================
     HERO
============================================================ -->

<section
    class="hero-gallery bg-gradient-to-r from-[#003366] via-[#00509e] to-[#0077b6] text-white py-16 md:py-24"
>

    <div
        class="container mx-auto px-4 md:px-6 max-w-6xl"
    >

        <span
            class="inline-block bg-blue-500/30 border border-blue-300/30 px-4 py-1.5 rounded-full text-[10px] md:text-xs font-bold uppercase tracking-wider mb-4"
        >
            Galeri Wisata
        </span>

        <h1
            class="text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight"
        >
            Jelajahi Momen
            <br>
            <span class="text-cyan-300">
                Perjalanan Bersama BPW
            </span>
        </h1>

        <p
            class="text-slate-200 text-xs sm:text-sm md:text-base max-w-2xl mt-4 leading-relaxed"
        >
            Lihat berbagai dokumentasi perjalanan wisata,
            destinasi menarik, dan pengalaman liburan bersama
            Bayu Prima Wisata.
        </p>

    </div>

</section>


<!-- ============================================================
     FILTER KATEGORI
============================================================ -->

<section class="relative -mt-7 z-10">

    <div
        class="container mx-auto px-4 md:px-6 max-w-6xl"
    >

        <div
            class="bg-white rounded-xl shadow-lg border border-gray-100 p-3 md:p-4"
        >

            <div
                class="flex flex-wrap gap-2 justify-center"
                id="categoryContainer"
            >

                <!-- Semua -->
                <button
                    type="button"
                    class="category-button active px-4 py-2 rounded-lg border border-gray-200 text-[10px] md:text-xs font-semibold transition"
                    data-category="semua"
                >
                    <i class="fa-solid fa-border-all mr-1"></i>
                    Semua
                </button>


                <?php foreach ($kategoriList as $kategori): ?>

                    <button
                        type="button"
                        class="category-button px-4 py-2 rounded-lg border border-gray-200 text-[10px] md:text-xs font-semibold text-gray-600 hover:border-blue-400 hover:text-blue-600 transition"
                        data-category="<?= e(strtolower($kategori)); ?>"
                    >
                        <i class="fa-solid fa-images mr-1"></i>

                        <?= e(ucwords($kategori)); ?>

                    </button>

                <?php endforeach; ?>

            </div>

        </div>

    </div>

</section>


<!-- ============================================================
     GALERI
============================================================ -->

<section class="py-10 md:py-16 bg-white">

    <div
        class="container mx-auto px-4 md:px-6 max-w-6xl"
    >

        <!-- Header -->
        <div
            class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8"
        >

            <div>

                <span
                    class="text-blue-600 font-extrabold text-[10px] md:text-xs uppercase tracking-wider block mb-1"
                >
                    Dokumentasi
                </span>

                <h2
                    class="text-2xl sm:text-3xl md:text-4xl font-bold text-custom-blue"
                >
                    Galeri Perjalanan
                </h2>

                <p
                    class="text-xs md:text-sm text-gray-400 mt-2"
                >
                    Kumpulan dokumentasi Perjalanan Wisata BPW.
                </p>

            </div>


            <!-- Search -->
            <div
                class="relative w-full md:w-72"
            >

                <i
                    class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"
                ></i>

                <input
                    type="text"
                    id="gallerySearch"
                    placeholder="Cari galeri..."
                    class="w-full border border-gray-200 rounded-lg pl-9 pr-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400"
                >

            </div>

        </div>


        <!-- ====================================================
             GRID GALERI
        ==================================================== -->

        <?php if (!empty($galeriData)): ?>

            <div
                id="galleryGrid"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-6"
            >

                <?php foreach ($galeriData as $galeri): ?>

                    <?php

                    $gambarPath = getGaleriImage($galeri['gambar']);

                    $kategori = !empty($galeri['kategori'])
                        ? $galeri['kategori']
                        : 'Wisata';

                    $judul = !empty($galeri['judul'])
                        ? $galeri['judul']
                        : 'Galeri Wisata';

                    $deskripsi = !empty($galeri['deskripsi'])
                        ? $galeri['deskripsi']
                        : 'Dokumentasi perjalanan wisata bersama Bayu Prima Wisata.';

                    ?>

                    <div
                        class="gallery-card gallery-item bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm"
                        data-category="<?= e(strtolower($kategori)); ?>"
                        data-title="<?= e(strtolower($judul)); ?>"
                    >

                        <!-- Gambar -->
                        <div
                            class="relative h-56 sm:h-60 md:h-64 overflow-hidden bg-slate-100"
                        >

                            <?php if ($gambarPath): ?>

                                <img
                                    src="<?= e($gambarPath); ?>"
                                    alt="<?= e($judul); ?>"
                                    class="gallery-image w-full h-full object-cover"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                >

                                <!-- Fallback jika gambar tidak ditemukan -->
                                <div
                                    class="absolute inset-0 hidden items-center justify-center bg-slate-100 text-gray-400"
                                >

                                    <div class="text-center">

                                        <i
                                            class="fa-solid fa-image text-3xl mb-2"
                                        ></i>

                                        <p class="text-xs">
                                            Gambar Tidak Tersedia
                                        </p>

                                    </div>

                                </div>

                            <?php else: ?>

                                <div
                                    class="w-full h-full flex items-center justify-center bg-slate-100 text-gray-400"
                                >

                                    <div class="text-center">

                                        <i
                                            class="fa-solid fa-image text-3xl mb-2"
                                        ></i>

                                        <p class="text-xs">
                                            Gambar Tidak Tersedia
                                        </p>

                                    </div>

                                </div>

                            <?php endif; ?>


                            <!-- Overlay -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"
                            ></div>


                            <!-- Kategori -->
                            <div
                                class="absolute bottom-3 left-3"
                            >

                                <span
                                    class="bg-white/95 backdrop-blur text-blue-700 px-3 py-1.5 rounded-lg text-[9px] md:text-[10px] font-bold shadow-sm"
                                >

                                    <i
                                        class="fa-solid fa-tag mr-1"
                                    ></i>

                                    <?= e(ucwords($kategori)); ?>

                                </span>

                            </div>


                            <!-- Tombol lihat -->
                            <?php if ($gambarPath): ?>

                                <button
                                    type="button"
                                    class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 text-blue-600 flex items-center justify-center shadow hover:bg-white transition"
                                    onclick="openGalleryModal(
                                        '<?= e($gambarPath); ?>',
                                        '<?= e($judul); ?>'
                                    )"
                                    title="Lihat gambar"
                                >

                                    <i
                                        class="fa-solid fa-expand"
                                    ></i>

                                </button>

                            <?php endif; ?>

                        </div>


                        <!-- Informasi -->
                        <div class="p-4 md:p-5">

                            <h3
                                class="font-bold text-sm md:text-base text-slate-800 line-clamp-2"
                            >
                                <?= e($judul); ?>
                            </h3>


                            <p
                                class="text-[10px] md:text-xs text-gray-400 leading-relaxed mt-2 line-clamp-3"
                            >
                                <?= e($deskripsi); ?>
                            </p>


                            <div
                                class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100"
                            >

                                <span
                                    class="text-[9px] md:text-[10px] text-gray-400"
                                >

                                    <i
                                        class="fa-regular fa-image mr-1"
                                    ></i>

                                    Dokumentasi BPW

                                </span>


                                <button
                                    type="button"
                                    onclick="openGalleryModal(
                                        '<?= e($gambarPath); ?>',
                                        '<?= e($judul); ?>'
                                    )"
                                    class="text-blue-600 hover:text-blue-700 text-[10px] md:text-xs font-bold flex items-center gap-1"
                                >

                                    Lihat Foto

                                    <i
                                        class="fa-solid fa-arrow-right text-[9px]"
                                    ></i>

                                </button>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>


            <!-- Tidak ditemukan saat search -->
            <div
                id="noSearchResult"
                class="hidden text-center py-16"
            >

                <div
                    class="w-16 h-16 mx-auto bg-slate-100 rounded-full flex items-center justify-center text-gray-400 mb-4"
                >

                    <i
                        class="fa-solid fa-magnifying-glass text-xl"
                    ></i>

                </div>

                <h3
                    class="font-bold text-gray-700 text-sm"
                >
                    Galeri tidak ditemukan
                </h3>

                <p
                    class="text-xs text-gray-400 mt-1"
                >
                    Coba gunakan kata pencarian lainnya.
                </p>

            </div>

        <?php else: ?>

            <!-- =================================================
                 BELUM ADA DATA
            ================================================= -->

            <div
                class="text-center py-20 border border-gray-100 rounded-xl bg-slate-50"
            >

                <div
                    class="w-20 h-20 mx-auto bg-white rounded-full shadow-sm flex items-center justify-center text-gray-300 mb-5"
                >

                    <i
                        class="fa-solid fa-images text-3xl"
                    ></i>

                </div>

                <h3
                    class="font-bold text-gray-700 text-base"
                >
                    Belum Ada Galeri
                </h3>

                <p
                    class="text-xs text-gray-400 mt-2"
                >
                    Data galeri yang ditambahkan melalui halaman admin
                    akan tampil di sini.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>


<!-- ============================================================
     MODAL GAMBAR
============================================================ -->

<div
    id="galleryModal"
    class="fixed inset-0 bg-black/80 z-[9999] hidden items-center justify-center p-4"
>

    <div
        class="relative max-w-5xl w-full flex items-center justify-center"
    >

        <button
            type="button"
            onclick="closeGalleryModal()"
            class="absolute -top-10 right-0 md:right-2 text-white text-xl hover:text-gray-300"
        >

            <i class="fa-solid fa-xmark"></i>

        </button>


        <div
            class="bg-white rounded-xl overflow-hidden shadow-2xl max-w-5xl w-full"
        >

            <div
                class="bg-slate-900 flex items-center justify-center"
            >

                <img
                    id="modalImage"
                    src=""
                    alt=""
                    class="modal-image w-full"
                >

            </div>


            <div
                class="p-4 bg-white"
            >

                <h3
                    id="modalTitle"
                    class="font-bold text-sm md:text-base text-slate-800"
                ></h3>

            </div>

        </div>

    </div>

</div>


<!-- ============================================================
     JAVASCRIPT
============================================================ -->

<script>

/*
|--------------------------------------------------------------------------
| FILTER KATEGORI
|--------------------------------------------------------------------------
*/

const categoryButtons = document.querySelectorAll('.category-button');

const galleryItems = document.querySelectorAll('.gallery-item');

const gallerySearch = document.getElementById('gallerySearch');

const noSearchResult = document.getElementById('noSearchResult');

let activeCategory = 'semua';


categoryButtons.forEach(button => {

    button.addEventListener('click', function () {

        categoryButtons.forEach(btn => {
            btn.classList.remove('active');
        });

        this.classList.add('active');

        activeCategory = this.dataset.category;

        filterGallery();

    });

});


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

if (gallerySearch) {

    gallerySearch.addEventListener('input', function () {

        filterGallery();

    });

}


/*
|--------------------------------------------------------------------------
| FILTER GALLERY
|--------------------------------------------------------------------------
*/

function filterGallery() {

    const keyword = gallerySearch
        ? gallerySearch.value.toLowerCase().trim()
        : '';

    let visibleCount = 0;


    galleryItems.forEach(item => {

        const category = item.dataset.category || '';

        const title = item.dataset.title || '';

        const categoryMatch =
            activeCategory === 'semua' ||
            category === activeCategory;

        const searchMatch =
            keyword === '' ||
            title.includes(keyword) ||
            category.includes(keyword);


        if (categoryMatch && searchMatch) {

            item.classList.remove('hidden');

            visibleCount++;

        } else {

            item.classList.add('hidden');

        }

    });


    if (noSearchResult) {

        if (visibleCount === 0) {

            noSearchResult.classList.remove('hidden');

        } else {

            noSearchResult.classList.add('hidden');

        }

    }

}


/*
|--------------------------------------------------------------------------
| MODAL
|--------------------------------------------------------------------------
*/

function openGalleryModal(image, title) {

    if (!image) {
        return;
    }

    const modal = document.getElementById('galleryModal');

    const modalImage = document.getElementById('modalImage');

    const modalTitle = document.getElementById('modalTitle');


    modalImage.src = image;

    modalImage.alt = title;

    modalTitle.textContent = title;


    modal.classList.remove('hidden');

    modal.classList.add('flex');


    document.body.style.overflow = 'hidden';

}


function closeGalleryModal() {

    const modal = document.getElementById('galleryModal');

    const modalImage = document.getElementById('modalImage');


    modal.classList.add('hidden');

    modal.classList.remove('flex');


    modalImage.src = '';

    document.body.style.overflow = '';

}


/*
|--------------------------------------------------------------------------
| TUTUP MODAL KLIK BACKDROP
|--------------------------------------------------------------------------
*/

document
    .getElementById('galleryModal')
    ?.addEventListener('click', function (event) {

        if (event.target === this) {

            closeGalleryModal();

        }

    });


/*
|--------------------------------------------------------------------------
| TUTUP MODAL DENGAN ESC
|--------------------------------------------------------------------------
*/

document.addEventListener('keydown', function (event) {

    if (event.key === 'Escape') {

        closeGalleryModal();

    }

});

</script>


<?php
// ============================================================
// FOOTER
// ============================================================

include __DIR__ . '/layout/footer.php';
?>

</body>
</html>