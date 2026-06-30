$(function() {

    const $btnHelp = $('#help');
    const $playlists = $('#playlists');
    const $inputSearch = $('#search');
    const $btnFavorites = $('#btnFavorites');
    const $baseItem = $('.base-item');

    let playlists = [];
    let favorites = [];
    let search = '';
    let onlyFavorites = false;

    /**
     * Inicializar página.
     */
    function init() {
        resize();
        loadFavorites();
        loadPlaylists();
        showTutorial();
        $('[data-tooltip]').tooltip();
    }

    /**
     * Carregar favoritos salvos.
     */
    function loadFavorites() {
        const data = localStorage.getItem('favorites');

        if(data) {
            favorites = JSON.parse(data);
        }
    }

    /**
     * Salvar favoritos.
     */
    function saveFavorites() {
        localStorage.setItem('favorites', JSON.stringify(favorites));
        updatePlaylists();
    }

    /**
     * Carregar playlists.
     */
    function loadPlaylists() {
        $.getJSON('playlists', response => {
            playlists = response;
            updatePlaylists();
        });
    }

    /**
     * Atualizar lista de playlists.
     */
    function updatePlaylists() {
        $playlists.empty();

        let sortedPlaylists = [...playlists].sort((a, b) => {
            const aFav = favorites.includes(a.id);
            const bFav = favorites.includes(b.id);

            if(aFav && !bFav) return -1;
            if(!aFav && bFav) return 1;
            return 0;
        });

        if(search !== '') {
            sortedPlaylists = sortedPlaylists.filter(playlist => playlist.name.toLowerCase().includes(search));
        }

        if(onlyFavorites) {
            sortedPlaylists = sortedPlaylists.filter(playlist => favorites.includes(playlist.id));
        }

        if(sortedPlaylists.length === 0) {
            $playlists.append('<div class="text-center fs-5 py-5 text-muted">Nenhuma playlist encontrada.</div>');
            return;
        }

        sortedPlaylists.forEach(playlist => {
            const $item = $baseItem.clone();

            $item.find('img').attr('src', playlist.picture_url).attr('alt', playlist.name).attr('title', playlist.name);
            $item.find('a').attr('href', playlist.id === 'daily' ? 'daily' : `playlist/${playlist.id}`);

            $item.find('.btn-favorite').on('click', function() {
                if(favorites.includes(playlist.id)) {
                    favorites = favorites.filter(id => id !== playlist.id);
                } else {
                    favorites.push(playlist.id);
                }

                saveFavorites();
            }).toggleClass('active', favorites.includes(playlist.id));

            $item.removeClass('d-none');

            $playlists.append($item);
        });
    }

    /**
     * Exibir o tutorial.
     * @param {*} force 
     */
    function showTutorial(force = false) {
        if(force) {
            $('#tutorialModal').modal('show');
        } else if(!localStorage.getItem('tutorial')) {
            $('#tutorialModal').modal('show');
        }

        localStorage.setItem('tutorial', true);
    }

    /**
     * Redimensionar tela.
     */
    function resize() {
        $('html').css('font-size', ((window.innerHeight / 980) * 16 * 1.1) + 'px');
    }

    window.onresize = function() {
        setTimeout(resize, 500);
    };

    $btnHelp.on('click', function() {
        showTutorial(true);
    });

    $inputSearch.on('input', function() {
        search = $(this).val().toLowerCase().trim();
        updatePlaylists();
    });

    $btnFavorites.on('click', function() {
        onlyFavorites = !onlyFavorites;
        $(this).toggleClass('btn-outline-secondary', !onlyFavorites);
        $(this).toggleClass('btn-secondary', onlyFavorites);
        updatePlaylists();
    });

    init();

});