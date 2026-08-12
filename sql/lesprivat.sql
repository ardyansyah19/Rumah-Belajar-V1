-- =========================================================
-- LES PRIVAT PINTAR - Database Schema
-- Import file ini lewat phpMyAdmin (tab Import) atau:
--   mysql -u root -p < lesprivat.sql
-- =========================================================

CREATE DATABASE IF NOT EXISTS lesprivat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lesprivat;

DROP TABLE IF EXISTS jawaban_murid;
DROP TABLE IF EXISTS hasil_kuis;
DROP TABLE IF EXISTS soal;
DROP TABLE IF EXISTS kuis;
DROP TABLE IF EXISTS materi;
DROP TABLE IF EXISTS users;

-- ---------------------------------------------------------
-- Tabel users (guru & murid jadi satu tabel, dibedakan kolom role)
-- ---------------------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('guru','murid') NOT NULL DEFAULT 'murid',
    jenjang ENUM('SD','SMP','SMA') NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unik_nama (nama_lengkap)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Tabel materi belajar (teks/artikel + file PDF opsional)
-- ---------------------------------------------------------
CREATE TABLE materi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    konten TEXT NOT NULL,
    file_pdf VARCHAR(255) NULL,
    jenjang ENUM('SD','SMP','SMA') NOT NULL,
    guru_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guru_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Tabel kuis (kumpulan soal pilihan ganda per jenjang)
-- ---------------------------------------------------------
CREATE TABLE kuis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    deskripsi VARCHAR(255) NULL,
    jenjang ENUM('SD','SMP','SMA') NOT NULL,
    guru_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (guru_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Tabel soal pilihan ganda milik sebuah kuis
-- ---------------------------------------------------------
CREATE TABLE soal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kuis_id INT NOT NULL,
    pertanyaan TEXT NOT NULL,
    pilihan_a VARCHAR(255) NOT NULL,
    pilihan_b VARCHAR(255) NOT NULL,
    pilihan_c VARCHAR(255) NOT NULL,
    pilihan_d VARCHAR(255) NOT NULL,
    jawaban_benar ENUM('A','B','C','D') NOT NULL,
    FOREIGN KEY (kuis_id) REFERENCES kuis(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Tabel hasil pengerjaan kuis oleh murid (rekap skor)
-- ---------------------------------------------------------
CREATE TABLE hasil_kuis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kuis_id INT NOT NULL,
    murid_id INT NOT NULL,
    skor INT NOT NULL DEFAULT 0,
    jumlah_benar INT NOT NULL DEFAULT 0,
    jumlah_soal INT NOT NULL DEFAULT 0,
    tanggal_pengerjaan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kuis_id) REFERENCES kuis(id) ON DELETE CASCADE,
    FOREIGN KEY (murid_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY satu_kali_kerjakan (kuis_id, murid_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Tabel detail jawaban murid per soal (untuk fitur koreksi guru)
-- ---------------------------------------------------------
CREATE TABLE jawaban_murid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hasil_id INT NOT NULL,
    soal_id INT NOT NULL,
    jawaban_dipilih ENUM('A','B','C','D') NULL,
    benar TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (hasil_id) REFERENCES hasil_kuis(id) ON DELETE CASCADE,
    FOREIGN KEY (soal_id) REFERENCES soal(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Akun guru default -> silakan login lalu ganti password lewat phpMyAdmin
-- Nama Lengkap : Guru
-- Password     : guru123
-- ---------------------------------------------------------
INSERT INTO users (nama_lengkap, password, role, jenjang) VALUES
('Guru', '$2y$10$JNwgX24nDBih4zqjjrkHVeXM6Fq7m6b3LTuXekMsstd90WhMB00uC', 'guru', NULL);
