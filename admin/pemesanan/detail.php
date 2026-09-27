<?php
/**
 * ============================================================
 * ADMIN - DETAIL PEMESANAN + UBAH STATUS
 * Bayu Prima Wisata
 * ============================================================
 */

ob_start();
session_start();
require_once __DIR__ . '/../../config/database.php';

// ============================================================
// AMBIL ID
// ============================================================
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['admin_msg'] = 'ID pemesanan tidak valid.';
    $_SESSION['admin_msg_type'] = 'error';
    header('Location: ../pemesanan.php');
    exit;
}

// ============================================================
// AMBIL DATA
// ============================================================
$p = null;

try {
    $p = db_get("SELECT * FROM pemesanan WHERE id = :id LIMIT 1", [':id' => $id]);
} catch (PDOException $e) {
    $_SESSION['admin_msg'] = 'Error query: ' . $e->getMessage();
    $_SESSION['admin_msg_type'] = 'error';
    header('Location: ../pemesanan.php');
    exit;
}

// ============================================================
// GUARD
// ============================================================
if (!$p || !is_array($p)) {
    $_SESSION['admin_msg'] = 'Pemesanan dengan ID ' . $id . ' tidak ditemukan.';
    $_SESSION['admin_msg_type'] = 'error';
    header('Location: ../pemesanan.php');
    exit;
}

// ============================================================
// AUTO MARK AS READ
// ============================================================
if (empty($p['is_read'])) {
    try {
        db_update('pemesanan', ['is_read' => 1], 'id', $id);
    } catch (PDOException $e) {
        // silent
    }
}

// ============================================================
// HANDLE UPDATE STATUS
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $_POST['status'];
    $allowed   = ['pending', 'dikonfirmasi', 'dp', 'lunas', 'batal', 'selesai'];

    if (in_array($newStatus, $allowed, true)) {
        try {
            db_update('pemesanan', ['status' => $newStatus], 'id', $id);
            $_SESSION['admin_msg'] = 'Status berhasil diubah menjadi ' . ucfirst($newStatus) . '.';
            $_SESSION['admin_msg_type'] = 'success';
        } catch (PDOException $e) {
            $_SESSION['admin_msg'] = 'Gagal ubah status: ' . $e->getMessage();
            $_SESSION['admin_msg_type'] = 'error';
        }
    } else {
        $_SESSION['admin_msg'] = 'Status tidak valid.';
        $_SESSION['admin_msg_type'] = 'error';
    }

    header('Location: detail.php?id=' . $id);
    exit;
}

// ============================================================
// NOTIFIKASI
// ============================================================
$msg     = $_SESSION['admin_msg'] ?? null;
$msgType = $_SESSION['admin_msg_type'] ?? 'success';
unset($_SESSION['admin_msg'], $_SESSION['admin_msg_type']);

// ============================================================
// HELPER
// ============================================================
function safe($arr, $key, $default = '') {
    return isset($arr[$key]) && $arr[$key] !== '' ? $arr[$key] : $default;
}

function badgeStatus($status) {
    $map = [
        'pending'      => ['bg-yellow-100 text-yellow-700', 'fa-clock',          'Pending'],
        'dikonfirmasi' => ['bg-blue-100 text-blue-700',    'fa-check',           'Dikonfirmasi'],
        'dp'           => ['bg-purple-100 text-purple-700','fa-money-bill-wave', 'DP'],
        'lunas'        => ['bg-green-100 text-green-700',  'fa-circle-check',    'Lunas'],
        'batal'        => ['bg-red-100 text-red-700',      'fa-circle-xmark',    'Batal'],
        'selesai'      => ['bg-slate-200 text-slate-700',  'fa-flag-checkered',  'Selesai'],
    ];
    return $map[$status] ?? ['bg-gray-100 text-gray-700', 'fa-circle', ucfirst($status ?: '-')];
}

// ============================================================
// AMBIL NILAI AMAN
// ============================================================
$status       = safe($p, 'status', 'pending');
$kodeBook     = safe($p, 'kode_booking', '-');
$namaPemesan  = safe($p, 'nama_lengkap', '-');
$email        = safe($p, 'email', '');
$noHp         = safe($p, 'no_hp', '');
$kota         = safe($p, 'kota', '');
$namaPaket    = safe($p, 'nama_paket', '-');
$tglBerangkat = safe($p, 'tanggal_berangkat', '');
$totalPeserta = (int) safe($p, 'total_peserta', 0);
$jumlahDewasa = (int) safe($p, 'jumlah_dewasa', 0);
$jumlahAnak   = (int) safe($p, 'jumlah_anak', 0);
$metodeBayar  = safe($p, 'metode_bayar', '-');
$totalHarga   = (float) safe($p, 'total_harga', 0);
$catatan      = safe($p, 'catatan', '');
$createdAt    = safe($p, 'created_at', '');
$pemesananId  = (int) safe($p, 'id', 0);

