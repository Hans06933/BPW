<?php
/**
 * ============================================================
 * ADMIN - PROFIL SAYA
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();
require_once __DIR__ . '/../config/database.php';

// ============================================================
// PROTEKSI LOGIN
// ============================================================
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$adminId = (int) ($_SESSION['admin_id'] ?? 0);

if ($adminId <= 0) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// ============================================================
// HANDLE UPDATE
// ============================================================
$msg     = $_SESSION['admin_msg'] ?? null;
$msgType = $_SESSION['admin_msg_type'] ?? 'success';
unset($_SESSION['admin_msg'], $_SESSION['admin_msg_type']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {

    $aksi = $_POST['aksi'];

    try {
        // ========================================================
        // UPDATE PROFIL
        // ========================================================
        if ($aksi === 'update_profil') {

            $full_name = trim($_POST['full_name'] ?? '');
            $email     = trim($_POST['email'] ?? '');

            $errors = [];

            if ($full_name === '') {
                $errors[] = 'Nama lengkap wajib diisi.';
            }
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email tidak valid.';
            }

            // Cek email sudah dipakai admin lain
            if (empty($errors)) {
                $cekEmail = db_get(
                    "SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1",
                    [':email' => $email, ':id' => $adminId]
                );
                if ($cekEmail) {
                    $errors[] = 'Email sudah digunakan admin lain.';
                }
            }

            if (!empty($errors)) {
                $_SESSION['admin_msg'] = implode(' ', $errors);
                $_SESSION['admin_msg_type'] = 'error';
                header('Location: profile.php');
                exit;
            }

            // Update
            $dataUpdate = [
                'full_name' => $full_name,
                'email'     => $email,
            ];

            // Upload avatar
            if (!empty($_FILES['avatar']['name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {

                $folderUpload = __DIR__ . '/../uploads/avatar/';
                if (!is_dir($folderUpload)) mkdir($folderUpload, 0777, true);

                $ext     = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];
                $maxSize = 2 * 1024 * 1024;

                if (!in_array($ext, $allowed, true)) {
                    $_SESSION['admin_msg'] = 'Format avatar harus JPG, PNG, atau WEBP.';
                    $_SESSION['admin_msg_type'] = 'error';
                    header('Location: profile.php');
                    exit;
                }

                if ($_FILES['avatar']['size'] > $maxSize) {
                    $_SESSION['admin_msg'] = 'Ukuran avatar maksimal 2 MB.';
                    $_SESSION['admin_msg_type'] = 'error';
                    header('Location: profile.php');
                    exit;
                }

                $namaBaru = 'avatar_' . $adminId . '_' . date('YmdHis') . '.' . $ext;

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $folderUpload . $namaBaru)) {
                    // Hapus avatar lama
                    $oldAvatar = $_SESSION['admin_avatar'] ?? null;
                    if (!empty($oldAvatar)) {
                        $oldFile = __DIR__ . '/../' . $oldAvatar;
                        if (file_exists($oldFile) && strpos($oldAvatar, 'uploads/avatar/') === 0) {
                            @unlink($oldFile);
                        }
                    }
                    $dataUpdate['avatar'] = 'uploads/avatar/' . $namaBaru;
                    $_SESSION['admin_avatar'] = 'uploads/avatar/' . $namaBaru;
                }
            }

            db_update('users', $dataUpdate, 'id', $adminId);

            $_SESSION['admin_full_name'] = $full_name;
            $_SESSION['admin_email']     = $email;

            $_SESSION['admin_msg'] = 'Profil berhasil diperbarui.';
            $_SESSION['admin_msg_type'] = 'success';
            header('Location: profile.php');
            exit;
        }

        // ========================================================
        // GANTI PASSWORD
        // ========================================================
        elseif ($aksi === 'update_password') {

            $pwLama     = $_POST['password_lama'] ?? '';
            $pwBaru     = $_POST['password_baru'] ?? '';
            $pwKonfirm  = $_POST['password_konfirm'] ?? '';

            $errors = [];

            if ($pwLama === '')    $errors[] = 'Password lama wajib diisi.';
            if (strlen($pwBaru) < 6) $errors[] = 'Password baru minimal 6 karakter.';
            if ($pwBaru !== $pwKonfirm) $errors[] = 'Konfirmasi password tidak cocok.';

            // Cek password lama
            if (empty($errors)) {
                $user = db_get("SELECT password FROM users WHERE id = :id", ['id' => $adminId]);

                if (!$user || !password_verify($pwLama, $user['password'])) {
                    $errors[] = 'Password lama salah.';
                }
            }

            if (!empty($errors)) {
                $_SESSION['admin_msg'] = implode(' ', $errors);
                $_SESSION['admin_msg_type'] = 'error';
                header('Location: profile.php');
                exit;
            }

            $hashBaru = password_hash($pwBaru, PASSWORD_DEFAULT);
            db_update('users', ['password' => $hashBaru], 'id', $adminId);

            $_SESSION['admin_msg'] = 'Password berhasil diubah. Silakan login ulang.';
            $_SESSION['admin_msg_type'] = 'success';

            // Logout paksa untuk keamanan (opsional)
            // session_destroy();
            // header('Location: login.php');
            // exit;

            header('Location: profile.php');
            exit;
        }

        // ========================================================
        // HAPUS AVATAR
        // ========================================================
        elseif ($aksi === 'hapus_avatar') {

            $oldAvatar = $_SESSION['admin_avatar'] ?? null;

            if (!empty($oldAvatar)) {
                $oldFile = __DIR__ . '/../' . $oldAvatar;
                if (file_exists($oldFile)) @unlink($oldFile);
            }

            db_update('users', ['avatar' => null], 'id', $adminId);
            unset($_SESSION['admin_avatar']);

            $_SESSION['admin_msg'] = 'Avatar berhasil dihapus.';
            $_SESSION['admin_msg_type'] = 'success';
            header('Location: profile.php');
            exit;
        }

    } catch (PDOException $e) {
        $_SESSION['admin_msg'] = 'Gagal: ' . $e->getMessage();
        $_SESSION['admin_msg_type'] = 'error';
        header('Location: profile.php');
        exit;
    }
}

// ============================================================
// AMBIL DATA USER
// ============================================================
$user = db_get("SELECT * FROM users WHERE id = :id LIMIT 1", ['id' => $adminId]);

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// ============================================================
// HELPER: Badge Role
// ============================================================
function badgeRole($role) {
    $map = [
        'super_admin' => ['bg-red-100 text-red-700',      'fa-crown',       'Super Admin'],
        'admin'       => ['bg-blue-100 text-blue-700',    'fa-user-shield', 'Admin'],
        'editor'      => ['bg-purple-100 text-purple-700','fa-pen-fancy',   'Editor'],
    ];
    return $map[$role] ?? ['bg-gray-100 text-gray-700', 'fa-user', ucfirst($role)];
}

function getRoleDesc($role) {
    $map = [
        'super_admin' => 'Akses penuh termasuk kelola admin lain',
        'admin'       => 'Akses semua menu kecuali kelola admin',
        'editor'      => 'Hanya kelola konten (blog, galeri, destinasi, promo)',
    ];
    return $map[$role] ?? 'Akses standar';
}

// ============================================================
// INCLUDE HEADER
// ============================================================
include __DIR__ . '/../layout/admin_header.php';

$avatarUrl = !empty($user['avatar']) && file_exists(__DIR__ . '/../' . $user['avatar'])
    ? '../' . $user['avatar']
    : null;

$initial = strtoupper(substr($user['full_name'] ?: $user['username'], 0, 1));

list($roleClass, $roleIcon, $roleLabel) = badgeRole($user['role'] ?? 'admin');
?>

<!-- ============================================================
     BREADCRUMB
============================================================ -->
<div class="container mx-auto px-4 md:px-6 max-w-5xl mt-4">
    <nav class="flex items-center gap-2 text-xs text-gray-500 flex-wrap">
        <a href="index.php" class="hover:text-blue-600 transition">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
        <i class="fa-solid fa-chevron-right text-[8px]"></i>
        <span class="text-gray-800 font-semibold">Profil Saya</span>
    </nav>
</div>


<!-- ============================================================
     NOTIFIKASI
============================================================ -->
<?php if ($msg): ?>
    <div id="adminNotif" class="container mx-auto px-4 md:px-6 max-w-5xl mt-4">
        <div class="<?= $msgType === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700' ?> border-l-4 p-3 rounded-md text-sm flex items-center gap-2">
            <i class="fa-solid <?= $msgType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
            <?= htmlspecialchars($msg) ?>
        </div>
    </div>
    <script>setTimeout(() => document.getElementById('adminNotif')?.remove(), 4000);</script>
<?php endif; ?>


<!-- ============================================================
     CONTENT
============================================================ -->
<section class="container mx-auto px-4 md:px-6 max-w-5xl my-6 pb-16">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i class="fa-solid fa-circle-user text-blue-600"></i>
            Profil Saya
        </h1>
        <p class="text-xs text-gray-400 mt-1">Kelola informasi akun dan keamanan Anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KOLOM KIRI: KARTU PROFIL -->
        <div class="lg:col-span-1 space-y-4">

            <!-- Card Avatar -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">

                <div class="relative inline-block">
                    <?php if ($avatarUrl): ?>
                        <img src="<?= htmlspecialchars($avatarUrl) ?>"
                             class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg mx-auto"
                             alt="Avatar">
                    <?php else: ?>
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center text-white text-3xl font-bold mx-auto shadow-lg">
                            <?= $initial ?>
                        </div>
                    <?php endif; ?>

                    <span class="absolute bottom-1 right-1 w-6 h-6 bg-green-500 border-4 border-white rounded-full" title="Online"></span>
                </div>

                <h2 class="font-bold text-gray-800 text-lg mt-3">
                    <?= htmlspecialchars($user['full_name'] ?: $user['username']) ?>
                </h2>
                <p class="text-xs text-gray-400">@<?= htmlspecialchars($user['username']) ?></p>

                <div class="mt-3">
                    <span class="text-[10px] font-bold px-3 py-1 rounded-full <?= $roleClass ?>">
                        <i class="fa-solid <?= $roleIcon ?> text-[8px]"></i>
                        <?= $roleLabel ?>
                    </span>
                </div>

                <?php if ($avatarUrl): ?>
                    <form method="POST" class="mt-4">
                        <input type="hidden" name="aksi" value="hapus_avatar">
                        <button type="submit"
                                onclick="return confirm('Hapus avatar?')"
                                class="text-[10px] text-red-500 hover:text-red-700 font-semibold">
                            <i class="fa-solid fa-trash"></i> Hapus Avatar
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <!-- Info Detail -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-3 text-sm">
                <div>
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Email</div>
                    <div class="font-semibold text-gray-800 text-xs break-all">
                        <?= htmlspecialchars($user['email']) ?>
                    </div>
                </div>

                <div class="border-t pt-3">
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Role</div>
                    <div class="text-xs text-gray-600"><?= getRoleDesc($user['role'] ?? 'admin') ?></div>
                </div>

                <div class="border-t pt-3">
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Login Terakhir</div>
                    <div class="text-xs text-gray-600">
                        <?php if (!empty($user['last_login'])): ?>
                            <i class="fa-regular fa-clock text-gray-400"></i>
                            <?= date('d F Y, H:i', strtotime($user['last_login'])) ?>
                        <?php else: ?>
                            <span class="italic text-gray-400">Belum pernah</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="border-t pt-3">
                    <div class="text-[10px] text-gray-400 uppercase tracking-wider mb-0.5">Terdaftar Sejak</div>
                    <div class="text-xs text-gray-600">
                        <i class="fa-regular fa-calendar text-gray-400"></i>
                        <?= date('d F Y', strtotime($user['created_at'])) ?>
                    </div>
                </div>
            </div>

        </div>


        <!-- KOLOM KANAN: FORM -->
        <div class="lg:col-span-2 space-y-5">

            <!-- FORM EDIT PROFIL -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 bg-slate-50 border-b">
                    <h2 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-user-pen text-blue-600"></i>
                        Edit Profil
                    </h2>
                </div>

                <form method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                    <input type="hidden" name="aksi" value="update_profil">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Username</label>
                            <input type="text" value="<?= htmlspecialchars($user['username']) ?>"
                                   disabled
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-slate-50 text-gray-500 cursor-not-allowed">
                            <p class="text-[10px] text-gray-400 mt-1">Username tidak dapat diubah.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="full_name" required
                                   value="<?= htmlspecialchars($user['full_name'] ?? '') ?>"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" required
                                   value="<?= htmlspecialchars($user['email']) ?>"
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-blue-600">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Foto Profil (Avatar)</label>

                            <?php if ($avatarUrl): ?>
                                <div class="mb-3 flex items-center gap-4">
                                    <img src="<?= htmlspecialchars($avatarUrl) ?>"
                                         class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                                    <span class="text-[10px] text-gray-400">Avatar saat ini</span>
                                </div>
                            <?php endif; ?>

                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp"
                                   class="w-full text-xs text-slate-500 border border-slate-200 rounded-lg p-2">
                            <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, WEBP. Maks 2 MB.</p>
                        </div>

                    </div>

                    <div class="flex justify-end pt-2 border-t">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition inline-flex items-center gap-2">
                            <i class="fa-solid fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>


            <!-- FORM GANTI PASSWORD -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 bg-slate-50 border-b">
                    <h2 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-key text-blue-600"></i>
                        Ganti Password
                    </h2>
                </div>

                <form method="POST" class="p-5 space-y-4">
                    <input type="hidden" name="aksi" value="update_password">

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Password Lama <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password_lama" id="pwLama" required
                                   class="w-full border border-slate-200 rounded-lg px-3 py-2.5 pr-10 text-sm focus:outline-none focus:border-blue-600">
                            <button type="button" onclick="togglePw('pwLama', this)"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="password_baru" id="pwBaru" required minlength="6"
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 pr-10 text-sm focus:outline-none focus:border-blue-600">
                                <button type="button" onclick="togglePw('pwBaru', this)"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                                    <i class="fa-regular fa-eye text-sm"></i>
                                </button>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Min. 6 karakter.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="password_konfirm" id="pwKonfirm" required minlength="6"
                                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 pr-10 text-sm focus:outline-none focus:border-blue-600">
                                <button type="button" onclick="togglePw('pwKonfirm', this)"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                                    <i class="fa-regular fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>

                    </div>

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded text-xs text-yellow-800">
                        <i class="fa-solid fa-circle-info"></i>
                        Setelah ganti password, disarankan untuk login ulang.
                    </div>

                    <div class="flex justify-end pt-2 border-t">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold text-sm transition inline-flex items-center gap-2">
                            <i class="fa-solid fa-key"></i> Ganti Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 rounded-2xl border border-red-100 p-5">
                <h3 class="font-bold text-red-700 text-sm mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Zona Berbahaya
                </h3>
                <p class="text-xs text-red-600 mb-3">
                    Logout dari semua sesi di browser ini.
                </p>
                <a href="logout.php"
                   onclick="return confirm('Yakin ingin logout?')"
                   class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-semibold transition">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout Sekarang
                </a>
            </div>

        </div>

    </div>
</section>


<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>