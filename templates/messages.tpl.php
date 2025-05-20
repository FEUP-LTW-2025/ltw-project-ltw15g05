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
    <?php  ?>
    <head>
        <link rel="stylesheet" href="../css/chat.css">
    </head>
    <body>
    <div class="chat-page">
    <aside class="chat-sidebar">
        <h2>Chats</h2>
        <ul>
        <?php foreach ($recentChats as $chat): ?>
            <li class="<?= $chat['id'] == $chatWithId ? 'active' : '' ?>">
            <a href="chat.php?chat_with=<?= $chat['id'] ?>">
                <?= htmlspecialchars($chat['username']) ?>
            </a>
            </li>
        <?php endforeach; ?>
        </ul>
    </aside>
    <div class="chat-container">
        <div class="chat-header">
            Chat with User #<?= htmlspecialchars($chatWithId) ?>
        </div>
        <div class="chat-messages" id="messages">
            <?php foreach ($messages as $msg): ?>
                <?= drawMessage($msg, $currentUserId); ?>
            <?php endforeach; ?>
        </div>
        <form method="POST" action="../actions/action_send_message.php" class="chat-form">
            <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($chatWithId) ?>">
            <textarea name="content" required></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
    </div>
    </body>
<?php }
