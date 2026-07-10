<?php

$host = "mysql-2e04885e-michaelhaule689-1f49.j.aivencloud.com";
$dbname = "defaultdb";
$username = "avnadmin";
$password = "AVNS_3C7MnuVLriS-XVorkXq";
$port = "25263"; 

try {
    // Tumeunganisha host na port kwa kutumia colona ($host:$port) kuzuia error ya Linux socket kwenye Render
    $conn = new PDO(
        "mysql:host=$host:$port;dbname=$dbname;charset=utf8",
        $username,
        $password
    );
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Connection Failed: " . $e->getMessage());
}
?>
