-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2026 at 04:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_bpw`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `konten` longtext NOT NULL,
  `excerpt` text DEFAULT NULL,
  `kategori` enum('destinasi','tips','budaya','kuliner','event','berita') DEFAULT 'destinasi',
  `gambar_utama` varchar(255) DEFAULT NULL,
  `penulis` varchar(100) DEFAULT 'Admin BPW',
  `views` int(11) DEFAULT 0,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id`, `judul`, `slug`, `konten`, `excerpt`, `kategori`, `gambar_utama`, `penulis`, `views`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(1, '10 Destinasi Wisata Terbaik di Bali', '10-destinasi-wisata-terbaik-di-bali', '<p>Bali selalu menjadi destinasi favorit wisatawan mancanegara...</p>', 'Bali memiliki begitu banyak tempat menarik untuk dikunjungi', 'destinasi', NULL, 'Admin BPW', 0, 'published', '2026-05-30 03:13:00', '2026-05-29 20:13:00', '2026-05-29 20:13:00'),
(2, 'Tips Liburan Hemat ke Yogyakarta', 'tips-liburan-hemat-ke-yogyakarta', '<p>Liburan ke Jogja nggak selalu mahal. Simak tips hemat berikut ini...</p>', 'Yogyakarta terkenal dengan destinasi yang ramah di kantong', 'tips', NULL, 'Admin BPW', 0, 'published', '2026-05-30 03:13:00', '2026-05-29 20:13:00', '2026-05-29 20:13:00');

-- --------------------------------------------------------

--
-- Table structure for table `destinasi`
--

