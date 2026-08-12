# Les Privat Pintar 📘

Website belajar online untuk les privat, mirip konsep Ruang Guru dalam skala kecil —
dibuat khusus untuk **PHP native + MySQL**, siap diimpor dan langsung jalan di
XAMPP/Laragon/hosting cPanel biasa. Tidak pakai framework, jadi mudah dimodifikasi.

## ✨ Fitur

**Untuk Guru:**
- Dashboard ringkasan (jumlah murid, materi, kuis, hasil pengerjaan terbaru)
- Kelola materi belajar: teks/artikel + lampiran file PDF, per jenjang (SD/SMP/SMA)
- Buat kuis pilihan ganda per jenjang, kelola soal & kunci jawaban
- **Koreksi hasil kuis**: sistem menilai otomatis, guru bisa melihat rekap skor semua
  murid per kuis, lalu masuk ke detail untuk melihat jawaban benar/salah tiap soal
- Data murid: daftar murid terdaftar beserta jumlah kuis dikerjakan & rata-rata skor

**Untuk Murid:**
- Daftar akun sendiri (nama lengkap, password, pilih jenjang — hanya itu)
- Login cukup nama lengkap + password
- Baca materi yang otomatis terfilter sesuai jenjangnya
- Kerjakan kuis pilihan ganda, skor langsung keluar setelah submit (auto-koreksi)
- Lihat pembahasan: soal mana yang benar/salah beserta kunci jawabannya

## 🗂️ Struktur Folder

```
lesprivat/
├── sql/lesprivat.sql        <- Import file ini ke MySQL/phpMyAdmin
├── config/database.php      <- Ubah kredensial database di sini
├── includes/                <- Fungsi bantu, header/footer, sidebar
├── auth/                    <- Login, register, logout
├── guru/                    <- Semua halaman khusus guru
├── murid/                   <- Semua halaman khusus murid
├── assets/css/style.css     <- Semua styling ada di sini
├── uploads/materi/          <- Tempat menyimpan file PDF yang diunggah guru
└── index.php                <- Gerbang awal (redirect otomatis)
```

## 🚀 Cara Instalasi (XAMPP / Laragon)

1. **Copy folder** `lesprivat` ke folder `htdocs` (XAMPP) atau `www` (Laragon).
2. **Buat database**: buka phpMyAdmin → tab **Import** → pilih file
   `sql/lesprivat.sql` → klik **Go**. Database `lesprivat` beserta seluruh
   tabel dan 1 akun guru akan langsung terbuat.
3. **Cek koneksi**: buka `config/database.php`, sesuaikan bila perlu:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'lesprivat');
   define('DB_USER', 'root');
   define('DB_PASS', '');   // isi jika MySQL Anda pakai password
   ```
4. **Pastikan folder `uploads/materi/` bisa ditulis** (writable) oleh server —
   di Windows/XAMPP biasanya otomatis, di Linux/hosting jalankan:
   ```
   chmod -R 755 uploads/
   ```
5. **Akses website** lewat browser: `http://localhost/lesprivat/`

## 🔑 Akun Default

| Role | Nama Lengkap | Password |
|------|--------------|----------|
| Guru | `Guru`       | `guru123` |

> ⚠️ Segera login dan ganti password guru lewat phpMyAdmin (kolom `password`
> di tabel `users`, isi dengan hasil `password_hash()` PHP) atau tambahkan
> fitur ganti password jika ingin dikembangkan lebih lanjut.

Akun **murid** didaftarkan sendiri oleh murid lewat halaman **Daftar** —
guru tidak perlu membuatkan satu per satu.

## 🧭 Alur Pemakaian

1. Murid mendaftar mandiri: isi nama lengkap, jenjang (SD/SMP/SMA), dan password.
2. Guru login dengan akun default, lalu menambahkan **Materi** dan **Kuis**
   sesuai jenjang yang dituju.
3. Murid login, otomatis hanya melihat materi & kuis sesuai jenjangnya sendiri.
4. Murid mengerjakan kuis pilihan ganda → skor langsung dihitung sistem.
5. Guru membuka menu **Kuis & Koreksi → Koreksi Hasil** untuk memeriksa
   jawaban tiap murid secara detail per soal.

## 🔒 Catatan Keamanan

- Semua query database memakai **prepared statement (PDO)** — aman dari SQL Injection.
- Password disimpan dengan **`password_hash()` (bcrypt)**, bukan teks polos.
- Semua output ke halaman disaring dengan `htmlspecialchars()` — aman dari XSS.
- Folder `uploads/` sudah diberi `.htaccess` supaya file yang diunggah
  tidak bisa dieksekusi sebagai skrip PHP.
- Setiap halaman guru/murid dilindungi pengecekan role (`require_role()`),
  jadi murid tidak bisa mengakses halaman guru begitupun sebaliknya.

## 🛠️ Ide Pengembangan Lanjutan

Website ini sengaja dibuat dengan fondasi yang rapi supaya mudah dikembangkan,
misalnya:
- Tambah fitur ganti password & edit profil
- Tambah jenis soal essay yang dikoreksi manual oleh guru
- Kelompokkan materi/kuis per mata pelajaran, tidak hanya per jenjang
- Tambah fitur reset/ulangi kuis dengan batas waktu percobaan
- Tambah statistik grafik perkembangan nilai murid dari waktu ke waktu

## 💻 Kebutuhan Server

- PHP 7.4 ke atas (disarankan PHP 8.x)
- MySQL 5.7+ / MariaDB 10+
- Ekstensi PHP: `pdo_mysql`, `mbstring` (opsional, sudah ada fallback aman
  jika tidak tersedia)

---
Dibuat dengan ❤️ untuk mendukung kegiatan belajar mengajar les privat.
