# BIMBINGKU: Back End Upload Foto (Studi Kasus Manajemen Kursus)

Modul **Pendaftaran Siswa** dan **Kartu Member** untuk BIMBINGKU, dibangun dengan PHP 8 + MySQL/MariaDB.
Tampilan memakai gaya yang sama dengan web PPDB sebelumnya (marun, krem, serif italic).

## Cara menjalankan (XAMPP / Laragon)

1. Salin folder `bimbingku` ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Nyalakan Apache dan MySQL, lalu impor `database.sql` lewat phpMyAdmin.
3. Cek konstanta di `lib/bootstrap.php` (bawaan XAMPP: user `root`, password kosong, database `bimbingku`).
4. Buka `http://localhost/bimbingku/`.
5. Pastikan folder `uploads/foto` dan `uploads/bukti` bisa ditulis oleh web server.

Akun admin awal: `admin@bimbingku.test` / `password` (wajib diganti saat login pertama).
Siswa mendaftar sendiri lewat tombol **Daftar**.

## Alur

1. Siswa membuat akun, lalu mengisi formulir: biodata, cabang, kelas, **foto (JPG/PNG, maks 2 MB)**, dan **bukti pembayaran (opsional, JPG/PNG/PDF)**.
2. Sistem menyimpan data, membuat nomor registrasi `REG-TAHUN-NNNNN`, status awal **Menunggu**.
3. Admin membuka detail pendaftar, memeriksa foto dan bukti, lalu mengubah status ke **Diterima** atau **Ditolak** (alasan wajib saat ditolak).
4. Saat **Diterima**, kartu member terbit otomatis (nomor `BMB-TAHUN-NNNNN`, QR Code). Siswa melihatnya di dashboard, admin bisa mencetaknya.
5. Siswa yang ditolak dapat memperbaiki data dan mengirim ulang.
6. Publik bisa cek status lewat nomor registrasi dan tanggal lahir (nama disamarkan).

## Cara kerja upload (inti tugas)

| Aspek | Implementasi |
|-------|--------------|
| Jenis file | Dicek dari **isi file** (`finfo`), bukan dari nama atau ekstensi kiriman. Foto: JPG/PNG. Bukti: JPG/PNG/PDF. |
| Ukuran | Maksimal 2 MB, dicek di browser (JS) dan di server (PHP). |
| Gambar valid | `getimagesize()`, minimal 100x100 dan maksimal 8000x8000 piksel. |
| Nama file | Diacak (`bin2hex(random_bytes(16))` + ekstensi dari jenis file), nama asli tidak dipakai. |
| Lokasi | `uploads/foto` dan `uploads/bukti`, ditutup dengan `.htaccess`. File hanya keluar lewat `file.php` setelah cek login dan kepemilikan (admin atau pemilik data). |
| Ganti file | File lama dihapus setelah data baru tersimpan. Jika simpan ke database gagal, file baru ikut dibatalkan. |
| Hapus pendaftar | Foto dan bukti ikut terhapus dari disk. |

## Struktur

| File | Fungsi |
|------|--------|
| `lib/bootstrap.php` | Koneksi DB, session, CSRF otomatis untuk semua POST, helper, kerangka halaman, helper upload |
| `siswa/form.php` | Formulir pendaftaran + upload foto dan bukti (tambah dan ubah) |
| `siswa/index.php` | Dashboard siswa: status, data, tombol kartu |
| `kartu.php` | Kartu member (QR Code, cetak / simpan PDF dari browser) |
| `file.php` | Penyaji foto dan bukti dengan cek hak akses |
| `admin/index.php` | Dashboard admin: statistik dan pendaftar terbaru |
| `admin/siswa.php` | Daftar pendaftar: cari, filter status/cabang, pagination |
| `admin/siswa-detail.php` | Lihat foto dan bukti, ubah status, hapus |
| `admin/kelas.php` | CRUD kelas (kuota, biaya, jadwal) |
| `login.php`, `register.php`, `logout.php`, `ganti-password.php` | Autentikasi |
| `index.php`, `profil.php`, `kelas.php`, `cek-status.php` | Halaman publik |

## Keamanan

Prepared statement di semua query, `password_hash()` (bcrypt), CSRF token untuk semua POST,
`htmlspecialchars` untuk output, pembatasan percobaan login/registrasi/cek status, session cookie `HttpOnly` + `SameSite`,
akses halaman berdasarkan role, kuota kelas dicek di server.

> Jika memakai Nginx, `.htaccess` tidak berlaku. Tambahkan `location /uploads { deny all; }`.

## Yang tidak dikerjakan (di luar fokus tugas upload)

Role pengajar, admin cabang, dan keuangan; presensi; nilai; materi; pengumuman; laporan PDF/Excel; notifikasi email/WhatsApp.
Di tabel `member_cards`, kolom `qr_code` dan `pdf_card` dari spesifikasi tidak dipakai karena QR dibuat langsung di browser
dan PDF dibuat lewat fitur cetak browser (Save as PDF).
