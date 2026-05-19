<?php

require_once __DIR__ . '/functions/database.php';
$db = db_connect();

if (!empty($_GET['playlist_id'])) {
    $playlist = db_query($db, 'SELECT * FROM playlists WHERE id = :id', [
        ':id' => $_GET['playlist_id']
    ])[0] ?? null;

    if (empty($playlist)) {
        header('Location: index.php');
        exit;
    }
} else {
    $playlist = [
        'id' => 'daily',
        'name' => 'Desafio Diário',
    ];
}

$folder = trim(mb_substr($_SERVER['PHP_SELF'], 0, mb_strpos($_SERVER['PHP_SELF'], '/game.php')), '/');
$baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/' . $folder . (!empty($folder) ? '/' : '');

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trackle</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $baseUrl; ?>assets/app.css?version=<?= md5(filemtime(__DIR__ . '/assets/app.css')); ?>">
    <base href="<?= $baseUrl; ?>">
    <link rel="shortcut icon" href="<?= $baseUrl; ?>assets/icon.png">
</head>

<body class="bg-dark text-white" data-playlist-id="<?= $playlist['id']; ?>">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-6 position-relative">
                <h1 class="mb-2">Trackle</h1>
                <button class="btn btn-help" id="help" data-bs-toggle="tooltip" title="AJUDA">
                    <i class="fas fa-question-circle"></i>
                </button>
                <h2 class="mb-4">
                    <?= $playlist['name']; ?>
                    <a href="index.php" class="text-white ms-1" data-bs-toggle="tooltip" title="TROCAR PLAYLIST">
                        <i class="fas fa-arrow-right-arrow-left"></i>
                    </a>
                </h2>
                <div class="guesses" id="guesses"></div>
                <button class="btn btn-play" id="play" disabled tabindex="-1">
                    <i class="fas fa-spinner fa-spin"></i>
                </button>
                <div class="d-flex gap-3 align-items-center controls">
                    <div class="position-relative w-100">
                        <div class="options-box d-none bg-secondary" id="options"></div>
                        <input type="text" class="form-control" placeholder="Pesquise por nome ou artista" id="input">
                    </div>
                    <button class="btn btn-success text-nowrap" id="submit" disabled data-bs-toggle="tooltip" title="PALPITE">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                    <button class="btn btn-secondary text-nowrap" id="skip" data-bs-toggle="tooltip" title="PULAR">
                        <i class="fas fa-forward"></i>
                    </button>
                </div>
                <small class="d-block mt-4 text-secondary">
                    Este jogo é gratuito e utiliza dados fornecidos pela API do Deezer. Todos os direitos sobre as obras pertencem aos seus respectivos artistas e gravadoras.
                </small>
            </div>
        </div>
    </div>

    <div class="modal fade" id="endModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h1 class="mb-2" id="modal-title">Você acertou! 🎉</h1>
                    <h5 id="streak" class="mb-4">Sequência atual: 0</h5>
                    <a href="" id="result-url" class="d-block text-decoration-none text-white" target="_blank">
                        <img src="" id="result-img" class="w-100 img-cover mb-4">
                        <h4 id="result-name"></h4>
                        <h5 class="mb-4" id="result-artist"></h5>
                    </a>
                    <div class="d-flex gap-3 justify-content-center">
                        <?php if ($playlist['id'] !== 'daily') : ?>
                            <button class="btn btn-lg btn-primary" id="replay">
                                <i class="fas fa-sync me-1"></i>
                                Jogar novamente
                            </button>
                        <?php else : ?>
                            <button class="btn btn-lg" disabled>
                                Próximo em <span id="countdown"></span>
                            </button>
                        <?php endif; ?>
                        <button class="btn btn-light" id="share" data-bs-toggle="tooltip" title="COMPARTILHAR">
                            <i class="fas fa-share-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shareModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body position-relative text-center">
                    <h5 class="mb-4">Compartilhar</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                    <div class="d-flex justify-content-center gap-3">
                        <button class="btn btn-lg btn-secondary btn-share" data-via="x">
                            <i class="fab fa-x-twitter"></i>
                        </button>
                        <button class="btn btn-lg btn-primary btn-share" data-via="facebook">
                            <i class="fab fa-facebook"></i>
                        </button>
                        <button class="btn btn-lg btn-success btn-share" data-via="whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tutorialModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body position-relative">
                    <h3 class="text-center mb-4">Bem-vindo ao Trackle!</h3>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                    <ul class="fa-ul">
                        <li>
                            <span class="fa-li">
                                <i class="fas fa-music"></i>
                            </span>
                            O objetivo do jogo é acertar a música no <strong>menor tempo</strong> possível.
                        </li>
                        <li>
                            <span class="fa-li">
                                <i class="fas fa-hourglass"></i>
                            </span>
                            Você tem <strong>10 tentativas</strong> para acertar qual é a música.
                        </li>
                        <li>
                            <span class="fa-li">
                                <i class="far fa-play-circle"></i>
                            </span>
                            Clique no botão <strong>OUVIR</strong> para ouvir um trecho da música.
                        </li>
                        <li>
                            <span class="fa-li">
                                <i class="fas fa-paper-plane"></i>
                            </span>
                            Digite seu palpite no campo de busca e clique em <strong>PALPITE</strong>.
                        </li>
                        <li>
                            <span class="fa-li">
                                <i class="fas fa-forward"></i>
                            </span>
                            Se não souber, clique em <strong>PULAR</strong> para ir para a próxima tentativa.
                        </li>
                        <li>
                            <span class="fa-li">
                                <i class="fas fa-plus-circle"></i>
                            </span>
                            A cada tentativa, você pode ouvir <strong>+1 segundo</strong> da música.
                        </li>
                        <li>
                            <span class="fa-li">
                                <i class="fas fa-arrow-right-arrow-left"></i>
                            </span>
                            Você pode tentar outras playlists clicando no botão <strong>TROCAR</strong>.
                        </li>
                    </ul>
                    <div class="text-center mt-3">
                        <button class="btn btn-lg btn-primary" data-bs-dismiss="modal">
                            Jogar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/app.js?version=<?= md5(filemtime(__DIR__ . '/assets/app.js')); ?>"></script>
    <script src="<?= $baseUrl; ?>assets/responsive.js?version=<?= md5(filemtime(__DIR__ . '/assets/responsive.js')); ?>"></script>
</body>

</html>