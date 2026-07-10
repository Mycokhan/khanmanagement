<?php

$host = "mysql-2e04885e-michaelhaule689-1f49.j.aivencloud.com";
$dbname = "defaultdb";
$username = "avnadmin";
$password = "AVNS_3C7MnuVLriS-XVorkXq";
$port = "25263";

try {
    // Imerekebishwa hapa: protocol=TCP inaiambia PHP isitafute 'file or directory' ya local sock
    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8;protocol=TCP",
        $username,
        $password
    );
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Connection Failed: " . $e->getMessage());
}
?>
