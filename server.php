<?php

if (! headers_sent() && function_exists('header_remove')) {
    header_remove('X-Powered-By');
}

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Serve static files from public/ with security headers so php artisan serve
// does not omit X-Content-Type-Options or expose X-Powered-By.
if ($uri !== '/' && file_exists($publicPath.$uri) && ! is_dir($publicPath.$uri)) {
    $filePath = realpath($publicPath.$uri);
    $publicRoot = realpath($publicPath);

    if ($filePath !== false && $publicRoot !== false && str_starts_with($filePath, $publicRoot.DIRECTORY_SEPARATOR)) {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css' => 'text/css; charset=utf-8',
            'js' => 'application/javascript; charset=utf-8',
            'txt' => 'text/plain; charset=utf-8',
            'ico' => 'image/x-icon',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'webp' => 'image/webp',
            'woff2' => 'font/woff2',
            'json' => 'application/json; charset=utf-8',
        ];

        header('Content-Type: '.($mimeTypes[$ext] ?? (mime_content_type($filePath) ?: 'application/octet-stream')));
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; object-src 'none'; frame-ancestors 'self'; form-action 'self'; base-uri 'self';");
        readfile($filePath);

        return true;
    }
}

$formattedDateTime = date('D M j H:i:s Y');

$requestMethod = $_SERVER['REQUEST_METHOD'];
$remoteAddress = $_SERVER['REMOTE_ADDR'].':'.$_SERVER['REMOTE_PORT'];

file_put_contents('php://stdout', "[$formattedDateTime] $remoteAddress [$requestMethod] URI: $uri\n");

require_once $publicPath.'/index.php';
