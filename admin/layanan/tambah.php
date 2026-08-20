<?php
require_once "../../config/database.php";

include "../../layout/admin_header.php";
?>

<div class="p-4 md:p-6">

    <div class="max-w-3xl mx-auto">

        <div class="mb-6">

            <a href="../layanan.php"
               class="text-sm text-blue-600 hover:text-blue-700 inline-flex items-center gap-2">

                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Kelola Layanan

            </a>

            <h1 class="text-2xl font-bold text-slate-800 mt-4">
                Tambah Layanan
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Tambahkan layanan baru ke website.
            </p>

        </div>


        <form
            action="simpan.php"
            method="POST"
            enctype="multipart/form-data"
            class="bg-white border rounded-xl shadow-sm p-5 md:p-6"
        >

            <div class="space-y-5">


                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Layanan
                    </label>

                    <input
                        type="text"
                        name="nama_layanan"
                        required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: Paket Wisata"
                    >

                </div>


                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>

                    <textarea
                        name="deskripsi"
                        rows="5"
                        required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan deskripsi layanan..."
                    ></textarea>

                </div>


                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Gambar Layanan
                    </label>

                    <input
                        type="file"
                        name="gambar"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm"
                    >

                    <p class="text-xs text-gray-400 mt-2">
                        Format JPG, JPEG, PNG atau WEBP.
                    </p>

                </div>


                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >

                        <option value="Aktif">
                            Aktif
                        </option>

                        <option value="Nonaktif">
                            Nonaktif
                        </option>

                    </select>

                </div>


                <div class="flex justify-end gap-3 pt-4 border-t">

                    <a
                        href="../layanan.php"
                        class="px-5 py-2.5 rounded-lg border text-sm font-semibold text-gray-600 hover:bg-gray-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold"
                    >

                        <i class="fa-solid fa-save mr-1"></i>
                        Simpan

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
