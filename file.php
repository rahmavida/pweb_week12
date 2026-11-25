<?php
// file.php : melayani foto dan bukti pembayaran.
// Folder uploads/ ditutup dari akses langsung; file hanya keluar lewat sini setelah cek hak akses.
require 'config.php';
require_login();

$id    = (int)($_GET['id'] ?? 0);
$jenis = ($_GET['jenis'] ?? '') === 'bukti' ? 'bukti' : 'foto';
$kolom = $jenis === 'bukti' ? 'payment_proof' : 'photo';

$stmt = $db->prepare("SELECT user_id, $kolom AS berkas FROM students WHERE student_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

$u = current_user();
if (!$row || !$row['berkas'] || (!is_admin() && (int)$row['user_id'] !== (int)$u['id'])) {
    http_response_code(404);
    exit('File tidak ditemukan');
}

if (!preg_match('/^[a-f0-9]{32}\.(jpg|png|pdf)$/', $row['berkas'])) {
    http_response_code(404);
    exit('File tidak valid');
}

$path = upload_dir($jenis) . $row['berkas'];
if (!is_file($path)) {
    http_response_code(404);
    exit('File tidak tersedia');
}

$mime = (new finfo(FILEINFO_MIME_TYPE))->file($path);
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
header('Content-Disposition: inline; filename="' . $jenis . '-' . $id . '.' . pathinfo($path, PATHINFO_EXTENSION) . '"');
header('Cache-Control: private, max-age=0, no-cache');
readfile($path);
exit;
