<?php

require_once __DIR__ . '/functions/database.php';
require_once __DIR__ . '/functions/curl.php';

$db = db_connect();

$playlist_id = $_GET['playlist_id'] ?? null;

if (!empty($playlist_id) && is_numeric($playlist_id)) {
    $song = db_query($db, 'SELECT * FROM songs WHERE playlist_id = :playlist_id ORDER BY RANDOM() LIMIT 1', [
        ':playlist_id' => $playlist_id
    ])[0];
} else if ($playlist_id === 'daily') {
    $song = db_query($db, 'SELECT * FROM songs ORDER BY md5(:hoje || id) LIMIT 1', [
        ':hoje' => date('Y-m-d')
    ])[0];
}

if (!empty($song)) {
    $url_track = 'https://api.deezer.com/track/' . $song['deezer_id'];
    $track_data = curl_get($url_track);
    $song['preview_url'] = $track_data['preview'];

    header('Content-Type: application/octet-stream');
    echo base64_encode(json_encode($song, JSON_NUMERIC_CHECK));
}
