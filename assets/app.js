$(function() {

    const $guessesContainer = $('#guesses');
    const $optionsContainer = $('#options');
    const $btnPlay = $('#play');
    const $btnSubmit = $('#submit');
    const $btnSkip = $('#skip');
    const $btnReplay = $('#replay');
    const $btnShare = $('#share');
    const $btnHelp = $('#help');
    const $inputSearch = $('#input');
    const $txtCountdown = $('#countdown');
    const $txtStreak = $('#streak');
    const $btnsShare = $('.btn-share');

    const playlistId = $('body').attr('data-playlist-id');
    const isDaily = playlistId === 'daily';

    let answer = null;
    let audio = null;
    let audioTimeout = null;
    let isPlaying = false;
    let currentGuess = 1;
    let guesses = [];
    let options = [];
    let selectedOption = null;
    let dailyInfo = null;
    let streak = {};

    $('[data-bs-toggle="tooltip"]').tooltip();

    /**
     * Inicializar jogo.
     */
    function init() {
        loadDailyInfo();
        updateGuesses();
        loadMusic();
        showTutorial();
        loadStreak();
    }

    /**
     * Carregar pontuação.
     */
    function loadStreak() {
        const data = localStorage.getItem('streak');

        if(data) {
            streak = JSON.parse(data);
        }

        $txtStreak.text(`Sequência atual: ${streak[playlistId] || 0}`);
    }

    /**
     * Atualizar pontuação.
     */
    function updateStreak(reset = false) {
        if(reset) {
            streak[playlistId] = 0;
        } else if(streak[playlistId]) {
            streak[playlistId]++;
        } else {
            streak[playlistId] = 1;
        }

        localStorage.setItem('streak', JSON.stringify(streak));
        $txtStreak.text(`Sequência atual: ${streak[playlistId] || 0}`);
    }

    /**
     * Carregar informações do jogo diário.
     */
    function loadDailyInfo() {
        if(isDaily) {
            const data = localStorage.getItem('daily-info');

            if(data) {
                const jsonData = JSON.parse(data);

                if(jsonData.date === new Date().toLocaleDateString()) {
                    dailyInfo = jsonData;
                    guesses = dailyInfo.guesses;
                    currentGuess = dailyInfo.guesses.length + 1;
                    answer = dailyInfo.answer;

                    if(dailyInfo.win) {
                        endGame(true);
                    } else if(dailyInfo.loss) {
                        endGame(false);
                    }
                } else {
                    localStorage.removeItem('daily-info');
                }
            }

            updateDailyTimer();
            setInterval(updateDailyTimer, 1000);
        }
    }

    /**
     * Atualiza contagem regressiva do próximo jogo diário.
     */
    function updateDailyTimer() {
        const now = new Date();
        const nextDay = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
        const diff = Math.floor((nextDay - now) / 1000);
        const diffText = new Date(diff * 1000).toISOString().substr(11, 8);
        $txtCountdown.text(diffText);
    }

    /**
     * Salvar informações do jogo diário.
     */
    function saveDailyInfo(win = false, loss = false) {
        if(isDaily) {
            const newData = {
                date: new Date().toLocaleDateString(),
                answer,
                guesses,
                win,
                loss
            };

            localStorage.setItem('daily-info', JSON.stringify(newData));
        }
    }

    /**
     * Carregar música e opções de resposta.
     */
    function loadMusic() {
        if(!dailyInfo?.win && !dailyInfo?.loss) {
            $.getJSON(`music?playlist_id=${playlistId}`, musicResponse => {
                answer = musicResponse;
                audio = new Audio(answer.preview_url);

                $btnPlay.prop('disabled', false).html(`<i class="far fa-play-circle me-2"></i>Ouvir ${currentGuess}s`);

                $.getJSON(`list?playlist_id=${playlistId}`, listResponse => {
                    options = listResponse;
                }).fail(error => {
                    alert('Erro ao carregar a lista de músicas. Por favor, tente novamente mais tarde.');
                    console.error(error);
                });

            }).fail(error => {
                alert('Erro ao carregar a música. Por favor, tente novamente mais tarde.');
                console.error(error);
            });
        }
    }

    /**
     * Atualizar lista de palpites.
     */
    function updateGuesses() {
        $guessesContainer.empty();

        for(let i = 1; i <= 10; i++) {
            const guess = guesses[i - 1];
            const $item = $(`<div class="guess"></div>`);

            if(i === currentGuess && !dailyInfo?.win && !dailyInfo?.loss) {
                $item.addClass('border-white');
            }

            if(guess) {
                $item.removeClass('border-white');
                if(guess.correct) {
                    $item.addClass('border-success text-success');
                    $item.html(`<i class="fa fa-check me-1"></i>${guess.text}`);
                } else if(guess.skipped) {
                    $item.addClass('border-secondary text-secondary');
                    $item.html(`<i class="fa fa-fast-forward me-1"></i>${guess.text}`);
                } else {
                    $item.addClass('border-danger text-danger');
                    $item.html(`<i class="fa fa-xmark me-1"></i>${guess.text}`);
                }
            }

            $guessesContainer.append($item);
        }
    }

    /**
     * Tocar áudio.
     * @param {*} createTimeout 
     */
    function playAudio(createTimeout = true) {
        if(audio) {
            isPlaying = true;

            audio.currentTime = 0;
            audio.play();

            if(createTimeout) {
                $btnPlay.html('<i class="far fa-stop-circle me-2"></i>Parar');
                audioTimeout = setTimeout(stopAudio, currentGuess * 1000);
            }
        }
    }

    /**
     * Parar áudio.
     */
    function stopAudio() {
        if(audio) {
            isPlaying = false;
            audio.pause();

            $btnPlay.html(`<i class="far fa-play-circle me-2"></i>Ouvir ${currentGuess}s`);

            if(audioTimeout) clearTimeout(audioTimeout);
            audioTimeout = null;
        }
    }

    /**
     * Remover acentos de string.
     * @param {*} string 
     * @returns 
     */
    function normalizeString(string) {
        return string.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    }

    /**
     * Atualizar lista de opções de resposta.
     */
    function updateOptions() {
        clearOptions();
        const searchQuery = $inputSearch.val().trim();

        if(searchQuery.length >= 3) {
            const filteredOptions = options.filter(option => {
                const name = normalizeString(`${option.name} - ${option.artist}`);
                const search = normalizeString(searchQuery);
                return name.includes(search);
            }).slice(0, 10);

            if(filteredOptions.length) {
                $optionsContainer.removeClass('d-none');

                filteredOptions.forEach(option => {
                    const text = `${option.name} - ${option.artist}`.toLowerCase();
                    const $option = $(`<div class="option">${text}</div>`);

                    $option.on('click', function() {
                        selectOption(option, text);
                    });

                    $optionsContainer.append($option);
                });
            } else {
                clearOptions();
            }
        }
    }

    /**
     * Limpar lista de opções de resposta.
     */
    function clearOptions() {
        $optionsContainer.empty();
        $optionsContainer.addClass('d-none');

        selectedOption = null;
        $btnSubmit.prop('disabled', true);
    }

    /**
     * Selecionar opção de música na busca.
     * @param {*} option 
     * @param {*} text 
     */
    function selectOption(option, text) {
        $optionsContainer.empty();
        $optionsContainer.addClass('d-none');

        selectedOption = option;
        $inputSearch.val(text);
        $btnSubmit.prop('disabled', false);
    }

    /**
     * Enviar palpite.
     */
    function makeGuess() {
        const text = `${selectedOption.name} - ${selectedOption.artist}`.toLowerCase();

        guesses.push({
            text,
            correct: selectedOption.id === answer.id,
            skipped: false
        });

        finishGuess();
    }

    /**
     * Pular palpite atual.
     */
    function skip() {
        guesses.push({
            text: 'pulado',
            correct: false,
            skipped: true
        });

        finishGuess();
    }

    /**
     * Finalizar jogada após palpite ou pulo.
     */
    function finishGuess() {
        $inputSearch.val('');
        clearOptions();
        nextGuess();
        updateGuesses();
    }

    /**
     * Ir para o próximo palpite e verificar fim de jogo.
     */
    function nextGuess() {
        if(guesses.some(guess => guess.correct)) {
            saveDailyInfo(true);
            endGame(true);
            updateStreak();
        } else if(currentGuess === 10) {
            saveDailyInfo(false, true);
            endGame();
            updateStreak(true);
        } else {
            saveDailyInfo();
            currentGuess++;

            if(!isPlaying) {
                $btnPlay.html(`<i class="far fa-play-circle me-2"></i>Ouvir ${currentGuess}s`);
            }
        }
    }

    /**
     * Finalizar o jogo.
     * @param {*} win 
     */
    function endGame(win = false) {
        $('#result-img').attr('src', answer.picture_url);
        $('#result-name').text(answer.name);
        $('#result-artist').text(answer.artist);
        $('#result-url').attr('href', `https://www.deezer.com/track/${answer.deezer_id}`);

        if(win) {
            $('#modal-title').text('Você acertou! 🎉');
        } else {
            $('#modal-title').text('Você perdeu! 😔');
        }

        if(!dailyInfo?.win && !dailyInfo?.loss) {
            stopAudio();
            playAudio(false);
        }

        $('#endModal').modal('show');
    }

    /**
     * Exibir o tutorial.
     * @param {*} force 
     */
    function showTutorial(force = false) {
        if(force) {
            $('#tutorialModal').modal('show');
        } else if(!localStorage.getItem('tutorial') && !dailyInfo?.win && !dailyInfo?.loss) {
            $('#tutorialModal').modal('show');
        }

        localStorage.setItem('tutorial', true);
    }

    /**
     * Função de compartilhamento.
     * @param {*} via 
     */
    function share(via) {
        const isCorrect = guesses.some(guess => guess.correct);
        const time = guesses.length === 1 ? 'segundo' : 'segundos';

        let text;
        let url;

        if(isDaily) {
            text = isCorrect ? `Acertei a música de hoje em ${guesses.length} ${time}! Será que você consegue em menos tempo?` : `Não acertei a música de hoje! Será que você consegue?`;
        } else {
            text = isCorrect ? `Acertei a música "${answer.name} - ${answer.artist}" em ${guesses.length} ${time}!` : `Não acertei a música "${answer.name} - ${answer.artist}". Será que você consegue?`;
        }

        if(via === 'x') {
            url = `https://x.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(window.location.href)}`;
        } else if(via === 'facebook') {
            url = `https://facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}&quote=${encodeURIComponent(text)}`;
        } else if(via === 'whatsapp') {
            url = `https://wa.me/?text=${encodeURIComponent(`${text} ${window.location.href}`)}`;
        }

        window.open(url, '_blank');
    }

    $(document).on('show.bs.modal', '.modal', function() {
        const zIndex = 1040 + 10 * $('.modal:visible').length;
        $(this).css('z-index', zIndex);
        setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
    });

    $inputSearch.on('input', updateOptions);

    $btnSubmit.on('click', makeGuess);

    $btnSkip.on('click', skip);

    $inputSearch.on('blur', function() {
        setTimeout(() => {
            $optionsContainer.addClass('d-none');
        }, 200);
    });

    $btnReplay.on('click', function() {
        location.reload();
    });

    $btnPlay.on('click', function() {
        if(!isPlaying) {
            playAudio();
        } else {
            stopAudio();
        }
    });

    $btnShare.on('click', function() {
        $('#shareModal').modal('show');
    });

    $btnsShare.on('click', function() {
        const via = $(this).attr('data-via');
        share(via);
    });

    $btnHelp.on('click', function() {
        showTutorial(true);
    });

    init();

});