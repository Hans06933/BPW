<?php
/**
 * ============================================================
 * LOGIN ADMIN
 * Bayu Prima Wisata
 * ============================================================
 */

session_start();

// Kalau sudah login → langsung ke dashboard
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi!';
    } else {
        try {
            $user = db_get(
                "SELECT * FROM users WHERE username = :username LIMIT 1",
                [':username' => $username]
            );

            if (!$user) {
                $error = 'Username atau password salah!';
            } elseif (!empty($user['status']) && $user['status'] === 'nonaktif') {
                $error = 'Akun Anda dinonaktifkan. Hubungi Super Admin.';
            } elseif (!password_verify($password, $user['password'])) {
                $error = 'Username atau password salah!';
            } else {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id']        = (int) $user['id'];
                $_SESSION['admin_username']  = $user['username'];
                $_SESSION['admin_email']     = $user['email'] ?? '';
                $_SESSION['admin_full_name'] = $user['full_name'] ?? $user['username'];
                $_SESSION['admin_role']      = $user['role'] ?? 'admin';
                $_SESSION['admin_avatar']    = $user['avatar'] ?? null;

                try {
                    db_update('users', ['last_login' => date('Y-m-d H:i:s')], 'id', $user['id']);
                } catch (PDOException $e) {}

                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan sistem. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Poppins', sans-serif; }

        /* ============================================================
           BACKGROUND GRADIENT BIRU
        ============================================================ */
        .login-bg {
            background: linear-gradient(135deg, #001a33 0%, #003366 50%, #004c99 100%);
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        /* ============================================================
           PARTIKEL / BINTANG BERGERAK
        ============================================================ */
        .particles {
            position: absolute;
            inset: 0;
            z-index: 1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(6, 182, 212, 0.8);
            animation: float-up linear infinite;
        }

        @keyframes float-up {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
                transform: translateY(90vh) scale(1);
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-10vh) scale(0.5);
                opacity: 0;
            }
        }

        /* ============================================================
           IKON TRAVEL MENGAPUNG
        ============================================================ */
        .floating-icon {
            position: absolute;
            color: rgba(255, 255, 255, 0.08);
            z-index: 2;
            animation: float-around 20s ease-in-out infinite;
            pointer-events: none;
        }

        .floating-icon i {
            display: block;
        }

        @keyframes float-around {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            25% {
                transform: translate(30px, -40px) rotate(15deg);
            }
            50% {
                transform: translate(-20px, -80px) rotate(-10deg);
            }
            75% {
                transform: translate(-50px, -20px) rotate(10deg);
            }
        }

        /* ============================================================
           PESAWAT TERBANG
        ============================================================ */
        .plane-path {
            position: absolute;
            top: 15%;
            left: -100px;
            z-index: 2;
            animation: fly-plane 25s linear infinite;
            color: rgba(6, 182, 212, 0.3);
        }

        @keyframes fly-plane {
            0% {
                transform: translate(0, 0) rotate(-10deg);
                opacity: 0;
            }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% {
                transform: translate(120vw, -100px) rotate(-20deg);
                opacity: 0;
            }
        }

        /* ============================================================
           ORB / BULATAN CAHAYA
        ============================================================ */
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 1;
            animation: orb-pulse 8s ease-in-out infinite;
        }

        .glow-orb-1 {
            width: 400px;
            height: 400px;
            background: rgba(6, 182, 212, 0.4);
            top: -100px;
            left: -100px;
        }

        .glow-orb-2 {
            width: 500px;
            height: 500px;
            background: rgba(59, 130, 246, 0.3);
            bottom: -150px;
            right: -100px;
            animation-delay: 2s;
        }

        .glow-orb-3 {
            width: 300px;
            height: 300px;
            background: rgba(14, 165, 233, 0.3);
            top: 40%;
            left: 30%;
            animation-delay: 4s;
        }

        @keyframes orb-pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.3);
                opacity: 0.8;
            }
        }

        /* ============================================================
           GLASS FORM CARD
        ============================================================ */
        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.5),
                0 0 100px rgba(6, 182, 212, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: card-entrance 0.8s ease-out;
        }

        @keyframes card-entrance {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ============================================================
           INPUT FIELD STYLE
        ============================================================ */
        .input-field {
            transition: all 0.3s ease;
        }

        .input-field:focus {
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        /* ============================================================
           LOGO PULSE
        ============================================================ */
        .logo-pulse {
            animation: logo-beat 3s ease-in-out infinite;
        }

        @keyframes logo-beat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* ============================================================
           SHIMMER EFFECT DI HEADER
        ============================================================ */
        .shimmer {
            position: relative;
            overflow: hidden;
        }

        .shimmer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            animation: shimmer-slide 3s infinite;
        }

        @keyframes shimmer-slide {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* ============================================================
           ROTATING COMPASS
        ============================================================ */
        .compass-rotate {
            position: absolute;
            bottom: 10%;
            right: 5%;
            font-size: 150px;
            color: rgba(6, 182, 212, 0.05);
            animation: compass-spin 60s linear infinite;
            z-index: 1;
        }

        @keyframes compass-spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* ============================================================
           RESPONSIVE
        ============================================================ */
        @media (max-width: 640px) {
            .glow-orb { filter: blur(60px); }
            .compass-rotate { font-size: 100px; }
            .floating-icon { font-size: 40px !important; }
        }
    </style>
</head>
<body class="login-bg flex items-center justify-center py-12 px-4">

<!-- ============================================================
     BACKGROUND LAYER 1: GLOW ORBS
============================================================ -->
<div class="glow-orb glow-orb-1"></div>
<div class="glow-orb glow-orb-2"></div>
<div class="glow-orb glow-orb-3"></div>


<!-- ============================================================
     BACKGROUND LAYER 2: FLOATING TRAVEL ICONS
============================================================ -->
<div class="floating-icon" style="top: 10%; left: 8%; font-size: 60px; animation-delay: 0s;">
    <i class="fa-solid fa-plane"></i>
</div>
<div class="floating-icon" style="top: 20%; right: 12%; font-size: 50px; animation-delay: 2s;">
    <i class="fa-solid fa-compass"></i>
</div>
<div class="floating-icon" style="top: 60%; left: 5%; font-size: 70px; animation-delay: 4s;">
    <i class="fa-solid fa-globe"></i>
</div>
<div class="floating-icon" style="bottom: 20%; right: 8%; font-size: 55px; animation-delay: 1s;">
    <i class="fa-solid fa-map-location-dot"></i>
</div>
<div class="floating-icon" style="top: 40%; right: 30%; font-size: 45px; animation-delay: 3s;">
    <i class="fa-solid fa-suitcase"></i>
</div>
<div class="floating-icon" style="bottom: 15%; left: 30%; font-size: 40px; animation-delay: 5s;">
    <i class="fa-solid fa-location-dot"></i>
</div>
<div class="floating-icon" style="top: 75%; right: 20%; font-size: 50px; animation-delay: 6s;">
    <i class="fa-solid fa-camera-retro"></i>
</div>
<div class="floating-icon" style="top: 30%; left: 25%; font-size: 38px; animation-delay: 7s;">
    <i class="fa-solid fa-route"></i>
</div>


<!-- ============================================================
     BACKGROUND LAYER 3: FLYING PLANE
============================================================ -->
<div class="plane-path" style="font-size: 32px; top: 20%;">
    <i class="fa-solid fa-plane"></i>
</div>
<div class="plane-path" style="font-size: 24px; top: 65%; animation-delay: 10s; animation-duration: 30s;">
    <i class="fa-solid fa-plane"></i>
</div>


<!-- ============================================================
     BACKGROUND LAYER 4: ROTATING COMPASS
============================================================ -->
<div class="compass-rotate">
    <i class="fa-solid fa-compass"></i>
</div>


<!-- ============================================================
     BACKGROUND LAYER 5: PARTICLES (dibuat oleh JS)
============================================================ -->
<div class="particles" id="particles"></div>


<!-- ============================================================
     LOGIN FORM CARD
============================================================ -->
<div class="max-w-md w-full relative z-10">

    <div class="glass-card rounded-3xl overflow-hidden">

        <!-- Header dengan Gradient -->
        <div class="bg-gradient-to-r from-blue-700 via-blue-600 to-cyan-500 py-8 text-center relative shimmer">

            <!-- Logo -->
            <div class="flex items-center justify-center mb-3">
                <div class="logo-pulse bg-white/20 backdrop-blur rounded-2xl p-3 inline-flex items-center justify-center">
                    <img src="../images/logo-bpw.png"
                        alt="Logo Bayu Prima Wisata"
                        class="w-12 h-12 object-contain rounded-lg">
                </div>
            </div>
            <h1 class="text-white text-2xl font-black tracking-wide mb-1">
                BPW ADMIN
            </h1>
            <p class="text-blue-100 text-xs tracking-widest uppercase">
                Bayu Prima Wisata
            </p>
        </div>

        <!-- Body Form -->
        <div class="p-8 bg-white/95">

            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-1">
                    Selamat Datang Kembali
                </h2>
                <p class="text-xs text-gray-500">
                    Login untuk mengelola website Anda
                </p>
            </div>

            <!-- Error Alert -->
            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg mb-5 text-sm flex items-start gap-2 animate-pulse">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Username -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-xs font-semibold mb-2 uppercase tracking-wider">
                        Username
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="username" required autofocus
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                               placeholder="Masukkan username"
                               class="input-field w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 bg-gray-50 focus:bg-white">
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-xs font-semibold mb-2 uppercase tracking-wider">
                        Password
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="password" name="password" id="password" required
                               placeholder="Masukkan password"
                               class="input-field w-full pl-11 pr-11 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 bg-gray-50 focus:bg-white">

                        <!-- Toggle Show Password -->
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 p-1">
                            <i class="fa-regular fa-eye text-sm" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 text-white font-bold py-3.5 rounded-xl transition duration-300 flex items-center justify-center gap-2 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Masuk Dashboard</span>
                </button>
            </form>

            <!-- Footer Info -->
            <div class="mt-6 pt-5 border-t border-gray-100 text-center">
                <p class="text-[10px] text-gray-400 mt-2">
                    &copy; <?= date('Y') ?> Bayu Prima Wisata. All rights reserved.
                </p>
            </div>

        </div>
    </div>

</div>


<script>
// ============================================================
// GENERATE PARTICLES
// ============================================================
(function createParticles() {
    const container = document.getElementById('particles');
    if (!container) return;

    const particleCount = 30;

    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';

        // Random size
        const size = Math.random() * 4 + 1;
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';

        // Random position
        particle.style.left = Math.random() * 100 + '%';

        // Random animation duration
        const duration = Math.random() * 15 + 10;
        particle.style.animationDuration = duration + 's';

        // Random delay
        particle.style.animationDelay = Math.random() * 15 + 's';

        // Random opacity
        particle.style.opacity = Math.random() * 0.5 + 0.3;

        container.appendChild(particle);
    }
})();

// ============================================================
// TOGGLE PASSWORD VISIBILITY
// ============================================================
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');

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

// ============================================================
// ENTER KEY SUBMIT
// ============================================================
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        const form = document.querySelector('form');
        const submitBtn = form?.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.click();
    }
});
</script>

</body>
</html>