<?php
require __DIR__ . '/vendor/autoload.php';

use OTPHP\TOTP;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Generate TOTP
$totp = TOTP::create();
$totp->setLabel('user@example.com');
$totp->setIssuer('MyApp');
$secret = $totp->getSecret();

echo "Secret Key: $secret\n";

// Generate provisioning URI
$uri = $totp->getProvisioningUri();

// Generate QR code (versi 4.x)
$qrCode = new QrCode($uri);
$qrCode->setSize(200);
$qrCode->setMargin(10);

$writer = new PngWriter();
$result = $writer->write($qrCode);

// Simpan QR code ke file
$result->saveToFile('qrcode.png');

echo "QR Code tersimpan di qrcode.png\n";
