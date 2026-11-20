<?php $siteName = setting('site_name', 'BPS Kabupaten Kubu Raya'); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Cetak') ?> · <?= e($siteName) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <style>
        body { background: #fff; }
        .print-toolbar { max-width: 1100px; margin: 0 auto; padding: 16px 24px 0; display: flex; justify-content: space-between; gap: 12px; }
        .print-page { max-width: 1100px; margin: 0 auto; padding: 24px; }
        @media print {
            .print-toolbar { display: none; }
            .print-page { max-width: none; padding: 0; }
            body { font-size: 12px; }
        }
    </style>
</head>
<body>
<?= $content ?>
<script>window.addEventListener('load', function () { /* biarkan pengguna menekan tombol cetak */ });</script>
</body>
</html>
