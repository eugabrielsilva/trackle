<?php

require_once __DIR__ . '/functions/database.php';
$db = db_connect();

if (!empty($_GET['playlist_id'])) {
    $playlist = db_query($db, 'SELECT * FROM playlists WHERE id = :id', [
        ':id' => $_GET['playlist_id']
    ])[0] ?? null;

    if (empty($playlist)) {
        header('Location: ./');
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
<html lang="pt-br" data-bs-theme="dark">

<?php include_once __DIR__ . '/functions/headers.php'; ?>

<body class="bg-dark text-white" data-playlist-id="<?= $playlist['id']; ?>">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-6 position-relative">
                <h1 class="mb-2">Trackle</h1>
                <button class="btn btn-help" id="help" data-tooltip title="AJUDA">
                    <i class="fas fa-question-circle"></i>
                </button>
                <div class="dropdown volume">
                    <button class="btn btn-volume" data-tooltip data-bs-toggle="dropdown" title="VOLUME">
                        <i class="fas fa-volume-up"></i>
                    </button>
                    <div class="dropdown-menu rounded-4 p-0">
                        <div class="dropdown-item py-2 rounded-4 d-flex gap-2">
                            <input type="range" class="form-range" min="0" max="100" id="volume" value="100">
                            <div id="volume-value">100%</div>
                        </div>
                    </div>
                </div>
                <h2 class="fw-light mb-4">
                    <?= $playlist['name']; ?>
                    <a href="./" class="text-white ms-1" data-tooltip title="TROCAR PLAYLIST">
                        <i class="fas fa-arrow-right-arrow-left"></i>
                    </a>
                </h2>
                <div class="guesses" id="guesses"></div>
                <button class="btn btn-play" id="play" disabled tabindex="-1">
                    <i class="fas fa-spinner fa-spin"></i>
                </button>
                <div class="d-flex gap-3 align-items-stretch controls">
                    <div class="position-relative w-100">
                        <div class="options-box d-none" id="options"></div>
                        <input type="text" class="form-control h-100 rounded-3" placeholder="Pesquise por nome ou artista" id="input">
                    </div>
                    <button class="btn btn-lg btn-success text-nowrap" id="submit" disabled data-tooltip title="PALPITE">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                    <button class="btn btn-lg btn-secondary text-nowrap" id="skip" data-tooltip title="PULAR">
                        <i class="fas fa-forward"></i>
                    </button>
                </div>
                <small class="d-block mt-4 text-muted">
                    Este jogo é gratuito e utiliza dados fornecidos pela API do Deezer. Todos os direitos sobre as obras pertencem aos seus respectivos artistas e gravadoras. Desenvolvido por <a class="text-secondary" href="https://gabrielsilva.dev.br" target="_blank">Gabriel Silva</a>
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
                            <button class="btn btn-lg btn-info" id="replay">
                                <i class="fas fa-sync me-1"></i>
                                Jogar novamente
                            </button>
                        <?php else : ?>
                            <button class="btn btn-outline-success btn-lg" disabled>
                                Próximo em <span id="countdown">00:00:00</span>
                            </button>
                        <?php endif; ?>
                        <a href="./" class="btn btn-lg btn-secondary" data-tooltip title="TROCAR PLAYLIST">
                            <i class="fas fa-arrow-right-arrow-left align-middle"></i>
                        </a>
                        <button class="btn btn-lg btn-light" id="share" data-tooltip title="COMPARTILHAR">
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
                        <button class="btn btn-lg btn-dark btn-share" data-via="x">
                            <i class="fab fa-x-twitter"></i>
                        </button>
                        <button class="btn btn-lg btn-info btn-share" data-via="facebook">
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

    <?php include_once __DIR__ . '/functions/tutorial.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/app.js?version=<?= md5(filemtime(__DIR__ . '/assets/app.js')); ?>"></script>
</body>

</html>