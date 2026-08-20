<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Blog & Artikel Wisata - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }
        
        /* Hero Section */
        .bg-blog-hero {
            background: linear-gradient(135deg, rgba(0,51,102,0.88) 0%, rgba(0,76,153,0.75) 100%), url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
        
        /* Blog Card Hover */
        .blog-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .blog-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -12px rgba(0,0,0,0.15);
        }
        
        /* Category Badge */
        .category-badge {
            transition: all 0.2s ease;
        }
        
        .category-badge.active {
            background-color: #003366;
            color: white;
            border-color: #003366;
        }
        
        /* Sidebar Widget */
        .widget {
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #f0f0f0;
        }
        
        /* Popular Post Item */
        .popular-item {
            transition: all 0.2s ease;
        }
        
        .popular-item:hover {
            background-color: #f8fafc;
            transform: translateX(4px);
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
        
        /* Skeleton Loading */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        /* Search Box Focus */
        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(0,51,102,0.1);
        }
    </style>
</head>
<?php include "layout/header.php"; ?>
<body class="bg-slate-50">

    <!-- HERO SECTION -->
    <section class="bg-blog-hero py-20 md:py-28 text-white relative">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur rounded-full px-4 py-1.5 mb-5">
                    <i class="fa-regular fa-newspaper text-yellow-300 text-sm"></i>
                    <span class="text-xs font-bold tracking-wide">BLOG & ARTIKEL</span>
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-4">
                    Inspirasi<br>
                    <span class="text-yellow-300">Perjalanan Anda</span>
                </h1>
                <p class="text-slate-200 text-base max-w-xl leading-relaxed">
                    Temukan tips perjalanan, rekomendasi destinasi, dan cerita seru dari para traveler bersama Bayu Prima Wisata.
                </p>
                
                <!-- Search Bar -->
                <div class="mt-8">
                    <div class="bg-white rounded-full p-1.5 flex items-center shadow-lg max-w-md">
                        <div class="flex-1 px-4">
                            <input type="text" id="searchInput" placeholder="Cari artikel..." class="w-full py-2 text-sm text-gray-700 focus:outline-none search-input">
                        </div>
                        <button id="searchBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-full text-sm font-semibold transition flex items-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i> Cari
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <section class="py-10 md:py-12 bg-slate-50">
        <div class="container mx-auto px-4 md:px-6 max-w-7xl">
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- BLOG POSTS - MAIN CONTENT -->
                <div class="flex-1">
                    <!-- Categories -->
                    <div class="flex flex-wrap gap-2 mb-8">
                        <button data-cat="all" class="category-badge active px-4 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">Semua</button>
                        <button data-cat="destinasi" class="category-badge px-4 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                            <i class="fa-solid fa-map-location-dot mr-1"></i> Destinasi
                        </button>
                        <button data-cat="tips" class="category-badge px-4 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                            <i class="fa-solid fa-lightbulb mr-1"></i> Tips Travel
                        </button>
                        <button data-cat="budaya" class="category-badge px-4 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                            <i class="fa-solid fa-gopuram mr-1"></i> Budaya
                        </button>
                        <button data-cat="kuliner" class="category-badge px-4 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                            <i class="fa-solid fa-utensils mr-1"></i> Kuliner
                        </button>
                        <button data-cat="event" class="category-badge px-4 py-2 rounded-full text-xs font-semibold transition border border-gray-300 bg-white text-gray-700 hover:bg-gray-100">
                            <i class="fa-regular fa-calendar mr-1"></i> Event
                        </button>
                    </div>
                    
                    <!-- Featured Post (Grid 2) -->
                    <div id="featuredPost" class="mb-8"></div>
                    
                    <!-- Blog Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="blogGrid">
                        <!-- Blog cards will be generated via JS -->
                    </div>
                    
                    <!-- Load More Button -->
                    <div class="text-center mt-8" id="loadMoreContainer">
                        <button id="loadMoreBtn" class="bg-white border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 px-8 py-3 rounded-full text-sm font-semibold inline-flex items-center gap-2">
                            <i class="fa-solid fa-arrow-down"></i> Muat Lebih Banyak
                        </button>
                    </div>
                    
                    <!-- No Results -->
                    <div id="noResults" class="text-center py-12 hidden">
                        <i class="fa-regular fa-face-frown text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-400">Tidak ada artikel yang ditemukan. Coba kata kunci lain!</p>
                    </div>
                </div>
                
                <!-- SIDEBAR -->
                <aside class="lg:w-80 flex-shrink-0">
                    <div class="space-y-6">
                        <!-- Popular Posts Widget -->
                        <div class="widget">
                            <h3 class="font-bold text-base text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                                <i class="fa-solid fa-fire text-orange-500"></i> Populer Minggu Ini
                            </h3>
                            <div class="space-y-4" id="popularPosts">
                                <!-- Popular posts will be generated -->
                            </div>
                        </div>
                        
                        <!-- Categories Widget -->
                        <div class="widget">
                            <h3 class="font-bold text-base text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                                <i class="fa-solid fa-tag"></i> Kategori
                            </h3>
                            <div class="flex flex-wrap gap-2" id="categoryList">
                                <!-- Categories will be generated -->
                            </div>
                        </div>
                        
                        <!-- Newsletter Widget -->
                        <div class="widget bg-gradient-to-r from-blue-50 to-cyan-50 border-0">
                            <div class="text-center">
                                <i class="fa-regular fa-envelope text-3xl text-blue-600 mb-3"></i>
                                <h3 class="font-bold text-base text-gray-800 mb-2">Newsletter</h3>
                                <p class="text-xs text-gray-500 mb-4">Dapatkan artikel terbaru langsung ke email Anda!</p>
                                <form class="flex flex-col gap-2">
                                    <input type="email" placeholder="Email Anda" class="px-4 py-2 rounded-lg text-sm border border-gray-200 focus:outline-none focus:border-blue-400">
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                                        Berlangganan
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Follow Us Widget -->
                        <div class="widget">
                            <h3 class="font-bold text-base text-gray-800 mb-4 pb-2 border-b flex items-center gap-2">
                                <i class="fa-regular fa-share-from-square"></i> Ikuti Kami
                            </h3>
                            <div class="flex justify-center gap-4">
                                <a href="#" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="w-10 h-10 bg-pink-600 text-white rounded-full flex items-center justify-center hover:bg-pink-700 transition"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="w-10 h-10 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700 transition"><i class="fab fa-youtube"></i></a>
                                <a href="#" class="w-10 h-10 bg-black text-white rounded-full flex items-center justify-center hover:bg-gray-800 transition"><i class="fab fa-tiktok"></i></a>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="bg-gradient-to-r from-blue-700 to-cyan-600 rounded-2xl p-8 md:p-10 text-white text-center">
                <i class="fa-regular fa-pen-to-square text-4xl mb-4"></i>
                <h3 class="text-2xl md:text-3xl font-bold mb-3">Ingin Menjadi Contributor?</h3>
                <p class="text-blue-100 mb-6 max-w-lg mx-auto">Bagikan pengalaman perjalanan Anda dan dapatkan kesempatan untuk dipublikasikan di blog kami.</p>
                <a href="#" class="inline-flex items-center gap-2 bg-white text-blue-700 hover:bg-gray-100 px-8 py-3 rounded-full font-semibold transition shadow-lg">
                    <i class="fa-regular fa-pen-to-square"></i> Tulis Artikel
                </a>
            </div>
        </div>
    </section>

    <!-- SCROLL TO TOP -->
    <div class="scroll-top" id="scrollTopBtn">
        <i class="fa-solid fa-arrow-up"></i>
    </div>

    <script>
        // Blog Data
        const blogPosts = [
            { id: 1, title: "10 Destinasi Wisata Terbaik di Bali yang Wajib Dikunjungi", category: "destinasi", subcategory: "Bali", image: "https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=800&q=80", excerpt: "Bali selalu menjadi destinasi favorit wisatawan mancanegara. Yuk eksplor keindahan pulau dewata!", content: "Bali memiliki begitu banyak tempat menarik...", date: "2026-05-20", readTime: 5, views: 15234, author: "Tim BPW", authorAvatar: "https://ui-avatars.com/api/?name=BPW&background=003366&color=fff" },
            { id: 2, title: "Tips Liburan Hemat ke Yogyakarta untuk Keluarga", category: "tips", subcategory: "Yogyakarta", image: "https://images.unsplash.com/photo-1626125353112-98444f6f1943?auto=format&fit=crop&w=800&q=80", excerpt: "Liburan ke Jogja nggak selalu mahal. Simak tips hemat berikut ini!", content: "Yogyakarta terkenal dengan destinasi yang ramah di kantong...", date: "2026-05-18", readTime: 4, views: 10234, author: "Tim BPW" },
            { id: 3, title: "Mengenal Budaya Sasak di Lombok", category: "budaya", subcategory: "Lombok", image: "https://images.unsplash.com/photo-1514282401047-d79a71a590e8?auto=format&fit=crop&w=800&q=80", excerpt: "Budaya Sasak yang unik dan menarik untuk dipelajari saat berkunjung ke Lombok.", content: "Suku Sasak memiliki tradisi yang masih terjaga hingga kini...", date: "2026-05-15", readTime: 6, views: 8234, author: "Tim BPW" },
            { id: 4, title: "5 Kuliner Khas Bandung yang Wajib Dicoba", category: "kuliner", subcategory: "Bandung", image: "https://images.unsplash.com/photo-1594902319089-6fa2316e6d18?auto=format&fit=crop&w=800&q=80", excerpt: "Dari batagor hingga seblak, Bandung punya segudang kuliner lezat!", content: "Bandung terkenal sebagai surga kuliner dengan harga terjangkau...", date: "2026-05-12", readTime: 4, views: 7234, author: "Tim BPW" },
            { id: 5, title: "Menikmati Pesona Bawah Laut Raja Ampat", category: "destinasi", subcategory: "Raja Ampat", image: "https://images.unsplash.com/photo-1544644181-1484b3fdfc62?auto=format&fit=crop&w=800&q=80", excerpt: "Raja Ampat, surga tersembunyi dengan keindahan bawah laut terbaik di dunia.", content: "Keanekaragaman hayati laut di Raja Ampat sungguh luar biasa...", date: "2026-05-10", readTime: 7, views: 12345, author: "Tim BPW" },
            { id: 6, title: "Event Lombok Sumbawa Festival 2026", category: "event", subcategory: "Lombok", image: "https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?auto=format&fit=crop&w=800&q=80", excerpt: "Jangan lewatkan berbagai event seru di Lombok dan Sumbawa tahun ini!", content: "Berbagai festival budaya dan wisata akan digelar...", date: "2026-05-08", readTime: 3, views: 5234, author: "Tim BPW" },
            { id: 7, title: "Cara Packing Efektif untuk Perjalanan 7 Hari", category: "tips", subcategory: "Travel Tips", image: "https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=800&q=80", excerpt: "Tips packing pintar agar bawaan tetap ringan dan rapi selama liburan.", content: "Menyiasati packing agar tidak kelebihan barang...", date: "2026-05-05", readTime: 5, views: 6234, author: "Tim BPW" },
            { id: 8, title: "Upacara Kasada di Gunung Bromo", category: "budaya", subcategory: "Bromo", image: "https://images.unsplash.com/photo-1589308078059-be1415eab4c3?auto=format&fit=crop&w=800&q=80", excerpt: "Tradisi unik masyarakat Tengger dalam upacara Kasada di Bromo.", content: "Upacara Kasada merupakan ritual persembahan untuk Sang Hyang Widhi...", date: "2026-05-03", readTime: 6, views: 4234, author: "Tim BPW" },
            { id: 9, title: "Labuan Bajo: Gerbang Menuju Komodo", category: "destinasi", subcategory: "Labuan Bajo", image: "https://images.unsplash.com/photo-1516690561799-46d8f74f90f6?auto=format&fit=crop&w=800&q=80", excerpt: "Jelajahi keindahan Labuan Bajo dan temui Komodo di habitat aslinya.", content: "Labuan Bajo kini menjadi destinasi favorit para backpacker...", date: "2026-05-01", readTime: 6, views: 9234, author: "Tim BPW" },
            { id: 10, title: "Menikmati Sunrise Terbaik di Penanjakan Bromo", category: "destinasi", subcategory: "Bromo", image: "https://images.unsplash.com/photo-1503333149894-8af88f5a7a47?auto=format&fit=crop&w=800&q=80", excerpt: "Spot terbaik untuk menyaksikan matahari terbit di Bromo.", content: "Penanjakan Bromo menawarkan panorama sunrise yang memukau...", date: "2026-04-28", readTime: 5, views: 11234, author: "Tim BPW" }
        ];

        let currentCategory = "all";
        let searchKeyword = "";
        let visibleCount = 6;

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        function truncateText(text, maxLength) {
            if (text.length <= maxLength) return text;
            return text.substr(0, maxLength) + '...';
        }

        function getFilteredPosts() {
            let filtered = [...blogPosts];
            
            if (currentCategory !== "all") {
                filtered = filtered.filter(post => post.category === currentCategory);
            }
            
            if (searchKeyword) {
                const keyword = searchKeyword.toLowerCase();
                filtered = filtered.filter(post => 
                    post.title.toLowerCase().includes(keyword) || 
                    post.excerpt.toLowerCase().includes(keyword) ||
                    post.subcategory?.toLowerCase().includes(keyword)
                );
            }
            
            return filtered;
        }

        function renderBlogs() {
            const filtered = getFilteredPosts();
            const grid = document.getElementById("blogGrid");
            const featuredContainer = document.getElementById("featuredPost");
            const noResults = document.getElementById("noResults");
            const loadMoreContainer = document.getElementById("loadMoreContainer");
            
            // Featured post (first item on desktop, shown separately)
            if (filtered.length > 0 && window.innerWidth >= 768 && currentCategory === "all" && !searchKeyword) {
                const featured = filtered[0];
                featuredContainer.innerHTML = `
                    <div class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition blog-card mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                            <div class="h-64 md:h-auto overflow-hidden">
                                <img src="${featured.image}" class="w-full h-full object-cover" alt="${featured.title}">
                            </div>
                            <div class="p-6 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
                                        <span class="bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full">${featured.category.toUpperCase()}</span>
                                        <span><i class="fa-regular fa-calendar"></i> ${formatDate(featured.date)}</span>
                                        <span><i class="fa-regular fa-clock"></i> ${featured.readTime} menit</span>
                                    </div>
                                    <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-3 hover:text-blue-600 transition cursor-pointer" onclick="showPostDetail(${featured.id})">${featured.title}</h2>
                                    <p class="text-gray-500 text-sm leading-relaxed mb-4">${truncateText(featured.excerpt, 120)}</p>
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t">
                                    <div class="flex items-center gap-2">
                                        <img src="${featured.authorAvatar || 'https://ui-avatars.com/api/?name=' + featured.author + '&background=003366&color=fff'}" class="w-8 h-8 rounded-full">
                                        <span class="text-xs font-medium text-gray-700">${featured.author}</span>
                                    </div>
                                    <button onclick="showPostDetail(${featured.id})" class="text-blue-600 hover:text-blue-700 text-sm font-semibold flex items-center gap-1">
                                        Baca Selengkapnya <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                const remainingPosts = filtered.slice(1, visibleCount);
                if (remainingPosts.length === 0) {
                    grid.innerHTML = `<div class="col-span-full text-center py-8"><p class="text-gray-400">Tidak ada artikel lain</p></div>`;
                } else {
                    grid.innerHTML = remainingPosts.map(post => `
                        <div class="blog-card bg-white rounded-xl overflow-hidden shadow-md border border-gray-100">
                            <div class="relative h-48 overflow-hidden">
                                <img src="${post.image}" class="w-full h-full object-cover transition duration-300 hover:scale-105" alt="${post.title}" loading="lazy">
                                <span class="absolute top-3 left-3 bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded-full">${post.category.toUpperCase()}</span>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center gap-2 text-[10px] text-gray-400 mb-2">
                                    <span><i class="fa-regular fa-calendar"></i> ${formatDate(post.date)}</span>
                                    <span><i class="fa-regular fa-clock"></i> ${post.readTime} min</span>
                                </div>
                                <h3 class="font-bold text-sm text-gray-800 mb-2 hover:text-blue-600 transition cursor-pointer" onclick="showPostDetail(${post.id})">${post.title}</h3>
                                <p class="text-gray-500 text-[11px] leading-relaxed mb-3">${truncateText(post.excerpt, 80)}</p>
                                <div class="flex items-center justify-between pt-3 border-t">
                                    <div class="flex items-center gap-1.5">
                                        <img src="${post.authorAvatar || 'https://ui-avatars.com/api/?name=' + post.author + '&background=003366&color=fff'}" class="w-5 h-5 rounded-full">
                                        <span class="text-[10px] text-gray-500">${post.author}</span>
                                    </div>
                                    <button onclick="showPostDetail(${post.id})" class="text-blue-600 text-[10px] font-semibold flex items-center gap-1">
                                        Baca <i class="fa-solid fa-arrow-right text-[8px]"></i>
                                    </button>
                                </div>
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
                grid.innerHTML = displayPosts.map(post => `
                    <div class="blog-card bg-white rounded-xl overflow-hidden shadow-md border border-gray-100">
                        <div class="relative h-48 overflow-hidden">
                            <img src="${post.image}" class="w-full h-full object-cover transition duration-300 hover:scale-105" alt="${post.title}" loading="lazy">
                            <span class="absolute top-3 left-3 bg-blue-600 text-white text-[10px] font-bold px-2 py-1 rounded-full">${post.category.toUpperCase()}</span>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center gap-2 text-[10px] text-gray-400 mb-2">
                                <span><i class="fa-regular fa-calendar"></i> ${formatDate(post.date)}</span>
                                <span><i class="fa-regular fa-clock"></i> ${post.readTime} min</span>
                            </div>
                            <h3 class="font-bold text-sm text-gray-800 mb-2 hover:text-blue-600 transition cursor-pointer" onclick="showPostDetail(${post.id})">${post.title}</h3>
                            <p class="text-gray-500 text-[11px] leading-relaxed mb-3">${truncateText(post.excerpt, 80)}</p>
                            <div class="flex items-center justify-between pt-3 border-t">
                                <div class="flex items-center gap-1.5">
                                    <img src="${post.authorAvatar || 'https://ui-avatars.com/api/?name=' + post.author + '&background=003366&color=fff'}" class="w-5 h-5 rounded-full">
                                    <span class="text-[10px] text-gray-500">${post.author}</span>
                                </div>
                                <button onclick="showPostDetail(${post.id})" class="text-blue-600 text-[10px] font-semibold flex items-center gap-1">
                                    Baca <i class="fa-solid fa-arrow-right text-[8px]"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join("");
            }
            
            // Update load more button visibility
            const totalFiltered = filtered.length;
            const displayCount = currentCategory === "all" && !searchKeyword && window.innerWidth >= 768 ? visibleCount : visibleCount;
            if (displayCount >= totalFiltered || totalFiltered === 0) {
                loadMoreContainer.classList.add("hidden");
            } else {
                loadMoreContainer.classList.remove("hidden");
            }
        }

        // Show Post Detail (Modal)
        window.showPostDetail = (id) => {
            const post = blogPosts.find(p => p.id === id);
            if (!post) return;
            
            // Simple alert for demo - in real app, would open modal or new page
            alert(`📖 ${post.title}\n\n${post.excerpt}\n\n${post.content || 'Selengkapnya akan segera kami update. Kunjungi website untuk informasi lebih lanjut.'}\n\n📅 ${formatDate(post.date)} | ⏱️ ${post.readTime} menit membaca`);
            
            // For production, you can redirect to detail page:
            // window.location.href = `blog-detail.php?id=${id}`;
        };

        // Load More
        document.getElementById("loadMoreBtn")?.addEventListener("click", () => {
            visibleCount += 4;
            renderBlogs();
        });

        // Category Filter
        document.querySelectorAll(".category-badge").forEach(btn => {
            btn.addEventListener("click", function() {
                document.querySelectorAll(".category-badge").forEach(b => b.classList.remove("active"));
                this.classList.add("active");
                currentCategory = this.getAttribute("data-cat");
                visibleCount = 6;
                renderBlogs();
            });
        });

        // Search Function
        function handleSearch() {
            const searchInput = document.getElementById("searchInput");
            searchKeyword = searchInput.value.trim();
            visibleCount = 6;
            renderBlogs();
        }
        
        document.getElementById("searchBtn")?.addEventListener("click", handleSearch);
        document.getElementById("searchInput")?.addEventListener("keypress", (e) => {
            if (e.key === "Enter") handleSearch();
        });

        // Render Popular Posts
        function renderPopularPosts() {
            const popular = [...blogPosts].sort((a,b) => b.views - a.views).slice(0, 4);
            const container = document.getElementById("popularPosts");
            if (container) {
                container.innerHTML = popular.map(post => `
                    <div class="popular-item flex gap-3 cursor-pointer p-2 rounded-lg" onclick="showPostDetail(${post.id})">
                        <img src="${post.image}" class="w-16 h-16 rounded-lg object-cover" alt="${post.title}">
                        <div class="flex-1">
                            <h4 class="text-xs font-semibold text-gray-800 hover:text-blue-600 transition line-clamp-2">${post.title}</h4>
                            <div class="flex items-center gap-2 mt-1 text-[10px] text-gray-400">
                                <span><i class="fa-regular fa-eye"></i> ${post.views}</span>
                                <span><i class="fa-regular fa-clock"></i> ${post.readTime} min</span>
                            </div>
                        </div>
                    </div>
                `).join("");
            }
        }

        // Render Categories Widget
        function renderCategoriesWidget() {
            const categories = [
                { name: "Destinasi", count: blogPosts.filter(p => p.category === "destinasi").length, icon: "fa-map-location-dot" },
                { name: "Tips Travel", count: blogPosts.filter(p => p.category === "tips").length, icon: "fa-lightbulb" },
                { name: "Budaya", count: blogPosts.filter(p => p.category === "budaya").length, icon: "fa-gopuram" },
                { name: "Kuliner", count: blogPosts.filter(p => p.category === "kuliner").length, icon: "fa-utensils" },
                { name: "Event", count: blogPosts.filter(p => p.category === "event").length, icon: "fa-calendar" }
            ];
            const container = document.getElementById("categoryList");
            if (container) {
                container.innerHTML = categories.map(cat => `
                    <button data-cat="${cat.name.toLowerCase().replace(' ', '')}" class="category-badge px-3 py-1.5 rounded-full text-[11px] font-semibold transition border border-gray-200 bg-white text-gray-600 hover:bg-gray-100">
                        <i class="fa-solid ${cat.icon} mr-1"></i> ${cat.name} (${cat.count})
                    </button>
                `).join("");
                
                // Add event listeners to widget categories
                document.querySelectorAll("#categoryList .category-badge").forEach(btn => {
                    btn.addEventListener("click", function() {
                        const catValue = this.getAttribute("data-cat");
                        document.querySelectorAll(".category-badge").forEach(b => b.classList.remove("active"));
                        const mainCatBtn = Array.from(document.querySelectorAll(".category-badge")).find(b => b.getAttribute("data-cat") === catValue);
                        if (mainCatBtn) mainCatBtn.classList.add("active");
                        currentCategory = catValue;
                        visibleCount = 6;
                        renderBlogs();
                        window.scrollTo({ top: 400, behavior: "smooth" });
                    });
                });
            }
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

        // Handle window resize for featured post
        let resizeTimeout;
        window.addEventListener("resize", () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => renderBlogs(), 200);
        });

        // Initial render
        renderBlogs();
        renderPopularPosts();
        renderCategoriesWidget();
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

<?php include "layout/footer.php"; ?>
</body>
</html>