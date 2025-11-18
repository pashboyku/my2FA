<?php
require __DIR__ . '/vendor/autoload.php';

use OTPHP\TOTP;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Cek form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate TOTP
    $totp = TOTP::create();
    $totp->setLabel($username);
    $totp->setIssuer('My2FA WebApp');
    $secret = $totp->getSecret();

    // Generate QR code
    $uri = $totp->getProvisioningUri();
    $qrCode = new QrCode($uri);
    $qrCode->setSize(200);
    $qrCode->setMargin(10);
    $writer = new PngWriter();
    $qrResult = $writer->write($qrCode);

    // Simpan QR code sebagai base64
    $dataUri = $qrResult->getDataUri();

    // Simpan user
    $users = json_decode(file_get_contents('users.json'), true) ?: [];
    $users[] = [
        'username' => $username,
        'password' => $hashedPassword,
        'secret' => $secret
    ];
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));

    // Tampilkan QR code + secret + auto redirect + tombol skip
    echo "<h3>User '$username' berhasil dibuat!</h3>";
    echo "<p>Scan QR code di bawah dengan Google Authenticator:</p>";
    echo "<img src='$dataUri' alt='QR Code'><br>";
    echo "<p>Secret Key: $secret</p>";
    echo "<p>Halaman ini akan kembali ke form register dalam <span id='countdown'>10</span> detik...</p>";
    echo '<button id="skipBtn">Lewati dan kembali</button>';

    echo <<<HTML
<script>
let seconds = 10;
let countdown = document.getElementById('countdown');
let interval = setInterval(() => {
    seconds--;
    countdown.textContent = seconds;
    if(seconds <= 0){
        clearInterval(interval);
        window.location.href = 'register.php';
    }
}, 1000);

// Tombol skip
document.getElementById('skipBtn').addEventListener('click', () => {
    clearInterval(interval);
    window.location.href = 'register.php';
});
</script>
HTML;
    exit;
}
?>

<form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" required><br>
    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>
    <button type="submit">Register</button>
</form>
