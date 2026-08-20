<?php
// layout/footer.php
?>
    <footer class="w-full bg-[#003366] text-white pt-12 md:pt-16 pb-6 md:pb-8 border-t border-slate-700/50">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- ==================== TAMPILAN LAPTOP/DESKTOP (Grid 4 Kolom Original) ==================== -->
            <div class="hidden lg:grid lg:grid-cols-12 gap-8 lg:gap-12 pb-12 border-b border-slate-700/60">
                
                <!-- Kolom 1: Logo & Deskripsi -->
                <div class="lg:col-span-4 space-y-5">
                    <div class="flex items-center">
                        <div class="text-sky-400 font-bold flex items-center pr-3">
                            <svg class="w-8 h-8 transform -rotate-45" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L14 19v-5.5l8 2.5z"/>
                            </svg>
                        </div>
                        <div class="h-6 w-[1px] bg-white/40"></div>
                        <div class="leading-tight pl-3">
                            <span class="text-sm font-bold block tracking-normal">BAYU PRIMA WISATA</span>
                        </div>
                    </div>
                    
                    <p class="text-sm text-slate-300 font-light leading-relaxed max-w-sm">
                        Mitra perjalanan terpercaya Anda untuk menjelajahi keindahan Indonesia dengan pengalaman yang tak terlupakan.
                    </p>
                    
                    <div class="flex items-center space-x-4 pt-2">
                        <a href="https://facebook.com/username-bpw" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-[#1877F2] transition-all duration-300">
                            <i class="fab fa-facebook-f text-lg"></i>
                        </a>
                        <a href="https://instagram.com/username-bpw" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-[#E4405F] transition-all duration-300">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                        <a href="https://tiktok.com/@username-bpw" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-[#000000] transition-all duration-300">
                            <i class="fab fa-tiktok text-lg"></i>
                        </a>
                        <a href="https://wa.me/6281234567890" target="_blank" class="w-10 h-10 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-[#25D366] transition-all duration-300">
                            <i class="fab fa-whatsapp text-lg"></i>
                        </a>
                    </div>
                </div>

                <!-- Kolom 2: Menu -->
                <div class="lg:col-span-2 space-y-4">
                    <h4 class="text-base font-bold tracking-wider">Menu</h4>
                    <ul class="space-y-2.5 text-sm text-slate-300 font-light">
                        <li><a href="index.php" class="hover:text-sky-400 transition">Beranda</a></li>
                        <li><a href="tentang.php" class="hover:text-sky-400 transition">Tentang Kami</a></li>
                        <li><a href="paket-wisata.php" class="hover:text-sky-400 transition">Paket Wisata</a></li>
                        <li><a href="destinasi.php" class="hover:text-sky-400 transition">Destinasi</a></li>
                        <li><a href="layanan.php" class="hover:text-sky-400 transition">Layanan</a></li>
                        <li><a href="hotel.php" class="hover:text-sky-400 transition">Hotel</a></li>
                        <li><a href="promo.php" class="hover:text-sky-400 transition">Promo</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Kontak -->
                <div class="lg:col-span-3 space-y-4">
                    <h4 class="text-base font-bold tracking-wider">Kontak Kami</h4>
                    <ul class="space-y-3.5 text-sm text-slate-300 font-light">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt text-sky-400 mt-1 flex-shrink-0"></i>
                            <span>Jl. Raya Serang, Gembong, Kec. Balaraja, Kabupaten Tangerang, Banten 15610,</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-phone-alt text-sky-400 flex-shrink-0"></i>
                            <span>0812-3456-7890</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-sky-400 flex-shrink-0"></i>
                            <span class="break-all">ayuprimawisata@gmail.com</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-clock text-sky-400 mt-1 flex-shrink-0"></i>
                            <span>Senin - Minggu : 08.00 - 20.00</span>
                        </li>
                    </ul>
                </div>

                <!-- Kolom 4: Newsletter -->
                <div class="lg:col-span-3 space-y-4">
                    <h4 class="text-base font-bold tracking-wider">Newsletter</h4>
                    <p class="text-xs text-slate-300 font-light leading-relaxed">
                        Dapatkan informasi promo & update terbaru dari kami!
                    </p>
                    <form action="#" method="POST" class="flex w-full items-center pt-2">
                        <input type="email" placeholder="Masukkan email Anda" required
                            class="flex-1 bg-[#002244] text-white text-sm font-light rounded-l-lg px-4 py-3 focus:outline-none focus:ring-1 focus:ring-sky-500 border border-slate-700/80 placeholder-slate-400">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-r-lg transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- ==================== TAMPILAN HP/MOBILE (3 Kolom + Newsletter di Bawah) ==================== -->
            <div class="lg:hidden">
                
                <!-- 3 KOLOM UTAMA: Logo+MedSos | Menu | Kontak -->
                <!-- Menggunakan flex row dengan flex-nowrap agar tidak turun ke bawah -->
                <div class="flex flex-row justify-between gap-3">
                    
                    <!-- KOLOM KIRI: Logo + Deskripsi + Medsos (4 icon kesamping) -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center mb-2">
                            <div class="text-sky-400 font-bold flex items-center pr-1.5">
                                <svg class="w-6 h-6 transform -rotate-45" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L14 19v-5.5l8 2.5z"/>
                                </svg>
                            </div>
                            <div class="h-4 w-[1px] bg-white/40"></div>
                            <div class="leading-tight pl-1.5">
                                <span class="text-[9px] font-bold block tracking-normal">BAYU PRIMA<br>WISATA</span>
                            </div>
                        </div>
                        
                        <p class="text-[9px] text-slate-300 font-light leading-relaxed mb-2">
                            Mitra perjalanan terpercaya untuk menjelajahi keindahan Indonesia.
                        </p>
                        
                        <!-- 4 ICON MEDSOS KESAMPING (tidak ada yang turun ke bawah) -->
                        <div class="flex flex-row items-center space-x-2">
                            <a href="#" class="w-6 h-6 flex items-center justify-center rounded-full bg-white/5 hover:bg-[#1877F2] transition">
                                <i class="fab fa-facebook-f text-[9px]"></i>
                            </a>
                            <a href="#" class="w-6 h-6 flex items-center justify-center rounded-full bg-white/5 hover:bg-[#E4405F] transition">
                                <i class="fab fa-instagram text-[9px]"></i>
                            </a>
                            <a href="#" class="w-6 h-6 flex items-center justify-center rounded-full bg-white/5 hover:bg-[#000000] transition">
                                <i class="fab fa-tiktok text-[9px]"></i>
                            </a>
                            <a href="#" class="w-6 h-6 flex items-center justify-center rounded-full bg-white/5 hover:bg-[#25D366] transition">
                                <i class="fab fa-whatsapp text-[9px]"></i>
                            </a>
                        </div>
                    </div>

                    <!-- KOLOM TENGAH: Menu -->
                    <div class="flex-1 min-w-0">
                        <h4 class="text-[11px] font-bold tracking-wider mb-2">Menu</h4>
                        <ul class="space-y-1 text-[9px] text-slate-300 font-light">
                            <li><a href="index.php" class="hover:text-sky-400 transition">Beranda</a></li>
                            <li><a href="tentang.php" class="hover:text-sky-400 transition">Tentang</a></li>
                            <li><a href="paket-wisata.php" class="hover:text-sky-400 transition">Paket</a></li>
                            <li><a href="destinasi.php" class="hover:text-sky-400 transition">Destinasi</a></li>
                            <li><a href="layanan.php" class="hover:text-sky-400 transition">Layanan</a></li>
                        </ul>
                    </div>

                    <!-- KOLOM KANAN: Kontak -->
                    <div class="flex-1 min-w-0">
                        <h4 class="text-[11px] font-bold tracking-wider mb-2">Kontak</h4>
                        <ul class="space-y-1 text-[9px] text-slate-300 font-light">
                            <li class="flex items-start space-x-1">
                                <i class="fas fa-map-marker-alt text-sky-400 text-[8px] mt-0.5 flex-shrink-0"></i>
                                <span class="leading-tight">Jl. Margonda No 88, Depok</span>
                            </li>
                            <li class="flex items-center space-x-1">
                                <i class="fas fa-phone-alt text-sky-400 text-[8px] flex-shrink-0"></i>
                                <span>0812-3456-7890</span>
                            </li>
                            <li class="flex items-center space-x-1">
                                <i class="fas fa-envelope text-sky-400 text-[8px] flex-shrink-0"></i>
                                <span class="break-all text-[8px]">info@bpw.com</span>
                            </li>
                            <li class="flex items-start space-x-1">
                                <i class="fas fa-clock text-sky-400 text-[8px] mt-0.5 flex-shrink-0"></i>
                                <span>08.00-20.00</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- NEWSLETTER - FULL WIDTH DI BAWAH 3 KOLOM -->
                <div class="border-t border-slate-700/60 pt-5 mt-4">
                    <h4 class="text-[11px] font-bold tracking-wider mb-1.5 text-center">Newsletter</h4>
                    <p class="text-[9px] text-slate-300 font-light leading-relaxed mb-2 text-center">
                        Dapatkan informasi promo & update terbaru!
                    </p>
                    <form action="#" method="POST" class="flex w-full">
                        <input type="email" placeholder="Masukkan email Anda" required
                            class="flex-1 bg-[#002244] text-white text-[10px] font-light rounded-l-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-sky-500 border border-slate-700/80 placeholder-slate-400">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-r-lg transition">
                            <i class="fas fa-paper-plane text-[10px]"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- COPYRIGHT (sama untuk semua device) -->
            <div class="flex flex-col sm:flex-row items-center justify-between pt-6 text-[10px] sm:text-xs text-slate-400 font-light space-y-3 sm:space-y-0">
                <div>
                    &copy; 2026 Bayu Prima Wisata (BPW). All Rights Reserved.
                </div>
                <div class="flex space-x-3">
                    <a href="#" class="hover:text-white transition">Syarat & Ketentuan</a>
                    <span>|</span>
                    <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
                </div>
            </div>

        </div>
    </footer>

</body>
</html>