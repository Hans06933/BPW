<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>FAQ - Pertanyaan Umum - Bayu Prima Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .bg-custom-blue { background-color: #003366; }
        .text-custom-blue { color: #003366; }
        
        /* Hero Section */
        .bg-faq-hero {
            background: linear-gradient(135deg, rgba(0,51,102,0.88) 0%, rgba(0,76,153,0.75) 100%), url('https://images.unsplash.com/photo-1557804506-669a67965ba0?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
        }
        
        /* FAQ Category Card */
        .category-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .category-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        }
        
        .category-card.active {
            background-color: #003366;
            border-color: #003366;
        }
        
        .category-card.active h3,
        .category-card.active p,
        .category-card.active i {
            color: white !important;
        }
        
        /* FAQ Item */
        .faq-item {
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        
        .faq-question {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .faq-question:hover {
            background-color: #f8fafc;
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .faq-item.active .faq-answer {
            max-height: 500px;
        }
        
        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }
        
        /* Search Box */
        .search-faq:focus {
            box-shadow: 0 0 0 3px rgba(0,51,102,0.1);
            border-color: #003366;
        }
        
        /* Contact Card */
        .contact-card {
            transition: all 0.3s ease;
        }
        
        .contact-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
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
        
        /* Loading Skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<?php include "layout/header.php"; ?>
<body class="bg-slate-50">

    <!-- HERO SECTION -->
    <section class="bg-faq-hero py-20 md:py-28 text-white relative">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur rounded-full px-4 py-1.5 mb-5">
                    <i class="fa-regular fa-circle-question text-yellow-300 text-sm"></i>
                    <span class="text-xs font-bold tracking-wide">FAQ</span>
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-4">
                    Pertanyaan<br>
                    <span class="text-yellow-300">Umum</span>
                </h1>
                <p class="text-slate-200 text-base max-w-xl leading-relaxed">
                    Temukan jawaban atas pertanyaan yang sering diajukan tentang layanan, pemesanan, dan perjalanan bersama Bayu Prima Wisata.
                </p>
                
                <!-- Search Bar -->
                <div class="mt-8">
                    <div class="bg-white rounded-full p-1.5 flex items-center shadow-lg max-w-md">
                        <div class="flex-1 px-4">
                            <input type="text" id="searchFAQ" placeholder="Cari pertanyaan..." class="w-full py-2 text-sm text-gray-700 focus:outline-none search-faq">
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
    <section class="py-10 md:py-16 bg-slate-50">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            
            <!-- FAQ Categories -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-12">
                <div data-cat="all" class="category-card active bg-white rounded-xl p-4 text-center border border-gray-200 shadow-sm">
                    <i class="fa-solid fa-grid-2 text-blue-600 text-xl mb-2"></i>
                    <h3 class="font-bold text-sm text-gray-800">Semua</h3>
                    <p class="text-[10px] text-gray-400" id="totalCount">0 Pertanyaan</p>
                </div>
                <div data-cat="pemesanan" class="category-card bg-white rounded-xl p-4 text-center border border-gray-200 shadow-sm">
                    <i class="fa-solid fa-calendar-check text-blue-600 text-xl mb-2"></i>
                    <h3 class="font-bold text-sm text-gray-800">Pemesanan</h3>
                    <p class="text-[10px] text-gray-400" id="pemesananCount">0 Pertanyaan</p>
                </div>
                <div data-cat="pembayaran" class="category-card bg-white rounded-xl p-4 text-center border border-gray-200 shadow-sm">
                    <i class="fa-solid fa-credit-card text-blue-600 text-xl mb-2"></i>
                    <h3 class="font-bold text-sm text-gray-800">Pembayaran</h3>
                    <p class="text-[10px] text-gray-400" id="pembayaranCount">0 Pertanyaan</p>
                </div>
                <div data-cat="destinasi" class="category-card bg-white rounded-xl p-4 text-center border border-gray-200 shadow-sm">
                    <i class="fa-solid fa-map-location-dot text-blue-600 text-xl mb-2"></i>
                    <h3 class="font-bold text-sm text-gray-800">Destinasi</h3>
                    <p class="text-[10px] text-gray-400" id="destinasiCount">0 Pertanyaan</p>
                </div>
                <div data-cat="layanan" class="category-card bg-white rounded-xl p-4 text-center border border-gray-200 shadow-sm">
                    <i class="fa-solid fa-headset text-blue-600 text-xl mb-2"></i>
                    <h3 class="font-bold text-sm text-gray-800">Layanan</h3>
                    <p class="text-[10px] text-gray-400" id="layananCount">0 Pertanyaan</p>
                </div>
            </div>
            
            <!-- FAQ Accordion Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- FAQ List - Main Content -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 px-6 py-4 border-b">
                            <h2 class="font-bold text-base text-gray-800 flex items-center gap-2">
                                <i class="fa-regular fa-comments text-blue-600"></i>
                                Pertanyaan yang Sering Diajukan
                            </h2>
                            <p class="text-xs text-gray-500 mt-1" id="resultInfo">Menampilkan semua pertanyaan</p>
                        </div>
                        <div id="faqList" class="divide-y divide-gray-100">
                            <!-- FAQ items will be generated via JS -->
                        </div>
                        <!-- No Results -->
                        <div id="noResults" class="text-center py-12 hidden">
                            <i class="fa-regular fa-face-frown text-5xl text-gray-300 mb-4"></i>
                            <p class="text-gray-400">Tidak ada pertanyaan yang ditemukan. Coba kata kunci lain!</p>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar - Kontak & Bantuan -->
                <div class="lg:col-span-1 space-y-5">
                    <!-- Hubungi Kami Card -->
                    <div class="contact-card bg-white rounded-2xl p-6 text-center border border-gray-100 shadow-sm">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-headset text-blue-600 text-2xl"></i>
                        </div>
                        <h3 class="font-bold text-base text-gray-800 mb-2">Masih Bingung?</h3>
                        <p class="text-xs text-gray-500 mb-4">Tim customer service kami siap membantu Anda</p>
                        <a href="https://wa.me/6281234567890" target="_blank" class="block bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl font-semibold text-sm transition flex items-center justify-center gap-2">
                            <i class="fa-brands fa-whatsapp"></i> Chat via WhatsApp
                        </a>
                    </div>
                    
                    <!-- Quick Info -->
                    <div class="bg-gradient-to-r from-blue-700 to-cyan-600 rounded-2xl p-6 text-white text-center">
                        <i class="fa-regular fa-clock text-3xl mb-3"></i>
                        <h3 class="font-bold text-base mb-2">Jam Operasional</h3>
                        <p class="text-sm">Senin - Minggu</p>
                        <p class="text-lg font-bold">08.00 - 20.00 WIB</p>
                        <div class="mt-4 pt-4 border-t border-white/20">
                            <p class="text-xs text-blue-100">📞 0812-3456-7890</p>
                            <p class="text-xs text-blue-100 mt-1">✉️ info@bayuprimawisata.com</p>
                        </div>
                    </div>
                    
                    <!-- Download Brochure -->
                    <div class="bg-white rounded-2xl p-6 text-center border border-gray-100 shadow-sm">
                        <i class="fa-regular fa-file-pdf text-red-500 text-3xl mb-3"></i>
                        <h3 class="font-bold text-sm text-gray-800 mb-2">Download Brochure</h3>
                        <p class="text-[11px] text-gray-500 mb-3">Dapatkan informasi lengkap tentang paket wisata kami</p>
                        <button class="w-full border border-red-500 text-red-500 hover:bg-red-50 py-2 rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-download"></i> Download PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 md:px-6 max-w-6xl">
            <div class="bg-gradient-to-r from-blue-700 to-cyan-600 rounded-2xl p-8 md:p-10 text-white text-center">
                <i class="fa-regular fa-message text-4xl mb-4"></i>
                <h3 class="text-2xl md:text-3xl font-bold mb-3">Pertanyaan Anda Belum Terjawab?</h3>
                <p class="text-blue-100 mb-6 max-w-lg mx-auto">Hubungi tim customer service kami, kami akan dengan senang hati membantu Anda.</p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full font-semibold transition shadow-lg">
                        <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
                    </a>
                    <a href="#" class="inline-flex items-center gap-2 bg-white text-blue-700 hover:bg-gray-100 px-6 py-3 rounded-full font-semibold transition shadow-lg">
                        <i class="fa-regular fa-envelope"></i> Email Kami
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
        // FAQ Data
        const faqData = [
            // Pemesanan
            { id: 1, category: "pemesanan", question: "Bagaimana cara memesan paket wisata di BPW?", answer: "Anda dapat memesan paket wisata melalui website kami dengan mengisi form pemesanan, atau menghubungi customer service kami via WhatsApp di 0812-3456-7890. Tim kami akan membantu proses pemesanan Anda dengan cepat dan mudah.", icon: "fa-calendar-check" },
            { id: 2, category: "pemesanan", question: "Apakah ada minimal pemesanan untuk paket wisata?", answer: "Minimal pemesanan bervariasi tergantung paket yang dipilih. Untuk paket reguler umumnya minimal 2 orang dewasa. Namun kami juga menyediakan paket private tour untuk 1 orang atau kelompok besar. Silakan konsultasikan dengan tim kami.", icon: "fa-calendar-check" },
            { id: 3, category: "pemesanan", question: "Berapa lama proses konfirmasi pemesanan?", answer: "Setelah Anda melakukan pemesanan dan pembayaran, konfirmasi akan kami kirimkan dalam waktu maksimal 2x24 jam melalui email atau WhatsApp. Untuk pemesanan H-7, konfirmasi akan lebih cepat.", icon: "fa-calendar-check" },
            { id: 4, category: "pemesanan", question: "Apakah bisa memesan untuk rombongan besar (study tour/gathering)?", answer: "Tentu saja! BPW memiliki layanan khusus untuk rombongan besar seperti study tour, gathering perusahaan, atau wisata keluarga besar. Tim kami akan membantu merancang paket yang sesuai dengan kebutuhan Anda.", icon: "fa-calendar-check" },
            
            // Pembayaran
            { id: 5, category: "pembayaran", question: "Metode pembayaran apa saja yang tersedia?", answer: "Kami menerima pembayaran melalui transfer bank (BCA, Mandiri, BNI, BRI), kartu kredit, dan pembayaran via QRIS (DANA, OVO, GoPay, LinkAja).", icon: "fa-credit-card" },
            { id: 6, category: "pembayaran", question: "Apakah ada sistem DP atau cicilan?", answer: "Ya, untuk pemesanan paket wisata Anda dapat membayar DP sebesar 50% dari total harga. Pelunasan dapat dilakukan maksimal H-7 sebelum keberangkatan. Untuk cicilan tidak tersedia.", icon: "fa-credit-card" },
            { id: 7, category: "pembayaran", question: "Bagaimana jika terjadi pembatalan pemesanan?", answer: "Kebijakan pembatalan: Pembatalan H-30: refund 75%, H-14: refund 50%, H-7: refund 25%, H-3: tidak ada refund. Untuk detail lengkap silakan lihat Syarat & Ketentuan.", icon: "fa-credit-card" },
            { id: 8, category: "pembayaran", question: "Apakah harga sudah termasuk pajak dan biaya lainnya?", answer: "Ya, semua harga yang tertera sudah termasuk pajak (PPN) dan biaya layanan. Namun untuk tiket pesawat dan beberapa tiket masuk destinasi tertentu mungkin ada biaya tambahan.", icon: "fa-credit-card" },
            
            // Destinasi
            { id: 9, category: "destinasi", question: "Destinasi apa saja yang tersedia di BPW?", answer: "Kami menyediakan paket wisata ke berbagai destinasi di Indonesia seperti Bali, Yogyakarta, Labuan Bajo, Raja Ampat, Bromo, Lombok, Bandung, dan masih banyak lagi. Cek halaman Destinasi untuk informasi lengkap.", icon: "fa-map-location-dot" },
            { id: 10, category: "destinasi", question: "Apakah BPW menyediakan paket wisata luar negeri?", answer: "Saat ini BPW fokus pada paket wisata domestik di seluruh Indonesia. Untuk paket luar negeri, kami sedang dalam pengembangan. Ikuti terus update kami!", icon: "fa-map-location-dot" },
            { id: 11, category: "destinasi", question: "Bisakah saya request destinasi yang tidak ada dalam paket?", answer: "Tentu! BPW menerima custom tour sesuai keinginan Anda. Silakan konsultasikan dengan tim kami tentang destinasi impian Anda, kami akan bantu merancang perjalanan yang sempurna.", icon: "fa-map-location-dot" },
            
            // Layanan
            { id: 12, category: "layanan", question: "Apa saja yang termasuk dalam paket wisata BPW?", answer: "Paket wisata BPW umumnya sudah termasuk: transportasi (selama tur), akomodasi hotel, tour guide profesional, tiket wisata sesuai itinerary, dan beberapa kali makan. Detail lengkap ada di halaman setiap paket.", icon: "fa-headset" },
            { id: 13, category: "layanan", question: "Apakah BPW menyediakan tour guide yang bisa berbahasa asing?", answer: "Ya, kami memiliki tour guide yang menguasai bahasa Inggris. Untuk bahasa asing lainnya seperti Mandarin atau Jepang, dapat dipesan dengan konfirmasi minimal 14 hari sebelumnya.", icon: "fa-headset" },
            { id: 14, category: "layanan", question: "Apakah BPW memiliki layanan asuransi perjalanan?", answer: "Ya, BPW bekerjasama dengan perusahaan asuransi terpercaya untuk memberikan perlindungan perjalanan. Asuransi dapat ditambahkan dengan biaya tambahan sesuai ketentuan.", icon: "fa-headset" },
            { id: 15, category: "layanan", question: "Bagaimana jika ada masalah selama perjalanan?", answer: "Tim BPW menyediakan layanan darurat 24 jam. Anda dapat menghubungi nomer hotline yang akan diberikan saat keberangkatan. Kami akan segera membantu menyelesaikan masalah Anda.", icon: "fa-headset" }
        ];

        let currentCategory = "all";
        let searchKeyword = "";

        // Count questions per category
        function updateCounts() {
            const total = faqData.length;
            const pemesanan = faqData.filter(f => f.category === "pemesanan").length;
            const pembayaran = faqData.filter(f => f.category === "pembayaran").length;
            const destinasi = faqData.filter(f => f.category === "destinasi").length;
            const layanan = faqData.filter(f => f.category === "layanan").length;
            
            document.getElementById("totalCount").innerHTML = `${total} Pertanyaan`;
            document.getElementById("pemesananCount").innerHTML = `${pemesanan} Pertanyaan`;
            document.getElementById("pembayaranCount").innerHTML = `${pembayaran} Pertanyaan`;
            document.getElementById("destinasiCount").innerHTML = `${destinasi} Pertanyaan`;
            document.getElementById("layananCount").innerHTML = `${layanan} Pertanyaan`;
        }

        function getFilteredFaq() {
            let filtered = [...faqData];
            
            if (currentCategory !== "all") {
                filtered = filtered.filter(f => f.category === currentCategory);
            }
            
            if (searchKeyword) {
                const keyword = searchKeyword.toLowerCase();
                filtered = filtered.filter(f => 
                    f.question.toLowerCase().includes(keyword) || 
                    f.answer.toLowerCase().includes(keyword)
                );
            }
            
            return filtered;
        }

        function renderFaq() {
            const filtered = getFilteredFaq();
            const container = document.getElementById("faqList");
            const noResults = document.getElementById("noResults");
            const resultInfo = document.getElementById("resultInfo");
            
            if (resultInfo) {
                if (searchKeyword) {
                    resultInfo.innerHTML = `Hasil pencarian: ${filtered.length} pertanyaan ditemukan untuk "${searchKeyword}"`;
                } else {
                    resultInfo.innerHTML = `Menampilkan ${filtered.length} pertanyaan dari kategori ${currentCategory === "all" ? "semua" : currentCategory}`;
                }
            }
            
            if (filtered.length === 0) {
                container.innerHTML = '';
                noResults.classList.remove("hidden");
                return;
            }
            
            noResults.classList.add("hidden");
            
            container.innerHTML = filtered.map((faq, index) => `
                <div class="faq-item ${index === 0 ? 'active' : ''}" data-id="${faq.id}">
                    <div class="faq-question flex justify-between items-center p-5 cursor-pointer hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid ${faq.icon} text-blue-500 text-sm"></i>
                            <h3 class="font-semibold text-sm text-gray-800 pr-4">${faq.question}</h3>
                        </div>
                        <i class="fa-solid fa-chevron-down faq-icon text-gray-400 text-xs transition-transform duration-300"></i>
                    </div>
                    <div class="faq-answer">
                        <div class="p-5 pt-0 pl-12 pr-5">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-gray-600 text-sm leading-relaxed">${faq.answer}</p>
                                ${faq.category === "pemesanan" ? `
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <a href="https://wa.me/6281234567890" class="text-blue-600 text-xs font-semibold hover:underline inline-flex items-center gap-1">
                                        <i class="fa-brands fa-whatsapp"></i> Hubungi kami untuk pemesanan
                                    </a>
                                </div>
                                ` : ''}
                                ${faq.category === "pembayaran" ? `
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <span class="text-gray-400 text-[10px]">*Syarat dan ketentuan berlaku</span>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `).join("");
            
            // Add event listeners to FAQ items
            document.querySelectorAll(".faq-item").forEach(item => {
                const question = item.querySelector(".faq-question");
                question.addEventListener("click", () => {
                    // Close other items
                    document.querySelectorAll(".faq-item").forEach(other => {
                        if (other !== item && other.classList.contains("active")) {
                            other.classList.remove("active");
                        }
                    });
                    item.classList.toggle("active");
                });
            });
        }

        // Category Filter
        document.querySelectorAll(".category-card").forEach(card => {
            card.addEventListener("click", function() {
                document.querySelectorAll(".category-card").forEach(c => c.classList.remove("active"));
                this.classList.add("active");
                currentCategory = this.getAttribute("data-cat");
                searchKeyword = "";
                document.getElementById("searchFAQ").value = "";
                renderFaq();
            });
        });

        // Search Function
        function handleSearch() {
            const searchInput = document.getElementById("searchFAQ");
            searchKeyword = searchInput.value.trim();
            currentCategory = "all";
            document.querySelectorAll(".category-card").forEach(c => c.classList.remove("active"));
            document.querySelector(".category-card[data-cat='all']").classList.add("active");
            renderFaq();
        }
        
        document.getElementById("searchBtn")?.addEventListener("click", handleSearch);
        document.getElementById("searchFAQ")?.addEventListener("keypress", (e) => {
            if (e.key === "Enter") handleSearch();
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

        // Initial render
        updateCounts();
        renderFaq();
    </script>

    <style>
        /* Additional styles for FAQ */
        .faq-icon {
            transition: transform 0.3s ease;
        }
        
        .faq-question {
            transition: background-color 0.2s ease;
        }
        
        .category-card {
            transition: all 0.2s ease;
        }
    </style>

<?php include "layout/footer.php"; ?>
</body>
</html>