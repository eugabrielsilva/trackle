<?php

require_once __DIR__ . '/functions/database.php';

$playlist_id = $_GET['playlist_id'] ?? null;

$db = db_connect();

if (!empty($playlist_id) && is_numeric($playlist_id)) {
    $songs = db_query($db, 'SELECT id, name, artist FROM songs WHERE playlist_id = :playlist_id ORDER BY name ASC', [
        ':playlist_id' => $playlist_id
    ]);

    header('Content-Type: application/json');
    echo json_encode($songs, JSON_NUMERIC_CHECK);
} else {
    $songs = db_query($db, 'SELECT MIN(id) AS id, MIN(name) AS name, MIN(artist) AS artist FROM songs GROUP BY deezer_id ORDER BY name ASC');

    header('Content-Type: application/json');
    echo json_encode($songs, JSON_NUMERIC_CHECK);
}
