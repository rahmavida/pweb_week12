<?php
require 'config.php';

if (current_user()) {
    go(home_url());
}

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pw    = $_POST['password'] ?? '';

    if (!rate_limit($db, 'register:' . ip_klien(), 10, 60)) {
        $err = 'Terlalu banyak percobaan. Coba lagi nanti.';
    } elseif ($nama === '' || mb_strlen($nama) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $err = 'Nama atau email tidak valid.';
    } elseif (
        strlen($pw) < 8 || strlen($pw) > 72 ||
        !preg_match('/[A-Za-z]/', $pw) || !preg_match('/[0-9]/', $pw)
    ) {
        $err = 'Password 8–72 karakter dan harus mengandung huruf serta angka.';
    } else {
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        try {
            $stmt = $db->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'siswa')");
            $stmt->bind_param('sss', $nama, $email, $hash);
            $stmt->execute();

            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'                   => (int)$db->insert_id,
                'nama'                 => $nama,
                'email'                => $email,
                'role'                 => 'siswa',
                'must_change_password' => 0,
            ];
            audit($db, 'register', 'Akun siswa dibuat');
            flash('success', 'Akun berhasil dibuat. Lengkapi formulir pendaftaran di bawah.');
            go('siswa/form.php');
        } catch (mysqli_sql_exception $e) {
            $err = ($e->getCode() === 1062) ? 'Email sudah digunakan.' : 'Registrasi gagal.';
        }
    }
}

page_start('Buat Akun', 'register.php');
?>

<section class="section" style="max-width:600px; margin:auto;">
    <h2>Buat Akun Siswa</h2>

    <?php if ($err): ?>
        <div class="alert error"><?= e($err) ?></div>
    <?php endif; ?>

    <form method="post">
        <?= csrf_field() ?>
        <div class="field">
            <label>Nama lengkap</label>
            <input name="nama" maxlength="100" required value="<?= e($_POST['nama'] ?? '') ?>">
        </div>
        <div class="field">
            <label>Email</label>
            <input type="email" name="email" maxlength="100" required value="<?= e($_POST['email'] ?? '') ?>">
        </div>
        <div class="field">
            <label>Password</label>
            <input type="password" name="password" minlength="8" maxlength="72" required>
            <small>8–72 karakter, mengandung huruf dan angka.</small>
        </div>
        <button>Daftar Akun</button>
    </form>

    <p>Sudah punya akun? <a href="login.php">Masuk</a></p>
</section>

<?php page_end(); ?>
