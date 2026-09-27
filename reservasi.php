<?php
/**
 * ============================================================
 * FORM RESERVASI PAKET WISATA
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once 'config/database.php';

$db = (new Database())->getConnection();

// =========================================================
// AMBIL SLUG PAKET DARI URL
// =========================================================
$slug = trim($_GET['paket'] ?? '');

if ($slug === '') {
    header('Location: paket_wisata.php');
    exit;
}

// =========================================================
// AMBIL DATA PAKET
// =========================================================
try {
    $stmt = $db->prepare("
        SELECT *
        FROM paket_wisata
        WHERE slug = :slug
        AND status = 'aktif'
        LIMIT 1
    ");
    $stmt->execute([':slug' => $slug]);
    $paket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paket) {
        header('Location: paket_wisata.php');
        exit;
    }
} catch (PDOException $e) {
    die('Gagal mengambil data: ' . $e->getMessage());
}

// =========================================================
// HARGA FINAL
// =========================================================
$hargaFinal     = !empty($paket['harga_diskon']) ? (float) $paket['harga_diskon'] : (float) $paket['harga_normal'];
$hargaNormalRaw = (float) $paket['harga_normal'];
$hargaFinalRaw  = $hargaFinal;

// =========================================================
// AMBIL ERROR / OLD INPUT
// =========================================================
$errors = $_SESSION['reservasi_error'] ?? [];
$old    = $_SESSION['reservasi_old'] ?? [];
unset($_SESSION['reservasi_error'], $_SESSION['reservasi_old']);

// =========================================================
// WHATSAPP
// =========================================================
$namaPaket = $paket['nama_paket'] ?? 'Paket Wisata';
$urlWa     = "https://wa.me/6285281441565?text=" . urlencode("Halo Admin, saya ingin bertanya mengenai paket: " . $namaPaket);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi: <?= htmlspecialchars($namaPaket) ?> - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        .bg-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #6d28d9 100%);
        }

        .input-field {
            transition: all 0.2s;
        }
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }

        .sticky-sidebar {
            position: sticky;
            top: 100px;
        }
        @media (max-width: 1024px) {
            .sticky-sidebar { position: static; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

<!-- NAVBAR -->
<header class="bg-white/80 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="h-16 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-2 font-extrabold text-xl bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                <i class="fa-solid fa-plane-departure text-blue-600"></i>
                Bayu Prima Wisata
            </a>
            <a href="detail-paket.php?slug=<?= urlencode($paket['slug']) ?>" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition group">
                <i class="fa-solid fa-arrow-left mr-1 group-hover:-translate-x-1 transition"></i>
                Kembali ke Detail Paket
            </a>
        </div>
    </div>
</header>

<!-- HERO -->
<section class="bg-hero text-white py-12 md:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-1.5 mb-4">
            <i class="fa-solid fa-calendar-check text-yellow-300 text-sm"></i>
            <span class="text-xs font-bold tracking-wide">FORM RESERVASI</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold mb-3">
            Reservasi Paket Wisata
        </h1>
        <p class="text-blue-100 text-sm md:text-base max-w-xl mx-auto">
            Isi data di bawah untuk memesan paket <strong><?= htmlspecialchars($namaPaket) ?></strong>.
        </p>
    </div>
</section>

<!-- MAIN CONTENT -->
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 -mt-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KIRI: FORM -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-slate-100 p-6 md:p-8">

                <h2 class="text-xl font-bold text-slate-800 mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-blue-600"></i>
                    Data Pemesan
                </h2>
                <p class="text-xs text-slate-400 mb-6">
                    Pastikan data yang diisi sudah benar.
                </p>

                <?php if (!empty($errors)): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded-md text-xs mb-5">
                        <ul class="list-disc pl-4 space-y-0.5">
                            <?php foreach ($errors as $err): ?>
                                <li><?= htmlspecialchars($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="proses-reservasi.php" method="POST" class="space-y-4">

                    <input type="hidden" name="paket_id" value="<?= (int) $paket['id'] ?>">
                    <input type="hidden" name="nama_paket" value="<?= htmlspecialchars($namaPaket) ?>">
                    <input type="hidden" name="harga_per_orang" value="<?= $hargaFinalRaw ?>">

                    <!-- Nama -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_lengkap" required
                               value="<?= htmlspecialchars($old['nama_lengkap'] ?? '') ?>"
                               placeholder="Cth: Budi Santoso"
                               class="input-field w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Email & HP -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" required
                                   value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                   placeholder="budi@email.com"
                                   class="input-field w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                                No. HP / WhatsApp <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="no_hp" required
                                   value="<?= htmlspecialchars($old['no_hp'] ?? '') ?>"
                                   placeholder="0812xxxxxxx"
                                   class="input-field w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Kota -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kota Asal</label>
                        <input type="text" name="kota"
                               value="<?= htmlspecialchars($old['kota'] ?? '') ?>"
                               placeholder="Cth: Jakarta"
                               class="input-field w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tanggal Berangkat <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal_berangkat" required
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                               value="<?= htmlspecialchars($old['tanggal_berangkat'] ?? '') ?>"
                               class="input-field w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Peserta -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Dewasa</label>
                            <input type="number" name="jumlah_dewasa" min="1"
                                   value="<?= htmlspecialchars($old['jumlah_dewasa'] ?? 1) ?>"
                                   oninput="hitungTotal()"
                                   class="input-field w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Anak-anak</label>
                            <input type="number" name="jumlah_anak" min="0"
                                   value="<?= htmlspecialchars($old['jumlah_anak'] ?? 0) ?>"
                                   oninput="hitungTotal()"
                                   class="input-field w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Metode Bayar -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Metode Bayar</label>
                        <select name="metode_bayar"
                                class="input-field w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500">
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Cash">Cash / Tunai</option>
                            <option value="QRIS">QRIS</option>
                            <option value="E-Wallet">E-Wallet (OVO/Gopay/Dana)</option>
                        </select>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Catatan (opsional)</label>
                        <textarea name="catatan" rows="3"
                                  placeholder="Contoh: minta kamar twin, vegetarian, dll..."
                                  class="input-field w-full px-4 py-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500"><?= htmlspecialchars($old['catatan'] ?? '') ?></textarea>
                    </div>

                    <!-- Total -->
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-slate-600">Total Peserta</span>
                            <span class="text-sm font-bold text-slate-800" id="totalPeserta">1</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-slate-600">Estimasi Total</span>
                            <span class="text-xl font-extrabold text-blue-700" id="totalHarga">
                                Rp <?= number_format($hargaFinalRaw, 0, ',', '.') ?>
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2">
                            <i class="fa-solid fa-circle-info"></i>
                            Harga final akan dikonfirmasi admin.
                        </p>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-4 rounded-xl font-bold text-sm transition inline-flex items-center justify-center gap-2 shadow-lg">
                        <i class="fa-solid fa-paper-plane"></i>
                        Kirim Reservasi
                    </button>

                    <p class="text-[10px] text-slate-400 text-center leading-relaxed">
                        Dengan memesan, Anda menyetujui syarat & ketentuan kami.
                    </p>

                </form>
            </div>
        </div>

        <!-- KANAN: RINGKASAN PAKET -->
        <div class="lg:col-span-1">
            <div class="sticky-sidebar space-y-4">

                <!-- Card Paket -->
                <div class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden">
                    <img src="<?= htmlspecialchars('images/paket/' . basename($paket['gambar_utama'] ?? '')) ?>"
                         alt="<?= htmlspecialchars($namaPaket) ?>"
                         class="w-full h-40 object-cover"
                         onerror="this.src='https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80';">

                    <div class="p-5">
                        <div class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-1">
                            Paket Wisata
                        </div>
                        <h3 class="font-bold text-slate-800 mb-3 leading-tight">
                            <?= htmlspecialchars($namaPaket) ?>
                        </h3>

                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-500">
                                    <i class="fa-solid fa-location-dot text-blue-500"></i> Destinasi
                                </span>
                                <span class="font-semibold text-slate-800"><?= htmlspecialchars($paket['destinasi']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">
                                    <i class="fa-regular fa-clock text-blue-500"></i> Durasi
                                </span>
                                <span class="font-semibold text-slate-800"><?= htmlspecialchars($paket['durasi']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">
                                    <i class="fa-solid fa-users text-blue-500"></i> Min. Peserta
                                </span>
                                <span class="font-semibold text-slate-800"><?= (int) ($paket['minimal_peserta'] ?? 1) ?> orang</span>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 my-4"></div>

                        <div class="text-xs text-slate-500 mb-1">Harga per orang</div>
                        <?php if (!empty($paket['harga_diskon'])): ?>
                            <div class="text-xs text-slate-400 line-through">
                                Rp <?= number_format($hargaNormalRaw, 0, ',', '.') ?>
                            </div>
                            <div class="text-2xl font-extrabold text-blue-700">
                                Rp <?= number_format($hargaFinalRaw, 0, ',', '.') ?>
                            </div>
                        <?php else: ?>
                            <div class="text-2xl font-extrabold text-blue-700">
                                Rp <?= number_format($hargaNormalRaw, 0, ',', '.') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Info Bantuan -->
                <div class="bg-gradient-to-br from-green-500 to-emerald-600 text-white rounded-2xl p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-brands fa-whatsapp text-2xl"></i>
                        <div class="font-bold">Butuh Bantuan?</div>
                    </div>
                    <p class="text-xs text-green-50 mb-3">
                        Hubungi kami jika ada pertanyaan.
                    </p>
                    <a href="<?= $urlWa ?>" target="_blank"
                       class="inline-flex items-center gap-2 bg-white text-green-700 px-4 py-2 rounded-lg text-xs font-bold hover:bg-green-50 transition w-full justify-center">
                        <i class="fa-brands fa-whatsapp"></i> Chat Sekarang
                    </a>
                </div>

                <!-- Trust -->
                <div class="bg-white rounded-2xl p-4 border border-slate-100">
                    <div class="flex items-center justify-center gap-3 text-[10px] text-slate-400">
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-lock text-blue-400"></i>
                            Aman
                        </span>
                        <span class="w-px h-3 bg-slate-200"></span>
                        <span class="flex items-center gap-1">
                            <i class="fa-regular fa-clock text-blue-400"></i>
                            Cepat
                        </span>
                        <span class="w-px h-3 bg-slate-200"></span>
                        <span class="flex items-center gap-1">
                            <i class="fa-regular fa-star text-yellow-400"></i>
                            Terpercaya
                        </span>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

<script>
    // ============================================================
    // HITUNG TOTAL OTOMATIS
    // ============================================================
    const HARGA_PER_ORANG = <?= (float) $hargaFinalRaw ?>;

    function hitungTotal() {
        const dewasa = parseInt(document.querySelector('input[name="jumlah_dewasa"]').value) || 0;
        const anak   = parseInt(document.querySelector('input[name="jumlah_anak"]').value) || 0;

        const totalPeserta = dewasa + anak;
        const totalHarga   = totalPeserta * HARGA_PER_ORANG;

        document.getElementById('totalPeserta').textContent = totalPeserta;
        document.getElementById('totalHarga').textContent   = 'Rp ' + totalHarga.toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', hitungTotal);
</script>

</body>
</html>