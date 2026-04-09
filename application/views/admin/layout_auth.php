<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking — Admin Login</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="auth-body">
    <?= $this->load->view($page, $data ?? [], TRUE) ?>
</body>
</html>
