<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan');
}

if (current_user()) {
    audit($db, 'logout', 'Logout');
}

$_SESSION = [];
session_regenerate_id(true);

go('index.php');
