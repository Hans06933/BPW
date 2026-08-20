<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Tentang Kami - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }
        .bg-hero-gradient {
            background: linear-gradient(to bottom, rgba(0, 51, 102, 0.85), rgba(0, 76, 153, 0.6)), url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
        /* Wave dipisahkan untuk menghindari masalah responsif */
        .wave-top {
            position: relative;
            background: #fff;
            margin-top: -2px;
        }
        /* Perbaikan responsif */
        @media (max-width: 768px) {
            .hero-padding {
                padding-top: 2rem;
                padding-bottom: 3rem;
            }
            .stats-card {
                padding: 0.75rem;
            }
        }
    </style>
</head>
<?php
include "layout/header.php";
?>
<body class="bg-slate-50 text-gray-800">

    <!-- HERO SECTION - RESPONSIF -->
    <section class="bg-hero-gradient hero-padding pt-16 md:pt-20 pb-20 md:pb-32 text-white relative">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <!-- Breadcrumb -->
            <div class="flex items-center space-x-2 text-[10px] md:text-xs text-slate-300 mb-3 md:mb-4 flex-wrap">
                <a href="index.php" class="hover:text-white"><i class="fa-solid fa-house"></i></a>
                <i class="fa-solid fa-chevron-right text-[8px] md:text-[9px]"></i>
                <span class="text-slate-200">Tentang Kami</span>
            </div>
            
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold mb-3 md:mb-4 leading-tight">
                Tentang Kami<br>
                <span class="text-cyan-300">Bayu Prima Wisata (BPW)</span>
            </h1>
            <p class="text-slate-200 text-sm md:text-base max-w-2xl leading-relaxed">
                Kami adalah perusahaan tour & travel terpercaya yang berkomitmen memberikan pengalaman perjalanan terbaik di seluruh destinasi pilihan di Indonesia.
            </p>
        </div>
    </section>

    <!-- SEJARAH SECTION - RESPONSIF -->
    <section class="wave-top pb-12 md:pb-16 relative z-10">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12 items-center">
                <!-- Konten Teks -->
                <div class="lg:col-span-6 space-y-3 md:space-y-4 order-2 lg:order-1">
                    <span class="text-blue-600 font-extrabold text-[10px] md:text-xs uppercase tracking-wider block">Sejarah Kami</span>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-custom-blue leading-tight">
                        Perjalanan Kami Dimulai dengan Sebuah Komitmen
                    </h2>
                    <p class="text-xs md:text-sm text-gray-600 leading-relaxed text-justify">
                        Bayu Prima Wisata (BPW) berdiri sejak tahun 2012 dengan komitmen untuk menjadi sahabat perjalanan terpercaya bagi setiap pelanggan. Berawal dari sebuah tim kecil yang memiliki semangat besar di dunia pariwisata, kami terus berkembang dan berinovasi untuk memberikan layanan terbaik, aman, nyaman, dan berkesan.
                    </p>
                    <p class="text-xs md:text-sm text-gray-600 leading-relaxed text-justify">
                        Selama lebih dari satu dekade, kami telah melayani ribuan pelanggan dari berbagai kalangan untuk menjelajahi keindahan Indonesia, dari Sabang sampai Merauke.
                    </p>
                </div>
                
                <!-- Gambar -->
                <div class="lg:col-span-6 relative order-1 lg:order-2">
                    <div class="rounded-2xl overflow-hidden shadow-xl h-56 sm:h-64 md:h-72">
                        <img src="https://images.unsplash.com/photo-1527631746610-bca00a040d60?auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover" alt="Travelers" loading="lazy">
                    </div>
                    <div class="absolute bottom-3 right-3 md:bottom-4 md:right-4 bg-blue-600 text-white p-2 md:p-4 rounded-xl shadow-lg flex items-center space-x-2 md:space-x-3 border border-white/20">
                        <div class="w-7 h-7 md:w-10 md:h-10 bg-white/20 rounded-lg flex items-center justify-center text-sm md:text-lg">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div>
                            <span class="block text-[8px] md:text-[10px] uppercase tracking-wide text-blue-200">Berdiri sejak</span>
                            <span class="text-sm md:text-xl font-black tracking-wide">2012</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATISTIK CARD - RESPONSIF (grid menyesuaikan) -->
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 p-4 md:p-6 grid grid-cols-2 md:grid-cols-5 gap-4 md:gap-6 mt-10 md:mt-16 text-center">
                <div class="flex items-center space-x-2 md:space-x-3 justify-center md:border-r border-gray-100 last:border-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm md:text-base shadow-sm flex-shrink-0"><i class="fa-solid fa-briefcase"></i></div>
                    <div class="text-left">
                        <span class="block font-black text-sm md:text-base text-slate-800">10+</span>
                        <span class="text-[8px] md:text-[10px] text-gray-400 font-medium whitespace-nowrap">Tahun Pengalaman</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2 md:space-x-3 justify-center md:border-r border-gray-100 last:border-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm md:text-base shadow-sm flex-shrink-0"><i class="fa-solid fa-face-smile"></i></div>
                    <div class="text-left">
                        <span class="block font-black text-sm md:text-base text-slate-800">5000+</span>
                        <span class="text-[8px] md:text-[10px] text-gray-400 font-medium whitespace-nowrap">Pelanggan Puas</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2 md:space-x-3 justify-center md:border-r border-gray-100 last:border-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm md:text-base shadow-sm flex-shrink-0"><i class="fa-solid fa-map-location-dot"></i></div>
                    <div class="text-left">
                        <span class="block font-black text-sm md:text-base text-slate-800">100+</span>
                        <span class="text-[8px] md:text-[10px] text-gray-400 font-medium whitespace-nowrap">Destinasi Wisata</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2 md:space-x-3 justify-center md:border-r border-gray-100 last:border-0">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm md:text-base shadow-sm flex-shrink-0"><i class="fa-solid fa-bus"></i></div>
                    <div class="text-left">
                        <span class="block font-black text-sm md:text-base text-slate-800">50+</span>
                        <span class="text-[8px] md:text-[10px] text-gray-400 font-medium whitespace-nowrap">Armada Transportasi</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2 md:space-x-3 justify-center col-span-2 md:col-span-1">
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm md:text-base shadow-sm flex-shrink-0"><i class="fa-solid fa-headset"></i></div>
                    <div class="text-left">
                        <span class="block font-black text-sm md:text-base text-slate-800">24/7</span>
                        <span class="text-[8px] md:text-[10px] text-gray-400 font-medium whitespace-nowrap">Layanan Pelanggan</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NILAI KAMI SECTION - RESPONSIF -->
    <section class="py-12 md:py-16 bg-white border-t">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl text-center">
            <span class="text-blue-600 font-extrabold text-[10px] md:text-xs uppercase tracking-wider block mb-1">Nilai Kami</span>
            <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-custom-blue mb-8 md:mb-12">Nilai yang Menjadi Landasan Kami</h2>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4">
                <?php
                $values = [
                    ['icon' => 'fa-shield-check', 'title' => 'Amanah', 'desc' => 'Kami selalu menjunjung tinggi kepercayaan yang diberikan pelanggan.'],
                    ['icon' => 'fa-user-tie', 'title' => 'Profesional', 'desc' => 'Tim berpengalaman dan berkompeten siap memberikan layanan terbaik.'],
                    ['icon' => 'fa-thumbs-up', 'title' => 'Kualitas', 'desc' => 'Kami berkomitmen memberikan kualitas layanan terbaik.'],
                    ['icon' => 'fa-lightbulb', 'title' => 'Inovatif', 'desc' => 'Selalu berinovasi untuk menciptakan pengalaman perjalanan yang berkesan.'],
                    ['icon' => 'fa-handshake-angle', 'title' => 'Bersahabat', 'desc' => 'Kami hadir sebagai sahabat perjalanan Anda.']
                ];

                foreach ($values as $v) {
                    echo "
                    <div class='bg-white p-3 md:p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col items-center text-center group'>
                        <div class='w-10 h-10 md:w-12 md:h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-base md:text-xl mb-2 md:mb-4 group-hover:bg-blue-600 group-hover:text-white transition shadow-inner'>
                            <i class='fa-solid {$v['icon']}'></i>
                        </div>
                        <h3 class='font-bold text-[11px] md:text-xs text-gray-800 mb-1'>{$v['title']}</h3>
                        <p class='text-[9px] md:text-[10px] text-gray-400 leading-relaxed hidden sm:block'>{$v['desc']}</p>
                    </div>";
                }
                ?>
            </div>
        </div>
    </section>

    <!-- MENGAPA MEMILIH BPW SECTION - RESPONSIF -->
    <section class="py-12 md:py-16 bg-slate-50 border-t border-b">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10 items-center">
                <!-- Konten Kiri -->
                <div class="lg:col-span-6 space-y-4 md:space-y-6 order-2 lg:order-1">
                    <div>
                        <span class="text-blue-600 font-extrabold text-[10px] md:text-xs uppercase tracking-wider block mb-1">Mengapa Memilih BPW?</span>
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-custom-blue">Alasan Perjalanan Anda Lebih Baik Bersama Kami</h2>
                    </div>
                    <ul class="space-y-2 md:space-y-3 text-xs md:text-sm text-gray-700 font-medium">
                        <li class="flex items-start space-x-2 md:space-x-3"><i class="fa-solid fa-circle-check text-blue-600 text-sm md:text-base mt-0.5 flex-shrink-0"></i> <span>Paket wisata beragam dengan harga kompetitif</span></li>
                        <li class="flex items-start space-x-2 md:space-x-3"><i class="fa-solid fa-circle-check text-blue-600 text-sm md:text-base mt-0.5 flex-shrink-0"></i> <span>Tim profesional, ramah, dan berpengalaman</span></li>
                        <li class="flex items-start space-x-2 md:space-x-3"><i class="fa-solid fa-circle-check text-blue-600 text-sm md:text-base mt-0.5 flex-shrink-0"></i> <span>Pilihan destinasi terbaik di seluruh Indonesia</span></li>
                        <li class="flex items-start space-x-2 md:space-x-3"><i class="fa-solid fa-circle-check text-blue-600 text-sm md:text-base mt-0.5 flex-shrink-0"></i> <span>Layanan lengkap: transportasi, akomodasi, tiket, tour guide</span></li>
                        <li class="flex items-start space-x-2 md:space-x-3"><i class="fa-solid fa-circle-check text-blue-600 text-sm md:text-base mt-0.5 flex-shrink-0"></i> <span>Layanan pelanggan responsif 24/7</span></li>
                    </ul>
                </div>

                <!-- Gambar Kanan -->
                <div class="lg:col-span-6 relative rounded-2xl overflow-hidden shadow-lg h-56 sm:h-64 md:h-72 order-1 lg:order-2">
                    <img src="https://images.unsplash.com/photo-1506929562872-bb421503ef21?auto=format&fit=crop&w=800&q=80" class="w-full h-full object-cover" alt="Scenic Beach" loading="lazy">
                    <div class="absolute bottom-3 right-3 left-3 md:bottom-4 md:right-4 md:left-auto md:w-72 bg-black/40 backdrop-blur-md p-3 md:p-4 rounded-xl border border-white/10 text-white text-xs leading-relaxed">
                        <i class="fa-solid fa-quote-left text-cyan-400 text-base md:text-lg block mb-1"></i>
                        <p class="font-medium italic text-[10px] md:text-xs mb-1 md:mb-2">"Kepuasan Anda adalah tujuan perjalanan kami."</p>
                        <span class="block text-[8px] md:text-[10px] text-slate-300 font-semibold">– BPW Team</span>
                    </div>
                </div>
            </div>

            <!-- DUA CARD BAWAH - RESPONSIF -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mt-8 md:mt-12">
                <div class="bg-blue-50/50 rounded-2xl p-4 md:p-6 border border-blue-100/60 flex items-center justify-between space-x-3 md:space-x-4">
                    <div class="space-y-1 md:space-y-1.5">
                        <h4 class="font-bold text-[11px] md:text-xs text-custom-blue">Tim Berpengalaman</h4>
                        <p class="text-[10px] md:text-[11px] text-gray-500 leading-relaxed">Didukung oleh tim yang profesional di bidang pariwisata, kami siap merancang perjalanan terbaik sesuai kebutuhan Anda.</p>
                    </div>
                    <i class="fa-solid fa-users text-blue-200 text-3xl md:text-4xl opacity-80 shrink-0"></i>
                </div>
                <div class="bg-blue-50/50 rounded-2xl p-4 md:p-6 border border-blue-100/60 flex items-center justify-between space-x-3 md:space-x-4">
                    <div class="space-y-1 md:space-y-1.5">
                        <h4 class="font-bold text-[11px] md:text-xs text-custom-blue">Komitmen Kami</h4>
                        <p class="text-[10px] md:text-[11px] text-gray-500 leading-relaxed">Kami akan terus berkomitmen memberikan pelayanan terbaik, membangun kepercayaan, dan menjadi bagian dari setiap cerita perjalanan indah Anda di Indonesia.</p>
                    </div>
                    <i class="fa-solid fa-bullseye text-blue-200 text-3xl md:text-4xl opacity-80 shrink-0"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION - RESPONSIF -->
    <section class="py-10 md:py-12 bg-white">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="bg-gradient-to-r from-blue-700 to-cyan-500 rounded-2xl p-5 md:p-8 text-white flex flex-col md:flex-row justify-between items-center shadow-md space-y-4 md:space-y-0">
                <div class="flex items-center space-x-3 md:space-x-4 text-center md:text-left flex-col md:flex-row space-y-3 md:space-y-0">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-white/10 rounded-full flex items-center justify-center text-lg md:text-xl shrink-0"><i class="fa-solid fa-headset text-cyan-300"></i></div>
                    <div>
                        <h3 class="font-extrabold text-sm md:text-base">Siap Memulai Perjalanan Anda Bersama BPW?</h3>
                        <p class="text-[10px] md:text-[11px] text-slate-100 opacity-90">Mari jelajahi keindahan Indonesia bersama kami dan ciptakan kenangan yang tak terlupakan.</p>
                    </div>
                </div>
                <button class="bg-white hover:bg-slate-50 text-blue-700 font-bold text-[11px] md:text-xs px-4 md:px-5 py-2.5 md:py-3 rounded-md flex items-center space-x-2 shadow transition shrink-0">
                    <span>Hubungi Kami Sekarang</span> <i class="fa-solid fa-arrow-right text-[10px] md:text-xs"></i>
                </button>
            </div>
        </div>
    </section>

<?php
include "layout/footer.php";
?>
</body>
</html>