CREATE TABLE `destinasi` (
  `id` int(11) NOT NULL,
  `nama_destinasi` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `wilayah` varchar(100) NOT NULL,
  `kategori` enum('pantai','gunung','danau','budaya','kota','taman','pulau') DEFAULT 'pantai',
  `deskripsi` text DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `harga_tiket_masuk` decimal(15,0) DEFAULT NULL,
  `jam_operasional` varchar(100) DEFAULT NULL,
  `gambar_utama` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `total_review` int(11) DEFAULT 0,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `destinasi`
--

INSERT INTO `destinasi` (`id`, `nama_destinasi`, `slug`, `wilayah`, `kategori`, `deskripsi`, `alamat`, `harga_tiket_masuk`, `jam_operasional`, `gambar_utama`, `rating`, `total_review`, `status`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Pantai Kuta', 'pantai-kuta', 'Bali', 'pantai', 'Pantai ikonik dengan pasir putih dan ombak yang cocok untuk berselancar', NULL, NULL, NULL, NULL, 4.8, 0, 'aktif', 0, '2026-05-29 20:11:27', '2026-05-29 20:11:27'),
(2, 'Candi Borobudur', 'candi-borobudur', 'Yogyakarta', 'budaya', 'Candi Buddha terbesar di dunia, warisan budaya UNESCO', NULL, NULL, NULL, NULL, 4.9, 0, 'aktif', 0, '2026-05-29 20:11:27', '2026-05-29 20:11:27'),
(3, 'Taman Nasional Komodo', 'taman-nasional-komodo', 'Labuan Bajo', 'taman', 'Habitat asli komodo dan keindahan alam yang menakjubkan', NULL, NULL, NULL, NULL, 4.9, 0, 'aktif', 0, '2026-05-29 20:11:27', '2026-05-29 20:11:27'),
(4, 'Wayag Islands', 'wayag-islands', 'Raja Ampat', 'pulau', 'Gugusan pulau karst yang ikonik di Raja Ampat', NULL, NULL, NULL, NULL, 4.9, 0, 'aktif', 0, '2026-05-29 20:11:27', '2026-05-29 20:11:27'),
(5, 'Gunung Bromo', 'gunung-bromo', 'Bromo', 'gunung', 'Gunung berapi aktif dengan lautan pasir dan sunrise terbaik', NULL, NULL, NULL, NULL, 4.8, 0, 'aktif', 0, '2026-05-29 20:11:27', '2026-05-29 20:11:27');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `jawaban` text NOT NULL,
  `kategori` enum('pemesanan','pembayaran','destinasi','layanan','umum') DEFAULT 'umum',
  `urutan` int(3) DEFAULT 0,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `pertanyaan`, `jawaban`, `kategori`, `urutan`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Bagaimana cara memesan paket wisata di BPW?', 'Anda dapat memesan paket wisata melalui website kami dengan mengisi form pemesanan, atau menghubungi customer service kami via WhatsApp di 0812-3456-7890. Tim kami akan membantu proses pemesanan Anda dengan cepat dan mudah.', 'pemesanan', 1, 'aktif', '2026-05-29 20:13:20', '2026-05-29 20:13:20'),
(2, 'Apakah ada minimal pemesanan untuk paket wisata?', 'Minimal pemesanan bervariasi tergantung paket yang dipilih. Untuk paket reguler umumnya minimal 2 orang dewasa. Namun kami juga menyediakan paket private tour untuk 1 orang atau kelompok besar.', 'pemesanan', 2, 'aktif', '2026-05-29 20:13:20', '2026-05-29 20:13:20'),
(3, 'Metode pembayaran apa saja yang tersedia?', 'Kami menerima pembayaran melalui transfer bank (BCA, Mandiri, BNI, BRI), kartu kredit, dan pembayaran via QRIS (DANA, OVO, GoPay, LinkAja).', 'pembayaran', 3, 'aktif', '2026-05-29 20:13:20', '2026-05-29 20:13:20'),
(4, 'Apakah bisa custom itinerary perjalanan?', 'Tentu! BPW menerima custom tour sesuai keinginan Anda. Silakan konsultasikan dengan tim kami tentang destinasi impian Anda.', 'layanan', 4, 'aktif', '2026-05-29 20:13:20', '2026-05-29 20:13:20');

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `kategori` enum('destinasi','kegiatan','makanan','akomodasi','testimoni') DEFAULT 'destinasi',
  `destinasi` varchar(100) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hotel`
--

CREATE TABLE `hotel` (
  `id` int(11) NOT NULL,
  `nama_hotel` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `destinasi` varchar(100) NOT NULL,
  `bintang` int(1) DEFAULT 3,
  `harga_per_malam` decimal(15,0) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `fasilitas` text DEFAULT NULL,
  `gambar_utama` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `total_review` int(11) DEFAULT 0,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kontak_masuk`
--

CREATE TABLE `kontak_masuk` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `subjek` varchar(200) NOT NULL,
  `pesan` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paket_wisata`
--

CREATE TABLE `paket_wisata` (
  `id` int(11) NOT NULL,
  `nama_paket` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `destinasi` varchar(100) NOT NULL,
  `durasi` varchar(50) NOT NULL,
  `harga_normal` decimal(15,0) NOT NULL,
  `harga_diskon` decimal(15,0) DEFAULT NULL,
  `diskon_persen` int(3) DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  `itinerary` text DEFAULT NULL,
  `fasilitas` text DEFAULT NULL,
  `termasuk` text DEFAULT NULL,
  `tidak_termasuk` text DEFAULT NULL,
  `gambar_utama` varchar(255) DEFAULT NULL,
  `gambar_lain` text DEFAULT NULL,
  `kuota` int(11) DEFAULT 0,
  `tersisa` int(11) DEFAULT 0,
  `minimal_peserta` int(11) DEFAULT 2,
  `status` enum('aktif','nonaktif','habis') DEFAULT 'aktif',
  `is_featured` tinyint(1) DEFAULT 0,
  `is_flash_sale` tinyint(1) DEFAULT 0,
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `paket_wisata`
--

INSERT INTO `paket_wisata` (`id`, `nama_paket`, `slug`, `destinasi`, `durasi`, `harga_normal`, `harga_diskon`, `diskon_persen`, `deskripsi`, `itinerary`, `fasilitas`, `termasuk`, `tidak_termasuk`, `gambar_utama`, `gambar_lain`, `kuota`, `tersisa`, `minimal_peserta`, `status`, `is_featured`, `is_flash_sale`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Bali 3 Hari 2 Malam', 'bali-3-hari-2-malam', 'Bali', '3 Hari 2 Malam', 2150000, 2150000, 0, 'Nikmati keindahan Pulau Dewata dengan paket wisata lengkap', NULL, 'Hotel, Transportasi, Tour Guide, Breakfast, Tiket Wisata', NULL, NULL, NULL, NULL, 0, 0, 2, 'aktif', 1, 0, 0, '2026-05-29 20:11:09', '2026-05-29 20:11:09'),
(2, 'Yogyakarta 3 Hari 2 Malam', 'yogyakarta-3-hari-2-malam', 'Yogyakarta', '3 Hari 2 Malam', 1850000, 1850000, 0, 'Jelajahi kota budaya dengan candi-candi megah', NULL, 'Hotel, Transportasi, Tour Guide, Breakfast, Tiket Wisata', NULL, NULL, NULL, NULL, 0, 0, 2, 'aktif', 1, 0, 0, '2026-05-29 20:11:09', '2026-05-29 20:11:09'),
(3, 'Labuan Bajo 4 Hari 3 Malam', 'labuan-bajo-4-hari-3-malam', 'Labuan Bajo', '4 Hari 3 Malam', 4750000, 4250000, 11, 'Jelajahi keindahan alam Labuan Bajo dan Komodo', NULL, 'Liveaboard, Snorkeling, Tour Guide, Meals, Tiket Masuk', NULL, NULL, NULL, NULL, 0, 0, 2, 'aktif', 1, 0, 0, '2026-05-29 20:11:09', '2026-05-29 20:11:09'),
(4, 'Raja Ampat 5 Hari 4 Malam', 'raja-ampat-5-hari-4-malam', 'Raja Ampat', '5 Hari 4 Malam', 6950000, 6950000, 0, 'Surga bawah laut dengan keindahan terbaik dunia', NULL, 'Homestay, Snorkeling Gear, Local Guide, Meals', NULL, NULL, NULL, NULL, 0, 0, 2, 'aktif', 1, 0, 0, '2026-05-29 20:11:09', '2026-05-29 20:11:09'),
(5, 'Bromo Midnight Tour', 'bromo-midnight-tour', 'Bromo', '2 Hari 1 Malam', 1450000, 1250000, 14, 'Menyaksikan sunrise terbaik di Gunung Bromo', NULL, 'Jeep 4x4, Breakfast, Tiket Masuk, Tour Guide', NULL, NULL, NULL, NULL, 0, 0, 2, 'aktif', 1, 0, 0, '2026-05-29 20:11:09', '2026-05-29 20:11:09'),
(6, 'Bandung Getaway', 'bandung-getaway', 'Bandung', '2 Hari 1 Malam', 1250000, 950000, 24, 'Liburan seru di kota kembang Bandung', NULL, 'Hotel, Transportasi, Tiket Wisata, Breakfast', NULL, NULL, NULL, NULL, 0, 0, 2, 'aktif', 0, 0, 0, '2026-05-29 20:11:09', '2026-05-29 20:11:09');

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id` int(11) NOT NULL,
  `kode_pemesanan` varchar(20) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `alamat` text DEFAULT NULL,
  `paket_id` int(11) NOT NULL,
  `paket_nama` varchar(200) NOT NULL,
  `jumlah_peserta` int(3) NOT NULL,
  `tanggal_berangkat` date NOT NULL,
  `total_harga` decimal(15,0) NOT NULL,
  `status_pembayaran` enum('pending','dp','lunas','expired','cancel') DEFAULT 'pending',
  `status_pemesanan` enum('pending','confirmed','processing','completed','cancelled') DEFAULT 'pending',
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) DEFAULT 'general',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `setting_key`, `setting_value`, `setting_group`, `description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Bayu Prima Wisata', 'general', 'Nama website', '2026-05-29 20:13:35', '2026-05-29 20:13:35'),
(2, 'site_tagline', 'Your Journey, Our Priority', 'general', 'Tagline website', '2026-05-29 20:13:35', '2026-05-29 20:13:35'),
(3, 'site_email', 'info@bayuprimawisata.com', 'general', 'Email utama', '2026-05-29 20:13:35', '2026-05-29 20:13:35'),
(4, 'site_phone', '0812-3456-7890', 'general', 'Nomor telepon', '2026-05-29 20:13:35', '2026-05-29 20:13:35'),
(5, 'site_address', 'Jl. Raya Margonda No 88, Depok, Jawa Barat 16424', 'general', 'Alamat kantor', '2026-05-29 20:13:35', '2026-05-29 20:13:35'),
(6, 'facebook_url', 'https://facebook.com/bayuprimawisata', 'social', 'Facebook page', '2026-05-29 20:13:35', '2026-05-29 20:13:35'),
(7, 'instagram_url', 'https://instagram.com/bayuprimawisata', 'social', 'Instagram account', '2026-05-29 20:13:35', '2026-05-29 20:13:35'),
(8, 'youtube_url', 'https://youtube.com/@bayuprimawisata', 'social', 'YouTube channel', '2026-05-29 20:13:35', '2026-05-29 20:13:35'),
(9, 'tiktok_url', 'https://tiktok.com/@bayuprimawisata', 'social', 'TikTok account', '2026-05-29 20:13:35', '2026-05-29 20:13:35'),
(10, 'whatsapp_number', '6281234567890', 'contact', 'Nomor WhatsApp', '2026-05-29 20:13:35', '2026-05-29 20:13:35'),
(11, 'whatsapp_message', 'Halo BPW, saya ingin konsultasi tentang paket wisata', 'contact', 'Pesan default WhatsApp', '2026-05-29 20:13:35', '2026-05-29 20:13:35');

-- --------------------------------------------------------

--
-- Table structure for table `promo`
--

CREATE TABLE `promo` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `kode_promo` varchar(50) DEFAULT NULL,
  `diskon_persen` int(3) DEFAULT 0,
  `minimal_pembelian` decimal(15,0) DEFAULT 0,
  `maksimal_diskon` decimal(15,0) DEFAULT NULL,
  `berlaku_mulai` date NOT NULL,
  `berlaku_sampai` date NOT NULL,
  `kuota` int(11) DEFAULT NULL,
  `sisa_kuota` int(11) DEFAULT NULL,
  `kategori` enum('flash_sale','early_bird','group','member','umum') DEFAULT 'umum',
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif','habis') DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promo`
--

INSERT INTO `promo` (`id`, `judul`, `deskripsi`, `kode_promo`, `diskon_persen`, `minimal_pembelian`, `maksimal_diskon`, `berlaku_mulai`, `berlaku_sampai`, `kuota`, `sisa_kuota`, `kategori`, `gambar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Flash Sale Akhir Bulan', 'Diskon spesial untuk 5 pemesanan pertama setiap hari', NULL, 20, 0, NULL, '2026-05-30', '2026-06-29', NULL, NULL, 'flash_sale', NULL, 'aktif', '2026-05-29 20:12:35', '2026-05-29 20:12:35'),
(2, 'Early Bird Lebaran', 'Pesan lebih awal dapatkan diskon tambahan', NULL, 15, 0, NULL, '2026-05-30', '2026-12-31', NULL, NULL, 'early_bird', NULL, 'aktif', '2026-05-29 20:12:35', '2026-05-29 20:12:35'),
(3, 'Diskon Rombongan', 'Minimal 5 orang dapatkan diskon spesial', NULL, 10, 0, NULL, '2026-05-30', '2026-12-31', NULL, NULL, 'group', NULL, 'aktif', '2026-05-29 20:12:35', '2026-05-29 20:12:35');

-- --------------------------------------------------------

--
-- Table structure for table `testimoni`
--

CREATE TABLE `testimoni` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `destinasi` varchar(100) NOT NULL,
  `rating` int(1) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `testimoni` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `kota_asal` varchar(100) DEFAULT NULL,
  `tipe_perjalanan` enum('Keluarga','Couple','Solo','Teman','Bisnis') DEFAULT 'Keluarga',
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `testimoni`
--

INSERT INTO `testimoni` (`id`, `nama`, `email`, `destinasi`, `rating`, `testimoni`, `foto`, `kota_asal`, `tipe_perjalanan`, `status`, `is_featured`, `created_at`, `updated_at`) VALUES
(1, 'Siti Rahmawati', 'siti@email.com', 'Bali', 5, 'Pelayanan BPW sangat memuaskan! Itinerary terencana dengan baik, tour guide ramah dan berpengalaman. Pasti akan travel lagi dengan BPW!', NULL, 'Jakarta', 'Keluarga', 'approved', 1, '2026-05-29 20:12:04', '2026-05-29 20:12:04'),
(2, 'Andi Pratama', 'andi@email.com', 'Labuan Bajo', 5, 'Pengalaman luar biasa ke Labuan Bajo! Kapal bersih, snorkelingnya seru, dan bisa lihat Komodo langsung. Terima kasih BPW!', NULL, 'Bandung', 'Keluarga', 'approved', 1, '2026-05-29 20:12:04', '2026-05-29 20:12:04'),
(3, 'Dewi Lestari', 'dewi@email.com', 'Yogyakarta', 4, 'Liburan ke Jogja jadi lebih menyenangkan dengan BPW. Transportasi nyaman, akomodasi oke, harga terjangkau.', NULL, 'Surabaya', 'Keluarga', 'approved', 0, '2026-05-29 20:12:04', '2026-05-29 20:12:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('super_admin','admin','editor') DEFAULT 'admin',
  `avatar` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `avatar`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@bayuprimawisata.com', 'Administrator BPW', 'super_admin', NULL, NULL, '2026-05-29 20:10:35', '2026-05-29 20:10:35');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_dashboard_stats`
-- (See below for the actual view)
--
CREATE TABLE `view_dashboard_stats` (
`total_paket_aktif` bigint(21)
,`total_destinasi` bigint(21)
,`total_testimoni` bigint(21)
,`total_pemesanan` bigint(21)
,`pesan_belum_dibaca` bigint(21)
);

-- --------------------------------------------------------

--
-- Structure for view `view_dashboard_stats`
--
DROP TABLE IF EXISTS `view_dashboard_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_dashboard_stats`  AS SELECT (select count(0) from `paket_wisata` where `paket_wisata`.`status` = 'aktif') AS `total_paket_aktif`, (select count(0) from `destinasi` where `destinasi`.`status` = 'aktif') AS `total_destinasi`, (select count(0) from `testimoni` where `testimoni`.`status` = 'approved') AS `total_testimoni`, (select count(0) from `pemesanan`) AS `total_pemesanan`, (select count(0) from `kontak_masuk` where `kontak_masuk`.`status` = 'unread') AS `pesan_belum_dibaca` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_kategori` (`kategori`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tanggal` (`published_at`),
  ADD KEY `idx_blog_published` (`published_at`);

--
-- Indexes for table `destinasi`
--
ALTER TABLE `destinasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_wilayah` (`wilayah`),
  ADD KEY `idx_kategori` (`kategori`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kategori` (`kategori`),
  ADD KEY `idx_urutan` (`urutan`);

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kategori` (`kategori`);

--
-- Indexes for table `hotel`
--
ALTER TABLE `hotel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_destinasi` (`destinasi`),
  ADD KEY `idx_bintang` (`bintang`),
  ADD KEY `idx_harga` (`harga_per_malam`);

--
-- Indexes for table `kontak_masuk`
--
ALTER TABLE `kontak_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `paket_wisata`
--
ALTER TABLE `paket_wisata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_destinasi` (`destinasi`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_harga` (`harga_normal`),
  ADD KEY `idx_paket_destinasi` (`destinasi`),
  ADD KEY `idx_paket_harga` (`harga_normal`);

--
-- Indexes for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pemesanan` (`kode_pemesanan`),
  ADD KEY `idx_kode` (`kode_pemesanan`),
  ADD KEY `idx_status` (`status_pemesanan`),
  ADD KEY `idx_tanggal` (`tanggal_berangkat`),
  ADD KEY `paket_id` (`paket_id`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_tanggal` (`berlaku_mulai`,`berlaku_sampai`),
  ADD KEY `idx_promo_tanggal` (`berlaku_mulai`,`berlaku_sampai`);

--
-- Indexes for table `testimoni`
--
ALTER TABLE `testimoni`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_rating` (`rating`),
  ADD KEY `idx_destinasi` (`destinasi`),
  ADD KEY `idx_testimoni_rating` (`rating`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `destinasi`
--
ALTER TABLE `destinasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hotel`
--
ALTER TABLE `hotel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kontak_masuk`
--
ALTER TABLE `kontak_masuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paket_wisata`
--
ALTER TABLE `paket_wisata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `promo`
--
ALTER TABLE `promo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `testimoni`
--
ALTER TABLE `testimoni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `pemesanan_ibfk_1` FOREIGN KEY (`paket_id`) REFERENCES `paket_wisata` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
