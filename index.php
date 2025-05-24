<?php
declare(strict_types=1);

// Built-in PHP server routing script
$request_uri = $_SERVER['REQUEST_URI'];
$uri_path = parse_url($request_uri, PHP_URL_PATH);

// Handle root or index.php requests
if ($uri_path == '/' || $uri_path == '/index.php') {
    require_once(__DIR__ . '/includes/session.php');

    $session = Session::getInstance();
    $userData = $session->getUser();

    if ($userData !== null) { // Check if user data exists
        header('Location: pages/main.php');
        exit();
    }

    require_once(__DIR__ . '/templates/common.tpl.php');
    require_once(__DIR__ . '/templates/index.tpl.php');

    drawFrontPage();
    exit;
}

// For other requests
$file_path = __DIR__ . $uri_path;
if (file_exists($file_path)) {
    // Check if this is a PHP file
    $extension = pathinfo($file_path, PATHINFO_EXTENSION);
    
    if (strtolower($extension) === 'php') {
        // For PHP files, we need to tell the built-in server to process them normally
        return false; // This special return value tells PHP's built-in server to process the request normally
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