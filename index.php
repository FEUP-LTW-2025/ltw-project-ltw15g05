<?php
declare(strict_types=1);

// Check if the request is directly for index.php
$request_uri = $_SERVER['REQUEST_URI'];
    if ($request_uri == '/' || $request_uri == '/index.php') {
        require_once(__DIR__ . '/includes/session.php');

    $session = Session::getInstance();
    $userData = $session->getUser();

    if ($userData) {
        header('Location: pages/main.php');
        exit();
    }

    require_once(__DIR__ . '/templates/common.tpl.php');
    require_once(__DIR__ . '/templates/index.tpl.php');

    drawFrontPage();
    drawFooter();
    exit;
}

// For other requests
$file_path = __DIR__ . parse_url($request_uri, PHP_URL_PATH);
if (file_exists($file_path)) {
    // Check if this is a PHP file
    $extension = pathinfo($file_path, PATHINFO_EXTENSION);
    
    if (strtolower($extension) === 'php') {
        // For PHP files, include them to execute the code
        // This is important: return false to indicate the router handled this request
        return false;
    } 
    elseif (!is_dir($file_path)) {
        // For non-PHP files, serve the content
        $mime_type = mime_content_type($file_path);
        header('Content-Type: ' . $mime_type);
        readfile($file_path);
        exit;
    }
}
?>