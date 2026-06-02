<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Trackle</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?? ''; ?>assets/app.css?version=<?= md5(filemtime(__DIR__ . '/../assets/app.css')); ?>">
    <base href="<?= $baseUrl ?? ''; ?>">
    <link rel="shortcut icon" href="<?= $baseUrl ?? ''; ?>assets/icon.png">

    <!-- SEO -->
    <meta name="description" content="Será que você consegue adivinhar a música no menor tempo possível? Teste seus conhecimentos musicais no Trackle, o desafio diário para quem ama música!">
    <meta name="keywords" content="Trackle, jogo de musica, adivinhar a musica, qual é a musica, game musical, desafio diario de musica, guess the song, heardle, deezer, termo">
    <meta name="robots" content="index, follow">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= $baseUrl ?? ''; ?>">
    <meta property="og:title" content="Trackle">
    <meta property="og:description" content="Será que você consegue adivinhar a música no menor tempo possível? Venha testar seus ouvidos!">
    <meta property="og:image" content="<?= $baseUrl ?? ''; ?>assets/icon.png">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?= $baseUrl ?? ''; ?>">
    <meta name="twitter:title" content="Trackle">
    <meta name="twitter:description" content="Será que você consegue adivinhar a música no menor tempo possível? Desafie seus amigos!">
    <meta name="twitter:image" content="<?= $baseUrl ?? ''; ?>assets/icon.png">
</head>