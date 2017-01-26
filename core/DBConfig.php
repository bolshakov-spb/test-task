<?php
ini_set("default_charset", 'utf-8');

$dbName = 'cp483113_test';
$dbHost = 'localhost';
$dbUser = 'cp483113_root';
$dbPassword = 'TGUqLg@aik-8';

$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8";

$opt = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, $opt);
} catch (PDOException $e) {
    echo('Подключение не удалось: ' . $e->getMessage());
}