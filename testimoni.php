<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Testimoni Pelanggan - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }
        
        /* Hero Section */
        .bg-testimoni-hero {
            background: linear-gradient(135deg, rgba(0,51,102,0.88) 0%, rgba(0,76,153,0.75) 100%), url('https://images.unsplash.com/photo-1556741533-6e6a3bd8e341?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
        
        /* Testimoni Card */
        .testimoni-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .testimoni-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -12px rgba(0,0,0,0.15);
        }
        
        /* Star Rating */
        .star-rating i {
            color: #fbbf24;
            font-size: 12px;
        }
        
        /* Quote Icon */
        .quote-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            opacity: 0.1;
            font-size: 48px;
            transition: opacity 0.3s ease;
        }
        
        .testimoni-card:hover .quote-icon {
            opacity: 0.2;
        }
        
        /* Filter Button */
        .filter-btn {
            transition: all 0.2s ease;
        }
        
        .filter-btn.active {
            background-color: #003366;
            color: white;
            border-color: #003366;
        }
        
        /* Statistic Card */
        .stat-card {
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
        }
        
        /* Rating Summary */
        .rating-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .rating-fill {
            height: 100%;
            background: #fbbf24;
            border-radius: 4px;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.9);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .modal.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }
        
        .modal-content {
            max-width: 500px;
            width: 90%;
            background: white;
            border-radius: 1.5rem;
            cursor: default;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
        
        /* Swiper Container untuk Mobile */
        .swiper-container {
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }
        
        .swiper-slide {
            scroll-snap-align: start;
        }
        
        /* Hide scrollbar */
        .swiper-container::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<?php include "layout/header.php"; ?>
<body class="bg-slate-50">

    <!-- HERO SECTION -->
    <section class="bg-testimoni-hero py-20 md:py-28 text-white relative">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur rounded-full px-4 py-1.5 mb-5">
                    <i class="fa-regular fa-star text-yellow-300 text-sm"></i>
                    <span class="text-xs font-bold tracking-wide">TESTIMONI</span>
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-4">
                    Apa Kata<br>
                    <span class="text-yellow-300">Pelanggan Kami</span>
                </h1>
                <p class="text-slate-200 text-base max-w-xl leading-relaxed">
                    Lebih dari 5000 pelanggan puas telah mempercayakan perjalanan mereka kepada BPW. Simak pengalaman mereka di sini.
                </p>
            </div>
        </div>
    </section>

    <!-- STATISTIK SECTION -->
    <section class="py-8 md:py-10 bg-white border-b">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-center">
                <div class="stat-card p-3">
                    <i class="fa-regular fa-face-smile text-blue-600 text-2xl md:text-3xl mb-2"></i>
                    <div class="text-xl md:text-2xl font-bold text-gray-800">5.000+</div>
                    <div class="text-[10px] md:text-xs text-gray-400">Pelanggan Puas</div>
                </div>
                <div class="stat-card p-3">
                    <i class="fa-regular fa-star text-yellow-400 text-2xl md:text-3xl mb-2"></i>
                    <div class="text-xl md:text-2xl font-bold text-gray-800">4.9/5</div>
                    <div class="text-[10px] md:text-xs text-gray-400">Rating Pelanggan</div>
                </div>
                <div class="stat-card p-3">
                    <i class="fa-regular fa-building text-blue-600 text-2xl md:text-3xl mb-2"></i>
                    <div class="text-xl md:text-2xl font-bold text-gray-800">100+</div>
                    <div class="text-[10px] md:text-xs text-gray-400">Destinasi</div>
                </div>
                <div class="stat-card p-3">
                    <i class="fa-regular fa-calendar text-blue-600 text-2xl md:text-3xl mb-2"></i>
                    <div class="text-xl md:text-2xl font-bold text-gray-800">10+</div>
                    <div class="text-[10px] md:text-xs text-gray-400">Tahun Berpengalaman</div>
                </div>
                <div class="stat-card p-3">
                    <i class="fa-regular fa-comment text-blue-600 text-2xl md:text-3xl mb-2"></i>
                    <div class="text-xl md:text-2xl font-bold text-gray-800">98%</div>
                    <div class="text-[10px] md:text-xs text-gray-400">Rekomendasi</div>
                </div>
            </div>
        </div>
    </section>

    <!-- RATING SUMMARY SECTION -->
    <section class="py-8 bg-slate-50">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Left: Overall Rating -->
                    <div class="text-center md:text-left">
                        <div class="inline-flex items-center gap-2 bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold mb-3">
                            <i class="fa-regular fa-circle-check"></i> Terverifikasi
                        </div>
                        <div class="text-5xl md:text-6xl font-bold text-gray-800 mb-2">4.9</div>
                        <div class="star-rating mb-2">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star-half-alt"></i>
                        </div>
                        <p class="text-sm text-gray-500">Dari 5.234 ulasan pelanggan</p>
                        <div class="flex flex-wrap gap-2 justify-center md:justify-start mt-4">
                            <span class="bg-gray-100 px-3 py-1 rounded-full text-xs">Google Maps</span>
                            <span class="bg-gray-100 px-3 py-1 rounded-full text-xs">TripAdvisor</span>
                            <span class="bg-gray-100 px-3 py-1 rounded-full text-xs">Facebook</span>
                            <span class="bg-gray-100 px-3 py-1 rounded-full text-xs">Website</span>
                        </div>
                    </div>
                    <!-- Right: Rating Breakdown -->
                    <div class="space-y-2">
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>5 Bintang</span>
                                <span>85%</span>
                            </div>
                            <div class="rating-bar"><div class="rating-fill" style="width: 85%"></div></div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>4 Bintang</span>
                                <span>10%</span>
                            </div>
                            <div class="rating-bar"><div class="rating-fill" style="width: 10%"></div></div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>3 Bintang</span>
                                <span>3%</span>
                            </div>
                            <div class="rating-bar"><div class="rating-fill" style="width: 3%"></div></div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>2 Bintang</span>
                                <span>1%</span>
                            </div>
                            <div class="rating-bar"><div class="rating-fill" style="width: 1%"></div></div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>1 Bintang</span>
                                <span>1%</span>
                            </div>
                            <div class="rating-bar"><div class="rating-fill" style="width: 1%"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FILTER BUTTONS -->
    <section class="py-6 bg-white border-b sticky top-16 z-40">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="flex flex-wrap justify-center gap-2">
                <button data-filter="all" class="filter-btn active px-5 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">Semua</button>
                <button data-filter="bali" class="filter-btn px-5 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">🏝️ Bali</button>
                <button data-filter="yogya" class="filter-btn px-5 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">🛕 Yogyakarta</button>
                <button data-filter="labuanbajo" class="filter-btn px-5 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">🐉 Labuan Bajo</button>
                <button data-filter="bromo" class="filter-btn px-5 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">🌋 Bromo</button>
                <button data-filter="keluarga" class="filter-btn px-5 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">👨‍👩‍👧‍👦 Keluarga</button>
            </div>
        </div>
    </section>

    <!-- TESTIMONI GRID SECTION -->
    <section class="py-12 md:py-16 bg-slate-50">
        <div class="container mx-auto px-4 md:px-6 max-w-7xl">
            
            <!-- Featured Testimonial -->
            <div id="featuredTestimonial" class="mb-8"></div>
            
            <!-- Testimoni Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" id="testimoniGrid">
                <!-- Testimoni cards will be generated via JS -->
            </div>
            
            <!-- Load More Button -->
            <div class="text-center mt-10" id="loadMoreContainer">
                <button id="loadMoreBtn" class="bg-white border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 px-8 py-3 rounded-full text-sm font-semibold inline-flex items-center gap-2">
                    <i class="fa-solid fa-arrow-down"></i> Muat Lebih Banyak
                </button>
            </div>
            
            <!-- No Results -->
            <div id="noResults" class="text-center py-12 hidden">
                <i class="fa-regular fa-face-frown text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-400">Belum ada testimoni untuk kategori ini</p>
            </div>
        </div>
    </section>

    <!-- FORM UPLOAD TESTIMONI -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 md:px-6 max-w-4xl">
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl p-6 md:p-8 text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-regular fa-pen-to-square text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-gray-800 mb-3">Bagikan Pengalaman Anda</h3>
                <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">Sudah bepergian bersama BPW? Bagikan cerita Anda dan inspirasi traveler lainnya!</p>
                <button id="writeTestimoniBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-full font-semibold transition shadow-lg inline-flex items-center gap-2">
                    <i class="fa-regular fa-star"></i> Tulis Testimoni
                </button>
            </div>
        </div>
    </section>

    <!-- MODAL DETAIL TESTIMONI -->
    <div id="testimoniModal" class="modal">
        <div class="modal-content">
            <div class="relative p-6">
                <button class="absolute top-4 right-4 w-8 h-8 bg-gray-100 rounded-full hover:bg-gray-200 transition" id="closeModal">
                    <i class="fa-solid fa-xmark text-gray-600"></i>
                </button>
                <div id="modalBody"></div>
            </div>
        </div>
    </div>

    <!-- MODAL TULIS TESTIMONI -->
    <div id="writeModal" class="modal">
        <div class="modal-content max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg text-gray-800">Tulis Testimoni</h3>
                    <button id="closeWriteModal" class="w-8 h-8 bg-gray-100 rounded-full hover:bg-gray-200 transition">
                        <i class="fa-solid fa-xmark text-gray-600"></i>
                    </button>
                </div>
                <form id="testimoniForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-400" placeholder="Contoh: John Doe" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Destinasi</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-400">
                            <option>Bali</option>
                            <option>Yogyakarta</option>
                            <option>Labuan Bajo</option>
                            <option>Bromo</option>
                            <option>Raja Ampat</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                        <div class="flex gap-1 text-2xl" id="ratingStars">
                            <i class="fa-regular fa-star cursor-pointer hover:text-yellow-400 transition" data-rating="1"></i>
                            <i class="fa-regular fa-star cursor-pointer hover:text-yellow-400 transition" data-rating="2"></i>
                            <i class="fa-regular fa-star cursor-pointer hover:text-yellow-400 transition" data-rating="3"></i>
                            <i class="fa-regular fa-star cursor-pointer hover:text-yellow-400 transition" data-rating="4"></i>
                            <i class="fa-regular fa-star cursor-pointer hover:text-yellow-400 transition" data-rating="5"></i>
                        </div>
                        <input type="hidden" id="selectedRating" value="0">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Testimoni</label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-400" placeholder="Ceritakan pengalaman Anda bersama BPW..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold transition">
                        Kirim Testimoni
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-3">*Testimoni akan ditampilkan setelah diverifikasi</p>
                </form>
            </div>
        </div>
    </div>

    <!-- SCROLL TO TOP -->
    <div class="scroll-top" id="scrollTopBtn">
        <i class="fa-solid fa-arrow-up"></i>
    </div>

    <script>
        // Testimoni Data
        const testimonials = [
            { id: 1, name: "Siti Rahmawati", location: "Jakarta", destination: "bali", destinationName: "Bali", rating: 5, date: "2026-05-15", text: "Pelayanan BPW sangat memuaskan! Itinerary terencana dengan baik, tour guide ramah dan berpengalaman. Hotel yang dipilih juga nyaman. Pasti akan pakai jasa BPW lagi untuk liburan berikutnya!", image: "https://randomuser.me/api/portraits/women/1.jpg", tripType: "Keluarga", verified: true },
            { id: 2, name: "Andi Pratama", location: "Bandung", destination: "labuanbajo", destinationName: "Labuan Bajo", rating: 5, date: "2026-05-10", text: "Pengalaman luar biasa ke Labuan Bajo! Kapal bersih, snorkelingnya seru, dan bisa lihat Komodo langsung. Terima kasih BPW untuk liburan yang tak terlupakan!", image: "https://randomuser.me/api/portraits/men/2.jpg", tripType: "Couple", verified: true },
            { id: 3, name: "Dewi Lestari", location: "Surabaya", destination: "yogya", destinationName: "Yogyakarta", rating: 4, date: "2026-05-05", text: "Liburan ke Jogja jadi lebih menyenangkan dengan BPW. Transportasi nyaman, akomodasi oke, dan tour guide yang informatif. Harga terjangkau dengan pelayanan yang baik.", image: "https://randomuser.me/api/portraits/women/3.jpg", tripType: "Traveler", verified: true },
            { id: 4, name: "Budi Santoso", location: "Semarang", destination: "bromo", destinationName: "Bromo", rating: 5, date: "2026-04-28", text: "Sunrise di Bromo sungguh memukau! Terima kasih BPW yang sudah mengatur perjalanan dengan sangat baik. Jeep-nya nyaman, tour guide sangat membantu. Recommended!", image: "https://randomuser.me/api/portraits/men/4.jpg", tripType: "Solo", verified: true },
            { id: 5, name: "Rina Marlina", location: "Medan", destination: "bali", destinationName: "Bali", rating: 5, date: "2026-04-20", text: "Liburan keluarga ke Bali bersama BPW sangat berkesan. Anak-anak senang, semua akomodasi sudah diatur dengan baik. Terima kasih BPW, kami pasti kembali!", image: "https://randomuser.me/api/portraits/women/5.jpg", tripType: "Keluarga", verified: true },
            { id: 6, name: "Agus Wijaya", location: "Palembang", destination: "labuanbajo", destinationName: "Labuan Bajo", rating: 5, date: "2026-04-15", text: "Pelayanan BPW top markotop! Dari awal pemesanan sampai perjalanan selesai, semuanya profesional. Destinasi yang ditawarkan juga menarik. Puas banget!", image: "https://randomuser.me/api/portraits/men/6.jpg", tripType: "Traveler", verified: true },
            { id: 7, name: "Lisa Permata", location: "Makassar", destination: "yogya", destinationName: "Yogyakarta", rating: 4, date: "2026-04-10", text: "Perjalanan ke Yogyakarta terasa spesial dengan BPW. Candi Borobudur dan Prambanan sangat indah. Tour guide ramah dan banyak memberikan informasi sejarah. Terima kasih!", image: "https://randomuser.me/api/portraits/women/7.jpg", tripType: "Couple", verified: true },
            { id: 8, name: "Rizky Fadillah", location: "Batam", destination: "bromo", destinationName: "Bromo", rating: 5, date: "2026-04-05", text: "Bromo sunset tour keren banget! Harga bersahabat dengan pelayanan yang sangat memuaskan. Tim BPW responsif dan cepat tanggap. Pasti rekomen ke teman-teman!", image: "https://randomuser.me/api/portraits/men/8.jpg", tripType: "Solo", verified: true },
            { id: 9, name: "Mega Wati", location: "Pontianak", destination: "bali", destinationName: "Bali", rating: 5, date: "2026-03-28", text: "Liburan ke Bali 5 hari 4 malam bersama BPW sungguh amazing! Semua sudah diatur dengan baik, tinggal enjoy. Terima kasih BPW, jasa terbaik!", image: "https://randomuser.me/api/portraits/women/9.jpg", tripType: "Keluarga", verified: true },
            { id: 10, name: "Hendra Gunawan", location: "Denpasar", destination: "labuanbajo", destinationName: "Labuan Bajo", rating: 5, date: "2026-03-20", text: "Pengalaman liburan terbaik seumur hidup! Komodo, Pink Beach, Padar Island semuanya indah. BPW mengatur semuanya dengan sangat profesional. Terima kasih!", image: "https://randomuser.me/api/portraits/men/10.jpg", tripType: "Traveler", verified: true }
        ];

        let currentFilter = "all";
        let visibleCount = 6;

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        function getStarsHTML(rating) {
            let html = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= rating) {
                    html += '<i class="fa-solid fa-star text-yellow-400 text-xs"></i>';
                } else if (i - 0.5 <= rating) {
                    html += '<i class="fa-solid fa-star-half-alt text-yellow-400 text-xs"></i>';
                } else {
                    html += '<i class="fa-regular fa-star text-gray-300 text-xs"></i>';
                }
            }
            return html;
        }

        function getFilteredTestimonials() {
            let filtered = [...testimonials];
            if (currentFilter !== "all") {
                filtered = filtered.filter(t => t.destination === currentFilter);
            }
            return filtered;
        }

        function renderTestimonials() {
            const filtered = getFilteredTestimonials();
            const grid = document.getElementById("testimoniGrid");
            const featuredContainer = document.getElementById("featuredTestimonial");
            const noResults = document.getElementById("noResults");
            const loadMoreContainer = document.getElementById("loadMoreContainer");
            
            // Featured testimonial (first item on desktop)
            if (filtered.length > 0 && window.innerWidth >= 768 && currentFilter === "all") {
                const featured = filtered[0];
                featuredContainer.innerHTML = `
                    <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-2xl p-6 md:p-8 text-white mb-6">
                        <div class="flex flex-col md:flex-row gap-6 items-center">
                            <div class="w-20 h-20 md:w-24 md:h-24 rounded-full border-4 border-white/30 overflow-hidden flex-shrink-0">
                                <img src="${featured.image}" class="w-full h-full object-cover" alt="${featured.name}">
                            </div>
                            <div class="text-center md:text-left flex-1">
                                <div class="flex justify-center md:justify-start gap-1 mb-2">${getStarsHTML(featured.rating)}</div>
                                <p class="text-sm md:text-base italic mb-3">"${featured.text.substring(0, 150)}..."</p>
                                <div>
                                    <p class="font-semibold">${featured.name}</p>
                                    <p class="text-xs text-blue-200">${featured.location} • ${featured.tripType} • ${formatDate(featured.date)}</p>
                                </div>
                            </div>
                            <button onclick="showTestimonialDetail(${featured.id})" class="bg-white/20 hover:bg-white/30 text-white px-5 py-2 rounded-full text-sm font-semibold transition flex items-center gap-2">
                                Baca Selengkapnya <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                `;
                const remaining = filtered.slice(1, visibleCount);
                if (remaining.length === 0) {
                    grid.innerHTML = `<div class="col-span-full text-center py-8"><p class="text-gray-400">Belum ada testimoni lain</p></div>`;
                } else {
                    grid.innerHTML = remaining.map(t => `
                        <div class="testimoni-card bg-white rounded-xl p-5 shadow-sm border border-gray-100 relative cursor-pointer" onclick="showTestimonialDetail(${t.id})">
                            <i class="fa-solid fa-quote-right quote-icon"></i>
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg overflow-hidden">
                                    ${t.image ? `<img src="${t.image}" class="w-full h-full object-cover">` : `<span>${t.name.charAt(0)}</span>`}
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm text-gray-800">${t.name}</h3>
                                    <p class="text-[10px] text-gray-400">${t.location} • ${t.tripType}</p>
                                </div>
                                ${t.verified ? '<i class="fa-solid fa-circle-check text-green-500 text-sm ml-auto"></i>' : ''}
                            </div>
                            <div class="flex gap-0.5 mb-2">${getStarsHTML(t.rating)}</div>
                            <p class="text-gray-500 text-xs leading-relaxed line-clamp-3">"${t.text}"</p>
                            <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
                                <span class="text-[10px] text-gray-400">${t.destinationName} • ${formatDate(t.date)}</span>
                                <span class="text-blue-600 text-[10px] font-semibold">Baca →</span>
                            </div>
                        </div>
                    `).join("");
                }
            } else {
                featuredContainer.innerHTML = '';
                const displayPosts = filtered.slice(0, visibleCount);
                if (displayPosts.length === 0) {
                    grid.innerHTML = '';
                    noResults.classList.remove("hidden");
                    loadMoreContainer.classList.add("hidden");
                    return;
                }
                noResults.classList.add("hidden");
                grid.innerHTML = displayPosts.map(t => `
                    <div class="testimoni-card bg-white rounded-xl p-5 shadow-sm border border-gray-100 relative cursor-pointer" onclick="showTestimonialDetail(${t.id})">
                        <i class="fa-solid fa-quote-right quote-icon"></i>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg overflow-hidden">
                                ${t.image ? `<img src="${t.image}" class="w-full h-full object-cover">` : `<span>${t.name.charAt(0)}</span>`}
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-gray-800">${t.name}</h3>
                                <p class="text-[10px] text-gray-400">${t.location} • ${t.tripType}</p>
                            </div>
                            ${t.verified ? '<i class="fa-solid fa-circle-check text-green-500 text-sm ml-auto"></i>' : ''}
                        </div>
                        <div class="flex gap-0.5 mb-2">${getStarsHTML(t.rating)}</div>
                        <p class="text-gray-500 text-xs leading-relaxed line-clamp-3">"${t.text}"</p>
                        <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-[10px] text-gray-400">${t.destinationName} • ${formatDate(t.date)}</span>
                            <span class="text-blue-600 text-[10px] font-semibold">Baca →</span>
                        </div>
                    </div>
                `).join("");
            }
            
            // Update load more button
            const totalFiltered = filtered.length;
            const displayCount = currentFilter === "all" && window.innerWidth >= 768 ? visibleCount : visibleCount;
            if (displayCount >= totalFiltered || totalFiltered === 0) {
                loadMoreContainer.classList.add("hidden");
            } else {
                loadMoreContainer.classList.remove("hidden");
            }
        }

        // Show Testimonial Detail Modal
        window.showTestimonialDetail = (id) => {
            const t = testimonials.find(t => t.id === id);
            if (!t) return;
            
            const modalBody = document.getElementById("modalBody");
            const modal = document.getElementById("testimoniModal");
            
            modalBody.innerHTML = `
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4 overflow-hidden">
                        ${t.image ? `<img src="${t.image}" class="w-full h-full object-cover">` : `<i class="fa-regular fa-user text-blue-600 text-3xl"></i>`}
                    </div>
                    <h3 class="font-bold text-lg text-gray-800">${t.name}</h3>
                    <p class="text-xs text-gray-400 mb-2">${t.location} • ${t.tripType}</p>
                    <div class="flex justify-center gap-0.5 mb-3">${getStarsHTML(t.rating)}</div>
                    <p class="text-gray-600 text-sm leading-relaxed italic mb-4">"${t.text}"</p>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500"><i class="fa-solid fa-location-dot text-blue-500"></i> Destinasi: ${t.destinationName}</p>
                        <p class="text-xs text-gray-500 mt-1"><i class="fa-regular fa-calendar"></i> Tanggal: ${formatDate(t.date)}</p>
                    </div>
                    <button onclick="closeModal()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full text-sm font-semibold transition">
                                        Tutup
                                    </button>
                                </div>
                            `;
                            modal.classList.add("active");
                            document.body.style.overflow = "hidden";
                        };
                        
                        function closeModal() {
                            const modal = document.getElementById("testimoniModal");
                            modal.classList.remove("active");
                            document.body.style.overflow = "";
                        }
                        
                        document.getElementById("closeModal")?.addEventListener("click", closeModal);
                        document.getElementById("testimoniModal")?.addEventListener("click", (e) => {
                            if (e.target === document.getElementById("testimoniModal")) closeModal();
                        });
                        
                        // Load More
                        document.getElementById("loadMoreBtn")?.addEventListener("click", () => {
                            visibleCount += 4;
                            renderTestimonials();
                        });
                        
                        // Category Filter
                        document.querySelectorAll(".filter-btn").forEach(btn => {
                            btn.addEventListener("click", function() {
                                document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
                                this.classList.add("active");
                                currentFilter = this.getAttribute("data-filter");
                                visibleCount = 6;
                                renderTestimonials();
                                window.scrollTo({ top: 450, behavior: "smooth" });
                            });
                        });
                        
                        // Write Testimoni Modal
                        const writeModal = document.getElementById("writeModal");
                        const writeBtn = document.getElementById("writeTestimoniBtn");
                        const closeWriteModal = document.getElementById("closeWriteModal");
                        
                        writeBtn?.addEventListener("click", () => {
                            writeModal.classList.add("active");
                            document.body.style.overflow = "hidden";
                        });
                        
                        closeWriteModal?.addEventListener("click", () => {
                            writeModal.classList.remove("active");
                            document.body.style.overflow = "";
                        });
                        
                        writeModal?.addEventListener("click", (e) => {
                            if (e.target === writeModal) {
                                writeModal.classList.remove("active");
                                document.body.style.overflow = "";
                            }
                        });
                        
                        // Rating Stars
                        const ratingStars = document.querySelectorAll("#ratingStars i");
                        const selectedRating = document.getElementById("selectedRating");
                        
                        ratingStars.forEach(star => {
                            star.addEventListener("click", function() {
                                const rating = parseInt(this.getAttribute("data-rating"));
                                selectedRating.value = rating;
                                ratingStars.forEach((s, i) => {
                                    if (i < rating) {
                                        s.className = "fa-solid fa-star text-yellow-400 text-xl cursor-pointer";
                                    } else {
                                        s.className = "fa-regular fa-star text-gray-300 text-xl cursor-pointer";
                                    }
                                });
                            });
                        });
                        
                        // Submit Testimoni Form
                        document.getElementById("testimoniForm")?.addEventListener("submit", (e) => {
                            e.preventDefault();
                            alert("Terima kasih! Testimoni Anda akan kami proses dan tampilkan setelah diverifikasi.");
                            writeModal.classList.remove("active");
                            document.body.style.overflow = "";
                        });
                        
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
                        
                        // Handle window resize
                        let resizeTimeout;
                        window.addEventListener("resize", () => {
                            clearTimeout(resizeTimeout);
                            resizeTimeout = setTimeout(() => renderTestimonials(), 200);
                        });
                        
                        // Initial render
                        renderTestimonials();
                    </script>
                    
                    <style>
                        .line-clamp-3 {
                            display: -webkit-box;
                            -webkit-line-clamp: 3;
                            -webkit-box-orient: vertical;
                            overflow: hidden;
                        }
                    </style>
                    
                    <?php include "layout/footer.php"; ?>
                    </body>
                    </html>