list($badgeClass, $badgeIcon, $badgeLabel) = badgeStatus($status);

// WhatsApp
$waNumber = $noHp !== '' ? preg_replace('/^0/', '62', preg_replace('/\D/', '', $noHp)) : '';
$waText   = urlencode('Halo ' . $namaPemesan . ', terkait reservasi ' . $kodeBook . ' untuk paket ' . $namaPaket . '.');

include __DIR__ . '/../../layout/admin_header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemesanan - Admin BPW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>* { font-family: 'Poppins', sans-serif; }</style>
</head>

<body class="bg-slate-50 text-gray-800">

<!-- BREADCRUMB -->
<div class="bg-white border-b">
    <div class="container mx-auto px-4 md:px-6 max-w-5xl py-3">
        <nav class="flex items-center gap-2 text-xs text-gray-500 flex-wrap">
            <a href="../pemesanan.php" class="hover:text-blue-600 transition">
                <i class="fa-solid fa-cart-shopping"></i> Pemesanan
            </a>
            <i class="fa-solid fa-chevron-right text-[8px]"></i>
            <span class="text-gray-800 font-semibold">Detail</span>
        </nav>
    </div>
</div>

<!-- NOTIFIKASI -->
<?php if ($msg): ?>
    <div id="adminNotif" class="container mx-auto px-4 md:px-6 max-w-5xl mt-4">
        <div class="<?= $msgType === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700' ?> border-l-4 p-3 rounded-md text-sm flex items-center gap-2">
            <i class="fa-solid <?= $msgType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
            <?= htmlspecialchars($msg) ?>
        </div>
    </div>
    <script>setTimeout(() => document.getElementById('adminNotif')?.remove(), 4000);</script>
<?php endif; ?>


