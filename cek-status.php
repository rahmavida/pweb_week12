<?php
require 'config.php';

$res = null;
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no  = strtoupper(trim($_POST['reg_number'] ?? ''));
    $tgl = $_POST['birth_date'] ?? '';

    if (!rate_limit($db, 'status:' . ip_klien(), 15, 10)) {
        $err = 'Terlalu banyak permintaan. Coba lagi nanti.';
    } else {
        $stmt = $db->prepare('SELECT s.name, s.status, s.admin_note, c.class_name, b.branch_name
                              FROM students s
                              JOIN classes c ON c.class_id = s.class_id
                              JOIN branches b ON b.branch_id = s.branch_id
                              WHERE s.reg_number = ? AND s.birth_date = ?');
        $stmt->bind_param('ss', $no, $tgl);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if (!$res) {
            $err = 'Data tidak ditemukan. Periksa nomor registrasi dan tanggal lahir.';
        }
    }
}

page_start('Cek Status', 'cek-status.php');
?>

<section class="section" style="max-width:650px; margin:auto;">
    <h2>Cek Status Pendaftaran</h2>
    <p class="muted">Masukkan nomor registrasi dan tanggal lahir. Nama akan ditampilkan secara tersamar.</p>

    <?php if ($err): ?>
        <div class="alert error"><?= e($err) ?></div>
    <?php endif; ?>

    <form method="post">
        <?= csrf_field() ?>
        <div class="field">
            <label>Nomor registrasi</label>
            <input name="reg_number" placeholder="REG-2026-00001" required value="<?= e($_POST['reg_number'] ?? '') ?>">
        </div>
        <div class="field">
            <label>Tanggal lahir</label>
            <input type="date" name="birth_date" required>
        </div>
        <button>Cek Status</button>
    </form>

    <?php if ($res): ?>
        <?php
        $parts  = explode(' ', trim($res['name']));
        $masked = mb_substr($parts[0], 0, 1) . '***';
        if (count($parts) > 1) {
            $masked .= ' ' . mb_substr($parts[1], 0, 1) . '*****';
        }
        ?>
        <div class="notice">
            <p>Nama: <?= e($masked) ?></p>
            <p>Status: <?= status_badge($res['status']) ?></p>
            <p>Kelas: <?= e($res['class_name']) ?> (<?= e($res['branch_name']) ?>)</p>
            <?php if ($res['admin_note']): ?>
                <p>Catatan: <?= e($res['admin_note']) ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<?php page_end(); ?>
