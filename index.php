<?php

require_once __DIR__ . '/functions/database.php';
$db = db_connect();
$playlists = db_query($db, 'SELECT * FROM playlists ORDER BY RANDOM()');

$song_count = db_query($db, 'SELECT COUNT(DISTINCT deezer_id) AS count FROM songs')[0]['count'];

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Trackle</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="assets/app.css">
    <link rel="shortcut icon" href="assets/icon.png">
</head>

<body class="bg-dark text-white">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8 position-relative">
                <h1 class="mb-2">Trackle</h1>
                <a href="https://github.com/eugabrielsilva/trackle" target="_blank" class="btn btn-help" id="help" data-bs-toggle="tooltip" title="GITHUB">
                    <i class="fab fa-github"></i>
                </a>
                <h2 class="mb-4">Escolha sua playlist favorita:</h2>
                <div class="row g-3">
                    <div class="col-6 col-sm-4 col-md-3">
                        <a href="daily">
                            <img src="assets/daily.png" alt="Desafio Diário" class="w-100 border border-secondary">
                        </a>
                    </div>
                    <?php foreach ($playlists as $playlist): ?>
                        <div class="col-6 col-sm-4 col-md-3">
                            <a href="playlist/<?= $playlist['id'] ?>">
                                <img src="<?= $playlist['picture_url'] ?>" alt="<?= $playlist['name'] ?>" class="w-100 border border-secondary">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <h5 class="mt-4"><?= $song_count ?> músicas disponíveis e subindo!</h5>
                <small class="d-block mt-4 text-secondary">
                    Este jogo é gratuito e utiliza dados fornecidos pela API do Deezer. <br> Todos os direitos sobre as obras pertencem aos seus respectivos artistas e gravadoras.
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
    <script src="assets/responsive.js"></script>
</body>

</html>