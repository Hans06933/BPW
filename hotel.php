<?php
require_once __DIR__ . '/config/database.php';

// =========================================================
// AMBIL DATA HOTEL
// =========================================================
$hotelRows = db_get_all("
    SELECT
        h.id,
        h.nama_hotel,
        h.slug,
        h.destinasi,
        h.bintang,
        h.harga_per_malam,
        h.deskripsi,
        h.alamat,
        h.fasilitas,
        h.gambar_utama,
        h.rating,
        h.total_review,
        h.status,
        hg.id AS galeri_id,
        hg.gambar AS galeri_gambar
    FROM hotel h
    LEFT JOIN hotel_galeri hg ON hg.hotel_id = h.id
    WHERE h.status = 'aktif'
    ORDER BY h.id DESC, hg.id ASC
");

if (!is_array($hotelRows)) {
    $hotelRows = [];
}

// =========================================================
// KELOMPOKKAN DATA HOTEL + GALERI
// =========================================================
$hotels = [];

foreach ($hotelRows as $row) {
    $hotelId = (int)($row['id'] ?? 0);

    if ($hotelId <= 0) {
        continue;
    }

    if (!isset($hotels[$hotelId])) {
        $hotels[$hotelId] = [
            'id'            => $hotelId,
            'nama_hotel'    => (string)($row['nama_hotel'] ?? ''),
            'slug'          => (string)($row['slug'] ?? ''),
            'destinasi'     => (string)($row['destinasi'] ?? ''),
            'bintang'       => (int)($row['bintang'] ?? 0),
            'harga_per_malam' => (float)($row['harga_per_malam'] ?? 0),
            'deskripsi'     => (string)($row['deskripsi'] ?? ''),
            'alamat'        => (string)($row['alamat'] ?? ''),
            'fasilitas'     => (string)($row['fasilitas'] ?? ''),
            'gambar_utama'  => (string)($row['gambar_utama'] ?? ''),
            'rating'        => (float)($row['rating'] ?? 0),
            'total_review'  => (int)($row['total_review'] ?? 0),
            'status'        => (string)($row['status'] ?? ''),
            'galeri'        => []
        ];
    }

    // Tambahkan gambar galeri
    if (!empty($row['galeri_id']) && !empty($row['galeri_gambar'])) {
        $galleryImage = trim((string)$row['galeri_gambar']);

        if ($galleryImage !== '' && !in_array($galleryImage, $hotels[$hotelId]['galeri'], true)) {
            $hotels[$hotelId]['galeri'][] = $galleryImage;
        }
    }
}

// RESET INDEX ARRAY
$hotels = array_values($hotels);

// =========================================================
// FUNGSI HELPER
// =========================================================
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function formatRupiah($value)
{
    return 'Rp ' . number_format((float)$value, 0, ',', '.');
}

function getHotelImage($image)
{
    $image = trim((string)$image);

    if ($image === '') {
        return 'images/hotel/default-hotel.jpg';
    }

    if (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0 || strpos($image, '//') === 0) {
        return $image;
    }

    return 'images/hotel/' . basename($image);
}

function getHotelGalleryImage($image)
{
    $image = trim((string)$image);

    if ($image === '') {
        return '';
    }

    if (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0 || strpos($image, '//') === 0) {
        return $image;
    }

    return 'images/hotel/' . basename($image);
}

function getFacilityArray($fasilitas)
{
    if (empty($fasilitas)) {
        return [];
    }

    $items = preg_split('/[,;|\r\n]+/', $fasilitas);
    $items = array_map('trim', $items);
    $items = array_filter($items, function ($item) {
        return $item !== '';
    });

    return array_values(array_unique($items));
}

function getFacilityCode($facility)
{
    $facility = strtolower(trim((string)$facility));
    $facility = preg_replace('/[^a-z0-9]+/', '-', $facility);
    $facility = trim($facility, '-');

    return $facility !== '' ? $facility : 'facility';
}

function getAmenityCodes($fasilitasArray)
{
    $amenities = [];

    foreach ($fasilitasArray as $fasilitas) {
        $code = getFacilityCode($fasilitas);

        if ($code !== '' && !in_array($code, $amenities, true)) {
            $amenities[] = $code;
        }
    }

    return $amenities;
}

function getFacilityIcon($fasilitas)
{
    $f = strtolower(trim((string)$fasilitas));

    if (strpos($f, 'wifi') !== false || strpos($f, 'wi-fi') !== false) {
        return 'fa-wifi';
    }

    if (strpos($f, 'sarapan') !== false || strpos($f, 'breakfast') !== false) {
        return 'fa-utensils';
    }

    if (strpos($f, 'kolam') !== false || strpos($f, 'pool') !== false) {
        return 'fa-water-ladder';
    }

    if (strpos($f, 'spa') !== false || strpos($f, 'wellness') !== false) {
        return 'fa-spa';
    }

    if (strpos($f, 'restoran') !== false || strpos($f, 'restaurant') !== false) {
        return 'fa-mug-hot';
    }

    if (strpos($f, 'parkir') !== false || strpos($f, 'parking') !== false) {
        return 'fa-square-parking';
    }

    if (strpos($f, 'ac') !== false || strpos($f, 'air conditioner') !== false) {
        return 'fa-snowflake';
    }

    if (strpos($f, 'tv') !== false || strpos($f, 'televisi') !== false) {
        return 'fa-tv';
    }

    if (strpos($f, 'gym') !== false || strpos($f, 'fitness') !== false) {
        return 'fa-dumbbell';
    }

    if (strpos($f, 'lift') !== false || strpos($f, 'elevator') !== false) {
        return 'fa-elevator';
    }

    if (strpos($f, 'resepsionis') !== false || strpos($f, 'reception') !== false) {
        return 'fa-bell-concierge';
    }

    if (strpos($f, 'laundry') !== false || strpos($f, 'cuci') !== false) {
        return 'fa-shirt';
    }

    if (strpos($f, 'shuttle') !== false || strpos($f, 'antar') !== false) {
        return 'fa-car';
    }

    return 'fa-circle-check';
}

// =========================================================
// FILTER BINTANG DINAMIS
// =========================================================
$availableStars = [];

foreach ($hotels as $hotel) {
    $star = (int)($hotel['bintang'] ?? 0);

    if ($star > 0) {
        $availableStars[] = $star;
    }
}

$availableStars = array_values(array_unique($availableStars));
rsort($availableStars, SORT_NUMERIC);

// =========================================================
// HITUNG HOTEL BERDASARKAN BINTANG
// =========================================================
$starCounts = [];

foreach ($availableStars as $star) {
    $starCounts[$star] = 0;
}

foreach ($hotels as $hotel) {
    $star = (int)($hotel['bintang'] ?? 0);

    if (isset($starCounts[$star])) {
        $starCounts[$star]++;
    }
}

// =========================================================
// FILTER FASILITAS DINAMIS
// =========================================================
$dynamicFacilityFilters = [];

foreach ($hotels as $hotel) {
    $fasilitasArray = getFacilityArray($hotel['fasilitas'] ?? '');

    foreach ($fasilitasArray as $fasilitas) {
        $fasilitas = trim((string)$fasilitas);

        if ($fasilitas === '') {
            continue;
        }

        $code = getFacilityCode($fasilitas);

        if (!isset($dynamicFacilityFilters[$code])) {
            $dynamicFacilityFilters[$code] = [
                'code'  => $code,
                'label' => $fasilitas,
                'count' => 0
            ];
        }

        $dynamicFacilityFilters[$code]['count']++;
    }
}

// =========================================================
// URUTKAN FASILITAS
// =========================================================
uasort($dynamicFacilityFilters, function ($a, $b) {
    return strcasecmp($a['label'], $b['label']);
});

$dynamicFacilityFilters = array_values($dynamicFacilityFilters);

// =========================================================
// HARGA MAKSIMUM DINAMIS
// =========================================================
$maxHotelPrice = 0;

foreach ($hotels as $hotel) {
    $price = (float)($hotel['harga_per_malam'] ?? 0);

    if ($price > $maxHotelPrice) {
        $maxHotelPrice = $price;
    }
}

if ($maxHotelPrice <= 0) {
    $maxHotelPrice = 1000000;
}

$priceFilterMax = (int)(ceil($maxHotelPrice / 100000) * 100000);

// =========================================================
// DATA HOTEL UNTUK JAVASCRIPT
// =========================================================
$hotelJavascriptData = [];

foreach ($hotels as $hotel) {
    $fasilitasArray = getFacilityArray($hotel['fasilitas'] ?? '');
    $amenities = getAmenityCodes($fasilitasArray);

    $mainImage = getHotelImage($hotel['gambar_utama'] ?? '');

    $gallery = [];
    foreach (($hotel['galeri'] ?? []) as $galleryImage) {
        $galleryImage = getHotelGalleryImage($galleryImage);

        if ($galleryImage !== '' && !in_array($galleryImage, $gallery, true)) {
            $gallery[] = $galleryImage;
        }
    }

    $hotelJavascriptData[] = [
        'id'            => (int)$hotel['id'],
        'name'          => (string)($hotel['nama_hotel'] ?? ''),
        'location'      => (string)($hotel['destinasi'] ?? ''),
        'stars'         => (int)($hotel['bintang'] ?? 0),
        'price'         => (float)($hotel['harga_per_malam'] ?? 0),
        'image'         => $mainImage,
        'gallery'       => $gallery,
        'amenities'     => $amenities,
        'facilities'    => $fasilitasArray,
        'description'   => (string)($hotel['deskripsi'] ?? ''),
        'address'       => (string)($hotel['alamat'] ?? ''),
        'rating'        => (float)($hotel['rating'] ?? 0),
        'total_review'  => (int)($hotel['total_review'] ?? 0)
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Hotel & Akomodasi - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }
        .bg-hotel-hero {
            background: linear-gradient(135deg, rgba(0, 51, 102, 0.88) 0%, rgba(0, 76, 153, 0.70) 100%),
                        url('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
        .hotel-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hotel-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -12px rgba(0, 0, 0, 0.20);
        }
        .star-rating i {
            color: #fbbf24;
            font-size: 12px;
        }
        .amenity-badge {
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 10px;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .scroll-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #003366;
            color: white;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 99;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s;
        }
        .scroll-top:hover {
            background: #004c99;
            transform: translateY(-3px);
        }
        input[type="range"] {
            -webkit-appearance: none;
            appearance: none;
            background: #e2e8f0;
            height: 4px;
            border-radius: 5px;
        }
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            background: #003366;
            border-radius: 50%;
            cursor: pointer;
        }
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.80);
            z-index: 9999;
            overflow-y: auto;
            padding: 20px;
        }
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            background: white;
            border-radius: 1.5rem;
        }
        .hotel-card.hidden-card {
            display: none;
        }
        .hotel-gallery-thumb {
            transition: all 0.2s;
        }
        .hotel-gallery-thumb:hover {
            transform: scale(1.03);
        }
        .hotel-gallery-thumb img {
            display: block;
        }
        #hotelMainGalleryImage {
            transition: opacity 0.2s ease;
        }
    </style>
