<?php

require_once __DIR__ . '/functions/database.php';

$playlists = db_query($db, 'SELECT * FROM playlists ORDER BY name ASC');

array_unshift($playlists, [
    'id' => 'daily',
    'name' => 'Desafio Diário',
    'picture_url' => 'assets/daily.png',
]);

header('Content-Type: application/json');
echo json_encode($playlists, JSON_NUMERIC_CHECK);
