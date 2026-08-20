<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    
    <!-- Title & Meta Tags -->
    <title>Hubungi Kami - Bayu Prima Wisata | Tour & Travel Terpercaya</title>
    <meta name="description" content="Hubungi tim customer service Bayu Prima Wisata. Konsultasikan rencana perjalanan Anda, dapatkan info promo, atau bantuan pemesanan paket wisata. Siap melayani Anda 24/7.">
    <meta name="keywords" content="kontak bpw, customer service bpw, hubungi bayu prima wisata, konsultasi travel, tour travel depok">
    <meta name="author" content="Bayu Prima Wisata">
    <meta name="robots" content="index, follow">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="https://www.bayuprimawisata.com/kontak.php">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.bayuprimawisata.com/kontak.php">
    <meta property="og:title" content="Hubungi Kami - Bayu Prima Wisata">
    <meta property="og:description" content="Hubungi tim customer service BPW untuk konsultasi perjalanan, pemesanan paket wisata, atau informasi promo terbaru.">
    <meta property="og:image" content="https://www.bayuprimawisata.com/assets/images/og-kontak.jpg">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Hubungi Kami - Bayu Prima Wisata">
    <meta name="twitter:description" content="Hubungi tim customer service BPW untuk konsultasi perjalanan Anda.">
    <meta name="twitter:image" content="https://www.bayuprimawisata.com/assets/images/twitter-kontak.jpg">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <!-- CSS Libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }
        
        /* Hero Section Modern */
        .hero-modern {
            position: relative;
            overflow: hidden;
        }
        
        .hero-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0,51,102,0.92) 0%, rgba(0,76,153,0.85) 100%);
            z-index: 1;
        }
        
        .hero-bg-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        /* Animated Wave */
        .wave-bottom {
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            overflow: hidden;
            line-height: 0;
            z-index: 2;
        }
        
        .wave-bottom svg {
            position: relative;
            display: block;
            width: calc(100% + 1.3px);
            height: 60px;
        }
        
        /* Floating Elements Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        
        @keyframes floatDelay {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        .float-animation {
            animation: float 4s ease-in-out infinite;
        }
        
        .float-animation-delay {
            animation: floatDelay 5s ease-in-out infinite;
        }
        
        .float-animation-slow {
            animation: floatSlow 6s ease-in-out infinite;
        }
        
        /* Glassmorphism Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            border-radius: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #ffffff 0%, #e0f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Contact Card Hover */
        .contact-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .contact-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -12px rgba(0,0,0,0.15);
        }
        
        /* Form Input Focus */
        .form-input {
            transition: all 0.2s ease;
        }
        
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(0,51,102,0.1);
            border-color: #003366;
            outline: none;
        }
        
        /* Map Container */
        .map-container {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
        }
        
        /* Scroll to top */
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s;
        }
        
        .scroll-top:hover {
            background: #004c99;
            transform: translateY(-3px);
        }
        
        /* Success Message */
        .success-message {
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* WhatsApp Button Pulse */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .wa-pulse {
            animation: pulse 2s infinite;
        }
        
        /* Glow Effect */
        .glow {
            box-shadow: 0 0 20px rgba(0,102,204,0.3);
            transition: box-shadow 0.3s ease;
        }
        
        .glow:hover {
            box-shadow: 0 0 30px rgba(0,102,204,0.5);
        }
    </style>
</head>
<?php include "layout/header.php"; ?>
<body class="bg-slate-50">

    <!-- HERO SECTION MODERN -->
    <section class="hero-modern min-h-[500px] md:min-h-[600px] flex items-center relative overflow-hidden">
        <!-- Background Image dengan Overlay Gradasi -->
        <div class="absolute inset-0 bg-cover bg-center scale-110" style="background-image: url('https://images.unsplash.com/photo-1422222948315-28aadb7a2cb6?auto=format&fit=crop&w=1920&q=80'); background-size: cover; background-position: center;"></div>
        
        <!-- Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/95 via-blue-800/90 to-cyan-800/85"></div>
        
        <!-- Pattern Overlay -->
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="1"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-repeat: repeat;"></div>
        
        <!-- Floating Elements -->
        <div class="absolute top-20 left-10 opacity-20 float-animation">
            <i class="fa-regular fa-message text-7xl text-white"></i>
        </div>
        <div class="absolute bottom-32 right-10 opacity-15 float-animation-delay">
            <i class="fa-regular fa-compass text-8xl text-white"></i>
        </div>
        <div class="absolute top-1/2 right-20 opacity-10 float-animation-slow">
            <i class="fa-regular fa-map text-6xl text-white"></i>
        </div>
        
        <!-- Hero Content -->
        <div class="container mx-auto px-4 md:px-6 max-w-6xl relative z-10 py-16 md:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <!-- Left Content -->
                <div>
                    <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur rounded-full px-5 py-2 mb-6">
                        <i class="fa-regular fa-address-card text-yellow-300 text-sm"></i>
                        <span class="text-xs font-bold tracking-wider text-white">HUBUNGI KAMI</span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-extrabold leading-tight mb-5">
                        Siap Melayani<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-orange-400">
                            Perjalanan Anda
                        </span>
                    </h1>
                    
                    <p class="text-white/80 text-base md:text-lg max-w-xl leading-relaxed mb-6">
                        Tim customer service profesional kami siap membantu Anda 24/7. Konsultasikan rencana perjalanan Anda sekarang juga!
                    </p>
                    
                    <!-- Contact Quick Info Badges -->
                    <div class="flex flex-wrap gap-3 mb-8">
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur rounded-full px-4 py-2 border border-white/20">
                            <i class="fa-regular fa-clock text-yellow-300 text-xs"></i>
                            <span class="text-xs text-white">Respon Cepat &lt; 1 Jam</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur rounded-full px-4 py-2 border border-white/20">
                            <i class="fa-solid fa-headset text-yellow-300 text-xs"></i>
                            <span class="text-xs text-white">Support 24/7</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur rounded-full px-4 py-2 border border-white/20">
                            <i class="fa-solid fa-location-dot text-yellow-300 text-xs"></i>
                            <span class="text-xs text-white">Kantor Pusat Depok</span>
                        </div>
                    </div>
                    
                    <!-- Quick Contact Buttons -->
                    <div class="flex flex-wrap gap-4">
                        <a href="https://wa.me/6281234567890" target="_blank" class="group inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fa-brands fa-whatsapp text-lg group-hover:scale-110 transition"></i>
                            Chat WhatsApp
                        </a>
                        <a href="tel:081234567890" class="group inline-flex items-center gap-2 bg-white/20 backdrop-blur hover:bg-white/30 text-white px-6 py-3 rounded-full font-semibold transition-all duration-300 border border-white/30">
                            <i class="fa-solid fa-phone text-lg group-hover:scale-110 transition"></i>
                            Telepon Sekarang
                        </a>
                    </div>
                </div>
                
                <!-- Right Content - Glass Card Info -->
                <div class="glass-card p-6 md:p-8 hidden lg:block">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fa-regular fa-clock text-white text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Jam Operasional</h3>
                        <p class="text-white/80 text-sm">Senin - Minggu: 08.00 - 20.00</p>
                        <p class="text-white/60 text-xs mt-1">Call Center: 24 Jam Nonstop</p>
                    </div>
                    
                    <div class="border-t border-white/20 pt-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-phone text-yellow-300 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-white/60 text-[10px]">Hotline</p>
                                <p class="text-white font-semibold">0852-8144-1565</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center">
                                <i class="fa-regular fa-envelope text-yellow-300 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-white/60 text-[10px]">Email</p>
                                <p class="text-white font-semibold">bayuprimawisata.com</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Wave Bottom -->
        <div class="wave-bottom">
            <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill" fill="#F8FAFC"></path>
            </svg>
        </div>
    </section>

    <!-- CONTACT INFO CARDS -->
    <section class="py-10 md:py-12 bg-slate-50 relative z-20">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
                <!-- Card 1: Alamat -->
                <div class="contact-card bg-white rounded-2xl p-5 md:p-6 text-center border border-gray-100 shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-location-dot text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-sm text-gray-800 mb-2">Kantor Pusat</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Jl. Raya Margonda No 88<br>
                        Depok, Jawa Barat 16424<br>
                        Indonesia
                    </p>
                    <a href="https://maps.google.com/?q=Jl.+Raya+Margonda+No+88+Depok" target="_blank" class="inline-block mt-3 text-blue-600 text-xs font-semibold hover:underline">
                        <i class="fa-solid fa-arrow-up-right-from-marker"></i> Buka di Maps
                    </a>
                </div>
                
                <!-- Card 2: Telepon -->
                <div class="contact-card bg-white rounded-2xl p-5 md:p-6 text-center border border-gray-100 shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-phone text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-sm text-gray-800 mb-2">Telepon</h3>
                    <p class="text-xs text-gray-500">
                        Kantor: (021) 1234-5678<br>
                        Hotline: 0812-3456-7890
                    </p>
                    <a href="tel:081234567890" class="inline-block mt-3 text-blue-600 text-xs font-semibold hover:underline">
                        <i class="fa-solid fa-phone"></i> Hubungi Sekarang
                    </a>
                </div>
                
                <!-- Card 3: Email -->
                <div class="contact-card bg-white rounded-2xl p-5 md:p-6 text-center border border-gray-100 shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-regular fa-envelope text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-sm text-gray-800 mb-2">Email</h3>
                    <p class="text-xs text-gray-500">
                        bayuprimawisata.com<br>
                    </p>
                    <a href="mailto:info@bayuprimawisata.com" class="inline-block mt-3 text-blue-600 text-xs font-semibold hover:underline">
                        <i class="fa-regular fa-envelope"></i> Kirim Email
                    </a>
                </div>
                
                <!-- Card 4: Jam Operasional -->
                <div class="contact-card bg-white rounded-2xl p-5 md:p-6 text-center border border-gray-100 shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-regular fa-clock text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-sm text-gray-800 mb-2">Jam Operasional</h3>
                    <p class="text-xs text-gray-500">
                        Senin - Minggu: 08.00 - 20.00<br>
                        Call Center: 24 Jam
                    </p>
                    <span class="inline-block mt-3 text-green-600 text-xs font-semibold">
                        <i class="fa-solid fa-circle"></i> Buka Hari Ini
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- FORM & MAP SECTION (Sama seperti sebelumnya) -->
    <section class="py-10 md:py-16 bg-white">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12">
                
                <!-- FORM KIRIM PESAN -->
                <div>
                    <div class="mb-6">
                        <span class="text-blue-600 font-bold text-xs uppercase tracking-wider block mb-1">Kirim Pesan</span>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Ada yang Bisa Kami Bantu?</h2>
                        <p class="text-sm text-gray-500 mt-2">Isi form di bawah ini, tim kami akan segera merespon pertanyaan Anda dalam waktu kurang dari 1 jam.</p>
                    </div>
                    
                    <form id="contactForm" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" id="name" required class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none transition text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="email" required class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none transition text-sm">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Telepon</label>
                                <input type="tel" id="phone" class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none transition text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Subjek <span class="text-red-500">*</span></label>
                                <select id="subject" required class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none transition text-sm bg-white">
                                    <option value="">Pilih Subjek</option>
                                    <option value="Pemesanan Paket Wisata">Pemesanan Paket Wisata</option>
                                    <option value="Informasi Destinasi">Informasi Destinasi</option>
                                    <option value="Konsultasi Perjalanan">Konsultasi Perjalanan</option>
                                    <option value="Kerjasama">Kerjasama</option>
                                    <option value="Keluhan / Saran">Keluhan / Saran</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Pesan <span class="text-red-500">*</span></label>
                            <textarea id="message" rows="5" required class="form-input w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none transition text-sm resize-none"></textarea>
                        </div>
                        
                        <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl transition flex items-center justify-center gap-2 text-sm shadow-md hover:shadow-lg">
                            <i class="fa-regular fa-paper-plane"></i> Kirim Pesan
                        </button>
                        
                        <div id="formSuccess" class="hidden success-message bg-green-50 border border-green-200 rounded-xl p-4 text-green-700 text-sm flex items-center gap-3">
                            <i class="fa-solid fa-circle-check text-lg"></i>
                            <span>Pesan Anda berhasil dikirim! Tim kami akan segera menghubungi Anda.</span>
                        </div>
                        
                        <p class="text-[11px] text-gray-400 text-center md:text-left">
                            <i class="fa-regular fa-shield"></i> Data Anda aman dan tidak akan dibagikan kepada pihak ketiga.
                        </p>
                    </form>
                </div>
                
                <!-- MAP & SOCIAL MEDIA -->
                <div>
                    <!-- Google Maps -->
                    <div class="mb-6">
                        <h3 class="font-bold text-base text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-map text-blue-600"></i> Lokasi Kami
                        </h3>
                        <div class="map-container h-64 md:h-72 rounded-xl overflow-hidden shadow-md">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.280860215202!2d106.822738!3d-6.369188!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ec6f0b6a9e0b%3A0x6e8e8e8e8e8e8e8e!2sJl.%20Raya%20Margonda%2C%20Depok!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                            <i class="fa-regular fa-circle-info"></i> Klik peta untuk melihat rute lengkap
                        </p>
                    </div>
                    
                    <!-- Social Media -->
                    <div class="bg-slate-50 rounded-2xl p-5 md:p-6">
                        <h3 class="font-bold text-base text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fa-regular fa-share-from-square text-blue-600"></i> Ikuti Kami
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="https://facebook.com/username-bpw" target="_blank" class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-3 md:px-4 py-3 hover:bg-blue-50 hover:border-blue-200 transition group">
                                <div class="w-8 h-8 md:w-10 md:h-10 bg-[#1877F2]/10 rounded-full flex items-center justify-center">
                                    <i class="fab fa-facebook-f text-[#1877F2] text-sm md:text-base"></i>
                                </div>
                                <div>
                                    <p class="text-xs md:text-sm font-semibold text-gray-800 group-hover:text-blue-600">Facebook</p>
                                    <p class="text-[9px] md:text-[10px] text-gray-400">Bayu Prima Wisata</p>
                                </div>
                            </a>
                            <a href="https://instagram.com/username-bpw" target="_blank" class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-3 md:px-4 py-3 hover:bg-pink-50 hover:border-pink-200 transition group">
                                <div class="w-8 h-8 md:w-10 md:h-10 bg-[#E4405F]/10 rounded-full flex items-center justify-center">
                                    <i class="fab fa-instagram text-[#E4405F] text-sm md:text-base"></i>
                                </div>
                                <div>
                                    <p class="text-xs md:text-sm font-semibold text-gray-800 group-hover:text-pink-600">Instagram</p>
                                    <p class="text-[9px] md:text-[10px] text-gray-400">@bayuprimawisata</p>
                                </div>
                            </a>
                            <a href="https://youtube.com/@username-bpw" target="_blank" class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-3 md:px-4 py-3 hover:bg-red-50 hover:border-red-200 transition group">
                                <div class="w-8 h-8 md:w-10 md:h-10 bg-[#FF0000]/10 rounded-full flex items-center justify-center">
                                    <i class="fab fa-youtube text-[#FF0000] text-sm md:text-base"></i>
                                </div>
                                <div>
                                    <p class="text-xs md:text-sm font-semibold text-gray-800 group-hover:text-red-600">YouTube</p>
                                    <p class="text-[9px] md:text-[10px] text-gray-400">BPW Travel</p>
                                </div>
                            </a>
                            <a href="https://tiktok.com/@username-bpw" target="_blank" class="flex items-center gap-3 bg-white border border-gray-200 rounded-xl px-3 md:px-4 py-3 hover:bg-black/5 hover:border-gray-300 transition group">
                                <div class="w-8 h-8 md:w-10 md:h-10 bg-black/10 rounded-full flex items-center justify-center">
                                    <i class="fab fa-tiktok text-black text-sm md:text-base"></i>
                                </div>
                                <div>
                                    <p class="text-xs md:text-sm font-semibold text-gray-800">TikTok</p>
                                    <p class="text-[9px] md:text-[10px] text-gray-400">@bayuprimawisata</p>
                                </div>
                            </a>
                        </div>
                    </div>
                    
                    <!-- WhatsApp Floating Button Info -->
                    <div class="mt-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl p-4 border border-green-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center wa-pulse">
                                <i class="fa-brands fa-whatsapp text-white text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-800">Chat via WhatsApp</p>
                                <p class="text-[10px] text-gray-500">Respons cepat dalam 5 menit</p>
                            </div>
                        </div>
                        <a href="https://wa.me/6281234567890" target="_blank" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-xs font-semibold transition shadow-md">
                            Chat Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ PREVIEW SECTION -->
    <section class="py-12 md:py-16 bg-slate-50">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="text-center mb-8">
                <span class="text-blue-600 font-bold text-xs uppercase tracking-wider block mb-1">Butuh Jawaban Cepat?</span>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Pertanyaan Umum</h2>
                <p class="text-sm text-gray-500 mt-2">Temukan jawaban dari pertanyaan yang sering diajukan pelanggan kami</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-5 max-w-4xl mx-auto">
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <i class="fa-regular fa-circle-question text-blue-600 text-lg mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold text-sm text-gray-800 mb-1">Bagaimana cara memesan paket wisata?</h3>
                            <p class="text-xs text-gray-500">Anda bisa memesan melalui form di website, WhatsApp, atau datang langsung ke kantor kami. Tim kami akan membantu proses pemesanan dengan cepat.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <i class="fa-regular fa-circle-question text-blue-600 text-lg mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold text-sm text-gray-800 mb-1">Apakah ada diskon untuk rombongan?</h3>
                            <p class="text-xs text-gray-500">Ya, kami memberikan diskon khusus untuk pemesanan rombongan minimal 5 orang. Semakin besar rombongan, semakin besar diskonnya.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <i class="fa-regular fa-circle-question text-blue-600 text-lg mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold text-sm text-gray-800 mb-1">Berapa lama proses konfirmasi?</h3>
                            <p class="text-xs text-gray-500">Konfirmasi akan dikirim dalam maksimal 2x24 jam setelah pembayaran melalui email atau WhatsApp.</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition">
                    <div class="flex items-start gap-3">
                        <i class="fa-regular fa-circle-question text-blue-600 text-lg mt-0.5"></i>
                        <div>
                            <h3 class="font-semibold text-sm text-gray-800 mb-1">Apakah bisa custom itinerary?</h3>
                            <p class="text-xs text-gray-500">Tentu! Kami menyediakan layanan custom tour sesuai keinginan Anda. Silakan konsultasikan dengan tim kami.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-6">
                <a href="faq.php" class="text-blue-600 text-sm font-semibold hover:underline inline-flex items-center gap-1">
                    Lihat semua FAQ <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="py-12 bg-white border-t">
        <div class="container mx-auto px-4 md:px-6 max-w-4xl text-center">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl p-8 text-white">
                <i class="fa-regular fa-building text-3xl mb-4"></i>
                <h3 class="text-xl md:text-2xl font-bold mb-2">Butuh Bantuan Langsung?</h3>
                <p class="text-blue-100 mb-5">Kunjungi kantor kami atau hubungi melalui telepon untuk konsultasi langsung</p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-2 bg-white text-blue-600 hover:bg-gray-100 px-6 py-3 rounded-full font-semibold transition shadow-lg">
                        <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
                    </a>
                    <a href="tel:081234567890" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full font-semibold transition shadow-lg">
                        <i class="fa-solid fa-phone"></i> Telepon Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- SCROLL TO TOP -->
    <div class="scroll-top" id="scrollTopBtn">
        <i class="fa-solid fa-arrow-up"></i>
    </div>

    <script>
        // Contact Form Handler
        const contactForm = document.getElementById("contactForm");
        const formSuccess = document.getElementById("formSuccess");
        
        if (contactForm) {
            contactForm.addEventListener("submit", function(e) {
                e.preventDefault();
                
                const name = document.getElementById("name").value;
                const email = document.getElementById("email").value;
                const subject = document.getElementById("subject").value;
                const message = document.getElementById("message").value;
                
                if (!name || !email || !subject || !message) {
                    alert("Harap isi semua field yang bertanda *");
                    return;
                }
                
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert("Harap masukkan alamat email yang valid");
                    return;
                }
                
                formSuccess.classList.remove("hidden");
                contactForm.reset();
                formSuccess.scrollIntoView({ behavior: "smooth", block: "center" });
                
                setTimeout(() => {
                    formSuccess.classList.add("hidden");
                }, 5000);
            });
        }
        
        // Scroll to top
        const scrollBtn = document.getElementById("scrollTopBtn");
        window.addEventListener("scroll", () => {
            if (window.scrollY > 400) {
                scrollBtn.style.display = "flex";
            } else {
                scrollBtn.style.display = "none";
            }
        });
        scrollBtn?.addEventListener("click", () => {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
        
        // Map error handling
        const mapIframe = document.querySelector(".map-container iframe");
        if (mapIframe) {
            mapIframe.addEventListener("error", function() {
                this.parentElement.innerHTML = `
                    <div class="w-full h-full bg-gray-100 flex flex-col items-center justify-center">
                        <i class="fa-solid fa-map text-gray-400 text-4xl mb-2"></i>
                        <p class="text-gray-500 text-sm">Tidak dapat memuat peta</p>
                        <a href="https://maps.google.com/?q=Jl.+Raya+Margonda+No+88+Depok" target="_blank" class="text-blue-600 text-xs mt-2">Buka di Google Maps →</a>
                    </div>
                `;
            });
        }
    </script>

<?php include "layout/footer.php"; ?>
</body>
</html>