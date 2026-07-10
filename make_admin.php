<?php
require_once 'connect.php';

try {
    // Query ya kubadilisha role kuwa admin
    $sql = "UPDATE users SET role = 'admin' WHERE id = 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    echo "✔ Safi sana! Mtumiaji mwenye ID namba 1 sasa hivi amekuwa ADMIN mtandaoni!";
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
