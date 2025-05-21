<?php
declare(strict_types=1);

function drawMessage($message, $currentUserId) {
    $class = $message['sender_id'] == $currentUserId ? 'sent' : 'received';
    $content = htmlspecialchars($message['content']);
    $time = htmlspecialchars($message['sent_at']);
    return "<div class='message $class'><div>$content</div><small>$time</small></div>";
}
?>

<?php function drawChat($messages, $currentUserId, $chatWithId, $recentChats){ ?>
    <head>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/chat.css">
    </head>
    <body>
    <div class="chat-page">
    <aside class="chat-sidebar">
        <h2>Recent Chats</h2>
        <ul>
        <?php foreach ($recentChats as $chat): ?>
            <li class="<?= $chat['id'] == $chatWithId ? 'active' : '' ?>">
                <a href="chat.php?chat_with=<?= $chat['id'] ?>">
                    <strong><?= htmlspecialchars($chat['name']) ?></strong>
                    <small>@<?= htmlspecialchars($chat['username']) ?></small>
                    <?php if (!empty($chat['last_message_content'])): ?>
                        <p class="last-message"><?= htmlspecialchars(substr($chat['last_message_content'], 0, 30)) ?>...</p>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
        </ul>
    </aside>
    <div class="chat-container">
        <?php $chatWithUser = User::get_user_by_id((int)$chatWithId); ?>
        <div class="chat-header">
            Chat with <?= htmlspecialchars($chatWithUser['name']) ?> (@<?= htmlspecialchars($chatWithUser['username']) ?>)
        </div>
        <div class="chat-messages" id="messages">
            <?php foreach ($messages as $msg): ?>
                <?= drawMessage($msg, $currentUserId); ?>
            <?php endforeach; ?>
        </div>
        <form method="POST" action="../actions/action_send_message.php" class="chat-form">
            <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($chatWithId) ?>">
            <div class="message-input-container">
                <textarea 
                    name="content" 
                    placeholder="Type your message here..." 
                    rows="1"
                    oninput="autoGrow(this)"
                    required
                ></textarea>
                <button type="submit" class="send-button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
    </div>
    <script src="../js/chat.js"></script>
    </body>
<?php }
