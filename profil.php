<?php
require 'config.php';
page_start('Profil', 'profil.php');
?>

<section class="hero">
    <h1>Profil BIMBINGKU</h1>
    <p>Sistem manajemen kursus dan bimbingan belajar yang mempertemukan siswa, pengajar, dan admin cabang dalam satu platform.</p>
</section>

<section class="section">
    <h2>Tentang Kami</h2>
    <p>BIMBINGKU adalah lembaga bimbingan belajar simulasi untuk proyek pembelajaran. Seluruh data cabang, kelas, dan pendaftar di dalamnya adalah data contoh.</p>

    <h2>Visi</h2>
    <p>Menjadi bimbingan belajar yang mudah diakses, terukur, dan berfokus pada perkembangan setiap siswa.</p>

    <h2>Misi</h2>
    <ul>
        <li>Menyediakan pendaftaran online yang cepat dan transparan.</li>
        <li>Menyelenggarakan kelas terjadwal di setiap cabang.</li>
        <li>Memberikan identitas member digital bagi setiap siswa yang diterima.</li>
    </ul>
</section>

<section class="section">
    <h2>Alur Pendaftaran</h2>
    <div class="grid">
        <div class="card"><h3>1. Buat Akun</h3><p>Daftar dengan nama, email, dan password.</p></div>
        <div class="card"><h3>2. Isi Formulir</h3><p>Lengkapi biodata, pilih cabang dan kelas, unggah foto.</p></div>
        <div class="card"><h3>3. Verifikasi</h3><p>Admin memeriksa data dan bukti pembayaran.</p></div>
        <div class="card"><h3>4. Kartu Member</h3><p>Setelah diterima, kartu member dengan QR Code terbit otomatis.</p></div>
    </div>
</section>

<?php page_end(); ?>
