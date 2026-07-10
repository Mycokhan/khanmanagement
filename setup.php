<?php
require_once 'connect.php';

try {
    // Kuzuia shida ya Foreign Key checks wakati tunaunda table zinazotegemeana
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // 1. TABLE: users
    $sql_users = "CREATE TABLE IF NOT EXISTS `users` (
      `id` int NOT NULL AUTO_INCREMENT,
      `full_name` varchar(100) NOT NULL,
      `email` varchar(100) NOT NULL,
      `password` varchar(255) NOT NULL,
      `role` enum('admin','user') NOT NULL DEFAULT 'user',
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      `phone_number` varchar(20) DEFAULT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $conn->exec($sql_users);
    echo "✔ 1. Table 'users' imetengenezwa!<br>";

    // 2. TABLE: groups (Neno 'groups' limefungwa na backticks kuzuia syntax error)
    $sql_groups = "CREATE TABLE IF NOT EXISTS `groups` (
      `id` int NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `description` text DEFAULT NULL,
      `created_by` int DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `created_by` (`created_by`),
      CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $conn->exec($sql_groups);
    echo "✔ 2. Table 'groups' imetengenezwa!<br>";

    // 3. TABLE: group_members
    $sql_group_members = "CREATE TABLE IF NOT EXISTS `group_members` (
      `id` int NOT NULL AUTO_INCREMENT,
      `group_id` int NOT NULL,
      `user_id` int NOT NULL,
      `joined_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `unique_member` (`group_id`,`user_id`),
      KEY `user_id` (`user_id`),
      CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
      CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $conn->exec($sql_group_members);
    echo "✔ 3. Table 'group_members' imetengenezwa!<br>";

    // 4. TABLE: messages
    $sql_messages = "CREATE TABLE IF NOT EXISTS `messages` (
      `id` int NOT NULL AUTO_INCREMENT,
      `sender_id` int NOT NULL,
      `receiver_id` int DEFAULT NULL,
      `title` varchar(255) NOT NULL,
      `message` text NOT NULL,
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `sender_id` (`sender_id`),
      KEY `receiver_id` (`receiver_id`),
      CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
      CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $conn->exec($sql_messages);
    echo "✔ 4. Table 'messages' imetengenezwa!<br>";

    // 5. TABLE: message_reads
    $sql_message_reads = "CREATE TABLE IF NOT EXISTS `message_reads` (
      `id` int NOT NULL AUTO_INCREMENT,
      `message_id` int NOT NULL,
      `user_id` int NOT NULL,
      `read_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `message_id` (`message_id`,`user_id`),
      KEY `user_id` (`user_id`),
      CONSTRAINT `message_reads_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
      CONSTRAINT `message_reads_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $conn->exec($sql_message_reads);
    echo "✔ 5. Table 'message_reads' imetengenezwa!<br>";

    // 6. TABLE: direct_messages
    $sql_direct_messages = "CREATE TABLE IF NOT EXISTS `direct_messages` (
      `id` int NOT NULL AUTO_INCREMENT,
      `sender_id` int NOT NULL,
      `receiver_id` int NOT NULL,
      `message` text NOT NULL,
      `is_read` tinyint(1) DEFAULT '0',
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `sender_id` (`sender_id`),
      KEY `receiver_id` (`receiver_id`),
      CONSTRAINT `direct_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
      CONSTRAINT `direct_messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $conn->exec($sql_direct_messages);
    echo "✔ 6. Table 'direct_messages' imetengenezwa!<br>";

    // 7. TABLE: group_messages (Imeongezewa group_id ili iungane na table ya groups rasmi)
    $sql_group_messages = "CREATE TABLE IF NOT EXISTS `group_messages` (
      `id` int NOT NULL AUTO_INCREMENT,
      `group_id` int NOT NULL,
      `sender_id` int NOT NULL,
      `message` text NOT NULL,
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `group_id` (`group_id`),
      KEY `sender_id` (`sender_id`),
      CONSTRAINT `group_messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
      CONSTRAINT `group_messages_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $conn->exec($sql_group_messages);
    echo "✔ 7. Table 'group_messages' imetengenezwa!<br>";

    // Kurudisha ulinzi wa Foreign Key checks baada ya kumaliza
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1;");

    echo "<br>🚀 <b>KAZI IMEISHA TAYARI!</b> Table zote 7 zimetengenezwa kwa muundo ule ule wa kompyuta yako.";

} catch (PDOException $e) {
    die("Error wakati wa kuunda table: " . $e->getMessage());
}
?>
