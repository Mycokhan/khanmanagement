<?php
ob_start(); // Inazuia kosa la "Headers already sent" kwa kuhifadhi output kwanza
session_start();

require "connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = (int) ($_SESSION['user_id'] ?? 0);
$current_user_name = $_SESSION['full_name'] ?? '';
$message = "";
$error = "";
$db_error = "";

if ($current_user_id > 0) {
    try {
        $stmt = $conn->prepare("SELECT full_name FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$current_user_id]);
        $current_user_row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($current_user_row && !empty($current_user_row['full_name'])) {
            $current_user_name = $current_user_row['full_name'];
            $_SESSION['full_name'] = $current_user_name;
        }
    } catch (PDOException $e) {
        $db_error = $e->getMessage();
    }
}

if ($current_user_name === '') {
    $current_user_name = ($_SESSION['role'] ?? 'user') === 'admin' ? 'Admin' : 'User';
}

try {
    $conn->exec("CREATE TABLE IF NOT EXISTS `groups` (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
    )");

    $conn->exec("CREATE TABLE IF NOT EXISTS group_members (
        id INT PRIMARY KEY AUTO_INCREMENT,
        group_id INT NOT NULL,
        user_id INT NOT NULL,
        joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_member (group_id, user_id),
        FOREIGN KEY (group_id) REFERENCES `groups`(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $conn->exec("CREATE TABLE IF NOT EXISTS group_messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        group_id INT NOT NULL,
        sender_id INT NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES `groups`(id) ON DELETE CASCADE,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_group (group_id),
        INDEX idx_created (created_at)
    )");

    $conn->exec("CREATE TABLE IF NOT EXISTS direct_messages (
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
    )");
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

$selected_type = $_POST['conversation_type'] ?? ($_GET['conversation_type'] ?? 'direct');
$selected_group_id = isset($_POST['group_id']) ? (int) $_POST['group_id'] : (isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0);
$selected_user_id = isset($_POST['receiver_id']) ? (int) $_POST['receiver_id'] : (isset($_GET['receiver_id']) ? (int) $_GET['receiver_id'] : 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_group'])) {
        $group_name = trim($_POST['group_name'] ?? '');
        $group_description = trim($_POST['group_description'] ?? '');
        $member_ids = $_POST['member_ids'] ?? [];

        if ($group_name === '') {
            $error = 'Please enter a group name.';
        } else {
            $member_ids = array_filter(array_map('intval', (array) $member_ids));
            $member_ids[] = $current_user_id;
            $member_ids = array_unique($member_ids);

            try {
                $stmt = $conn->prepare("INSERT INTO `groups` (name, description, created_by) VALUES (?, ?, ?)");
                $stmt->execute([$group_name, $group_description, $current_user_id]);
                $group_id = (int) $conn->lastInsertId();

                $stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
                foreach ($member_ids as $member_id) {
                    $stmt->execute([$group_id, $member_id]);
                }

                $message = 'Group created successfully.';
                $selected_type = 'group';
                $selected_group_id = $group_id;
            } catch (PDOException $e) {
                $error = 'Unable to create group right now.';
            }
        }
    } elseif (isset($_POST['send_message'])) {
        $message_text = trim($_POST['message_text'] ?? '');

        if ($message_text === '') {
            $error = 'Message cannot be empty.';
        } else {
            try {
                if ($selected_type === 'group') {
                    if ($selected_group_id <= 0) {
                        $error = 'Please select a group.';
                    } else {
                        $check = $conn->prepare("SELECT 1 FROM group_members WHERE group_id = ? AND user_id = ? LIMIT 1");
                        $check->execute([$selected_group_id, $current_user_id]);
                        if (!$check->fetch()) {
                            $error = 'You are not a member of that group.';
                        } else {
                            $stmt = $conn->prepare("INSERT INTO group_messages (group_id, sender_id, message) VALUES (?, ?, ?)");
                            $stmt->execute([$selected_group_id, $current_user_id, $message_text]);
                            header("Location: chatbox.php?conversation_type=group&group_id=" . $selected_group_id);
                            exit();
                        }
                    }
                } else {
                    if ($selected_user_id <= 0) {
                        $error = 'Please select a recipient.';
                    } else {
                        $stmt = $conn->prepare("INSERT INTO direct_messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
                        $stmt->execute([$current_user_id, $selected_user_id, $message_text]);
                        header("Location: chatbox.php?conversation_type=direct&receiver_id=" . $selected_user_id);
                        exit();
                    }
                }
            } catch (PDOException $e) {
                $error = 'Unable to save your message.';
            }
        }
    }
}

$stmt = $conn->prepare("SELECT id, full_name FROM users WHERE id != ? ORDER BY full_name ASC");
$stmt->execute([$current_user_id]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT g.id, g.name, g.description FROM `groups` g JOIN group_members gm ON gm.group_id = g.id WHERE gm.user_id = ? ORDER BY g.name ASC");
$stmt->execute([$current_user_id]);
$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($selected_type === 'group' && $selected_group_id <= 0 && !empty($groups)) {
    $selected_group_id = (int) $groups[0]['id'];
}

if ($selected_type !== 'group') {
    $selected_user_id = $selected_user_id > 0 ? $selected_user_id : 0;
}

$messages = [];
if ($selected_type === 'group' && $selected_group_id > 0) {
    $stmt = $conn->prepare("SELECT gm.id, gm.message, gm.created_at, gm.sender_id, u.full_name AS sender_name FROM group_messages gm JOIN users u ON u.id = gm.sender_id WHERE gm.group_id = ? ORDER BY gm.created_at ASC");
    $stmt->execute([$selected_group_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($selected_type !== 'group' && $selected_user_id > 0) {
    $stmt = $conn->prepare("SELECT dm.id, dm.message, dm.created_at, dm.sender_id, dm.receiver_id, sender.full_name AS sender_name, receiver.full_name AS receiver_name FROM direct_messages dm JOIN users sender ON sender.id = dm.sender_id JOIN users receiver ON receiver.id = dm.receiver_id WHERE ((dm.sender_id = ? AND dm.receiver_id = ?) OR (dm.sender_id = ? AND dm.receiver_id = ?)) ORDER BY dm.created_at ASC");
    $stmt->execute([$current_user_id, $selected_user_id, $selected_user_id, $current_user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbox</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; padding: 20px; color: #222; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
        h2, h3 { color: #0d6efd; }
        .alert { padding: 10px 12px; border-radius: 8px; margin-bottom: 15px; }
        .alert-success { background: #d1e7dd; color: #0f5132; }
        .alert-danger { background: #f8d7da; color: #842029; }
        .panel { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .box { border: 1px solid #e5e7eb; border-radius: 10px; padding: 15px; background: #fcfdff; }
        select, input[type="text"], textarea { width: 100%; padding: 10px; margin-top: 6px; border: 1px solid #ced4da; border-radius: 6px; box-sizing: border-box; }
        button { background: #0d6efd; color: white; border: 0; padding: 10px 14px; border-radius: 6px; cursor: pointer; margin-top: 10px; }
        .chat-window { border: 1px solid #e5e7eb; border-radius: 10px; padding: 15px; min-height: 300px; max-height: 420px; overflow-y: auto; background: #f9fbff; }
        .bubble { padding: 10px 12px; border-radius: 10px; margin-bottom: 8px; max-width: 80%; }
        .bubble.self { background: #0d6efd; color: white; margin-left: auto; }
        .bubble.other { background: #e9f2ff; color: #222; }
        .meta { font-size: 12px; opacity: 0.75; margin-bottom: 4px; }
        .small { font-size: 13px; color: #6c757d; }
        
        .member-dropdown-container {
            border: 1px solid #ced4da;
            border-radius: 6px;
            max-height: 150px;
            overflow-y: auto;
            padding: 8px;
            background: #fff;
            margin-top: 6px;
        }
        .member-dropdown-container label {
            display: flex;
            align-items: center;
            padding: 6px;
            margin: 2px 0;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .member-dropdown-container label:hover {
            background: #f0f6ff;
        }
        .member-dropdown-container input[type="checkbox"],
        .member-dropdown-container input[type="radio"] {
            margin-right: 10px;
            width: auto;
        }
        .member-dropdown-container label.selected-user {
            background: #e9f2ff;
            font-weight: bold;
            border-left: 3px solid #0d6efd;
        }

        .status-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            background-color: #9ca3af;
            transition: background-color 0.3s ease;
        }
        .status-dot.online {
            background-color: #22c55e;
            box-shadow: 0 0 6px #22c55e;
        }

        .todashboard { margin-bottom: 20px; }
        .todashboard button { background: #198754; color: white; border: 0; padding: 10px 14px; border-radius: 6px; cursor: pointer; }
        .todashboard button a { color: white; text-decoration: none; }
        .todashboard button:hover { background: #157347; }
        @media (max-width: 760px) { .panel { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="todashboard">
        <button><a href="dashboard.php">🏠 Dashboard</a></button>
    </div>
<div class="container">
    <h2>💬 Group and Direct Messaging</h2>
    <p class="small">Welcome, <?php echo htmlspecialchars($current_user_name); ?>. Messages are end to end encrypted by mycokhan system.</p>

    <?php if ($message !== ''): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($db_error !== ''): ?>
        <div class="alert alert-danger">Database setup issue: <?php echo htmlspecialchars($db_error); ?></div>
    <?php endif; ?>

    <div class="panel">
        <div class="box">
            <h3>Switch conversation</h3>
            <form method="get" id="switch-type-form">
                <label>Conversation type</label>
                <select name="conversation_type" onchange="this.form.submit()">
                    <option value="direct" <?php echo $selected_type === 'direct' ? 'selected' : ''; ?>>Direct Message</option>
                    <option value="group" <?php echo $selected_type === 'group' ? 'selected' : ''; ?>>Group Message</option>
                </select>
            </form>
            <br>
            
            <?php if ($selected_type === 'group'): ?>
                <form method="get">
                    <input type="hidden" name="conversation_type" value="group">
                    <label>Group</label>
                    <select name="group_id" onchange="this.form.submit()">
                        <?php foreach ($groups as $group): ?>
                            <option value="<?php echo (int) $group['id']; ?>" <?php echo $selected_group_id === (int) $group['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($group['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php else: ?>
                <form method="get" id="recipient-form">
                    <input type="hidden" name="conversation_type" value="direct">
                    <label>Recipient (Select a member)</label>
                    <div class="member-dropdown-container">
                        <?php foreach ($users as $user): ?>
                            <?php $is_selected = ($selected_user_id === (int) $user['id']); ?>
                            <label class="<?php echo $is_selected ? 'selected-user' : ''; ?>">
                                <input type="radio" name="receiver_id" value="<?php echo (int) $user['id']; ?>" <?php echo $is_selected ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <span class="status-dot" data-user-id="<?php echo (int) $user['id']; ?>"></span>
                                <?php echo htmlspecialchars($user['full_name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <div class="box">
            <h3>Create a group</h3>
            <form method="post">
                <input type="hidden" name="create_group" value="1">
                <label>Group name</label>
                <input type="text" name="group_name" placeholder="e.g. Team Chat" required>
                <label>Description</label>
                <input type="text" name="group_description" placeholder="Optional description">
                <label>Select members</label>
                <div class="member-dropdown-container">
                    <?php foreach ($users as $user): ?>
                        <label>
                            <input type="checkbox" name="member_ids[]" value="<?php echo (int) $user['id']; ?>">
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit">Create group</button>
            </form>
        </div>
    </div>

    <div class="box">
        <h3><?php echo $selected_type === 'group' ? 'Group conversation' : 'Direct conversation'; ?></h3>
        <?php if ($selected_type !== 'group' && $selected_user_id <= 0): ?>
            <p class="small">Select a member from the list above to view and reply to their messages.</p>
        <?php else: ?>
            <div class="chat-window">
                <?php if (empty($messages)): ?>
                    <p class="small" id="no-messages-placeholder">No messages yet. Start the conversation below.</p>
                <?php else: ?>
                    <?php foreach ($messages as $row): ?>
                        <?php $is_self = ((int) $row['sender_id'] === $current_user_id); ?>
                        <div class="bubble <?php echo $is_self ? 'self' : 'other'; ?>" data-id="<?php echo (int) $row['id']; ?>">
                            <div class="meta">
                                <?php echo htmlspecialchars($is_self ? 'You' : $row['sender_name']); ?>
                                • <?php echo htmlspecialchars(date('M d, H:i', strtotime($row['created_at']))); ?>
                            </div>
                            <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <form method="post" id="chat-message-form" style="margin-top: 15px;">
                <input type="hidden" name="send_message" value="1">
                <input type="hidden" name="conversation_type" value="<?php echo htmlspecialchars($selected_type); ?>">
                <?php if ($selected_type === 'group'): ?>
                    <input type="hidden" name="group_id" value="<?php echo (int) $selected_group_id; ?>">
                <?php else: ?>
                    <input type="hidden" name="receiver_id" value="<?php echo (int) $selected_user_id; ?>">
                <?php endif; ?>
                <textarea name="message_text" rows="4" placeholder="Type your message here..." required></textarea>
                <button type="submit">Send message</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    let selectedType = '<?php echo htmlspecialchars($selected_type); ?>';
    let selectedUserId = <?php echo (int) $selected_user_id; ?>;
    let selectedGroupId = <?php echo (int) $selected_group_id; ?>;
    let currentUserId = <?php echo (int) $current_user_id; ?>;
    const chatWindow = document.querySelector('.chat-window');
    const messageForm = document.getElementById('chat-message-form');

    const socket = new WebSocket('wss://khanmanagement-1.onrender.com');

    socket.onopen = () => {
        console.log('Connected to WebSocket Server on Render directly!');
        socket.send(JSON.stringify({
            type: 'register',
            user_id: currentUserId
        }));
    };

    socket.onmessage = (event) => {
        try {
            const data = JSON.parse(event.data);

            if (data.type === 'online_status' || data.type === 'online_users') {
                updateOnlineStatus(data.users || data.onlineUserIds);
            }

            if (data.type === 'message') {
                let shouldDisplay = false;

                if (selectedType === 'group' && data.conversation_type === 'group' && parseInt(data.group_id) === selectedGroupId) {
                    shouldDisplay = true;
                } else if (selectedType === 'direct' && data.conversation_type === 'direct' && 
                          (parseInt(data.sender_id) === selectedUserId || parseInt(data.receiver_id) === selectedUserId)) {
                    shouldDisplay = true;
                }

                if (shouldDisplay) {
                    const placeholder = document.getElementById('no-messages-placeholder');
                    if (placeholder) { placeholder.remove(); }

                    const bubble = document.createElement('div');
                    const isSelf = parseInt(data.sender_id) === currentUserId;
                    
                    bubble.className = 'bubble ' + (isSelf ? 'self' : 'other');
                    bubble.innerHTML = `
                        <div class="meta">
                            ${isSelf ? 'You' : htmlspecialchars(data.sender_name)}
                            • ${data.time_formatted}
                        </div>
                        ${data.message_html}
                    `;
                    chatWindow.appendChild(bubble);
                    chatWindow.scrollTop = chatWindow.scrollHeight;
                }
            }
        } catch (e) {
            console.error("Error parsing message data:", e);
        }
    };

    function updateOnlineStatus(onlineUserIds) {
        if (!onlineUserIds || !Array.isArray(onlineUserIds)) return;
        const stringOnlineIds = onlineUserIds.map(id => String(id));

        document.querySelectorAll('.status-dot').forEach(dot => {
            const userId = dot.getAttribute('data-user-id');
            if (stringOnlineIds.includes(String(userId))) {
                dot.classList.add('online');
            } else {
                dot.classList.remove('online');
            }
        });
    }

    socket.onerror = (error) => { console.error('WebSocket Error:', error); };
    socket.onclose = () => { console.log('WebSocket disconnected.'); };

    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            // MABORESHO MAKUBWA: Kama WebSocket haipo OPEN, tunaruhusu fomu iende PHP ya kawaida!
            if (socket.readyState !== WebSocket.OPEN) {
                console.log('WebSocket is not connected. Sending message via standard PHP fallback...');
                return; // Inaruhusu fomu iji-submit kwa njia ya kawaida (Page reload)
            }

            // Kama WebSocket iko sawa, inatuma bila kurefresh page kama kawaida
            e.preventDefault(); 
            const textarea = this.querySelector('textarea[name="message_text"]');
            const messageText = textarea.value.trim();
            
            if (messageText !== '') {
                const now = new Date();
                const timeFormatted = now.toLocaleDateString('en-US', { month: 'short' }) + " " + 
                                     String(now.getDate()).padStart(2, '0') + ", " + 
                                     String(now.getHours()).padStart(2, '0') + ":" + 
                                     String(now.getMinutes()).padStart(2, '0');

                const payload = {
                    type: 'message',
                    conversation_type: selectedType,
                    sender_id: currentUserId,
                    sender_name: '<?php echo htmlspecialchars($current_user_name); ?>',
                    message_text: messageText,
                    message_html: messageText.replace(/\n/g, '<br>'), 
                    receiver_id: selectedUserId,
                    group_id: selectedGroupId,
                    time_formatted: timeFormatted
                };
                
                socket.send(JSON.stringify(payload));
                textarea.value = ''; 
                textarea.focus();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (chatWindow) { chatWindow.scrollTop = chatWindow.scrollHeight; }
    });

    function htmlspecialchars(string) {
        return string.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
</script>
</body>
</html>
