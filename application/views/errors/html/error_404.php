<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>404 – Page Not Found</title>
<style>
body{background:#fff;color:#333;font-family:Arial,sans-serif;padding:40px;text-align:center}
h1{font-size:80px;margin:0;color:#c00}
h2{margin-top:0;color:#666}
</style>
</head>
<body>
<h1>404</h1>
<h2>Page Not Found</h2>
<p>The page you requested was not found: <strong><?php echo htmlspecialchars($heading, ENT_QUOTES, 'UTF-8'); ?></strong></p>
</body>
</html>
