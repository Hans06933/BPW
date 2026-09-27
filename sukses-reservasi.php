<?php
/**
 * ============================================================
 * RESERVASI SUKSES
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/config/database.php';

$kode = trim($_GET['kode'] ?? '');

if ($kode === '') {
    header('Location: index.php');
    exit;
}

$reservasi = db_get("SELECT * FROM pemesanan WHERE kode_booking = :kode LIMIT 1", [':kode' => $kode]);

if (!$reservasi) {
    header('Location: index.php');
    exit;
}

unset($_SESSION['reservasi_success']);

$waAdmin = "6285281441565";
$pesanWa = "Halo Admin, saya sudah reservasi dengan kode " . $reservasi['kode_booking'] . ". Mohon konfirmasinya.";
$urlWa   = "https://wa.me/" . $waAdmin . "?text=" . urlencode($pesanWa);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi Berhasil - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>* { font-family: 'Inter', sans-serif; }</style>
</head>

<body class="bg-slate-50 min-h-screen py-12">

<div class="container mx-auto px-4 max-w-2xl">

    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-check text-green-500 text-3xl"></i>
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-800 mb-2">
            Reservasi Berhasil!
        </h1>
        <p class="text-sm text-gray-500">
            Terima kasih! Tim kami akan segera menghubungi Anda untuk konfirmasi.
        </p>
    </div>

    <!-- KODE BOOKING -->
    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-2xl p-6 text-center mb-6 shadow-xl">
        <div class="text-xs uppercase tracking-widest opacity-80 mb-1">Kode Booking Anda</div>
        <div class="text-2xl md:text-3xl font-extrabold tracking-wider mb-2">
            <?= htmlspecialchars($reservasi['kode_booking']) ?>
        </div>
        <p class="text-[11px] opacity-90">
            <i class="fa-solid fa-circle-info"></i>
            Simpan kode ini untuk referensi pemesanan Anda.
        </p>
    </div>

    <!-- DETAIL -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-clipboard-list text-blue-600"></i> Detail Reservasi
        </h2>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Paket</span>
                <span class="font-semibold text-gray-800 text-right"><?= htmlspecialchars($reservasi['nama_paket']) ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Nama</span>
                <span class="font-semibold text-gray-800"><?= htmlspecialchars($reservasi['nama_lengkap']) ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Email</span>
                <span class="font-semibold text-gray-800"><?= htmlspecialchars($reservasi['email']) ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">No. HP</span>
                <span class="font-semibold text-gray-800"><?= htmlspecialchars($reservasi['no_hp']) ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Tanggal</span>
                <span class="font-semibold text-gray-800"><?= date('d F Y', strtotime($reservasi['tanggal_berangkat'])) ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Peserta</span>
                <span class="font-semibold text-gray-800">
                    <?= (int) $reservasi['jumlah_dewasa'] ?> Dewasa,
                    <?= (int) $reservasi['jumlah_anak'] ?> Anak
                </span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-500">Metode Bayar</span>
                <span class="font-semibold text-gray-800"><?= htmlspecialchars($reservasi['metode_bayar']) ?></span>
            </div>
            <div class="flex justify-between items-center pt-2">
                <span class="text-gray-500">Total Estimasi</span>
                <span class="text-xl font-extrabold text-blue-700">
                    Rp <?= number_format((float) $reservasi['total_harga'], 0, ',', '.') ?>
                </span>
            </div>
        </div>
    </div>

    <!-- NEXT STEP -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 mb-6">
        <div class="flex items-start gap-2">
            <i class="fa-solid fa-circle-info text-yellow-600 mt-0.5"></i>
            <div class="text-xs text-yellow-800 space-y-1">
                <p class="font-bold">Langkah Selanjutnya:</p>
                <ol class="list-decimal pl-4 space-y-0.5">
                    <li>Tim kami akan menghubungi Anda via WhatsApp/email dalam 1x24 jam.</li>
                    <li>Konfirmasi ketersediaan paket & detail harga final.</li>
                    <li>Lakukan pembayaran DP sesuai instruksi admin.</li>
                    <li>Anda akan menerima e-ticket & detail perjalanan.</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- ACTION -->
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="<?= $urlWa ?>" target="_blank"
           class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl font-semibold text-sm transition">
            <i class="fa-brands fa-whatsapp text-lg"></i>
            Konfirmasi via WhatsApp
        </a>
        <a href="index.php"
           class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold text-sm transition">
            <i class="fa-solid fa-house"></i>
            Kembali ke Beranda
        </a>
    </div>

</div>

</body>
</html>