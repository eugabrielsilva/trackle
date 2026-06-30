<?php

require_once __DIR__ . '/functions/database.php';

$playlist_id = $_GET['playlist_id'] ?? null;

$meme_mode = false;

if ($meme_mode) {
    $songs = db_query($db, 'SELECT deezer_id, name, artist FROM songs WHERE id = 1949');
} else if (!empty($playlist_id) && is_numeric($playlist_id)) {
    $songs = db_query($db, 'SELECT deezer_id, name, artist FROM songs WHERE playlist_id = :playlist_id ORDER BY name ASC', [
        ':playlist_id' => $playlist_id
    ]);
} else {
    $songs = db_query($db, 'SELECT MIN(songs.deezer_id) AS deezer_id, 
    MIN(songs.name) AS name, 
    MIN(songs.artist) AS artist 
    FROM songs 
    JOIN playlists 
    ON songs.playlist_id = playlists.id 
    WHERE playlists.daily = 1 
    GROUP BY songs.deezer_id 
    ORDER BY songs.name ASC');
}

if (!empty($songs)) {
    header('Content-Type: application/octet-stream');
    echo base64_encode(json_encode($songs, JSON_NUMERIC_CHECK));
    exit;
}

http_response_code(404);
