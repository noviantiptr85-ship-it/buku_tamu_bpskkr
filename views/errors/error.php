<?php $code = http_response_code() ?: 500; ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Terjadi Kesalahan') ?></title>
    <link rel="icon" href="/assets/img/logo-bps.svg" type="image/svg+xml">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="auth">
<main class="auth-wrap">
    <div class="auth-card" style="text-align:center;">
        <div style="font-size:56px;font-weight:800;color:var(--orange);line-height:1;"><?= (int) $code ?></div>
        <h1 style="margin-top:10px;"><?= e($title ?? 'Terjadi Kesalahan') ?></h1>
        <p class="muted"><?= e($message ?? '') ?></p>
        <a class="btn btn-primary" href="/" style="margin-top:12px;">Kembali ke Beranda</a>
    </div>
</main>
</body>
</html>
