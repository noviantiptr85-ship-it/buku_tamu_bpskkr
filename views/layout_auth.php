<?php $siteName = $siteName ?? setting('site_name', 'BPS Kabupaten Kubu Raya'); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(($title ?? 'Masuk') . ' · ' . $siteName) ?></title>
    <link rel="icon" href="/assets/img/logo-bps.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="auth">
<main class="auth-wrap">
    <?= $content ?>
</main>
</body>
</html>