</head>
<body class="bg-slate-50">

<?php include __DIR__ . '/layout/header.php'; ?>

<!-- HERO -->
<section class="bg-hotel-hero py-20 md:py-28 text-white relative">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur rounded-full px-4 py-1.5 mb-5">
                <i class="fa-solid fa-hotel text-yellow-300 text-sm"></i>
                <span class="text-xs font-bold tracking-wide">AKOMODASI TERBAIK</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-4">
                Temukan Hotel<br>
                <span class="text-yellow-300">Impian Anda</span>
            </h1>
            <p class="text-slate-200 text-base max-w-xl leading-relaxed mb-6">
                Dari hotel berbintang hingga resort mewah,
                kami menyediakan akomodasi terbaik di setiap
                destinasi wisata Indonesia.
            </p>
            <div class="bg-white rounded-xl p-3 shadow-2xl mt-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="flex items-center gap-2 px-3 border rounded-lg md:border-0">
                        <i class="fa-solid fa-location-dot text-blue-600"></i>
                        <input type="text" id="destinationSearch" placeholder="Cari Destinasi" class="w-full py-3 text-sm focus:outline-none text-gray-700">
                    </div>
                    <div class="flex items-center gap-2 px-3 border rounded-lg md:border-0">
                        <i class="fa-regular fa-calendar text-blue-600"></i>
                        <input type="date" class="w-full py-3 text-sm focus:outline-none text-gray-700">
                    </div>
                    <div class="flex items-center gap-2 px-3 border rounded-lg md:border-0">
                        <i class="fa-solid fa-user text-blue-600"></i>
                        <select class="w-full py-3 text-sm focus:outline-none text-gray-700 bg-transparent">
                            <option>2 Dewasa, 1 Kamar</option>
                            <option>1 Dewasa, 1 Kamar</option>
                            <option>4 Dewasa, 2 Kamar</option>
                        </select>
                    </div>
                    <button type="button" id="searchHotelBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Cari Hotel
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MAIN CONTENT -->
<section class="py-10 md:py-12 bg-slate-50">
    <div class="container mx-auto px-4 md:px-6 max-w-7xl">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- SIDEBAR -->
            <aside class="lg:w-72 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-24">
                    <h3 class="font-bold text-base text-gray-800 mb-4 pb-2 border-b">Filter Pencarian</h3>

                    <!-- BINTANG DINAMIS -->
                    <div class="mb-5">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">⭐ Bintang Hotel</h4>
                        <div class="space-y-2">
                            <?php if (!empty($availableStars)): ?>
                                <?php foreach ($availableStars as $star): ?>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" class="star-filter w-4 h-4 rounded text-blue-600" value="<?= (int)$star ?>">
                                        <span class="text-sm text-gray-600"><?= (int)$star ?> Bintang</span>
                                        <span class="text-xs text-gray-400 ml-auto"><?= (int)($starCounts[$star] ?? 0) ?> hotel</span>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-xs text-gray-400">Belum ada data bintang hotel.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- HARGA DINAMIS -->
                    <div class="mb-5">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">💰 Rentang Harga / Malam</h4>
                        <input type="range" id="priceRange" min="0" max="<?= (int)$priceFilterMax ?>" step="100000" value="<?= (int)$priceFilterMax ?>" class="w-full">
                        <div class="flex justify-between mt-2 text-xs text-gray-500">
                            <span>Rp 0</span>
                            <span id="priceValue"><?= formatRupiah($priceFilterMax) ?></span>
                        </div>
                    </div>

                    <!-- FASILITAS DINAMIS -->
                    <div class="mb-5">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">🏨 Fasilitas</h4>
                        <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                            <?php if (!empty($dynamicFacilityFilters)): ?>
                                <?php foreach ($dynamicFacilityFilters as $facility): ?>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" class="facility-filter w-4 h-4 rounded text-blue-600" value="<?= e($facility['code']) ?>">
                                        <span class="text-sm text-gray-600"><?= e($facility['label']) ?></span>
                                        <span class="text-xs text-gray-400 ml-auto"><?= (int)($facility['count']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-xs text-gray-400">Belum ada fasilitas hotel.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="button" id="resetFilterBtn" class="w-full border border-gray-300 text-gray-600 hover:bg-gray-50 py-2 rounded-lg text-sm font-semibold transition mt-3">Reset Filter</button>
                </div>
            </aside>

            <!-- HOTEL GRID -->
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Rekomendasi Hotel</h2>
                        <p class="text-xs text-gray-400 mt-1" id="resultCount">Menampilkan <?= count($hotels) ?> hotel</p>
                    </div>
                    <select id="sortHotel" class="border rounded-lg px-3 py-2 text-sm text-gray-600 focus:outline-none">
                        <option value="recommended">Rekomendasi</option>
                        <option value="price-low">Harga Terendah</option>
                        <option value="price-high">Harga Tertinggi</option>
                        <option value="star-high">Bintang Tertinggi</option>
                        <option value="rating-high">Rating Tertinggi</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5" id="hotelGrid">
                    <?php if (empty($hotels)): ?>
                        <div class="col-span-full text-center py-16" id="emptyDatabaseMessage">
                            <i class="fa-solid fa-hotel text-5xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-600">Belum Ada Hotel</h3>
                            <p class="text-sm text-gray-400 mt-1">Hotel yang ditambahkan melalui admin akan muncul di sini.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($hotels as $hotel):
                            $fasilitasArray = getFacilityArray($hotel['fasilitas'] ?? '');
                            $amenities = getAmenityCodes($fasilitasArray);

                            $gambarDB = trim($hotel['gambar_utama'] ?? '');
                            $gambar = $gambarDB !== '' ? getHotelImage($gambarDB) : 'images/hotel/default-hotel.jpg';

                            $gallery = [];
                            foreach (($hotel['galeri'] ?? []) as $galleryImage) {
                                $galleryImage = getHotelGalleryImage($galleryImage);
                                if ($galleryImage !== '' && !in_array($galleryImage, $gallery, true)) {
                                    $gallery[] = $galleryImage;
                                }
                            }

                            $rating = (float)($hotel['rating'] ?? 0);

                            $hotelData = [
                                'id'            => (int)$hotel['id'],
                                'name'          => (string)($hotel['nama_hotel'] ?? ''),
                                'location'      => (string)($hotel['destinasi'] ?? ''),
                                'stars'         => (int)($hotel['bintang'] ?? 0),
                                'price'         => (float)($hotel['harga_per_malam'] ?? 0),
                                'image'         => $gambar,
                                'gallery'       => $gallery,
                                'amenities'     => $amenities,
                                'facilities'    => $fasilitasArray,
                                'description'   => (string)($hotel['deskripsi'] ?? ''),
                                'address'       => (string)($hotel['alamat'] ?? ''),
                                'rating'        => $rating,
                                'total_review'  => (int)($hotel['total_review'] ?? 0)
                            ];
                        ?>
                            <div class="hotel-card bg-white rounded-xl overflow-hidden shadow-md border border-gray-100"
                                 data-id="<?= (int)$hotel['id'] ?>"
                                 data-stars="<?= (int)$hotel['bintang'] ?>"
                                 data-price="<?= (float)$hotel['harga_per_malam'] ?>"
                                 data-rating="<?= $rating ?>"
                                 data-location="<?= e(strtolower($hotel['destinasi'] ?? '')) ?>"
                                 data-name="<?= e(strtolower($hotel['nama_hotel'] ?? '')) ?>"
                                 data-amenities="<?= e(implode(',', $amenities)) ?>">
                                
                                <div class="relative h-48 overflow-hidden">
                                    <img src="<?= e($gambar) ?>" class="w-full h-full object-cover" alt="<?= e($hotel['nama_hotel']) ?>" loading="lazy" onerror="this.onerror=null;this.src='images/hotel/default-hotel.jpg';">
                                    <div class="absolute top-3 left-3 bg-white/90 backdrop-blur px-2 py-1 rounded-lg text-xs font-semibold text-gray-700">⭐ <?= (int)$hotel['bintang'] ?> Bintang</div>
                                    <?php if (count($gallery) > 0): ?>
                                        <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur px-2 py-1 rounded-lg text-white text-xs flex items-center gap-1">
                                            <i class="fa-solid fa-images"></i> <?= count($gallery) ?> Foto
                                        </div>
                                    <?php endif; ?>
                                    <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur px-2 py-1 rounded-lg text-white text-xs">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= (int)$hotel['bintang']): ?>
                                                <i class="fa-solid fa-star text-yellow-400 text-xs"></i>
                                            <?php else: ?>
                                                <i class="fa-regular fa-star text-gray-300 text-xs"></i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1 text-xs text-gray-400 mb-1">
                                                <i class="fa-solid fa-location-dot text-blue-500"></i>
                                                <span><?= e($hotel['destinasi'] ?? '') ?></span>
                                            </div>
                                            <h3 class="font-bold text-base text-gray-800 hover:text-blue-600 transition cursor-pointer truncate" title="<?= e($hotel['nama_hotel'] ?? '') ?>" onclick='showHotelDetail(<?= json_encode($hotelData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                                <?= e($hotel['nama_hotel'] ?? '') ?>
                                            </h3>
                                        </div>
                                        <div class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-lg ml-2 flex-shrink-0">
                                            <?= number_format($rating, 1) ?> ★
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-1.5 mt-2">
                                        <?php if (!empty($fasilitasArray)): ?>
                                            <?php foreach ($fasilitasArray as $fasilitas): ?>
                                                <span class="amenity-badge">
                                                    <i class="fa-solid <?= e(getFacilityIcon($fasilitas)) ?> text-xs"></i>
                                                    <?= e($fasilitas) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="amenity-badge">
                                                <i class="fa-solid fa-circle-info text-xs"></i> Informasi fasilitas belum tersedia
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="border-t mt-3 pt-3 flex justify-between items-center gap-3">
                                        <div>
                                            <span class="text-[10px] text-gray-400">Mulai dari</span>
                                            <p class="text-lg font-bold text-blue-600">
                                                <?= formatRupiah($hotel['harga_per_malam'] ?? 0) ?>
                                                <span class="text-xs text-gray-400">/malam</span>
                                            </p>
                                        </div>
                                        <button type="button" onclick='showHotelDetail(<?= json_encode($hotelData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold transition flex items-center gap-1 flex-shrink-0">
                                            Lihat Detail <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div id="filterEmptyMessage" class="hidden text-center py-16">
                    <i class="fa-regular fa-building text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600">Hotel Tidak Ditemukan</h3>
                    <p class="text-sm text-gray-400 mt-1">Tidak ada hotel yang sesuai dengan filter Anda.</p>
                    <button type="button" id="resetEmptyFilter" class="mt-4 text-blue-600 hover:underline text-sm font-semibold">Reset Filter</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 md:px-6 max-w-6xl">
        <div class="bg-gradient-to-r from-blue-700 to-cyan-600 rounded-2xl p-8 md:p-10 text-white text-center">
            <i class="fa-regular fa-building text-4xl mb-4"></i>
            <h3 class="text-2xl md:text-3xl font-bold mb-3">Butuh Bantuan Pilih Hotel?</h3>
            <p class="text-blue-100 mb-6 max-w-lg mx-auto">Tim kami siap membantu Anda menemukan akomodasi terbaik sesuai budget dan preferensi.</p>
            <a href="#" class="inline-flex items-center gap-2 bg-white text-blue-700 hover:bg-gray-100 px-8 py-3 rounded-full font-semibold transition shadow-lg">
                <i class="fa-brands fa-whatsapp"></i> Konsultasi Gratis
            </a>
        </div>
    </div>
</section>

<!-- HOTEL DETAIL MODAL -->
<div id="hotelModal" class="modal">
    <div class="modal-content">
        <div class="relative">
            <button type="button" class="absolute top-4 right-4 w-10 h-10 bg-black/40 hover:bg-black/60 rounded-full text-white z-20" id="closeModal" aria-label="Tutup">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
            <div id="modalBody"></div>
        </div>
    </div>
</div>

<!-- SCROLL TOP -->
<div class="scroll-top" id="scrollTopBtn"><i class="fa-solid fa-arrow-up"></i></div>

<script>
// DATA HOTEL DARI DATABASE
const hotels = <?= json_encode($hotelJavascriptData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

// NILAI FILTER DARI PHP
const HOTEL_PRICE_MAX = <?= (int)$priceFilterMax ?>;

// FILTER VARIABLE
let activeStars = [];
let activeFacilities = [];
let maxPrice = HOTEL_PRICE_MAX;
let searchDestination = '';

// GALERI HOTEL
let currentHotelImageIndex = 0;
let currentHotelGalleryImages = [];

// FORMAT HARGA
function formatPrice(price) {
    return 'Rp ' + Number(price || 0).toLocaleString('id-ID');
}

// STAR HTML
function getStarsHTML(stars) {
    let html = '';
    stars = parseInt(stars) || 0;

    for (let i = 1; i <= 5; i++) {
        if (i <= stars) {
            html += '<i class="fa-solid fa-star text-yellow-400 text-xs"></i>';
        } else {
            html += '<i class="fa-regular fa-star text-gray-300 text-xs"></i>';
        }
    }
    return html;
}

// ICON AMENITIES
function getAmenityIcon(amenity) {
    const code = String(amenity || '').toLowerCase();

    if (code.includes('wifi')) return 'fa-wifi';
    if (code.includes('sarapan') || code.includes('breakfast')) return 'fa-utensils';
    if (code.includes('kolam') || code.includes('pool')) return 'fa-water-ladder';
    if (code.includes('spa') || code.includes('wellness')) return 'fa-spa';
    if (code.includes('restoran') || code.includes('restaurant')) return 'fa-mug-hot';
    if (code.includes('parkir') || code.includes('parking')) return 'fa-square-parking';
    if (code.includes('ac')) return 'fa-snowflake';
    if (code.includes('tv') || code.includes('televisi')) return 'fa-tv';
    if (code.includes('gym') || code.includes('fitness')) return 'fa-dumbbell';
    if (code.includes('lift') || code.includes('elevator')) return 'fa-elevator';
    if (code.includes('resepsionis') || code.includes('reception')) return 'fa-bell-concierge';
    if (code.includes('laundry') || code.includes('cuci')) return 'fa-shirt';
    if (code.includes('shuttle') || code.includes('antar')) return 'fa-car';

    return 'fa-circle-check';
}

// KODE FASILITAS JAVASCRIPT
function getFacilityCodeJS(facility) {
    return String(facility || '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
}

// FILTER HOTEL
function filterHotels() {
    const cards = document.querySelectorAll('.hotel-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const stars = parseInt(card.dataset.stars || 0);
        const price = parseFloat(card.dataset.price || 0);
        const location = (card.dataset.location || '').toLowerCase();
        const name = (card.dataset.name || '').toLowerCase();
        const amenities = (card.dataset.amenities || '').split(',').map(item => item.trim()).filter(Boolean);

        let starMatch = true;
        if (activeStars.length > 0) {
            starMatch = activeStars.includes(String(stars));
        }

        const priceMatch = price <= maxPrice;

        let facilityMatch = true;
        if (activeFacilities.length > 0) {
            facilityMatch = activeFacilities.every(facility => amenities.includes(facility));
        }

        let destinationMatch = true;
        if (searchDestination !== '') {
            destinationMatch = location.includes(searchDestination) || name.includes(searchDestination);
        }

        const visible = starMatch && priceMatch && facilityMatch && destinationMatch;

        if (visible) {
            card.classList.remove('hidden-card');
            visibleCount++;
        } else {
            card.classList.add('hidden-card');
        }
    });

    const resultCount = document.getElementById('resultCount');
    if (resultCount) {
        resultCount.textContent = `Menampilkan ${visibleCount} hotel`;
    }

    const emptyMessage = document.getElementById('filterEmptyMessage');
    if (emptyMessage) {
        if (cards.length > 0 && visibleCount === 0) {
            emptyMessage.classList.remove('hidden');
        } else {
            emptyMessage.classList.add('hidden');
        }
    }
}

// SORT HOTEL
function sortHotels() {
    const grid = document.getElementById('hotelGrid');
    if (!grid) return;

    const cards = Array.from(grid.querySelectorAll('.hotel-card'));
    const sortType = document.getElementById('sortHotel')?.value || 'recommended';

    cards.sort((a, b) => {
        const priceA = parseFloat(a.dataset.price || 0);
        const priceB = parseFloat(b.dataset.price || 0);
        const starsA = parseInt(a.dataset.stars || 0);
        const starsB = parseInt(b.dataset.stars || 0);
        const ratingA = parseFloat(a.dataset.rating || 0);
        const ratingB = parseFloat(b.dataset.rating || 0);
        const idA = parseInt(a.dataset.id || 0);
        const idB = parseInt(b.dataset.id || 0);

        switch (sortType) {
            case 'price-low': return priceA - priceB;
            case 'price-high': return priceB - priceA;
            case 'star-high': return starsB - starsA;
            case 'rating-high': return ratingB - ratingA;
            default: return idB - idA;
        }
    });

    cards.forEach(card => grid.appendChild(card));
}

// SHOW HOTEL DETAIL
window.showHotelDetail = function(hotel) {
    if (!hotel) return;

    const modal = document.getElementById('hotelModal');
    const modalBody = document.getElementById('modalBody');
    if (!modal || !modalBody) return;

    currentHotelGalleryImages = [];

    if (hotel.image) {
        currentHotelGalleryImages.push(hotel.image);
    }

    if (Array.isArray(hotel.gallery)) {
        hotel.gallery.forEach(function(image) {
            if (image && !currentHotelGalleryImages.includes(image)) {
                currentHotelGalleryImages.push(image);
            }
        });
    }

    currentHotelImageIndex = 0;

    let galleryHTML = '';
    if (currentHotelGalleryImages.length > 0) {
        galleryHTML = `
            <div class="relative bg-black">
                <img id="hotelMainGalleryImage" src="${escapeAttribute(currentHotelGalleryImages[0])}" class="w-full h-64 md:h-80 object-cover rounded-t-3xl" alt="${escapeAttribute(hotel.name)}" onerror="this.onerror=null;this.src='images/hotel/default-hotel.jpg';">
                ${currentHotelGalleryImages.length > 1 ? `
                    <button type="button" onclick="previousHotelImage()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/50 hover:bg-black/70 text-white rounded-full flex items-center justify-center transition">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button type="button" onclick="nextHotelImage()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/50 hover:bg-black/70 text-white rounded-full flex items-center justify-center transition">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                    <div class="absolute bottom-3 right-3 bg-black/60 text-white text-xs px-3 py-1.5 rounded-full">
                        <i class="fa-solid fa-images mr-1"></i>
                        <span id="galleryCounter">1 / ${currentHotelGalleryImages.length}</span>
                    </div>
                ` : ''}
            </div>
            ${currentHotelGalleryImages.length > 1 ? `
                <div class="flex gap-2 p-3 overflow-x-auto bg-gray-50 border-b">
                    ${currentHotelGalleryImages.map(function(image, index) {
                        return `<button type="button" onclick="setHotelImage(${index})" class="hotel-gallery-thumb flex-shrink-0 rounded-lg overflow-hidden border-2 ${index === 0 ? 'border-blue-600' : 'border-transparent'}">
                            <img src="${escapeAttribute(image)}" class="w-20 h-14 object-cover" alt="Galeri ${index + 1}" onerror="this.style.display='none';">
                        </button>`;
                    }).join('')}
                </div>
            ` : ''}
        `;
    } else {
        galleryHTML = `<img src="images/hotel/default-hotel.jpg" class="w-full h-64 md:h-80 object-cover rounded-t-3xl" alt="${escapeAttribute(hotel.name)}">`;
    }

    let amenitiesHTML = '';
    if (Array.isArray(hotel.facilities) && hotel.facilities.length > 0) {
        amenitiesHTML = hotel.facilities.map(function(facility) {
            const code = getFacilityCodeJS(facility);
            return `<span class="bg-gray-100 px-3 py-1.5 rounded-full text-xs text-gray-600">
                <i class="fa-solid ${getAmenityIcon(code)} mr-1"></i> ${escapeHTML(facility)}
            </span>`;
        }).join('');
    } else {
        amenitiesHTML = `<span class="bg-gray-100 px-3 py-1.5 rounded-full text-xs text-gray-600">
            <i class="fa-solid fa-circle-info mr-1"></i> Informasi fasilitas belum tersedia
        </span>`;
    }

    let addressHTML = '';
    if (hotel.address) {
        addressHTML = `
            <div class="flex items-start gap-2 mb-4">
                <i class="fa-solid fa-location-dot text-blue-500 mt-1"></i>
                <div>
                    <p class="text-xs text-gray-400">Alamat</p>
                    <p class="text-sm text-gray-600">${escapeHTML(hotel.address)}</p>
                </div>
            </div>
        `;
    }

    let reviewHTML = '';
    if (hotel.total_review > 0) {
        reviewHTML = `<span class="text-xs text-gray-400">(${Number(hotel.total_review).toLocaleString('id-ID')} review)</span>`;
    }

    modalBody.innerHTML = `
        ${galleryHTML}
        <div class="p-6">
            <div class="flex flex-col md:flex-row justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">${escapeHTML(hotel.name)}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="text-sm text-gray-500"><i class="fa-solid fa-location-dot text-blue-500"></i> ${escapeHTML(hotel.location)}</span>
                        <span class="text-yellow-400">${getStarsHTML(hotel.stars)}</span>
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-lg text-xs font-semibold">${Number(hotel.rating || 0).toFixed(1)} ★</span>
                        ${reviewHTML}
                    </div>
                </div>
                <div class="text-left md:text-right">
                    <span class="text-2xl font-bold text-blue-600">${formatPrice(hotel.price)}</span>
                    <span class="text-xs text-gray-400">/malam</span>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mb-5">${amenitiesHTML}</div>
            ${addressHTML}

            <div class="mb-5">
                <h3 class="font-semibold text-gray-800 mb-2">Tentang Hotel</h3>
                <p class="text-gray-600 text-sm leading-relaxed">${escapeHTML(hotel.description || 'Deskripsi hotel belum tersedia.')}</p>
            </div>

            <div class="border-t pt-5 flex flex-col sm:flex-row gap-3">
                <button type="button" onclick="bookingHotel()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                    <i class="fa-regular fa-calendar-check"></i> Pesan Sekarang
                </button>
                <button type="button" onclick="tanyaHotelViaWA()" class="flex-1 border border-blue-600 text-blue-600 hover:bg-blue-50 py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                    <i class="fa-brands fa-whatsapp"></i> Tanya via WA
                </button>
            </div>
        </div>
    `;

    window.currentHotel = hotel;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
};

// PILIH GAMBAR GALERI
window.setHotelImage = function(index) {
    if (!Array.isArray(currentHotelGalleryImages) || currentHotelGalleryImages.length === 0) return;
    if (index < 0 || index >= currentHotelGalleryImages.length) return;

    currentHotelImageIndex = index;

    const mainImage = document.getElementById('hotelMainGalleryImage');
    if (mainImage) {
        mainImage.style.opacity = '0.4';
        setTimeout(function() {
            mainImage.src = currentHotelGalleryImages[index];
            mainImage.style.opacity = '1';
        }, 100);
    }

    const counter = document.getElementById('galleryCounter');
    if (counter) {
        counter.textContent = `${index + 1} / ${currentHotelGalleryImages.length}`;
    }

    document.querySelectorAll('.hotel-gallery-thumb').forEach(function(button, buttonIndex) {
        if (buttonIndex === index) {
            button.classList.remove('border-transparent');
            button.classList.add('border-blue-600');
        } else {
            button.classList.remove('border-blue-600');
            button.classList.add('border-transparent');
        }
    });
};

// GAMBAR SEBELUMNYA
window.previousHotelImage = function() {
    if (currentHotelGalleryImages.length === 0) return;
    currentHotelImageIndex--;
    if (currentHotelImageIndex < 0) {
        currentHotelImageIndex = currentHotelGalleryImages.length - 1;
    }
    setHotelImage(currentHotelImageIndex);
};

// GAMBAR BERIKUTNYA
window.nextHotelImage = function() {
    if (currentHotelGalleryImages.length === 0) return;
    currentHotelImageIndex++;
    if (currentHotelImageIndex >= currentHotelGalleryImages.length) {
        currentHotelImageIndex = 0;
    }
    setHotelImage(currentHotelImageIndex);
};

// ESCAPE HTML
function escapeHTML(value) {
    return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

function escapeAttribute(value) {
    return escapeHTML(value);
}

// CLOSE MODAL
function closeHotelModal() {
    const modal = document.getElementById('hotelModal');
    if (!modal) return;
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

document.getElementById('closeModal')?.addEventListener('click', closeHotelModal);
document.getElementById('hotelModal')?.addEventListener('click', function(event) {
    if (event.target === document.getElementById('hotelModal')) {
        closeHotelModal();
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') closeHotelModal();
});

// FILTER BINTANG
document.querySelectorAll('.star-filter').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            if (!activeStars.includes(this.value)) activeStars.push(this.value);
        } else {
            activeStars = activeStars.filter(function(star) { return star !== this.value; }.bind(this));
        }
        filterHotels();
    });
});

