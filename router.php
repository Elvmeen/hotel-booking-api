<?php
/**
 * PHP Built-in Server Router for CodeIgniter 3
 *
 * Used only in local/Replit development:
 *   php -S 0.0.0.0:8080 router.php
 *
 * In Docker/Render production the .htaccess rewrite rules take over.
 */

// Resolve the real path of the requested URI
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve real static files (assets, images, etc.) directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Inject the URI as PATH_INFO so CI3 can parse the segments
$_SERVER['PATH_INFO'] = $uri;

// Hand off to CI3 front controller
require __DIR__ . '/index.php';
