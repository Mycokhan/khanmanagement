<?php

$host = "localhost";
$dbname = "messaging_system";
$username = "root";
$password = "mycokhan";

try {

    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    die("Connection Failed: " . $e->getMessage());

}

?>