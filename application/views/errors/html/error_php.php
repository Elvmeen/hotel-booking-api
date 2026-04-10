<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Application Error</title>
<style>
body{background:#fff;color:#333;font-family:Arial,sans-serif;padding:40px}
h1{color:#c00;border-bottom:1px solid #ddd;padding-bottom:10px}
pre{background:#f5f5f5;padding:15px;border-left:4px solid #c00;overflow:auto}
</style>
</head>
<body>
<h1>Application Error</h1>
<p>A PHP error was encountered:</p>
<pre><?php echo htmlspecialchars($severity . ': ' . $message, ENT_QUOTES, 'UTF-8'); ?></pre>
<p><strong>File:</strong> <?php echo htmlspecialchars($filepath, ENT_QUOTES, 'UTF-8'); ?></p>
<p><strong>Line:</strong> <?php echo $line; ?></p>
</body>
</html>
