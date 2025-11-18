<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $users = json_decode(file_get_contents('users.json'), true);
    $user = null;
    foreach ($users as $u) {
        if ($u['username'] === $username) {
            $user = $u;
            break;
        }
    }

    if (!$user || !password_verify($password, $user['password'])) {
        echo "<p>Username atau password salah.</p>";
        exit;
    }

    // Password benar, simpan username ke session untuk 2FA
    $_SESSION['2fa_user'] = $username;
    header('Location: verify2fa.php');
    exit;
}
?>

<form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" required><br>
    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>
