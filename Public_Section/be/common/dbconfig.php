<?php

$dbhost = "127.0.0.1";
$dbname = "TastyBites";
$dbuser = "root";
$dbpassword = ""; 
$dbport = "3307"; 
$db = null;

try {
    $dsn = "mysql:host=$dbhost;dbname=$dbname;port=$dbport;";
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );
    $db = new PDO($dsn, $dbuser, $dbpassword, $options);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

?>