<!-- CONTENT -->
<section class="container mx-auto px-4 md:px-6 max-w-5xl mt-6 pb-16">

    <!-- HEADER CARD -->
    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-2xl p-6 mb-6 shadow-lg">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="text-xs uppercase tracking-widest opacity-80 mb-1">Kode Booking</div>
                <div class="text-2xl md:text-3xl font-extrabold tracking-wider mb-2">
                    <?= htmlspecialchars($kodeBook) ?>
                </div>
                <div class="text-xs opacity-90">
                    <i class="fa-regular fa-calendar"></i>
                    Dibuat: <?= $createdAt ? date('d F Y, H:i', strtotime($createdAt)) : '-' ?>
                </div>
            </div>
            <div class="text-center">
                <span class="text-xs font-bold px-4 py-2 rounded-full bg-white/20 backdrop-blur border border-white/30">
                    <i class="fa-solid <?= $badgeIcon ?>"></i>
                    <?= $badgeLabel ?>
                </span>
            </div>
        </div>
    </div>


    <!-- GRID INFO -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        <!-- INFO PEMESAN -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                <i class="fa-solid fa-user text-blue-600"></i>
                Data Pemesan
            </h3>
            <div class="space-y-3 text-sm">
                <div>
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider">Nama Lengkap</div>
                    <div class="font-semibold text-gray-800"><?= htmlspecialchars($namaPemesan) ?></div>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider">Email</div>
                    <div class="font-semibold text-gray-800 break-all"><?= htmlspecialchars($email ?: '-') ?></div>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider">No. HP</div>
                    <div class="font-semibold text-gray-800"><?= htmlspecialchars($noHp ?: '-') ?></div>
                </div>
                <?php if ($kota !== ''): ?>
                    <div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider">Kota Asal</div>
                        <div class="font-semibold text-gray-800"><?= htmlspecialchars($kota) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- INFO PAKET -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                <i class="fa-solid fa-suitcase text-blue-600"></i>
                Detail Paket
            </h3>
            <div class="space-y-3 text-sm">
                <div>
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider">Nama Paket</div>
                    <div class="font-semibold text-gray-800"><?= htmlspecialchars($namaPaket) ?></div>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider">Tanggal Berangkat</div>
                    <div class="font-semibold text-gray-800">
                        <?= $tglBerangkat ? date('d F Y', strtotime($tglBerangkat)) : '-' ?>
                    </div>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider">Jumlah Peserta</div>
                    <div class="font-semibold text-gray-800">
                        <?= $totalPeserta ?> orang
                        <span class="text-xs text-gray-400">
                            (<?= $jumlahDewasa ?> Dewasa, <?= $jumlahAnak ?> Anak)
                        </span>
                    </div>
                </div>
                <div>
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider">Metode Bayar</div>
                    <div class="font-semibold text-gray-800"><?= htmlspecialchars($metodeBayar) ?></div>
                </div>
            </div>
        </div>

    </div>


    <!-- TOTAL HARGA -->
    <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-2xl p-6 mb-6 shadow-lg">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="text-xs uppercase tracking-widest opacity-90 mb-1">Total Pembayaran</div>
                <div class="text-3xl md:text-4xl font-extrabold">
                    Rp <?= number_format($totalHarga, 0, ',', '.') ?>
                </div>
            </div>
            <i class="fa-solid fa-money-bill-wave text-5xl opacity-30"></i>
        </div>
    </div>


    <!-- CATATAN -->
    <?php if ($catatan !== ''): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
            <h3 class="text-sm font-bold text-gray-800 mb-3 pb-2 border-b flex items-center gap-2">
                <i class="fa-regular fa-note-sticky text-blue-600"></i>
                Catatan dari Pemesan
            </h3>
            <div class="text-sm text-gray-600 bg-slate-50 rounded-lg p-4 border border-slate-100 whitespace-pre-line">
                <?= htmlspecialchars($catatan) ?>
            </div>
        </div>
    <?php endif; ?>


    <!-- UBAH STATUS -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <h3 class="text-sm font-bold text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square text-blue-600"></i>
            Ubah Status Pemesanan
        </h3>

        <form method="POST" class="flex flex-wrap gap-2">
            <?php
            $statusButtons = [
                'pending'      => ['label' => 'Pending',       'color' => 'bg-yellow-500 hover:bg-yellow-600'],
                'dikonfirmasi' => ['label' => 'Dikonfirmasi',  'color' => 'bg-blue-500 hover:bg-blue-600'],
                'dp'           => ['label' => 'DP',            'color' => 'bg-purple-500 hover:bg-purple-600'],
                'lunas'        => ['label' => 'Lunas',         'color' => 'bg-green-500 hover:bg-green-600'],
                'selesai'      => ['label' => 'Selesai',       'color' => 'bg-slate-600 hover:bg-slate-700'],
                'batal'        => ['label' => 'Batal',         'color' => 'bg-red-500 hover:bg-red-600'],
            ];
            foreach ($statusButtons as $key => $btn):
                $isActive = $status === $key;
            ?>
                <button type="submit" name="status" value="<?= $key ?>"
                        class="<?= $btn['color'] ?> <?= $isActive ? 'ring-4 ring-offset-2 ring-blue-300' : '' ?> text-white px-4 py-2 rounded-lg text-xs font-semibold transition inline-flex items-center gap-2">
                    <?php if ($isActive): ?>
                        <i class="fa-solid fa-check"></i>
                    <?php endif; ?>
                    <?= $btn['label'] ?>
                </button>
            <?php endforeach; ?>
        </form>
    </div>


    <!-- ACTIONS -->
    <div class="flex flex-wrap gap-3">

        <?php if ($waNumber !== ''): ?>
            <a href="https://wa.me/<?= $waNumber ?>?text=<?= $waText ?>"
               target="_blank"
               class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold text-sm inline-flex items-center gap-2 transition shadow-md">
                <i class="fa-brands fa-whatsapp text-lg"></i>
                Hubungi via WhatsApp
            </a>
        <?php endif; ?>

        <?php if ($email !== ''): ?>
            <a href="mailto:<?= htmlspecialchars($email) ?>?subject=<?= urlencode('Reservasi ' . $kodeBook) ?>"
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold text-sm inline-flex items-center gap-2 transition shadow-md">
                <i class="fa-solid fa-envelope"></i>
                Kirim Email
            </a>
        <?php endif; ?>

        <form method="POST" action="aksi.php" class="inline ml-auto"
              onsubmit="return confirm('Yakin hapus pemesanan ini?\n\nTindakan ini tidak bisa dibatalkan.')">
            <input type="hidden" name="aksi" value="hapus">
            <input type="hidden" name="id" value="<?= $pemesananId ?>">
            <button type="submit"
                    class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-semibold text-sm inline-flex items-center gap-2 transition shadow-md">
                <i class="fa-solid fa-trash"></i>
                Hapus
            </button>
        </form>

        <a href="../pemesanan.php"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold text-sm inline-flex items-center gap-2 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

</section>

<?php ob_end_flush(); ?>
</body>
</html>