<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Bayu Prima Wisata (BPW) - Your Journey, Our Priority</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom font agar mirip dengan desain */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        /* Optimasi touch target untuk mobile */
        @media (max-width: 768px) {
            button, a, .nav-link {
                cursor: pointer;
                touch-action: manipulation;
            }
        }
        
        /* Animasi smooth untuk mobile menu */
        #mobile-menu {
            transition: all 0.3s ease-in-out;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
        }
        
        #mobile-menu:not(.hidden) {
            max-height: 600px;
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-50 text-slate-800">

    <header class="w-full bg-[#003366] text-white sticky top-0 z-50 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">
                
                <div class="flex items-center flex-shrink-0">
                    <div class="text-sky-400 font-bold flex items-center pr-3 md:pr-4">
                        <svg class="w-8 h-8 md:w-10 md:h-10" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2c-4.42 0-8 3.58-8 8v8c0 1.1.9 2 2 2h1v4c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-4h4v4c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-4h1c1.1 0 2-.9 2-2v-8c0-4.42-3.58-8-8-8zm-5 16c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm10 0c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm1-5H6V7h12v6z"/>
                        </svg>
                    </div>
                    
                    <div class="h-8 md:h-10 w-[1px] bg-white/40"></div>
                    
                    <div class="leading-tight pl-3 md:pl-4">
                        <span class="text-sm md:text-base font-bold block tracking-normal">BAYU PRIMA WISATA</span>
                        <span class="text-[10px] md:text-[12px] font-light text-slate-300 block tracking-wide">Your Journey, Our Priority</span>
                    </div>
                </div>

                <!-- Desktop Navigation - Hidden on mobile, visible on large screens -->
                <nav class="hidden lg:flex items-center space-x-3 xl:space-x-5 text-xs xl:text-sm font-medium">
                    <a href="index.php" class="nav-link py-2 px-1 transition duration-300 whitespace-nowrap <?= ($current_page == 'index.php' || $current_page == '') ? 'text-sky-400 border-b-2 border-sky-400 font-semibold' : 'text-white hover:text-sky-300' ?>">Beranda</a>
                    
                    <a href="tentang.php" class="nav-link py-2 px-1 transition duration-300 whitespace-nowrap <?= ($current_page == 'tentang.php') ? 'text-sky-400 border-b-2 border-sky-400 font-semibold' : 'text-white hover:text-sky-300' ?>">Tentang Kami</a>

                    <a href="paket_wisata.php" class="nav-link py-2 px-1 transition duration-300 whitespace-nowrap <?= ($current_page == 'paket_wisata.php') ? 'text-sky-400 border-b-2 border-sky-400 font-semibold' : 'text-white hover:text-sky-300' ?>">Paket Wisata</a>
                    
                    <a href="destinasi.php" class="nav-link py-2 px-1 transition duration-300 whitespace-nowrap <?= ($current_page == 'destinasi.php') ? 'text-sky-400 border-b-2 border-sky-400 font-semibold' : 'text-white hover:text-sky-300' ?>">Destinasi</a>
                    
                    <a href="hotel.php" class="nav-link py-2 px-1 transition duration-300 whitespace-nowrap <?= ($current_page == 'hotel.php') ? 'text-sky-400 border-b-2 border-sky-400 font-semibold' : 'text-white hover:text-sky-300' ?>">Hotel</a>
                    
                    <div class="relative group">
                        <a href="#" class="nav-link py-2 px-1 transition duration-300 text-white hover:text-sky-300 flex items-center space-x-1 whitespace-nowrap">
                            <span>Lainnya</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </a>
                        <div class="absolute left-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50">
                            <a href="layanan.php" class="block px-4 py-2 hover:bg-gray-100 rounded-t-lg text-sm">Layanan</a>
                            <a href="promo.php" class="block px-4 py-2 hover:bg-gray-100 text-sm">Promo</a>
                            <a href="galeri.php" class="block px-4 py-2 hover:bg-gray-100 text-sm">Galeri</a>
                            <a href="blog.php" class="block px-4 py-2 hover:bg-gray-100 text-sm">Blog</a>
                            <a href="testimoni.php" class="block px-4 py-2 hover:bg-gray-100 text-sm">Testimoni</a>
                            <a href="faq.php" class="block px-4 py-2 hover:bg-gray-100 rounded-b-lg text-sm">FAQ</a>
                            <a href="kontak.php" class="block px-4 py-2 hover:bg-gray-100 rounded-b-lg text-sm">Kontak</a>
                        </div>
                    </div>
                </nav>

                <!-- Contact Button - Responsive -->
                <div class="hidden md:block">
                    <a href="https://wa.me/6285281441565?text=Halo%20Admin,%20saya%20ingin%20memulai%20percakapan%20dengan%20chatbot." target="_blank" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white text-xs md:text-sm font-semibold py-2 md:py-2.5 px-3 md:px-5 rounded-full transition duration-300 shadow-lg">
                        <svg class="w-3 h-3 md:w-4 md:h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 15.5c-1.25 0-2.45-.2-3.57-.57a1.02 1.02 0 00-1.02.24l-2.2 2.2a15.04 15.04 0 01-6.59-6.59l2.2-2.21a1 1 0 00.25-1A11.36 11.36 0 018.5 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.5c0-.55-.45-1-1-1z"/>
                        </svg>
                        <span class="hidden sm:inline">Tanya Chatbot Kami</span>
                        <span class="sm:hidden">Chatbot</span>
                    </a>
                </div>

                <!-- Mobile Menu Button - Visible on tablet and mobile -->
                <div class="lg:hidden flex items-center space-x-3">
                    <!-- Mobile Contact Button -->
                    <a href="https://wa.me/6285281441565" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full transition shadow-lg">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 15.5c-1.25 0-2.45-.2-3.57-.57a1.02 1.02 0 00-1.02.24l-2.2 2.2a15.04 15.04 0 01-6.59-6.59l2.2-2.21a1 1 0 00.25-1A11.36 11.36 0 018.5 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.5c0-.55-.45-1-1-1z"/>
                        </svg>
                    </a>
                    
                    <button id="mobile-menu-button" class="text-white hover:text-sky-300 focus:outline-none p-2">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Menu - Enhanced for better UX -->
        <div id="mobile-menu" class="hidden lg:hidden bg-[#002244] border-t border-slate-700 shadow-xl">
            <div class="px-3 py-3 space-y-1 text-sm">
                <a href="index.php" class="block px-3 py-3 rounded-lg hover:bg-blue-900 transition duration-200 <?= ($current_page == 'index.php' || $current_page == '') ? 'bg-blue-900 text-sky-400 font-semibold' : '' ?>">
                    <i class="fas fa-home mr-3 text-sky-400"></i> Beranda
                </a>
                <a href="tentang.php" class="block px-3 py-3 rounded-lg hover:bg-blue-900 transition duration-200 <?= ($current_page == 'tentang.php') ? 'bg-blue-900 text-sky-400 font-semibold' : '' ?>">
                    <i class="fas fa-info-circle mr-3 text-sky-400"></i> Tentang Kami
                </a>
                <a href="paket-wisata.php" class="block px-3 py-3 rounded-lg hover:bg-blue-900 transition duration-200 <?= ($current_page == 'paket-wisata.php') ? 'bg-blue-900 text-sky-400 font-semibold' : '' ?>">
                    <i class="fas fa-box mr-3 text-sky-400"></i> Paket Wisata
                </a>
                <a href="destinasi.php" class="block px-3 py-3 rounded-lg hover:bg-blue-900 transition duration-200 <?= ($current_page == 'destinasi.php') ? 'bg-blue-900 text-sky-400 font-semibold' : '' ?>">
                    <i class="fas fa-map-marker-alt mr-3 text-sky-400"></i> Destinasi
                </a>
                
                <!-- Collapsible submenu for mobile -->
                <div class="space-y-1">
                    <button id="mobile-submenu-btn" class="w-full text-left px-3 py-3 rounded-lg hover:bg-blue-900 transition duration-200 flex items-center justify-between">
                        <span><i class="fas fa-ellipsis-h mr-3 text-sky-400"></i> Layanan Lainnya</span>
                        <svg id="submenu-icon" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="mobile-submenu" class="hidden ml-6 space-y-1">
                        <a href="layanan.php" class="block px-3 py-2 rounded-lg hover:bg-blue-900 transition duration-200 text-sm">Layanan</a>
                        <a href="transportasi.php" class="block px-3 py-2 rounded-lg hover:bg-blue-900 transition duration-200 text-sm">Transportasi</a>
                        <a href="hotel.php" class="block px-3 py-2 rounded-lg hover:bg-blue-900 transition duration-200 text-sm">Hotel</a>
                        <a href="promo.php" class="block px-3 py-2 rounded-lg hover:bg-blue-900 transition duration-200 text-sm">Promo</a>
                        <a href="galeri.php" class="block px-3 py-2 rounded-lg hover:bg-blue-900 transition duration-200 text-sm">Galeri</a>
                        <a href="blog.php" class="block px-3 py-2 rounded-lg hover:bg-blue-900 transition duration-200 text-sm">Blog</a>
                        <a href="testimoni.php" class="block px-3 py-2 rounded-lg hover:bg-blue-900 transition duration-200 text-sm">Testimoni</a>
                        <a href="faq.php" class="block px-3 py-2 rounded-lg hover:bg-blue-900 transition duration-200 text-sm">FAQ</a>
                    </div>
                </div>
                
                <!-- Mobile WA Button -->
                <a href="https://wa.me/6281234567890" target="_blank" class="block w-full text-center bg-gradient-to-r from-green-500 to-green-600 text-white mt-4 py-3 rounded-lg font-semibold hover:from-green-600 hover:to-green-700 transition duration-200 shadow-lg">
                    <i class="fab fa-whatsapp mr-2"></i> Hubungi via WhatsApp
                </a>
            </div>
        </div>
    </header>

    <script>
        // Mobile menu toggle with smooth animation
        const menuBtn = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                // Change icon when menu is open (optional)
                const icon = menuBtn.querySelector('svg');
                if (!mobileMenu.classList.contains('hidden')) {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
                } else {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                }
            });
        }
        
        // Mobile submenu toggle
        const submenuBtn = document.getElementById('mobile-submenu-btn');
        const submenu = document.getElementById('mobile-submenu');
        const submenuIcon = document.getElementById('submenu-icon');
        
        if (submenuBtn && submenu) {
            submenuBtn.addEventListener('click', () => {
                submenu.classList.toggle('hidden');
                submenuIcon.classList.toggle('rotate-180');
            });
        }
        
        // Close mobile menu when clicking outside (optional)
        document.addEventListener('click', function(event) {
            const isClickInside = menuBtn?.contains(event.target) || mobileMenu?.contains(event.target);
            if (!isClickInside && mobileMenu && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                const icon = menuBtn?.querySelector('svg');
                if (icon) {
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
                }
            }
        });
        
        // Handle window resize - close mobile menu when switching to desktop view
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) { // lg breakpoint
                if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });
    </script>