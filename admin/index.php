<?php
// admin/index.php
require_once '../layout/admin_header.php';
?>

<!-- Statistik Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="card-stats bg-white rounded-xl p-5 shadow-sm border-l-4 border-blue-600">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-400 text-sm">Paket Wisata</p>
                <p class="text-2xl font-bold text-gray-800" id="statPaket">24</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-suitcase text-blue-600"></i>
            </div>
        </div>
    </div>
    <div class="card-stats bg-white rounded-xl p-5 shadow-sm border-l-4 border-cyan-600">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-400 text-sm">Destinasi</p>
                <p class="text-2xl font-bold text-gray-800" id="statDestinasi">35</p>
            </div>
            <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-map-location-dot text-cyan-600"></i>
            </div>
        </div>
    </div>
    <div class="card-stats bg-white rounded-xl p-5 shadow-sm border-l-4 border-green-600">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-400 text-sm">Testimoni</p>
                <p class="text-2xl font-bold text-gray-800" id="statTestimoni">128</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-star text-green-600"></i>
            </div>
        </div>
    </div>
    <div class="card-stats bg-white rounded-xl p-5 shadow-sm border-l-4 border-yellow-600">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-400 text-sm">Pengunjung</p>
                <p class="text-2xl font-bold text-gray-800">12.345</p>
            </div>
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-eye text-yellow-600"></i>
            </div>
        </div>
    </div>
    <div class="card-stats bg-white rounded-xl p-5 shadow-sm border-l-4 border-purple-600">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-400 text-sm">Rating</p>
                <p class="text-2xl font-bold text-gray-800">4.9/5</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fa-solid fa-ranking-star text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities & Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Data Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-semibold text-gray-800">Paket Wisata Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600">Nama Paket</th>
                        <th class="px-4 py-3 text-left text-gray-600">Destinasi</th>
                        <th class="px-4 py-3 text-left text-gray-600">Harga</th>
                        <th class="px-4 py-3 text-left text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="px-4 py-3">Bali 3 Hari 2 Malam</td>
                        <td class="px-4 py-3">Bali</td>
                        <td class="px-4 py-3">Rp 2.150.000</td>
                        <td class="px-4 py-3"><span class="bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs">Aktif</span></td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-3">Yogyakarta 3 Hari 2 Malam</td>
                        <td class="px-4 py-3">Yogyakarta</td>
                        <td class="px-4 py-3">Rp 1.850.000</td>
                        <td class="px-4 py-3"><span class="bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs">Aktif</span></td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-3">Labuan Bajo 4 Hari 3 Malam</td>
                        <td class="px-4 py-3">Labuan Bajo</td>
                        <td class="px-4 py-3">Rp 4.750.000</td>
                        <td class="px-4 py-3"><span class="bg-green-100 text-green-600 px-2 py-1 rounded-full text-xs">Aktif</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t bg-gray-50">
            <a href="paket.php" class="text-blue-600 text-sm hover:underline">Kelola Paket Wisata →</a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-semibold text-gray-800">Aksi Cepat</h3>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4">
            <a href="paket.php?action=tambah" class="bg-blue-50 hover:bg-blue-100 p-4 rounded-xl text-center transition">
                <i class="fa-solid fa-plus text-blue-600 text-xl mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Tambah Paket</p>
            </a>
            <a href="destinasi.php?action=tambah" class="bg-cyan-50 hover:bg-cyan-100 p-4 rounded-xl text-center transition">
                <i class="fa-solid fa-location-dot text-cyan-600 text-xl mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Tambah Destinasi</p>
            </a>
            <a href="testimoni.php?action=tambah" class="bg-green-50 hover:bg-green-100 p-4 rounded-xl text-center transition">
                <i class="fa-solid fa-star text-green-600 text-xl mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Tambah Testimoni</p>
            </a>
            <a href="galeri.php?action=tambah" class="bg-purple-50 hover:bg-purple-100 p-4 rounded-xl text-center transition">
                <i class="fa-solid fa-image text-purple-600 text-xl mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Tambah Galeri</p>
            </a>
            <a href="promo.php?action=tambah" class="bg-red-50 hover:bg-red-100 p-4 rounded-xl text-center transition">
                <i class="fa-solid fa-tag text-red-600 text-xl mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Tambah Promo</p>
            </a>
            <a href="blog.php?action=tambah" class="bg-orange-50 hover:bg-orange-100 p-4 rounded-xl text-center transition">
                <i class="fa-regular fa-newspaper text-orange-600 text-xl mb-2"></i>
                <p class="text-sm font-medium text-gray-700">Tambah Artikel</p>
            </a>
        </div>
    </div>
</div>

<!-- Recent Testimonials -->
<div class="mt-6 bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b bg-gray-50">
        <h3 class="font-semibold text-gray-800">Testimoni Terbaru</h3>
    </div>
    <div class="divide-y">
        <div class="p-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="font-bold text-blue-600">SR</span>
            </div>
            <div class="flex-1">
                <p class="font-medium text-gray-800">Muhammad bayu Tamam Basyari</p>
                <p class="text-sm text-gray-500">"Pelayanan BPW sangat memuaskan! Tour guide ramah dan itinerary terencana..."</p>
            </div>
            <div class="text-yellow-400 text-sm">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
        </div>
        <div class="p-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="font-bold text-blue-600">AP</span>
            </div>
            <div class="flex-1">
                <p class="font-medium text-gray-800">Andi Pratama</p>
                <p class="text-sm text-gray-500">"Liburan ke Labuan Bajo bersama BPW sangat berkesan, semua terorganisir dengan baik!"</p>
            </div>
            <div class="text-yellow-400 text-sm">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
        </div>
    </div>
</div>

