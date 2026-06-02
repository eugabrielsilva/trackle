<?php

require_once __DIR__ . '/functions/database.php';

$db = db_connect();
$song_count = db_query($db, 'SELECT COUNT(DISTINCT deezer_id) AS count FROM songs')[0]['count'];

$folder = trim(mb_substr($_SERVER['PHP_SELF'], 0, mb_strpos($_SERVER['PHP_SELF'], '/index.php')), '/');
$baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/' . $folder . (!empty($folder) ? '/' : '');

?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">

<?php include_once __DIR__ . '/functions/headers.php'; ?>

<body class="bg-dark text-white d-block">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8 position-relative">
                <h1 class="mb-2">Trackle</h1>
                <button class="btn btn-help" id="help" data-tooltip title="AJUDA">
                    <i class="fas fa-question-circle"></i>
                </button>
                <div class="volume">
                    <a href="https://github.com/eugabrielsilva/trackle" target="_blank" class="btn" data-tooltip title="GITHUB">
                        <i class="fab fa-github"></i>
                    </a>
                </div>
                <h2 class="mb-4">Escolha sua playlist favorita:</h2>
                <div class="d-flex gap-3 mb-4">
                    <input type="text" class="form-control" id="search" placeholder="Pesquise uma playlist">
                    <button class="btn btn-outline-secondary" id="btnFavorites" data-tooltip title="FAVORITAS">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
                <div class="row g-3" id="playlists">
                    <div class=" col-6 col-sm-4 col-md-3 base-item d-none">
                        <div class="position-relative">
                            <button class="btn-favorite"><i class="far fa-heart"></i></button>
                            <a href="" class="d-block">
                                <img src="" class="w-100 border border-secondary">
                            </a>
                        </div>
                    </div>
                </div>
                <h5 class="mt-4"><?= number_format($song_count, 0, '', '.') ?> músicas disponíveis e subindo!</h5>
                <small class="d-block mt-4 text-secondary">
                    Este jogo é gratuito e utiliza dados fornecidos pela API do Deezer. Todos os direitos sobre as obras pertencem aos seus respectivos artistas e gravadoras. Desenvolvido por <a class="text-secondary" href="https://gabrielsilva.dev.br" target="_blank">Gabriel Silva</a>
                </small>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/functions/tutorial.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl; ?>assets/index.js?version=<?= md5(filemtime(__DIR__ . '/assets/index.js')); ?>"></script>
</body>

</html>