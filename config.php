<?php
$_ENV = parse_ini_file('.env');

if($_ENV['MODE'] === 'dev') {
    ini_set('display_errors',1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors',0);
}

include_once('functions.php');

//myPrint_r($_ENV);

try {
    $db = new PDO(
        sprintf('mysql:host=%s;dbname=%s;charset=utf8', $_ENV['DB_HOST'], $_ENV['DB_NAME']),
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(Exeception $e) {
    die('Erreur : ' . $e->getMessage());
}

//myPrint_r($db);

include("headers.php");

session_start();