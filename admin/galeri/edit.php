<?php

require_once "../../config/database.php";


$id = (int)($_GET['id'] ?? 0);


if ($id <= 0) {

    header("Location: ../galeri.php");
    exit;

}


try {

    $galeri = db_get(
        "SELECT * FROM galeri WHERE id = :id LIMIT 1",
        [
            'id' => $id
        ]
    );


    if (!$galeri) {

        header("Location: ../galeri.php?status=notfound");
        exit;

    }


} catch (PDOException $e) {

    die(
        "Gagal mengambil data: " .
        htmlspecialchars($e->getMessage())
    );

}


include "../../layout/admin_header.php";

?>


<div class="p-4 md:p-6">

    <div class="max-w-4xl mx-auto">

        <!-- HEADER -->

        <div class="mb-6">

            <div class="flex items-center gap-3 mb-2">

                <a
                    href="../galeri.php"
                    class="w-9 h-9 bg-slate-100 hover:bg-slate-200 rounded-lg flex items-center justify-center text-slate-600"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                </a>

                <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                    Edit Galeri
                </h1>

            </div>

            <p class="text-sm text-slate-500 ml-12">
                Perbarui informasi galeri.
            </p>

        </div>


        <!-- FORM -->

        <form
            action="update.php"
            method="POST"
            enctype="multipart/form-data"
            class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 md:p-6"
        >

            <input
                type="hidden"
                name="id"
                value="<?= (int)$galeri['id'] ?>"
            >


            <!-- JUDUL -->

            <div class="mb-5">

                <label class="block text-sm font-semibold text-slate-700 mb-2">

                    Judul Galeri

                    <span class="text-red-500">*</span>

                </label>

                <input
                    type="text"
                    name="judul"
                    required
                    maxlength="200"
                    value="<?= htmlspecialchars($galeri['judul']) ?>"
                    class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >

            </div>


            <!-- KATEGORI -->

            <div class="mb-5">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Kategori
                </label>

                <input
                    type="text"
                    name="kategori"
                    maxlength="100"
                    value="<?= htmlspecialchars($galeri['kategori'] ?? '') ?>"
                    class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >

            </div>


            <!-- GAMBAR LAMA -->

            <div class="mb-5">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Gambar Saat Ini
                </label>

                <?php if (!empty($galeri['gambar'])): ?>

                    <img
                        src="../../<?= htmlspecialchars(ltrim($galeri['gambar'], '/')) ?>"
                        alt="<?= htmlspecialchars($galeri['judul']) ?>"
                        class="w-full max-w-md h-56 object-cover rounded-lg border border-slate-200"
                        onerror="this.style.display='none'; document.getElementById('imageError').classList.remove('hidden');"
                    >

                    <div
                        id="imageError"
                        class="hidden w-full max-w-md h-56 bg-slate-100 rounded-lg items-center justify-center text-slate-400"
                    >

                        <div class="text-center">

                            <i class="fa-solid fa-image text-3xl mb-2"></i>

                            <p class="text-sm">
                                Gambar tidak ditemukan
                            </p>

                        </div>

                    </div>

                <?php else: ?>

                    <div class="w-full max-w-md h-56 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">

                        <div class="text-center">

                            <i class="fa-solid fa-image text-3xl mb-2"></i>

                            <p class="text-sm">
                                Belum ada gambar
                            </p>

                        </div>

                    </div>

                <?php endif; ?>

            </div>


            <!-- GANTI GAMBAR -->

            <div class="mb-5">

                <label class="block text-sm font-semibold text-slate-700 mb-2">

                    Ganti Gambar

                </label>

                <input
                    type="file"
                    name="gambar"
                    accept=".jpg,.jpeg,.png,.webp"
                    onchange="previewImage(event)"
                    class="w-full border border-slate-300 rounded-lg px-4 py-3 bg-white"
                >

                <p class="text-xs text-slate-400 mt-2">
                    Kosongkan jika tidak ingin mengganti gambar.
                    Maksimal 5 MB.
                </p>


                <div
                    id="previewContainer"
                    class="hidden mt-4"
                >

                    <p class="text-sm font-semibold text-slate-600 mb-2">
                        Preview Gambar Baru
                    </p>

                    <img
                        id="preview"
                        src=""
                        class="w-full max-w-md h-56 object-cover rounded-lg border"
                    >

                </div>

            </div>


            <!-- DESKRIPSI -->

            <div class="mb-5">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Deskripsi
                </label>

                <textarea
                    name="deskripsi"
                    rows="5"
                    class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                ><?= htmlspecialchars($galeri['deskripsi'] ?? '') ?></textarea>

            </div>


            <!-- STATUS -->

            <div class="mb-6">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full border border-slate-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >

                    <option
                        value="aktif"
                        <?= ($galeri['status'] === 'aktif') ? 'selected' : '' ?>
                    >
                        Aktif
                    </option>

                    <option
                        value="nonaktif"
                        <?= ($galeri['status'] === 'nonaktif') ? 'selected' : '' ?>
                    >
                        Nonaktif
                    </option>

                </select>

            </div>


            <!-- BUTTON -->

            <div class="flex flex-col sm:flex-row justify-end gap-3">

                <a
                    href="../galeri.php"
                    class="px-5 py-3 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold text-center"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="px-5 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold inline-flex items-center justify-center gap-2"
                >

                    <i class="fa-solid fa-save"></i>

                    Update Galeri

                </button>

            </div>

        </form>

    </div>

</div>


<script>

function previewImage(event) {

    const input = event.target;

    const preview = document.getElementById('preview');

    const container = document.getElementById('previewContainer');

    if (input.files && input.files[0]) {

        const reader = new FileReader();

        reader.onload = function(e) {

            preview.src = e.target.result;

            container.classList.remove('hidden');

        };

        reader.readAsDataURL(input.files[0]);

    }

}

</script>


