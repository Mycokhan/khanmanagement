<?php

$host = "mysql-2e04885e-michaelhaule689-1f49.j.aivencloud.com";
$dbname = "defaultdb";
$username = "avnadmin";
$password = "AVNS_3C7MnuVLriS-XVorkXq";
$port = "25263"; // Port maalumu kutoka Aiven iliyotajwa kwenye picha yako

try {
    // Tumeongeza ;port=$port kwenye DSN string hapa chini
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;port=$port;charset=utf8",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die("Connection Failed: " . $e->getMessage());

}

?>
