<?php
require_once 'admin/includes/db_connect.php';

$username = 'admin';
$password = 'admin123';
$email = 'elonberisha1999@gmail.com'; // Email yt i adminit

// Hash i ri i saktë
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Fshijmë userin e vjetër nëse ekziston (për të shmangur dublikimet ose gabimet)
    $pdo->exec("DELETE FROM users WHERE username = '$username'");

    // Fusim userin e ri
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (:u, :p, :e, 'admin')");
    $stmt->execute([
        'u' => $username,
        'p' => $hash,
        'e' => $email
    ]);

    echo "<h1>Sukses!</h1>";
    echo "<p>Përdoruesi <b>$username</b> u rivendos në MySQL.</p>";
    echo "<p>Fjalëkalimi: <b>$password</b></p>";
    echo "<p>Email: <b>$email</b></p>";
    echo "<br><a href='admin/login.php'>Shko tek Login</a>";

} catch (PDOException $e) {
    echo "<h1>Gabim!</h1>" . $e->getMessage();
}
?>
