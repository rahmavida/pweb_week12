<?php
require 'config.php';
require_login();

$u   = current_user();
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old     = $_POST['old'] ?? '';
    $new     = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    $stmt = $db->prepare('SELECT password FROM users WHERE id = ?');
    $stmt->bind_param('i', $u['id']);
    $stmt->execute();
    $hash = $stmt->get_result()->fetch_assoc()['password'];

    if (!password_verify($old, $hash)) {
        $err = 'Password lama salah.';
    } elseif (
        $new === $old || strlen($new) < 8 || strlen($new) > 72 ||
        !preg_match('/[A-Za-z]/', $new) || !preg_match('/[0-9]/', $new)
    ) {
        $err = 'Password baru harus berbeda, 8–72 karakter, mengandung huruf dan angka.';
    } elseif ($new !== $confirm) {
        $err = 'Konfirmasi password tidak sama.';
    } else {
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $db->prepare('UPDATE users SET password = ?, must_change_password = 0 WHERE id = ?');
        $stmt->bind_param('si', $newHash, $u['id']);
        $stmt->execute();

        $_SESSION['user']['must_change_password'] = 0;
        audit($db, 'password_changed', 'Password diganti');
        flash('success', 'Password berhasil diganti.');
        go(home_url());
    }
}

$fields = [
    'old'     => 'Password lama',
    'new'     => 'Password baru',
    'confirm' => 'Konfirmasi password baru',
];

page_start('Ganti Password', 'ganti-password.php');
?>

<section class="section" style="max-width:600px; margin:auto;">
    <h2>Ganti Password</h2>

    <?php if ($err): ?>
        <div class="alert error"><?= e($err) ?></div>
    <?php endif; ?>

    <form method="post">
        <?= csrf_field() ?>
        <?php foreach ($fields as $name => $label): ?>
            <div class="field">
                <label><?= e($label) ?></label>
                <input type="password" name="<?= e($name) ?>" required>
            </div>
        <?php endforeach; ?>
        <button>Simpan Password</button>
    </form>
</section>

<?php page_end(); ?>
