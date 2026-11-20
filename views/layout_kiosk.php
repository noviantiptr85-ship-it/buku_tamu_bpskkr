<?php
$siteName = setting('site_name', 'BPS Kabupaten Kubu Raya');
$bodyClass = $bodyClass ?? 'kiosk';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?= e(($title ?? 'Buku Tamu') . ' · ' . $siteName) ?></title>
    <link rel="icon" href="/assets/img/logo-bps.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/css/app.css">
    <link rel="stylesheet" href="/assets/css/kiosk.css">
</head>
<body class="<?= e($bodyClass) ?>">
<?= $content ?>
<script src="/assets/js/kiosk.js"></script>
</body>
</html>
