<?php

require_once __DIR__ . '/functions/database.php';
require_once __DIR__ . '/functions/curl.php';

$db = db_connect();

$playlist_id = $_GET['playlist_id'] ?? null;

$meme_mode = false;

if ($meme_mode) {
    $song = db_query($db, 'SELECT * FROM songs WHERE id = 1949')[0] ?? null;
} else if (!empty($playlist_id) && is_numeric($playlist_id)) {
    $song = db_query($db, 'SELECT * FROM songs WHERE playlist_id = :playlist_id ORDER BY RANDOM() LIMIT 1', [
        ':playlist_id' => $playlist_id
    ])[0] ?? null;
} else if ($playlist_id === 'daily') {
    $song = db_query($db, 'SELECT songs.* 
    FROM songs 
    JOIN playlists 
    ON songs.playlist_id = playlists.id 
    WHERE playlists.daily = 1 
    ORDER BY md5(:hoje || songs.id) 
    LIMIT 1', [
        ':hoje' => date('Y-m-d')
    ])[0] ?? null;
}

if (!empty($song)) {
    $url_track = 'https://api.deezer.com/track/' . $song['deezer_id'];
    $track_data = curl_get($url_track);
    $song['preview_url'] = $track_data['preview'];

    header('Content-Type: application/octet-stream');
    echo base64_encode(json_encode($song, JSON_NUMERIC_CHECK));
    exit;
}

http_response_code(404);
