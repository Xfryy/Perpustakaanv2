SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

DROP TABLE IF EXISTS `peminjaman`;
DROP TABLE IF EXISTS `buku`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id_user` int(15) NOT NULL AUTO_INCREMENT,
  `role` enum('siswa','admin') NOT NULL DEFAULT 'siswa',
  `email` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `buku` (
  `id_buku` int(15) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `pengarang` varchar(255) NOT NULL,
  `penerbit` varchar(255) NOT NULL,
  `deskripsi` longtext NOT NULL,
  `gambar_buku` varchar(500) DEFAULT NULL,
  `jumlah_buku` int(100) NOT NULL DEFAULT 1,
  `rak_buku` enum('rak_1','rak_2','rak_3') NOT NULL DEFAULT 'rak_1',
  `status` enum('di_pinjam','tidak_dipinjam') NOT NULL DEFAULT 'tidak_dipinjam',
  `tanggal` date NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_buku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(15) NOT NULL AUTO_INCREMENT,
  `id_buku` int(15) NOT NULL,
  `id_user` int(15) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `tgl_pinjam` datetime NOT NULL,
  `tgl_balik` datetime NOT NULL,
  `status` enum('pending','approved','rejected','returned') DEFAULT 'pending',
  `catatan_admin` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_peminjaman`),
  KEY `id_buku` (`id_buku`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE,
  CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Password: 123456
INSERT INTO `users` (`role`, `email`, `nama_lengkap`, `password`) VALUES
('siswa', 'Hazami200@gmail.com', 'Muhammad Hazami', '$2y$10$nOQm7tCeuRQ8EJ0pZXrO5OPX8KG8I4YQG5qQXtJp6h8VmQoVb9mZe'),
('siswa', 'faricandra@gmail.com', 'Faricandra', '$2y$10$nOQm7tCeuRQ8EJ0pZXrO5OPX8KG8I4YQG5qQXtJp6h8VmQoVb9mZe'),
('admin', 'admin@perpustakaan.com', 'Admin Perpustakaan', '$2y$10$nOQm7tCeuRQ8EJ0pZXrO5OPX8KG8I4YQG5qQXtJp6h8VmQoVb9mZe');

INSERT INTO `buku` (`judul`, `pengarang`, `penerbit`, `deskripsi`, `gambar_buku`, `jumlah_buku`, `rak_buku`, `status`, `tanggal`) VALUES
('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 'Novel tentang perjuangan anak-anak Belitung dalam meraih pendidikan di tengah keterbatasan. Kisah inspiratif yang mengharukan tentang persahabatan, semangat, dan mimpi.', NULL, 5, 'rak_1', 'tidak_dipinjam', '2024-01-10'),
('Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 'Novel sejarah yang menceritakan perjuangan Minke, seorang pribumi terpelajar di era kolonial Belanda. Karya agung sastra Indonesia yang diakui dunia.', NULL, 3, 'rak_1', 'tidak_dipinjam', '2024-01-15'),
('Matematika Dasar SMA', 'Tim Penyusun', 'Erlangga', 'Buku pelajaran matematika untuk tingkat SMA yang mencakup aljabar, geometri, trigonometri, dan kalkulus dasar. Dilengkapi soal latihan dan pembahasan.', NULL, 8, 'rak_2', 'tidak_dipinjam', '2024-02-01'),
('Sejarah Indonesia Modern', 'M.C. Ricklefs', 'Gadjah Mada University Press', 'Buku referensi sejarah Indonesia dari abad ke-14 hingga era reformasi. Analisis mendalam berbasis riset arsip dan sumber primer.', NULL, 4, 'rak_2', 'tidak_dipinjam', '2024-02-10'),
('Harry Potter dan Batu Bertuah', 'J.K. Rowling', 'Gramedia', 'Petualangan Harry Potter, seorang penyihir muda yang belajar di Hogwarts. Kisah tentang persahabatan, keberanian, dan perjuangan melawan kejahatan.', NULL, 6, 'rak_3', 'tidak_dipinjam', '2024-02-15'),
('Fisika Untuk SMA Kelas X', 'Marthen Kanginan', 'Erlangga', 'Buku fisika komprehensif untuk siswa SMA kelas X. Mencakup mekanika, termodinamika, dan gelombang dengan penjelasan mudah dipahami.', NULL, 7, 'rak_2', 'tidak_dipinjam', '2024-03-01'),
('Sang Pemimpi', 'Andrea Hirata', 'Bentang Pustaka', 'Sekuel Laskar Pelangi. Kisah Ikal dan Arai yang bermimpi besar untuk bersekolah di Eropa dari kampung kecil di Belitung.', NULL, 4, 'rak_1', 'tidak_dipinjam', '2024-03-10'),
('Kimia Organik Dasar', 'Fessenden & Fessenden', 'Erlangga', 'Buku teks kimia organik yang menjelaskan struktur, sifat, dan reaksi senyawa organik. Dilengkapi contoh soal dan latihan.', NULL, 3, 'rak_2', 'tidak_dipinjam', '2024-03-15');

ALTER TABLE `users` AUTO_INCREMENT=4;
ALTER TABLE `buku` AUTO_INCREMENT=9;
ALTER TABLE `peminjaman` AUTO_INCREMENT=1;

COMMIT;
