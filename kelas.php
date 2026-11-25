<?php
require 'config.php';
page_start('Kelas & Cabang', 'kelas.php');

$cabang = $db->query('SELECT * FROM branches ORDER BY branch_name')->fetch_all(MYSQLI_ASSOC);
$kelas  = $db->query("SELECT c.*,
                             (SELECT COUNT(*) FROM students s WHERE s.class_id = c.class_id AND s.status = 'Diterima') AS diterima
                      FROM classes c
                      WHERE c.is_active = 1
                      ORDER BY c.class_name")->fetch_all(MYSQLI_ASSOC);
?>

<section class="hero">
    <h1>Kelas &amp; Cabang</h1>
    <p>Pilih cabang terdekat dan kelas yang sesuai kebutuhan belajarmu.</p>
</section>

<?php foreach ($cabang as $b): ?>
    <section class="section">
        <h2><?= e($b['branch_name']) ?></h2>
        <p class="muted"><?= e($b['address']) ?> &middot; Telp <?= e($b['phone']) ?> &middot; <?= e($b['email']) ?></p>
        <div class="grid">
            <?php $ada = false; foreach ($kelas as $c): if ((int)$c['branch_id'] !== (int)$b['branch_id']) continue; $ada = true; ?>
                <div class="card">
                    <h3><?= e($c['class_name']) ?></h3>
                    <p><?= e($c['schedule_day']) ?> &middot; <?= e($c['schedule_time']) ?></p>
                    <p>Biaya <?= rupiah($c['fee']) ?></p>
                    <p>Kuota <?= (int)$c['quota'] ?> &middot; Sisa <?= max(0, (int)$c['quota'] - (int)$c['diterima']) ?></p>
                </div>
            <?php endforeach; ?>
            <?php if (!$ada): ?><p class="muted">Belum ada kelas di cabang ini.</p><?php endif; ?>
        </div>
    </section>
<?php endforeach; ?>

<?php page_end(); ?>
