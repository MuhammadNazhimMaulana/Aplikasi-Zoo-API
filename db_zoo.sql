-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Jul 2021 pada 11.57
-- Versi server: 10.4.18-MariaDB
-- Versi PHP: 7.4.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_zoo`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_hewan`
--

CREATE TABLE `tbl_hewan` (
  `id_hewan` int(11) NOT NULL,
  `id_jenis_hewan` int(11) NOT NULL,
  `nama_hewan` varchar(90) NOT NULL,
  `usia_hewan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_hewan`
--

INSERT INTO `tbl_hewan` (`id_hewan`, `id_jenis_hewan`, `nama_hewan`, `usia_hewan`) VALUES
(1, 2, 'Don', 2),
(2, 2, 'Dan', 1),
(3, 3, 'Dim', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_jenis_hewan`
--

CREATE TABLE `tbl_jenis_hewan` (
  `id_jenis_hewan` int(11) NOT NULL,
  `nama_spesies` varchar(125) NOT NULL,
  `ket_spesies` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_jenis_hewan`
--

INSERT INTO `tbl_jenis_hewan` (`id_jenis_hewan`, `nama_spesies`, `ket_spesies`) VALUES
(1, 'Harimau Bali', 'Sudah Punah'),
(2, 'Orang Utan', 'Hampir Punah'),
(3, 'Komodo', 'Hampir Punah');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_kandang`
--

CREATE TABLE `tbl_kandang` (
  `id_kandang` int(11) NOT NULL,
  `nama_kandang` varchar(100) NOT NULL,
  `posisi` varchar(150) NOT NULL,
  `jumlah_hewan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_kandang`
--

INSERT INTO `tbl_kandang` (`id_kandang`, `nama_kandang`, `posisi`, `jumlah_hewan`) VALUES
(1, 'Kandang Harimau', 'Berada di sebelah utara kebun binatang', 7),
(2, 'Kandang Gajah', 'Berada di sebelah selatan kebun binatang', 5),
(3, 'Kandang Monyet', 'Berada di sebelah selatan kandang Harimau', 9);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_pakan`
--

CREATE TABLE `tbl_pakan` (
  `id_pakan` int(11) NOT NULL,
  `jenis_pakan` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_pakan`
--

INSERT INTO `tbl_pakan` (`id_pakan`, `jenis_pakan`) VALUES
(3, ''),
(4, 'Pakan Omnivora'),
(5, 'Pakan Herbivora'),
(6, 'Pakan Karnivora');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_pegawai`
--

CREATE TABLE `tbl_pegawai` (
  `id_pegawai` int(11) NOT NULL,
  `nama_pegawai` varchar(100) NOT NULL,
  `tugas_pegawai` varchar(100) NOT NULL,
  `usia_pegawai` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_pegawai`
--

INSERT INTO `tbl_pegawai` (`id_pegawai`, `nama_pegawai`, `tugas_pegawai`, `usia_pegawai`) VALUES
(1, 'Mister Diablo', 'Membersihkan Kandang', 31),
(2, 'Rimuru', 'Membersihkan Kandang', 35),
(3, 'Beni', 'Memandikan Hewan', 31);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_pengguna`
--

CREATE TABLE `tbl_pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(150) NOT NULL,
  `api_key` varchar(100) NOT NULL,
  `email` varchar(75) NOT NULL,
  `nama_lengkap` varchar(88) NOT NULL,
  `hit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_pengguna`
--

INSERT INTO `tbl_pengguna` (`id_pengguna`, `username`, `password`, `api_key`, `email`, `nama_lengkap`, `hit`) VALUES
(1, 'anto', '12345', '321', 'anto@gmail.com', 'Diablo Anto', 2),
(2, 'bambang', 'aku123', 'aku123aku123', 'bambang@gmail.com', 'Bambang Anto', 7);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tbl_perawatan`
--

CREATE TABLE `tbl_perawatan` (
  `id_perawatan` int(11) NOT NULL,
  `id_kandang` int(11) NOT NULL,
  `id_hewan` int(11) NOT NULL,
  `id_pegawai` int(11) NOT NULL,
  `id_pakan` int(11) NOT NULL,
  `nama_kegiatan` varchar(175) NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `tanggal_kegiatan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tbl_perawatan`
--

INSERT INTO `tbl_perawatan` (`id_perawatan`, `id_kandang`, `id_hewan`, `id_pegawai`, `id_pakan`, `nama_kegiatan`, `keterangan`, `tanggal_kegiatan`) VALUES
(1, 3, 3, 3, 4, 'Pembersihan Kandang', 'Sedang dikerjakan', '2021-07-21'),
(2, 2, 2, 3, 5, 'Pembersihan Kandang', 'Sudah dikerjakan', '2021-06-21'),
(3, 2, 2, 2, 6, 'Pembersihan Kandang', 'Belum dikerjakan', '2021-08-11');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tbl_hewan`
--
ALTER TABLE `tbl_hewan`
  ADD PRIMARY KEY (`id_hewan`);

--
-- Indeks untuk tabel `tbl_jenis_hewan`
--
ALTER TABLE `tbl_jenis_hewan`
  ADD PRIMARY KEY (`id_jenis_hewan`);

--
-- Indeks untuk tabel `tbl_kandang`
--
ALTER TABLE `tbl_kandang`
  ADD PRIMARY KEY (`id_kandang`);

--
-- Indeks untuk tabel `tbl_pakan`
--
ALTER TABLE `tbl_pakan`
  ADD PRIMARY KEY (`id_pakan`);

--
-- Indeks untuk tabel `tbl_pegawai`
--
ALTER TABLE `tbl_pegawai`
  ADD PRIMARY KEY (`id_pegawai`);

--
-- Indeks untuk tabel `tbl_pengguna`
--
ALTER TABLE `tbl_pengguna`
  ADD PRIMARY KEY (`id_pengguna`);

--
-- Indeks untuk tabel `tbl_perawatan`
--
ALTER TABLE `tbl_perawatan`
  ADD PRIMARY KEY (`id_perawatan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tbl_hewan`
--
ALTER TABLE `tbl_hewan`
  MODIFY `id_hewan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tbl_jenis_hewan`
--
ALTER TABLE `tbl_jenis_hewan`
  MODIFY `id_jenis_hewan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tbl_kandang`
--
ALTER TABLE `tbl_kandang`
  MODIFY `id_kandang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tbl_pakan`
--
ALTER TABLE `tbl_pakan`
  MODIFY `id_pakan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tbl_pegawai`
--
ALTER TABLE `tbl_pegawai`
  MODIFY `id_pegawai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tbl_pengguna`
--
ALTER TABLE `tbl_pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tbl_perawatan`
--
ALTER TABLE `tbl_perawatan`
  MODIFY `id_perawatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
