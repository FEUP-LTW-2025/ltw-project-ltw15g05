<?php
declare(strict_types=1);

require_once '../includes/session.php';
require_once '../includes/database.php';
require_once '../database/user.class.php';
require_once '../database/messages.class.php';
require_once '../templates/messages.tpl.php';
$session = Session::getInstance();
$userData = $session->getUser();

if (!$userData) {
    header('Location: form_login.php');
    exit();
}
$currentUserId = $_SESSION['user_id'];
$chatWithId = $_GET['user'] ?? null;

if (!$chatWithId) {
    die('Select a user to chat with.');
}

$messages = Messages::getMessagesBetween($currentUserId, $chatWithId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat</title>
    <style>
        body { font-family: Arial; }
        .chat-box { width: 400px; margin: auto; border: 1px solid #ccc; padding: 10px; }
        .message { padding: 5px; margin: 5px 0; border-radius: 5px; }
        .sent { background: #dcf8c6; text-align: right; }
        .received { background: #f1f0f0; text-align: left; }
    </style>
</head>
<body>
    <div class="chat-box">
        <h3>Chat with User #<?= htmlspecialchars($chatWithId) ?></h3>
        <div id="messages">
            <?php foreach ($messages as $msg): ?>
                <?= drawMessage($msg, $currentUserId); ?>
            <?php endforeach; ?>
        </div>
        <form method="POST" action="../actions/action_send_message.php">
            <input type="hidden" name="receiver_id" value="<?= htmlspecialchars($chatWithId) ?>">
            <textarea name="content" required style="width:100%;"></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>
<?php
require_once '../includes/session.php';
$session = Session::getInstance();
$currentUserId = $session->getUserId();
echo "You are logged in as user #$currentUserId";
?>
