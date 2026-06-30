<?php

if (($_SERVER['HTTP_HOST'] ?? '') === 'localhost') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

date_default_timezone_set('America/Sao_Paulo');

function db_connect()
{
    $path = __DIR__ . '/../data/db.sqlite';

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    $pdo = new PDO("sqlite:$path", null, null, $options);

    $pdo->sqliteCreateFunction('md5', function ($string) {
        return md5($string);
    }, 1);

    return $pdo;
}

function db_query($db, $sql, $params = [])
{
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

$db = db_connect();
