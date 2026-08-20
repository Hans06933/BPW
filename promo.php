<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Promo & Diskon - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }
        
        /* Hero Section Gradient */
        .bg-promo-hero {
            background: linear-gradient(135deg, rgba(0,51,102,0.92) 0%, rgba(0,76,153,0.75) 100%), url('https://images.unsplash.com/photo-1506929562872-bb421503ef21?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
        
        /* Countdown Timer Style */
        .countdown-box {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(4px);
            border-radius: 0.75rem;
            padding: 0.5rem;
            min-width: 60px;
            text-align: center;
        }
        
        @media (min-width: 640px) {
            .countdown-box {
                min-width: 80px;
                padding: 0.75rem;
            }
        }
        
        /* Promo Card Hover Effect */
        .promo-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .promo-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -12px rgba(0,0,0,0.15);
        }
        
        /* Ribbon Discount */
        .discount-ribbon {
            position: absolute;
            top: 16px;
            right: -34px;
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
            font-weight: bold;
            font-size: 12px;
            padding: 6px 40px;
            transform: rotate(45deg);
            text-align: center;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        /* Tab Menu Style */
        .promo-tab {
            transition: all 0.2s ease;
        }
        
        .promo-tab.active {
            background-color: #003366;
            color: white;
            border-color: #003366;
        }
        
        /* Flash Sale Animation */
        @keyframes pulse-red {
            0%, 100% { background-color: #e53e3e; }
            50% { background-color: #c53030; }
        }
        
        .flash-badge {
            animation: pulse-red 1.5s infinite;
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
    </style>
</head>
<?php include "layout/header.php"; ?>
<body class="bg-slate-50">

    <!-- HERO SECTION -->
    <section class="bg-promo-hero py-16 md:py-24 text-white relative">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 bg-red-500/80 backdrop-blur rounded-full px-4 py-1.5 mb-5">
                    <i class="fa-solid fa-tag text-yellow-300 text-sm"></i>
                    <span class="text-xs font-bold tracking-wide">PROMO TERBATAS</span>
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-4">
                    Penawaran Spesial<br>
                    <span class="text-yellow-300">Untuk Perjalanan Anda</span>
                </h1>
                <p class="text-slate-200 text-base max-w-xl leading-relaxed mb-6">
                    Dapatkan diskon menarik dan bonus eksklusif untuk berbagai paket wisata pilihan. Periode terbatas, jangan sampai kelewatan!
                </p>
                
                <!-- COUNTDOWN TIMER -->
                <div class="bg-black/30 backdrop-blur-md rounded-2xl p-4 md:p-5 inline-block w-full md:w-auto">
                    <p class="text-xs text-slate-200 mb-2 flex items-center gap-2">
                        <i class="fa-regular fa-clock"></i> Promo berakhir dalam:
                    </p>
                    <div class="flex gap-2 md:gap-4" id="countdown">
                        <div class="countdown-box">
                            <span id="days" class="text-xl md:text-2xl font-bold block">00</span>
                            <span class="text-[10px] md:text-xs">Hari</span>
                        </div>
                        <div class="countdown-box">
                            <span id="hours" class="text-xl md:text-2xl font-bold block">00</span>
                            <span class="text-[10px] md:text-xs">Jam</span>
                        </div>
                        <div class="countdown-box">
                            <span id="minutes" class="text-xl md:text-2xl font-bold block">00</span>
                            <span class="text-[10px] md:text-xs">Menit</span>
                        </div>
                        <div class="countdown-box">
                            <span id="seconds" class="text-xl md:text-2xl font-bold block">00</span>
                            <span class="text-[10px] md:text-xs">Detik</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PROMO TABS CATEGORY -->
    <section class="py-8 bg-white border-b shadow-sm sticky top-16 z-40">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="flex flex-wrap justify-center gap-2 md:gap-3">
                <button data-tab="all" class="promo-tab active px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">Semua Promo</button>
                <button data-tab="flash" class="promo-tab px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                    <i class="fa-solid fa-bolt text-yellow-500 mr-1"></i> Flash Sale
                </button>
                <button data-tab="early" class="promo-tab px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                    <i class="fa-solid fa-calendar-week mr-1"></i> Early Bird
                </button>
                <button data-tab="group" class="promo-tab px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                    <i class="fa-solid fa-users mr-1"></i> Group Discount
                </button>
                <button data-tab="member" class="promo-tab px-5 py-2.5 rounded-full text-xs md:text-sm font-semibold transition-all border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                    <i class="fa-solid fa-gem mr-1"></i> Member Exclusive
                </button>
            </div>
        </div>
    </section>

    <!-- PROMO CARDS GRID -->
    <section class="py-12 md:py-16 bg-slate-50">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            
            <!-- Flash Sale Banner -->
            <div id="flashBanner" class="bg-gradient-to-r from-red-600 to-red-500 rounded-2xl p-4 md:p-6 mb-8 text-white flex flex-col md:flex-row justify-between items-center gap-4 flash-badge">
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 rounded-full w-12 h-12 flex items-center justify-center">
                        <i class="fa-solid fa-bolt text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">FLASH SALE!</h3>
                        <p class="text-sm text-red-100">Diskon hingga 50% untuk 5 pemesanan pertama setiap harinya</p>
                    </div>
                </div>
                <button class="bg-white text-red-600 px-6 py-2 rounded-full font-semibold text-sm hover:bg-gray-100 transition">
                    Pesan Sekarang
                </button>
            </div>

            <!-- Promo Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="promoGrid">
                <!-- Promo cards akan di-generate via JS -->
            </div>

            <!-- No Promo Message -->
            <div id="noPromoMessage" class="text-center py-12 hidden">
                <i class="fa-regular fa-face-frown text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-400">Belum ada promo untuk kategori ini. Cek kategori lainnya ya!</p>
            </div>

            <!-- Newsletter Subscription -->
            <div class="mt-16 bg-gradient-to-r from-blue-700 to-cyan-600 rounded-2xl p-6 md:p-8 text-white">
                <div class="text-center max-w-2xl mx-auto">
                    <i class="fa-regular fa-bell text-3xl mb-3"></i>
                    <h3 class="text-xl md:text-2xl font-bold mb-2">Dapatkan Info Promo Terbaru</h3>
                    <p class="text-blue-100 mb-5 text-sm">Bergabunglah dengan newsletter kami dan jadilah yang pertama tahu tentang promo spesial!</p>
                    <form class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                        <input type="email" placeholder="Masukkan email Anda" class="flex-1 px-4 py-3 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-yellow-400 text-sm">
                        <button type="submit" class="bg-yellow-400 hover:bg-yellow-300 text-gray-800 font-semibold px-6 py-3 rounded-lg transition text-sm">
                            Berlangganan
                        </button>
                    </form>
                    <p class="text-blue-200 text-xs mt-3">*Kamu akan menerima promo menarik setiap minggu</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SCROLL TO TOP -->
    <div class="scroll-top" id="scrollTopBtn">
        <i class="fa-solid fa-arrow-up"></i>
    </div>

    <script>
        // Data Promo
        const promoData = [
            // Flash Sale
            { id: 1, category: "flash", title: "Bali 4 Hari 3 Malam", location: "Bali", originalPrice: 3250000, discountPrice: 2150000, discountPercent: 34, image: "https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=500&q=80", validUntil: "2026-06-15", spots: 3, features: ["Hotel Bintang 4", "Breakfast", "Tour Guide", "Transport"] },
            { id: 2, category: "flash", title: "Labuan Bajo 4 Hari 3 Malam", location: "Labuan Bajo", originalPrice: 6750000, discountPrice: 4750000, discountPercent: 30, image: "https://images.unsplash.com/photo-1516690561799-46d8f74f90f6?auto=format&fit=crop&w=500&q=80", validUntil: "2026-06-15", spots: 2, features: ["Liveaboard", "Snorkeling", "Komodo Tour", "Meals"] },
            // Early Bird
            { id: 3, category: "early", title: "Raja Ampat 6 Hari 5 Malam", location: "Raja Ampat", originalPrice: 8950000, discountPrice: 6950000, discountPercent: 22, image: "https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=500&q=80", validUntil: "2026-07-30", spots: 10, features: ["Homestay", "Snorkeling Gear", "Local Guide", "Meals"] },
            { id: 4, category: "early", title: "Bromo Midnight Tour", location: "Bromo", originalPrice: 1650000, discountPrice: 1250000, discountPercent: 24, image: "https://images.unsplash.com/photo-1589308078059-be1415eab4c3?auto=format&fit=crop&w=500&q=80", validUntil: "2026-07-20", spots: 15, features: ["Jeep 4x4", "Breakfast", "Entrance Ticket"] },
            // Group Discount
            { id: 5, category: "group", title: "Yogyakarta Family Package", location: "Yogyakarta", originalPrice: 1850000, discountPrice: 1450000, discountPercent: 22, image: "https://images.unsplash.com/photo-1626125353112-98444f6f1943?auto=format&fit=crop&w=500&q=80", validUntil: "2026-08-15", spots: 20, features: ["Min 4 Pax", "Hotel", "Malioboro Tour", "Candi Visit"] },
            { id: 6, category: "group", title: "Bandung Getaway 2D1N", location: "Bandung", originalPrice: 1250000, discountPrice: 950000, discountPercent: 24, image: "https://images.unsplash.com/photo-1594902319089-6fa2316e6d18?auto=format&fit=crop&w=500&q=80", validUntil: "2026-08-10", spots: 25, features: ["Min 3 Pax", "Hotel", "Tiket Wisata", "Transport"] },
            // Member Exclusive
            { id: 7, category: "member", title: "Lombok Special 4D3N", location: "Lombok", originalPrice: 2850000, discountPrice: 2250000, discountPercent: 21, image: "https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=500&q=80", validUntil: "2026-09-01", spots: 8, features: ["Member Only", "Bonus 1 Night", "Free Tour Guide"] },
            { id: 8, category: "member", title: "Komodo Expedition", location: "Labuan Bajo", originalPrice: 5250000, discountPrice: 4150000, discountPercent: 21, image: "https://images.unsplash.com/photo-1508542318899-8fa664faa62a?auto=format&fit=crop&w=500&q=80", validUntil: "2026-09-10", spots: 6, features: ["Member Price", "Private Boat", "BBQ Dinner"] },
            // Additional Promos
            { id: 9, category: "flash", title: "Nusa Penida Day Tour", location: "Bali", originalPrice: 1250000, discountPrice: 850000, discountPercent: 32, image: "https://images.unsplash.com/photo-1587532266429-5a898f3dc397?auto=format&fit=crop&w=500&q=80", validUntil: "2026-06-18", spots: 5, features: ["Fast Boat", "Lunch", "Snorkeling", "Kelingking Visit"] },
            { id: 10, category: "early", title: "Danau Toba Escape", location: "Danau Toba", originalPrice: 1950000, discountPrice: 1550000, discountPercent: 21, image: "https://images.unsplash.com/photo-1504618223053-559bdef9dd5a?auto=format&fit=crop&w=500&q=80", validUntil: "2026-07-25", spots: 12, features: ["Hotel", "Kapal Wisata", "Breakfast"] }
        ];

        let currentTab = "all";

        function formatPrice(price) {
            return "Rp " + price.toLocaleString('id-ID');
        }

        function renderPromos() {
            const filtered = currentTab === "all" 
                ? promoData 
                : promoData.filter(item => item.category === currentTab);
            
            const grid = document.getElementById("promoGrid");
            const noMessage = document.getElementById("noPromoMessage");
            
            if (!grid) return;
            
            if (filtered.length === 0) {
                grid.classList.add("hidden");
                noMessage.classList.remove("hidden");
                return;
            }
            
            grid.classList.remove("hidden");
            noMessage.classList.add("hidden");
            
            grid.innerHTML = filtered.map(promo => `
                <div class="promo-card bg-white rounded-2xl overflow-hidden shadow-md border border-gray-100 relative group">
                    ${promo.category === 'flash' ? '<div class="discount-ribbon">FLASH SALE</div>' : ''}
                    <div class="relative h-48 overflow-hidden bg-gray-200">
                        <img src="${promo.image}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="${promo.title}" loading="lazy">
                        <div class="absolute bottom-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                            -${promo.discountPercent}%
                        </div>
                        ${promo.spots && promo.spots <= 3 ? `
                        <div class="absolute top-3 right-3 bg-yellow-500 text-white px-2 py-1 rounded-full text-[10px] font-bold flex items-center gap-1">
                            <i class="fa-solid fa-fire"></i> Sisa ${promo.spots}
                        </div>
                        ` : ''}
                        <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur text-white px-2 py-1 rounded-lg text-[10px] flex items-center gap-1">
                            <i class="fa-regular fa-clock"></i> ${promo.validUntil}
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <div class="flex items-center gap-1 text-xs text-gray-400 mb-1">
                                    <i class="fa-solid fa-location-dot text-blue-500"></i>
                                    <span>${promo.location}</span>
                                </div>
                                <h3 class="font-bold text-base text-gray-800 group-hover:text-blue-600 transition">${promo.title}</h3>
                            </div>
                            ${promo.category === 'member' ? '<i class="fa-solid fa-gem text-blue-500 text-sm"></i>' : ''}
                        </div>
                        <div class="flex flex-wrap gap-1.5 mt-3">
                            ${promo.features.map(f => `<span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-1 rounded-full">${f}</span>`).join('')}
                        </div>
                        <div class="border-t mt-4 pt-4 flex justify-between items-center">
                            <div>
                                <span class="text-[10px] text-gray-400 line-through block">${formatPrice(promo.originalPrice)}</span>
                                <span class="text-lg font-bold text-blue-600">${formatPrice(promo.discountPrice)}</span>
                                <span class="text-[10px] text-gray-400">/pax</span>
                            </div>
                            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1">
                                Pesan <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join("");
        }

        // Tab switching
        document.querySelectorAll(".promo-tab").forEach(tab => {
            tab.addEventListener("click", function() {
                document.querySelectorAll(".promo-tab").forEach(t => t.classList.remove("active"));
                this.classList.add("active");
                currentTab = this.getAttribute("data-tab");
                
                // Show/hide flash banner
                const flashBanner = document.getElementById("flashBanner");
                if (flashBanner) {
                    if (currentTab === "flash") {
                        flashBanner.style.display = "flex";
                    } else {
                        flashBanner.style.display = "flex";
                    }
                }
                
                renderPromos();
            });
        });

        // Countdown Timer (target date: 30 days from now)
        function setCountdown() {
            const targetDate = new Date();
            targetDate.setDate(targetDate.getDate() + 30);
            targetDate.setHours(23, 59, 59, 999);
            
            function updateCountdown() {
                const now = new Date().getTime();
                const distance = targetDate - now;
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                document.getElementById("days").innerHTML = days.toString().padStart(2, '0');
                document.getElementById("hours").innerHTML = hours.toString().padStart(2, '0');
                document.getElementById("minutes").innerHTML = minutes.toString().padStart(2, '0');
                document.getElementById("seconds").innerHTML = seconds.toString().padStart(2, '0');
            }
            
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
        
        setCountdown();
        renderPromos();

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
    </script>

<?php include "layout/footer.php"; ?>
</body>
</html>