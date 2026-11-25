<?php
require 'config.php';
page_start('Beranda', 'index.php');

$stat = $db->query("SELECT
        (SELECT COUNT(*) FROM branches) AS cabang,
        (SELECT COUNT(*) FROM classes WHERE is_active = 1) AS kelas,
        (SELECT COUNT(*) FROM students WHERE status = 'Diterima') AS siswa")->fetch_assoc();
?>

<section class="hero">
    <p>PENDAFTARAN SISWA BARU</p>
    <h1>BIMBINGKU</h1>
    <p>Sistem manajemen kursus dan bimbingan belajar. Daftar online, unggah foto, dan dapatkan kartu member digital setelah diterima.</p>
    <div class="actions" style="justify-content:center;">
        <a class="btn" href="register.php">Daftar Sekarang</a>
        <a class="btn btn-light" href="cek-status.php">Cek Status Pendaftaran</a>
    </div>
</section>

<section class="section">
    <h2>BIMBINGKU dalam Angka</h2>
    <div class="stats">
        <div class="stat"><strong><?= (int)$stat['cabang'] ?></strong>Cabang</div>
        <div class="stat"><strong><?= (int)$stat['kelas'] ?></strong>Kelas aktif</div>
        <div class="stat"><strong><?= (int)$stat['siswa'] ?></strong>Siswa diterima</div>
    </div>
</section>

<section class="section">
    <h2>Menu Utama</h2>
    <div class="grid">
        <a class="card feature" href="profil.php"><h3>Profil</h3><p>Kenali BIMBINGKU dan alur pendaftaran.</p></a>
        <a class="card feature" href="kelas.php"><h3>Kelas &amp; Cabang</h3><p>Jadwal, biaya, dan sisa kuota.</p></a>
        <a class="card feature" href="register.php"><h3>Pendaftaran</h3><p>Buat akun dan isi formulir online.</p></a>
        <a class="card feature" href="cek-status.php"><h3>Cek Status</h3><p>Pantau hasil verifikasi pendaftaranmu.</p></a>
    </div>
</section>

<?php page_end(); ?>
