<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
use OTPHP\TOTP;

if (!isset($_SESSION['2fa_user'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['2fa_user'];
$users = json_decode(file_get_contents('users.json'), true);
$user = null;
foreach ($users as $u) {
    if ($u['username'] === $username) {
        $user = $u;
        break;
    }
}

if (!$user) {
    echo "<p>User tidak ditemukan.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $totp = TOTP::create($user['secret']);

    if ($totp->verify($code)) {
        echo "<h3>Login berhasil! Selamat datang, $username</h3>";
        session_destroy();
        exit;
    } else {
        echo "<p>Kode 2FA salah. Coba lagi.</p>";
    }
}
?>

<form method="post">
    <label>Masukkan kode 6 digit Google Authenticator:</label><br>
    <input type="text" name="code" required><br><br>
    <button type="submit">Verifikasi 2FA</button>
</form>