// FILTER FASILITAS
document.querySelectorAll('.facility-filter').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            if (!activeFacilities.includes(this.value)) activeFacilities.push(this.value);
        } else {
            activeFacilities = activeFacilities.filter(function(facility) { return facility !== this.value; }.bind(this));
        }
        filterHotels();
    });
});

// PRICE RANGE
const priceRange = document.getElementById('priceRange');
const priceValue = document.getElementById('priceValue');

if (priceRange) {
    priceRange.addEventListener('input', function() {
        maxPrice = parseInt(this.value || 0);
        if (priceValue) priceValue.textContent = formatPrice(maxPrice);
        filterHotels();
    });
}

// SEARCH DESTINATION
const destinationSearch = document.getElementById('destinationSearch');
if (destinationSearch) {
    destinationSearch.addEventListener('input', function() {
        searchDestination = this.value.trim().toLowerCase();
        filterHotels();
    });
}

// SEARCH BUTTON
document.getElementById('searchHotelBtn')?.addEventListener('click', function() {
    searchDestination = (destinationSearch?.value || '').trim().toLowerCase();
    filterHotels();
    const grid = document.getElementById('hotelGrid');
    if (grid) grid.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

// SORT
document.getElementById('sortHotel')?.addEventListener('change', function() {
    sortHotels();
    filterHotels();
});

// RESET FILTER
function resetFilters() {
    activeStars = [];
    activeFacilities = [];
    maxPrice = HOTEL_PRICE_MAX;
    searchDestination = '';

    document.querySelectorAll('.star-filter').forEach(function(checkbox) { checkbox.checked = false; });
    document.querySelectorAll('.facility-filter').forEach(function(checkbox) { checkbox.checked = false; });

    if (priceRange) priceRange.value = HOTEL_PRICE_MAX;
    if (priceValue) priceValue.textContent = formatPrice(HOTEL_PRICE_MAX);
    if (destinationSearch) destinationSearch.value = '';

    const sortHotel = document.getElementById('sortHotel');
    if (sortHotel) sortHotel.value = 'recommended';

    document.querySelectorAll('.hotel-card').forEach(function(card) { card.classList.remove('hidden-card'); });

    filterHotels();
    sortHotels();
}

document.getElementById('resetFilterBtn')?.addEventListener('click', resetFilters);
document.getElementById('resetEmptyFilter')?.addEventListener('click', resetFilters);

// BOOKING
function bookingHotel() {
    const hotel = window.currentHotel;
    if (!hotel) return;
    const message = `Halo Bayu Prima Wisata, saya ingin memesan hotel ${hotel.name} di ${hotel.location}. Mohon informasi ketersediaan dan harga.`;
    window.open('https://wa.me/?text=' + encodeURIComponent(message), '_blank');
}

// TANYA VIA WHATSAPP
function tanyaHotelViaWA() {
    const hotel = window.currentHotel;
    if (!hotel) return;
    const message = `Halo Bayu Prima Wisata, saya ingin bertanya mengenai hotel ${hotel.name} di ${hotel.location}.`;
    window.open('https://wa.me/?text=' + encodeURIComponent(message), '_blank');
}

// SCROLL TO TOP
const scrollBtn = document.getElementById('scrollTopBtn');
window.addEventListener('scroll', function() {
    if (!scrollBtn) return;
    if (window.scrollY > 400) {
        scrollBtn.style.display = 'flex';
    } else {
        scrollBtn.style.display = 'none';
    }
});

scrollBtn?.addEventListener('click', function() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// KEYBOARD GALERI
document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('hotelModal');
    if (!modal || !modal.classList.contains('active')) return;
    if (event.key === 'ArrowLeft') previousHotelImage();
    if (event.key === 'ArrowRight') nextHotelImage();
});

// INITIAL
filterHotels();
sortHotels();
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
</body>
</html>