<?php
/**
 * ============================================================
 * PROSES RESERVASI PAKET WISATA
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/config/database.php';

// ============================================================
// HANYA IZINKAN POST
// ============================================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// ============================================================
// AMBIL INPUT
// ============================================================
$paketId          = (int) ($_POST['paket_id'] ?? 0);
$namaPaket        = trim($_POST['nama_paket'] ?? '');
$hargaPerOrang    = (float) ($_POST['harga_per_orang'] ?? 0);

$namaLengkap      = trim($_POST['nama_lengkap'] ?? '');
$email            = trim($_POST['email'] ?? '');
$noHp             = trim($_POST['no_hp'] ?? '');
$kota             = trim($_POST['kota'] ?? '');
$alamat           = trim($_POST['alamat'] ?? '');

$tanggalBerangkat = trim($_POST['tanggal_berangkat'] ?? '');
$jumlahDewasa     = max(0, (int) ($_POST['jumlah_dewasa'] ?? 1));
$jumlahAnak       = max(0, (int) ($_POST['jumlah_anak'] ?? 0));
$metodeBayar      = trim($_POST['metode_bayar'] ?? 'Transfer Bank');
$catatan          = trim($_POST['catatan'] ?? '');

// ============================================================
// CARI SLUG PAKET UNTUK REDIRECT
// ============================================================
$slugPaket = '';
try {
    $p = db_get("SELECT slug FROM paket_wisata WHERE id = :id", [':id' => $paketId]);
    if ($p) $slugPaket = $p['slug'];
} catch (PDOException $e) {
    // silent
}

$redirectReservasi = 'reservasi.php?paket=' . urlencode($slugPaket);

// ============================================================
// VALIDASI
// ============================================================
$errors = [];

if ($namaLengkap === '')  $errors[] = 'Nama lengkap wajib diisi.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
if ($noHp === '')         $errors[] = 'Nomor HP wajib diisi.';
if ($tanggalBerangkat === '') $errors[] = 'Tanggal berangkat wajib diisi.';
if ($tanggalBerangkat !== '' && strtotime($tanggalBerangkat) < strtotime('today')) {
    $errors[] = 'Tanggal berangkat tidak boleh di masa lalu.';
}
if ($jumlahDewasa + $jumlahAnak < 1) $errors[] = 'Minimal 1 peserta.';

if (!empty($errors)) {
    $_SESSION['reservasi_error'] = $errors;
    $_SESSION['reservasi_old']   = $_POST;
    header('Location: ' . $redirectReservasi);
    exit;
}

// ============================================================
// HITUNG TOTAL
// ============================================================
$totalPeserta = $jumlahDewasa + $jumlahAnak;
$totalHarga   = $totalPeserta * $hargaPerOrang;

// ============================================================
// GENERATE KODE BOOKING
// ============================================================
$kodeBooking = 'BPW-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

// ============================================================
// SIMPAN KE DATABASE
// ============================================================
try {
    $pemesananId = db_insert('pemesanan', [
        'kode_booking'      => $kodeBooking,
        'paket_id'          => $paketId,
        'nama_paket'        => $namaPaket,
        'nama_lengkap'      => $namaLengkap,
        'email'             => $email,
        'no_hp'             => $noHp,
        'kota'              => $kota,
        'alamat'            => $alamat,
        'tanggal_berangkat' => $tanggalBerangkat,
        'jumlah_dewasa'     => $jumlahDewasa,
        'jumlah_anak'       => $jumlahAnak,
        'total_peserta'     => $totalPeserta,
        'total_harga'       => $totalHarga,
        'catatan'           => $catatan,
        'metode_bayar'      => $metodeBayar,
        'status'            => 'pending',
        'is_read'           => 0,
    ]);

    // ========================================================
    // KIRIM NOTIFIKASI KE PESAN MASUK ADMIN
    // ========================================================
    if (function_exists('kirim_pesan_admin')) {
        kirim_pesan_admin(
            'reservasi',
            'Reservasi Baru: ' . $kodeBooking,
            'Paket "' . $namaPaket . '" oleh ' . $namaLengkap .
                ' (' . $totalPeserta . ' peserta, ' . date('d M Y', strtotime($tanggalBerangkat)) . '). ' .
                'Total: Rp ' . number_format($totalHarga, 0, ',', '.') . '.',
            'pemesanan.php',
            $namaLengkap,
            $email,
            $noHp
        );
    }

    $_SESSION['reservasi_success'] = 'Reservasi berhasil! Kode Booking: ' . $kodeBooking;

    header('Location: sukses-reservasi.php?kode=' . urlencode($kodeBooking));
    exit;

} catch (PDOException $e) {
    $_SESSION['reservasi_error'] = ['Gagal menyimpan reservasi: ' . $e->getMessage()];
    $_SESSION['reservasi_old']   = $_POST;
    header('Location: ' . $redirectReservasi);
    exit;
}