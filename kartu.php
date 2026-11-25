<?php
require 'config.php';
require_login();

$u = current_user();
if (is_admin()) {
    $s = siswa_by_id($db, (int)($_GET['id'] ?? 0));
} else {
    $s = siswa_by_user($db, $u['id']);
}
if (!$s) {
    http_response_code(404);
    exit('Data siswa tidak ditemukan.');
}
if ($s['status'] !== 'Diterima') {
    flash('warning', 'Kartu member hanya tersedia untuk siswa yang sudah diterima.');
    go(home_url());
}

// Kartu terbit otomatis saat status Diterima; pastikan barisnya ada
terbitkan_kartu($db, (int)$s['student_id']);
$stmt = $db->prepare('SELECT * FROM member_cards WHERE student_id = ?');
$stmt->bind_param('i', $s['student_id']);
$stmt->execute();
$kartu = $stmt->get_result()->fetch_assoc();

page_start('Kartu Member', 'kartu.php');
?>

<div class="no-print actions" style="margin-bottom:16px;">
    <button onclick="window.print()">Cetak / Simpan PDF</button>
    <a class="btn btn-light" href="<?= e(url(is_admin() ? 'admin/siswa-detail.php?id=' . (int)$s['student_id'] : 'siswa/index.php')) ?>">Kembali</a>
</div>

<div class="member-card">
    <div class="mc-head"><span><?= APP_NAME ?></span><small>KARTU MEMBER</small></div>
    <div class="mc-body">
        <img class="mc-photo" alt="Foto siswa" src="<?= e(url('file.php?jenis=foto&id=' . (int)$s['student_id'])) ?>">
        <div class="mc-info">
            <div class="mc-name"><?= e($s['name']) ?></div>
            <div>No. Kartu: <b><?= e($kartu['card_number']) ?></b></div>
            <div>ID Siswa: <?= (int)$s['student_id'] ?></div>
            <div>Kelas: <?= e($s['class_name']) ?></div>
            <div>Cabang: <?= e($s['branch_name']) ?></div>
            <div>Bergabung: <?= tgl($kartu['issue_date']) ?></div>
        </div>
    </div>
    <div class="mc-qr" id="qr"></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
// QR Code dibuat di browser dari nomor kartu, tanpa mengirim data ke layanan luar
new QRCode(document.getElementById('qr'), { text: <?= json_encode($kartu['card_number']) ?>, width: 92, height: 92 });
</script>

<?php page_end(); ?>
