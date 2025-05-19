<?php
declare(strict_types=1);

function drawMessage($message, $currentUserId) {
    $class = $message['sender_id'] == $currentUserId ? 'sent' : 'received';
    $content = htmlspecialchars($message['content']);
    $time = htmlspecialchars($message['sent_at']);
    return "<div class='message $class'><div>$content</div><small>$time</small></div>";
}
