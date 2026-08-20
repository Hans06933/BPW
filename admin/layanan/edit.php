<?php

require_once "../../config/database.php";

$id = (int)($_GET['id'] ?? 0);


if ($id <= 0) {
    header("Location: ../layanan.php");
    exit;
}


$data = db_get(
    "SELECT * FROM layanan WHERE id = ?",
    [$id]
);


if (!$data) {
    header("Location: ../layanan.php");
    exit;
}


include "../../layout/admin_header.php";

?>

<div class="p-4 md:p-6">

    <div class="max-w-3xl mx-auto">

        <div class="mb-6">

            <a
                href="../layanan.php"
                class="text-sm text-blue-600 hover:text-blue-700 inline-flex items-center gap-2"
            >

                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Kelola Layanan

            </a>

            <h1 class="text-2xl font-bold text-slate-800 mt-4">
                Edit Layanan
            </h1>

        </div>


        <form
            action="update.php"
            method="POST"
            enctype="multipart/form-data"
            class="bg-white border rounded-xl shadow-sm p-5 md:p-6"
        >

            <input
                type="hidden"
                name="id"
                value="<?= $data['id'] ?>"
            >


            <div class="space-y-5">


                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Layanan
                    </label>

                    <input
                        type="text"
                        name="nama_layanan"
                        value="<?= htmlspecialchars($data['nama_layanan']) ?>"
                        required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
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
                    ><?= htmlspecialchars($data['deskripsi']) ?></textarea>

                </div>


                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Gambar Saat Ini
                    </label>


                    <?php if (!empty($data['gambar'])): ?>

                        <img
                            src="../../<?= htmlspecialchars($data['gambar']) ?>"
                            class="w-40 h-28 object-cover rounded-lg border mb-3"
                            alt="Gambar layanan"
                        >

                    <?php else: ?>

                        <div class="w-40 h-28 bg-slate-100 rounded-lg flex items-center justify-center text-gray-400 mb-3">

                            <i class="fa-solid fa-image text-2xl"></i>

                        </div>

                    <?php endif; ?>


                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Ganti Gambar
                    </label>

                    <input
                        type="file"
                        name="gambar"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm"
                    >

                    <p class="text-xs text-gray-400 mt-2">
                        Kosongkan jika tidak ingin mengganti gambar.
                    </p>

                </div>


                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm"
                    >

                        <option
                            value="Aktif"
                            <?= $data['status'] === 'Aktif' ? 'selected' : '' ?>
                        >
                            Aktif
                        </option>

                        <option
                            value="Nonaktif"
                            <?= $data['status'] === 'Nonaktif' ? 'selected' : '' ?>
                        >
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
                        Update

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
