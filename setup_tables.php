<?php
// Database setup script to create necessary tables for group and direct messaging

include "connect.php";

try {
    // Create groups table
    $sql = "CREATE TABLE IF NOT EXISTS groups (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->exec($sql);
    echo "✓ Groups table created/verified<br>";

    // Create group_members table
    $sql = "CREATE TABLE IF NOT EXISTS group_members (
        id INT PRIMARY KEY AUTO_INCREMENT,
        group_id INT NOT NULL,
        user_id INT NOT NULL,
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_member (group_id, user_id),
        FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $conn->exec($sql);
    echo "✓ Group members table created/verified<br>";

    // Create group_messages table
    $sql = "CREATE TABLE IF NOT EXISTS group_messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        group_id INT NOT NULL,
        sender_id INT NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_group (group_id),
        INDEX idx_created (created_at)
    )";
    $conn->exec($sql);
    echo "✓ Group messages table created/verified<br>";

    // Create direct_messages table
    $sql = "CREATE TABLE IF NOT EXISTS direct_messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_receiver (receiver_id),
        INDEX idx_created (created_at)
    )";
    $conn->exec($sql);
    echo "✓ Direct messages table created/verified<br>";

    echo "<br><strong>✅ All tables set up successfully!</strong>";

} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
