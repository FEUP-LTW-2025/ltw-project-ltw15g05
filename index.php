<?php
declare(strict_types=1);

$request_uri = $_SERVER['REQUEST_URI'];
$uri_path = parse_url($request_uri, PHP_URL_PATH);
if ($uri_path == '/' || $uri_path == '/index.php') {
    require_once(__DIR__ . '/includes/session.php');

    $session = Session::getInstance();
    $userData = $session->getUser();

    if ($userData !== null) { 
        header('Location: pages/main.php');
        exit();
    }

    require_once(__DIR__ . '/templates/common.tpl.php');
    require_once(__DIR__ . '/templates/index.tpl.php');

    drawFrontPage();
    exit;
}

$file_path = __DIR__ . $uri_path;
if (file_exists($file_path)) {
    $extension = pathinfo($file_path, PATHINFO_EXTENSION);
    
    if (strtolower($extension) === 'php') {
        return false;
    } 
    elseif (!is_dir($file_path)) {
        $mime_type = mime_content_type($file_path);
        header('Content-Type: ' . $mime_type);
        readfile($file_path);
        exit;
    }
}
?>