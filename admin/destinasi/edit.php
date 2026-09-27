<?php
/**
 * ============================================================
 * ADMIN - EDIT DESTINASI
 * Bayu Prima Wisata
 * ============================================================
 */

require_once "../../config/database.php";

$db = (new Database())->getConnection();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    header("Location: ../destinasi.php");
    exit;
}

// ============================================================
// AMBIL DATA DESTINASI
// ============================================================
$stmt = $db->prepare("SELECT * FROM destinasi WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$destinasi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$destinasi) {
    die("Data destinasi tidak ditemukan.");
}

// ============================================================
// AMBIL ENUM KATEGORI DARI DATABASE
// ============================================================
function getEnumValues(PDO $db, string $table, string $column): array {
    $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    $columnInfo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$columnInfo || !preg_match("/^enum\((.*)\)$/i", $columnInfo['Type'], $matches)) {
        return [];
    }

    return str_getcsv($matches[1], ',', "'");
}

$kategoriList = getEnumValues($db, 'destinasi', 'kategori');

// ============================================================
// HANDLE SUBMIT
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_destinasi     = trim($_POST['nama_destinasi'] ?? '');
    $slug               = trim($_POST['slug'] ?? '');
    $wilayah            = trim($_POST['wilayah'] ?? '');
    $kategori           = trim($_POST['kategori'] ?? '');
    $deskripsi          = trim($_POST['deskripsi'] ?? '');
    $alamat             = trim($_POST['alamat'] ?? '');
    $harga_tiket_masuk  = ($_POST['harga_tiket_masuk'] ?? '') !== '' ? (int)$_POST['harga_tiket_masuk'] : null;
    $jam_operasional    = trim($_POST['jam_operasional'] ?? '');
    $rating             = isset($_POST['rating']) && $_POST['rating'] !== '' ? (float)$_POST['rating'] : 0.0;
    $total_review       = (int)($_POST['total_review'] ?? 0);
    $status             = $_POST['status'] ?? 'aktif';
    $views              = (int)($_POST['views'] ?? 0);

    // Validasi wajib
    if ($nama_destinasi === '' || $slug === '' || $wilayah === '') {
        die("Nama destinasi, slug, dan wilayah wajib diisi.");
    }

    // Cek duplikat slug
    $check = $db->prepare("SELECT id FROM destinasi WHERE slug = :slug AND id != :id LIMIT 1");
    $check->execute([':slug' => $slug, ':id' => $id]);

    if ($check->fetch()) {
        die("Slug sudah digunakan oleh destinasi lain.");
    }

    // ============================================================
    // UPLOAD GAMBAR (opsional)
    // ============================================================
    $gambar_utama = $destinasi['gambar_utama'];
    $uploadDir = __DIR__ . "/../../images/destinasi/";

    if (isset($_FILES['gambar_utama']) && $_FILES['gambar_utama']['error'] !== UPLOAD_ERR_NO_FILE) {

        if ($_FILES['gambar_utama']['error'] !== UPLOAD_ERR_OK) {
            die("Gagal mengupload gambar.");
        }

        if ($_FILES['gambar_utama']['size'] > 5 * 1024 * 1024) {
            die("Ukuran gambar maksimal 5 MB.");
        }

        $ext = strtolower(pathinfo($_FILES['gambar_utama']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed, true)) {
            die("Format gambar harus JPG, JPEG, PNG, atau WEBP.");
        }

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newImage = time() . "_" . bin2hex(random_bytes(5)) . "." . $ext;

        if (!move_uploaded_file($_FILES['gambar_utama']['tmp_name'], $uploadDir . $newImage)) {
            die("Gagal menyimpan gambar baru.");
        }

        // Hapus gambar lama
        if (!empty($gambar_utama) && file_exists($uploadDir . $gambar_utama)) {
            unlink($uploadDir . $gambar_utama);
        }

        $gambar_utama = $newImage;
    }

    // ============================================================
    // UPDATE DATABASE
    // ============================================================
    $sql = "UPDATE destinasi SET
                nama_destinasi = :nama_destinasi,
                slug = :slug,
                wilayah = :wilayah,
                kategori = :kategori,
                deskripsi = :deskripsi,
                alamat = :alamat,
                harga_tiket_masuk = :harga_tiket_masuk,
                jam_operasional = :jam_operasional,
                gambar_utama = :gambar_utama,
                rating = :rating,
                total_review = :total_review,
                status = :status,
                views = :views
            WHERE id = :id";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':nama_destinasi'    => $nama_destinasi,
        ':slug'              => $slug,
        ':wilayah'           => $wilayah,
        ':kategori'          => $kategori !== '' ? $kategori : null,
        ':deskripsi'         => $deskripsi !== '' ? $deskripsi : null,
        ':alamat'            => $alamat !== '' ? $alamat : null,
        ':harga_tiket_masuk' => $harga_tiket_masuk,
        ':jam_operasional'   => $jam_operasional !== '' ? $jam_operasional : null,
        ':gambar_utama'      => $gambar_utama,
        ':rating'            => $rating,
        ':total_review'      => $total_review,
        ':status'            => $status,
        ':views'             => $views,
        ':id'                => $id
    ]);

    header("Location: ../destinasi.php?status=success");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Destinasi - Admin BPW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>* { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-slate-100 p-4 md:p-6">

<div class="max-w-5xl mx-auto">

    <!-- HEADER -->
    <div class="flex items-center gap-3 mb-6">
        <a href="../destinasi.php"
           class="w-10 h-10 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-600 hover:bg-slate-50 transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Destinasi</h1>
            <p class="text-sm text-slate-500">Perbarui informasi destinasi.</p>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="id" value="<?= (int)$destinasi['id'] ?>">

        <!-- INFO UTAMA -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b bg-slate-50 rounded-t-xl">
                <h2 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-blue-600"></i>
                    Informasi Utama
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-2">
                        Nama Destinasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama_destinasi" required maxlength="200"
                           value="<?= htmlspecialchars($destinasi['nama_destinasi']) ?>"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">
                        Slug <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="slug" id="slugInput" required maxlength="200"
                           value="<?= htmlspecialchars($destinasi['slug']) ?>"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">
                        Wilayah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="wilayah" required maxlength="100"
                           value="<?= htmlspecialchars($destinasi['wilayah']) ?>"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Kategori</label>
                    <select name="kategori" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-600">
                        <?php foreach ($kategoriList as $kat): ?>
                            <option value="<?= htmlspecialchars($kat) ?>"
                                <?= ($destinasi['kategori'] ?? '') === $kat ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($kat)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Status</label>
                    <select name="status" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-600">
                        <option value="aktif"    <?= $destinasi['status'] === 'aktif'    ? 'selected' : '' ?>>Aktif</option>
                        <option value="nonaktif" <?= $destinasi['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="5"
                              class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:border-blue-600"><?= htmlspecialchars($destinasi['deskripsi'] ?? '') ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-2">Alamat</label>
                    <textarea name="alamat" rows="3"
                              class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:border-blue-600"><?= htmlspecialchars($destinasi['alamat'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- DETAIL TAMBAHAN -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="px-6 py-4 border-b bg-slate-50 rounded-t-xl">
                <h2 class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-blue-600"></i>
                    Detail & Statistik
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">

                <div>
                    <label class="block text-sm font-semibold mb-2">Harga Tiket Masuk</label>
                    <input type="number" name="harga_tiket_masuk" min="0"
                           value="<?= htmlspecialchars($destinasi['harga_tiket_masuk'] ?? '') ?>"
                           placeholder="0 = Gratis"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-600">
                    <p class="text-xs text-slate-400 mt-1">Isi 0 atau kosongkan jika gratis.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Jam Operasional</label>
                    <input type="text" name="jam_operasional" maxlength="100"
                           value="<?= htmlspecialchars($destinasi['jam_operasional'] ?? '') ?>"
                           placeholder="08.00 - 17.00 WIB"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Rating</label>
                    <input type="number" name="rating" min="0" max="5" step="0.1"
                           value="<?= htmlspecialchars($destinasi['rating'] ?? 0) ?>"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Total Review</label>
                    <input type="number" name="total_review" min="0"
                           value="<?= htmlspecialchars($destinasi['total_review'] ?? 0) ?>"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Views</label>
                    <input type="number" name="views" min="0"
                           value="<?= htmlspecialchars($destinasi['views'] ?? 0) ?>"
                           class="w-full border border-slate-300 rounded-lg px-4 py-2.5 focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">Gambar Utama</label>

                    <?php if (!empty($destinasi['gambar_utama'])): ?>
                        <div class="mb-3">
                            <img src="../../images/destinasi/<?= htmlspecialchars($destinasi['gambar_utama']) ?>"
                                 class="w-full h-40 object-cover rounded-lg border"
                                 onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'">
                            <p class="text-xs text-slate-400 mt-1">Gambar saat ini</p>
                        </div>
                    <?php endif; ?>

                    <input type="file" name="gambar_utama"
                           accept="image/jpeg,image/png,image/webp"
                           class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                    <p class="text-xs text-slate-400 mt-1">Kosongkan jika tidak ingin mengganti.</p>
                </div>
            </div>
        </div>

        <!-- ACTION -->
        <div class="flex justify-end gap-3 pb-8">
            <a href="../destinasi.php"
               class="px-5 py-2.5 rounded-lg bg-white border border-slate-300 font-semibold hover:bg-slate-50 transition">
                <i class="fa-solid fa-xmark"></i> Batal
            </a>
            <button type="submit"
                    class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition inline-flex items-center gap-2">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
    // Auto-generate slug dari nama (kalau slug dikosongkan)
    const namaInput = document.querySelector('input[name="nama_destinasi"]');
    const slugInput = document.getElementById('slugInput');

    slugInput.addEventListener('input', function() {
        this.dataset.manual = 'true';
    });

    namaInput.addEventListener('blur', function() {
        if (slugInput.value === '' || slugInput.dataset.manual !== 'true') {
            slugInput.value = this.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }
    });
</script>

</body>
</html>